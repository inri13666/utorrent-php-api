<?php
/**
 * User  : Nikita.Makarov
 * Date  : 7/20/15
 * Time  : 5:15 PM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma\Bittorrent\Common;


class Item
{
    /**
     * @var string
     */
    protected $magnetUri = NULL;
    /**
     * @var string
     */
    protected $name = NULL;
    /**
     * @var array
     */
    protected $trackers = array();
    /**
     * @var int
     */
    protected $size = NULL;

    public function getMagnetUri()
    {
        return $this->magnetUri;
    }

    /**
     * Web link to the file online
     */
    public function getSource()
    {

    }

    /**
     * @return string
     */
    public function getHash()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $tracker
     *
     * @return bool
     */
    public function hasTracker($tracker)
    {
        return in_array($tracker, $this->trackers);
    }

    /**
     * @param string $tracker
     *
     * @return $this
     */
    public function addTracker($tracker)
    {
        if (!$this->hasTracker($tracker)) {
            $this->trackers[] = $tracker;
        }
        return $this;
    }

    /**
     * @param string|array $tracker
     *
     * @return $this
     */
    public function removeTracker($tracker)
    {
        if ($this->hasTracker($tracker)) {
            $this->trackers = array_diff_assoc($this->trackers, (is_array($tracker) ? array_values($tracker) : array($tracker)));
        }
        return $this;
    }

    /**
     * @param string $torrentFile
     *
     * @return Item|null
     */
    public static function fromFile($torrentFile)
    {
        if (@is_readable($torrentFile)) {
            return self::fromString(file_get_contents($torrentFile, FILE_BINARY));
        } else {
            return NULL;
        }

    }

    public static function fromString($torrentString)
    {
        $ampersand = '&';
        $info = self::bdecode_getinfo($torrentString);
        return self::fromMagnetUri( sprintf('magnet:?xt=urn:btih:%2$s%1$sdn=%3$s%1$sxl=%4$d%1$str=%5$s',
            $ampersand,
            strtoupper($info['info_hash']),
            strtolower(urlencode($info['info']['name'])),
            $info['info']['size'],
            implode($ampersand . 'tr=',
                self::untier($info['announce-list'])
            )
        ));
    }

    /** Flatten announces list
     *
     * @param array announces list
     *
     * @return array flattened annonces list
     */
    static public function untier($announces)
    {
        $list = array();
        foreach ((array)$announces as $tier) {
            is_array($tier) ?
                $list = array_merge($list, self::untier($tier)) :
                array_push($list, urlencode($tier));
        }
        return $list;
    }

    public static function fromMagnetUri($magnetUri)
    {
        $sheme = parse_url($magnetUri, PHP_URL_SCHEME);
        // invalid URI scheme
        if ($sheme !== 'magnet') {
            return NULL;
        }
        $query = parse_url($magnetUri, PHP_URL_QUERY);
        if ($query === FALSE) {
            return NULL;
        }

        $query = str_replace('tr=', 'tr[]=', $query);
        parse_str($query, $data);
        /**
         * @@support-params-start
         * dn (Display Name) - Filename
         * xl (eXact Length) - Size in bytes
         * xt (eXact Topic) - URN containing file hash
         * as (Acceptable Source) - Web link to the file online
         * xs (eXact Source) - P2P link.
         * kt (Keyword Topic) - Key words for search
         * mt (Manifest Topic) - link to the metafile that contains a list of magneto (MAGMA - MAGnet MAnifest)
         * tr (address TRacker) - Tracker URL for BitTorrent downloads
         * @@support-params-end
         */
        $item = new self();
        $item->magnetUri = $magnetUri;
        $_xt = $data['xt'];
        $xt = explode(':', $_xt);
        $item->hash = array_pop($xt);
        $item->name = isset($data['dn']) ? $data['dn'] : NULL;
        if (isset($data['tr'])) {
            foreach ($data['tr'] as $tracker) {
                $item->addTracker($tracker);
            }
        }
        return $item;
    }

    public static function bdecode($s, &$pos = 0)
    {
        if ($pos >= strlen($s)) {
            return NULL;
        }
        switch ($s[$pos]) {
            case 'd':
                $pos++;
                $retval = array();
                while ($s[$pos] != 'e') {
                    $key = self::bdecode($s, $pos);
                    $val = self::bdecode($s, $pos);
                    if ($key === NULL || $val === NULL) {
                        break;
                    }
                    if ("name" == $key) {
                        $unpacked = unpack('a*', $val);
                        $retval[$key] = reset($unpacked);
                    } else {
                        $retval[$key] = $val;
                    }
                }
                $retval["isDct"] = TRUE;
                $pos++;
                return $retval;
            case 'l':
                $pos++;
                $retval = array();
                while ($s[$pos] != 'e') {
                    $val = self::bdecode($s, $pos);
                    if ($val === NULL) {
                        break;
                    }
                    $retval[] = $val;
                }
                $pos++;
                return $retval;
            case 'i':
                $pos++;
                $digits = strpos($s, 'e', $pos) - $pos;
                $val = (int)substr($s, $pos, $digits);
                $pos += $digits + 1;
                return $val;
            //	case "0": case "1": case "2": case "3": case "4":
            //	case "5": case "6": case "7": case "8": case "9":
            default:
                $digits = strpos($s, ':', $pos) - $pos;
                if ($digits < 0 || $digits > 20) {
                    return NULL;
                }
                $len = (int)substr($s, $pos, $digits);
                $pos += $digits + 1;
                $str = substr($s, $pos, $len);
                $pos += $len;
                //echo "pos: $pos str: [$str] len: $len digits: $digits\n";
                return (string)$str;
        }
    }

    public static function bencode(&$d)
    {
        $isDict = 0;
        if (is_array($d)) {
            $ret = "l";
            if (isset($d["isDct"]) && $d["isDct"]) {
                $isDict = 1;
                $ret = "d";
                // this is required by the specs, and BitTornado actualy chokes on unsorted dictionaries
                ksort($d, SORT_STRING);
            }
            foreach ($d as $key => $value) {
                if ($isDict) {
                    // skip the isDct element, only if it's set by us
                    if ($key == "isDct" and is_bool($value)) {
                        continue;
                    }
                    $ret .= strlen($key) . ":" . $key;
                }
                if (is_string($value)) {
                    $ret .= strlen($value) . ":" . $value;
                } elseif (is_int($value)) {
                    $ret .= "i${value}e";
                } else {
                    $ret .= self::bencode($value);
                }
            }
            return $ret . "e";
        } elseif (is_string($d)) // fallback if we're given a single bencoded string or int
        {
            return strlen($d) . ":" . $d;
        } elseif (is_int($d)) {
            return "i${d}e";
        } else {
            return NULL;
        }
    }

    public static function bdecode_file($filename)
    {
        $f = file_get_contents($filename, FILE_BINARY);
        return self::bdecode($f);
    }

    public static function bdecode_getinfo_file($filename)
    {
        return self::bdecode_getinfo(file_get_contents($filename, FILE_BINARY));
    }

    public static function bdecode_getinfo($data)
    {
        $t = self::bdecode($data);
        $t['info_hash'] = sha1(self::bencode($t['info']));
        if (is_array($t['info']['files'])) { //multifile
            $t['info']['size'] = 0;
            $t['info']['filecount'] = 0;
            foreach ($t['info']['files'] as $file) {
                $t['info']['filecount']++;
                $t['info']['size'] += $file['length'];
            }
        } else {
            $t['info']['size'] = $t['info']['length'];
            $t['info']["filecount"] = 1;
            $t['info']['files'][0]['path'] = $t['info']['name'];
            $t['info']['files'][0]['length'] = $t['info']['length'];
        }
        return $t;
    }
} 