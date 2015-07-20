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

    public static function fromFile($torrentFile)
    {
        return self::fromString(file_get_contents($torrentFile));

    }

    public function fromString($torrentString)
    {
        return new self();
    }

    public function fromMagnetUri($magnetUri)
    {
        return new self();
    }

} 