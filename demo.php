<?php
/**
 * User  : Nikita.Makarov
 * Date  : 7/20/15
 * Time  : 2:22 PM
 * E-Mail: nikita.makarov@effective-soft.com
 */
require_once 'lib/Akuma/Bittorrent/Client.php';
require_once 'lib/Akuma/Bittorrent/Queue/Torrent.php';

$service = new \Akuma\Bittorrent\Client('127.0.0.1','13666','admin','123456');
var_dump($service->isOnline());
var_dump($service->getTorrents());