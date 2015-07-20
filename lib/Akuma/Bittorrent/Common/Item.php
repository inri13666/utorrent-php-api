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
    protected $magnetUri = null;
    protected $name = null;
    protected $trackers = array();

    public function getMagnetUri(){
        return $this->magnetUri;
    }
    /**
     * Web link to the file online
     */
    public function getSource(){

    }

    public function getHash()
    {
        return '';
    }

    public function getName()
    {
        return "";
    }

    public function getSize()
    {
        return 0;
    }

    public function addTracker($tracker){
        if(!in_array($tracker,$this->trackers)){
            $this->trackers[] = $tracker;
        }
        return $this;
    }

    public static function fromFile($torrentFile)
    {
        return self::fromString(file_get_contents($torrentFile));

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

        $query= str_replace('tr=','tr[]=',$query);
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
        $item->hash = array_pop(explode(':',$data['xt']));
        $item->name = isset($data['dn'])?$data['dn']:null;
        if(isset($data['tr'])){
            foreach($data['tr'] as $tracker){
                $item->addTracker($tracker);
            }
        }
        return $item;
    }

} 