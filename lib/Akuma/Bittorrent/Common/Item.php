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
    protected $magnetUri = null;
    /**
     * @var string
     */
    protected $name = null;
    /**
     * @var array
     */
    protected $trackers = array();
    /**
     * @var int
     */
    protected $size = null;

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
            return self::fromString(file_get_contents($torrentFile));
        } else {
            return null;
        }

    }

    public static function fromString($torrentString)
    {
        return new self();
    }

    public static function fromMagnetUri($magnetUri)
    {
        $sheme = parse_url($magnetUri, PHP_URL_SCHEME);
        # invalid URI scheme
        if ($sheme !== 'magnet') return null;
        $query = parse_url($magnetUri, PHP_URL_QUERY);
        if ($query === false) return null;

        $query = str_replace('tr=', 'tr[]=', $query);
        parse_str($query, $data);
        //var_dump($data);die();
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
        $item->hash = array_pop(explode(':', $data['xt']));
        $item->name = isset($data['dn']) ? $data['dn'] : null;
        if (isset($data['tr'])) {
            foreach ($data['tr'] as $tracker) {
                $item->addTracker($tracker);
            }
        }
        return $item;
    }

} 