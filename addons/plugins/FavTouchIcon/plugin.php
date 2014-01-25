<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["FavTouchIcon"] = array(
	"name" => "FavTouchIcon",
	"description" => "Add a favicon and touch icon for iOS devices to your Forum",
	"version" => "1.0",
	"author" => "schnubor",
	"authorEmail" => "schnubor@gmail.com",
	"authorURL" => "http://chko.org",
	"license" => "GPLv2"
);

class ETPlugin_FavTouchIcon extends ETPlugin {

	public function handler_init( $sender ){
		$sender->addToHead("<link rel='shortcut icon' href='".getWebPath($this->getResource("favicon.ico"))."'>");
	}
}
?>
