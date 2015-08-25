<?php

if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Soundcloud"] = array(
	"name" => "Soundcloud",
	"description" => "Autoembedding Soundcloud Links",
	"version" => "1.2",
	"author" => "smoes, schnubor (html5)",
	"authorEmail" => "smoesorino@gmail.com, schnuppser@gmail.com",
	"authorURL" => "http://www.mcxu.com",
	"license" => "GPLv2"
);

class ETPlugin_Soundcloud extends ETPlugin {

	public function handler_conversationController_renderBefore($sender){
			$sender->addCSSFile($this->getResource("soundcloud.css"));
			$sender->addJSFile($this->getResource("soundcloud.js"));
	}

	public function handler_conversationController_getEditControls($sender, &$controls, $id)
	{
	}

	
	public function handler_memberController_renderBefore($sender){
		$this->handler_conversationController_renderBefore($sender);
	}
	
	public function handler_format_beforeFormat( $sender ){

 		$regexp = "/\[soundcloud\](.*?)\[\/soundcloud\]/si";

		$count = 0;
		
		if(preg_match_all('/(https?:\/\/soundcloud\.com\/[^\[\s]+)/', $sender->content, $matches)) {
  			foreach($matches[0] as $m) {
  				// New HTML5 embed code
  				$arr[] = "<iframe class='sc-iframe' width='100%' height='166' scrolling='no' frameborder='no' src='https://w.soundcloud.com/player/?url=$m&amp;color=ff6600&amp;auto_play=false&amp;show_artwork=true'></iframe><a href='$m'>$m</a>";

				// set dummy constant, since link appears in embedding itself, which leads to infinite recursion
				$sender->content = str_replace($m, "###".$count, $sender->content);
				$count++;
  			}
		}
		
		// Now replace dummy constants
		for($i = 0; $i < $count; $i++)
			$sender->content = str_replace("###".$i, array_shift($arr), $sender->content);
	}
}
?>
