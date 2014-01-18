<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Soundcloud"] = array(
	"name" => "Soundcloud",
	"description" => "Enable [soundcloud] to embed soundcloud player",
	"version" => "1.0",
	"author" => "Schnubor",
	"authorEmail" => "schnubor@gmail.com",
	"authorURL" => "http://chko.org",
	"license" => "GPLv2"
);

class ETPlugin_Soundcloud extends ETPlugin {

	public function handler_conversationController_renderBefore($sender){
			$sender->addCSSFile($this->getResource("soundcloud.css"));
			$sender->addJSFile($this->getResource("soundcloud.js"));
	}
	
	public function handler_memberController_renderBefore($sender){
		$this->handler_conversationController_renderBefore($sender);
	}
	
	public function handler_format_format( $sender ){
		// Todo...
	}
}

?>