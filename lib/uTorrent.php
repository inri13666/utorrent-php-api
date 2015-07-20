<?php
/**
 * User  : Nikita.Makarov
 * Date  : 7/20/15
 * Time  : 2:04 PM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma;

use Akuma\Queue\RssFeed;
use Akuma\Queue\RssFilter;
use Akuma\Queue\Torrent;

define("UTORRENT_TORRENT_HASH", 0);
define("UTORRENT_TORRENT_STATUS", 1);
define("UTORRENT_TORRENT_NAME", 2);
define("UTORRENT_TORRENT_SIZE", 3);
define("UTORRENT_TORRENT_PROGRESS", 4);
define("UTORRENT_TORRENT_DOWNLOADED", 5);
define("UTORRENT_TORRENT_UPLOADED", 6);
define("UTORRENT_TORRENT_RATIO", 7);
define("UTORRENT_TORRENT_UPSPEED", 8);
define("UTORRENT_TORRENT_DOWNSPEED", 9);
define("UTORRENT_TORRENT_ETA", 10);
define("UTORRENT_TORRENT_LABEL", 11);
define("UTORRENT_TORRENT_PEERS_CONNECTED", 12);
define("UTORRENT_TORRENT_PEERS_SWARM", 13);
define("UTORRENT_TORRENT_SEEDS_CONNECTED", 14);
define("UTORRENT_TORRENT_SEEDS_SWARM", 15);
define("UTORRENT_TORRENT_AVAILABILITY", 16);
define("UTORRENT_TORRENT_QUEUE_POSITION", 17);
define("UTORRENT_TORRENT_REMAINING", 18);
define("UTORRENT_FILEPRIORITY_HIGH", 3);
define("UTORRENT_FILEPRIORITY_NORMAL", 2);
define("UTORRENT_FILEPRIORITY_LOW", 1);
define("UTORRENT_FILEPRIORITY_SKIP", 0);
define("UTORRENT_TYPE_INTEGER", 0);
define("UTORRENT_TYPE_BOOLEAN", 1);
define("UTORRENT_TYPE_STRING", 2);
define("UTORRENT_STATUS_STARTED", 1);
define("UTORRENT_STATUS_CHECKED", 2);
define("UTORRENT_STATUS_START_AFTER_CHECK", 4);


class uTorrent
{
    /**
     * @var string
     */
    private static $base = "http://%s:%s/gui/%s";

    /**
     * @var  string
     */
    protected $host = '127.0.0.1';

    /**
     * @var  int
     */
    protected $port = '13666';

    /**
     * @var  string|null
     */
    protected $user = 'admin';

    /**
     * @var  string|null
     */
    protected $pwd = null;

    /**
     * @var  string|null
     */
    protected $token = null;

    /**
     * @var  string|null
     */
    protected $guid;

    public function __construct($host = null, $port = null, $user = null, $pwd = null)
    {
        if (!is_null($host)) {
            $this->host = $host;
        }
        if (!is_null($port)) {
            $this->port = $port;
        }
        if (!is_null($user)) {
            $this->user = $user;
        }
        if (!is_null($pwd)) {
            $this->pwd = $pwd;
        }

        if (!$this->getToken()) {
            //handle error here, don't know how to best do this yet
            throw new \Exception('could not get token');
        }
    }

    /**
     * @return null|string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return null|string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->pwd;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf(self::$base, $this->host, $this->port, 'token.html'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->pwd);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $headers = substr($output, 0, $info['header_size']);
        if (preg_match("@Set-Cookie: GUID=([^;]+);@i", $headers, $matches)) {
            $this->guid = $matches[1];
        }
        if (preg_match('/<div id=\'token\'.+>(.*)<\/div>/', $output, $m)) {
            $this->token = $m[1];
            return $this->token;
        }
        return false;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->pwd = $password;
        return $this;
    }

    /**
     * @param $token
     *
     * @return $this
     */
    protected function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function reload()
    {
        if (!$this->getToken()) {
            //handle error here, don't know how to best do this yet
            throw new \Exception('could not get token');
        }
    }

    /**
     * @return bool
     */
    public function isOnline()
    {
        return is_array($this->makeRequest("?"));
    }

    // performs request
    public function makeRequest($request, $decode = true, $options = array())
    {
        $request = preg_replace('/^\?/', '?token=' . $this->token . '&', $request);
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_URL, sprintf(self::$base, $this->host, $this->port, $request));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->pwd);
        curl_setopt($ch, CURLOPT_COOKIE, "GUID=" . $this->guid);
        $req = curl_exec($ch);
        curl_close($ch);
        return ($decode ? json_decode($req, true) : $req);
    }

    // returns the uTorrent build number
    public function getBuild()
    {
        $json = $this->makeRequest("?");
        return $json['build'];
    }
    // returns an array of files for the specified torrent hash
    // TODO:
    //  - (when implemented in API) allow multiple hashes to be specified
    public function getFiles($hash)
    {
        $json = $this->makeRequest("?action=getfiles&hash=" . $hash);
        return $json['files'];
    }

    // returns an array of all labels
    public function getLabels()
    {
        $json = $this->makeRequest("?list=1");
        return $json['label'];
    }
    // returns an array of the properties for the specified torrent hash
    // TODO:
    //  - (when implemented in API) allow multiple hashes to be specified
    public function getProperties($hash)
    {
        $json = $this->makeRequest("?action=getprops&hash=" . $hash);
        return $json['props'];
    }

    /**
     * returns an array of all settings
     *
     * @return mixed
     *
     * todo: return Settings
     */
    public function getSettings()
    {
        $json = $this->makeRequest("?action=getsettings");
        return $json['settings'];
    }

    /**
     * returns an array of all torrent jobs and related information
     *
     * @return array
     */
    public function getTorrents()
    {
        $json = $this->makeRequest("?list=1");
        $items = array();
        foreach($json['torrents'] as $item){
            $items[]= new Torrent($item);
        };
        return $items;
    }

    /**
     * @return array
     */
    public function getRssFeeds(){
        $json = $this->makeRequest("?list=1");
        $items = array();
        foreach($json['rssfeeds'] as $item){
            $items[]= new RssFeed($item);
        };
        return $items;
    }

    /**
     * Get all the RSS favourites/filters
     * @return RssFilter[]
     */
    public function getRSSFilters() {
        $json = $this->makeRequest("?list=1");
        $filters = array();
        foreach ($json['rssfilters'] as $filter) {
            $filters[] = RssFilter::fromData($filter);
        }
        return $filters;
    }

    /**
     * Update an RSS filter as retrieved from getRSSFilters
     *
     * @param RssFilter $filter
     *
     * @return mixed
     */
    public function setRSSFilter(RssFilter $filter) {
        $request = array_merge(array('action' => 'filter-update'), $filter->toParams());
        return $this->makeRequest('?'.http_build_query($request));
    }
    /**
     * Add a new RSS filter
     * Requires a utorrent > 2.2.1 (not sure which version exactly)
     * @param RssFilter $filter
     * @return int ID of the new filter
     */
    public function addRSSFilter(RssFilter $filter) {
        $filter->filterId = -1;
        $resp = $this->setRSSFilter($filter);
        if (!empty($resp['filter_ident'])) {
            return $resp['filter_ident'];
        } else {
            return 0;
        }
    }

    // sets the properties for the specified torrent hash
    // TODO:
    //  - allow multiple hashes, properties, and values to be set simultaneously
    public function setProperties($hash, $property, $value)
    {
        $this->makeRequest("?action=setprops&hash=" . $hash . "&s=" . $property . "&v=" . $value, false);
    }

    /**
     * sets the priorities for the specified files in the specified torrent hash
     *
     * @param $hash
     * @param $files
     * @param $priority
     */
    public function setPriority($hash, $files, $priority)
    {
        $this->makeRequest("?action=setprio&hash=" . $hash . "&p=" . $priority . $this->paramImplode("&f=", $files), false);
    }
    // sets the settings
    // TODO:
    //  - allow multiple settings and values to be set simultaneously
    public function setSetting($setting, $value)
    {
        $this->makeRequest("?action=setsetting&s=" . $setting . "&v=" . $value, false);
    }

    /**
     *
     * add a file to the list
     *
     * @param      $filename
     * @param bool $estring
     *
     * @return bool
     *
     * @deprecated
     */
    public function torrentAdd($filename, &$estring = false)
    {
        $split = explode(":", $filename, 2);
        if (count($split) > 1 && (stristr("|http|https|file|magnet|", "|" . $split[0] . "|") !== false)) {
            $this->makeRequest("?action=add-url&s=" . urlencode($filename), false);
        } elseif (file_exists($filename)) {
            $json = $this->makeRequest("?action=add-file", true, array(CURLOPT_POSTFIELDS => array("torrent_file" => "@" . realpath($filename))));
            if (isset($json['error'])) {
                if ($estring !== false) $estring = $json['error'];
                return false;
            }
            return true;
        } else {
            if ($estring !== false) $estring = "File doesn't exist!";
            return false;
        }
    }

    /**
     * force start the specified torrent hashes
     *
     * @param $hash
     */
    public function torrentForceStart($hash)
    {
        $this->makeRequest("?action=forcestart" . $this->paramImplode("&hash=", $hash), false);
    }

    /**
     * pause the specified torrent hashes
     *
     * @param $hash
     */
    public function torrentPause($hash)
    {
        $this->makeRequest("?action=pause" . $this->paramImplode("&hash=", $hash), false);
    }

    /**
     * recheck the specified torrent hashes
     *
     * @param $hash
     */
    public function torrentRecheck($hash)
    {
        $this->makeRequest("?action=recheck" . $this->paramImplode("&hash=", $hash), false);
    }

    /**
     * start the specified torrent hashes
     *
     * @param $hash
     */
    public function torrentStart($hash)
    {
        $this->makeRequest("?action=start" . $this->paramImplode("&hash=", $hash), false);
    }

    /**
     * stop the specified torrent hashes
     *
     * @param $hash
     */
    public function torrentStop($hash)
    {
        $this->makeRequest("?action=stop" . $this->paramImplode("&hash=", $hash), false);
    }

    /**
     * remove the specified torrent hashes (and data, if $data is set to true)
     *
     * @param      $hash
     * @param bool $data
     */
    public function torrentRemove($hash, $data = false)
    {
        $this->makeRequest("?action=" . ($data ? "removedata" : "remove") . $this->paramImplode("&hash=", $hash), false);
    }


    /**
     * implodes given parameter with glue, whether it is an array or not
     *
     * @param $glue
     * @param $param
     *
     * @return string
     */
    private function paramImplode($glue, $param) {
        return $glue.implode($glue, is_array($param) ? $param : array($param));
    }
}