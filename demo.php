<?php
/**
 * User  : Nikita.Makarov
 * Date  : 7/20/15
 * Time  : 2:22 PM
 * E-Mail: nikita.makarov@effective-soft.com
 */
require_once 'lib/Akuma/Bittorrent/Client.php';
require_once 'lib/Akuma/Bittorrent/Queue/Torrent.php';
require_once 'lib/Akuma/Bittorrent/Common/Item.php';

//$service = new \Akuma\Bittorrent\Client('127.0.0.1','13666','admin','123456');
//var_dump($service->isOnline());
//var_dump($service->getTorrents());
var_dump(\Akuma\Bittorrent\Common\Item::fromMagnetUri('magnet:?xt=urn:btih:66DA5157C83207E2BCEE24E4E9D43C618AC46ADE&dn=2002%20sad%20songs%20for%20dirty%20lovers&tr=http%3a%2f%2fbt3.rutracker.org%2fann%3fuk%3d5pZvx11XKg&tr=http%3a%2f%2fretracker.local%2fannounce'));