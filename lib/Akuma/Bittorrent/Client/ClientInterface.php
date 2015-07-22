<?php
/**
 * User  : Nikita.Makarov
 * Date  : 7/20/15
 * Time  : 2:04 PM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma\Bittorrent;

use Akuma\Bittorrent\Queue\RssFeed;
use Akuma\Bittorrent\Queue\RssFilter;
use Akuma\Bittorrent\Queue\Torrent;

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

/**
 * Interface ClientInterface
 *
 * @package Akuma\Bittorrent
 */
interface ClientInterface
{

    /**
     * @return null|string
     */
    public function getHost();

    /**
     * @return int|null
     */
    public function getPort();

    /**
     * @return null|string
     */
    public function getUser();

    /**
     * @return null|string
     */
    public function getPassword();

    /**
     * @return null|string
     */
    public function getToken();

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host);

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port);

    /**
     * @param $user
     *
     * @return $this
     */
    public function setUser($user);

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password);

    /**
     * @throws \Exception
     */
    public function reload();

    /**
     * @return bool
     */
    public function isOnline();

    /**
     * @param       $request
     * @param bool  $decode
     * @param array $options
     *
     * @return mixed
     */
    public function makeRequest($request, $decode = true, $options = array());

    /**
     * returns the Client build number
     *
     * @return mixed
     */
    public function getBuild();

    /**
     * returns an array of files for the specified torrent hash
     *
     * @param string|array $torrent
     *
     * @return array
     */
    public function getFiles($torrent);

    /**
     * returns an array of all labels
     *
     * @return array
     */
    public function getLabels();
    // returns an array of the properties for the specified torrent hash
    // TODO:
    //  - (when implemented in API) allow multiple hashes to be specified
    public function getProperties($torrent);

    /**
     * returns an array of all settings
     *
     * @return mixed
     *
     * todo: return Settings
     */
    public function getSettings();

    /**
     *
     * RSS Section
     *
     */

    /**
     * @return bool
     */
    public function isRssSupported();

    /**
     * returns an array of all rssfeeds and related information
     *
     * @return RssFeed[]
     */
    public function getRssFeeds();

    /**
     * Get all the RSS favourites/filters
     *
     * @return RssFilter[]
     */
    public function getRSSFilters();

    /**
     * Update an RSS filter as retrieved from getRSSFilters
     *
     * @param RssFilter $filter
     *
     * @return mixed
     */
    public function setRSSFilter(RssFilter $filter);

    /**
     * Add a new RSS filter
     * Requires a utorrent > 2.2.1 (not sure which version exactly)
     *
     * @param RssFilter $filter
     *
     * @return int ID of the new filter
     */
    public function addRSSFilter(RssFilter $filter);

    /**
     *
     * PROPERTIES SECTION
     *
     */
    /**
     * sets the properties for the specified torrent hash
     *
     * @param Torrent $torrent
     * @param         $property
     * @param         $value
     *
     * @return mixed
     *
     * TODO:
     * - allow multiple hashes, properties, and values to be set simultaneously
     */
    public function setProperties($torrent, $property, $value);

    /**
     * sets the priorities for the specified files in the specified torrent hash
     *
     * @param Torrent $torrent
     * @param         $files
     * @param         $priority
     */
    public function setPriority($torrent, $files, $priority);

    /**
     *
     * SETTINGS SECTION
     *
     */
    // sets the settings
    // TODO:
    //  - allow multiple settings and values to be set simultaneously
    public function setSetting($setting, $value);
    /**
     *
     * TORRENT SECTION
     *
     */

    /**
     * returns an array of all torrent jobs and related information
     *
     * @return Torrent[]
     */
    public function getTorrents();

    /**
     *
     * add a file to the list
     *
     * @param Torrent $torrent
     * @param bool    $estring
     *
     * @return bool
     *
     */
    public function torrentAdd($torrent, &$estring = false);

    /**
     * force start the specified torrent hashes
     *
     * @param Torrent $torrent
     */
    public function torrentForceStart($torrent);

    /**
     * pause the specified torrent hashes
     *
     * @param Torrent $torrent
     */
    public function torrentPause($torrent);

    /**
     * recheck the specified torrent hashes
     *
     * @param Torrent $torrent
     */
    public function torrentRecheck($torrent);

    /**
     * start the specified torrent hashes
     *
     * @param Torrent $torrent
     */
    public function torrentStart($torrent);

    /**
     * stop the specified torrent hashes
     *
     * @param Torrent $torrent
     */
    public function torrentStop($torrent);

    /**
     * remove the specified torrent hashes (and data, if $data is set to true)
     *
     * @param Torrent $torrent
     * @param bool    $data
     */
    public function torrentRemove($torrent, $data = false);
}