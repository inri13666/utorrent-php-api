<?php
/**
 * User  : Nikita.Makarov
 * Date  : 7/20/15
 * Time  : 2:52 PM
 * E-Mail: nikita.makarov@effective-soft.com
 */

namespace Akuma\Bittorrent\Queue;


class Torrent
{
    /**
     * @var string
     */
    protected $hash;
    /**
     * @var int
     */
    protected $status;
    /**
     * @var string
     */
    protected $name;
    /**
     * integer in bytes
     *
     * @var int
     */
    protected $size;
    /**
     * integer in per mils
     *
     * @var int
     */
    protected $percent_progress;
    /**
     * integer in bytes
     *
     * @var int
     */
    protected $downloaded;
    /**
     * integer in bytes
     *
     * @var int
     */
    protected $uploaded;
    /**
     * integer in per mils
     *
     * @var int
     */
    protected $ratio;
    /**
     * integer in bytes per second
     *
     * @var int
     */
    protected $upload_speed;
    /**
     * integer in bytes per second
     *
     * @var int
     */
    protected $download_speed;
    /**
     * integer in seconds
     *
     * @var int
     */
    protected $eta;
    /**
     * @var string
     */
    protected $label;
    /**
     * @var int
     */
    protected $peers_connected;
    /**
     * @var int
     */
    protected $peers_in_swarm;
    /**
     * @var int
     */
    protected $seeds_connected;
    /**
     * @var int
     */
    protected $seeds_in_swarm;
    /**
     * integer in 1/65535ths
     *
     * @var int
     */
    protected $availability;
    /**
     * @var int
     */
    protected $torrent_queue_order;
    /**
     * integer in bytes
     *
     * @var int
     */
    protected $remaining;
    /**
     * @var string
     */
    protected $download_url;
    /**
     * @var string
     */
    protected $rss_feed_url;
    /**
     * @var string
     */
    protected $status_message;
    /**
     * @var string
     */
    protected $stream_id;
    /**
     * integer in seconds
     *
     * @var int
     */
    protected $added_on;
    /**
     * integer in seconds
     *
     * @var int
     */
    protected $completed_on;
    /**
     * @var string
     */
    protected $app_update_url;

    public function __construct(array $data)
    {
        $i = 0;
        $this->hash = $data[$i++];
        $this->status = $data[$i++];
        $this->name = $data[$i++];
        $this->size = $data[$i++];
        $this->percent_progress = $data[$i++];
        $this->downloaded = $data[$i++];
        $this->uploaded = $data[$i++];
        $this->ratio = $data[$i++];
        $this->upload_speed = $data[$i++];
        $this->download_speed = $data[$i++];
        $this->eta = $data[$i++];
        $this->label = $data[$i++];
        $this->peers_connected = $data[$i++];
        $this->peers_in_swarm = $data[$i++];
        $this->seeds_connected = $data[$i++];
        $this->seeds_in_swarm = $data[$i++];
        $this->availability = $data[$i++];
        $this->torrent_queue_order = $data[$i++];
        $this->remaining = $data[$i++];
        $this->download_url = $data[$i++];
        $this->rss_feed_url = $data[$i++];
        $this->status_message = $data[$i++];
        $this->stream_id = $data[$i++];
        $this->added_on = $data[$i++];
        $this->completed_on = $data[$i++];
        $this->app_update_url = $data[$i++];
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->status_message;
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
    public function getPercentProgress()
    {
        return $this->percent_progress;
    }

}