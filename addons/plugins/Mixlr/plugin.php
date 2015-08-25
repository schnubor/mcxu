<?php

if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Mixlr"] = array(
	"name" => "Mixlr",
	"description" => "Autoembedding Mixlr Links",
	"version" => "1.0",
	"author" => "schnubor",
	"authorEmail" => "info@chko.org",
	"authorURL" => "http://www.chko.org",
	"license" => "GPLv2"
);

class ETPlugin_Mixlr extends ETPlugin {

	
	public function handler_memberController_renderBefore($sender){
		$this->handler_conversationController_renderBefore($sender);
	}
	
	public function handler_format_beforeFormat( $sender ){

		$count = 0;
		
		if(preg_match_all('/(https?:\/\/mixlr\.com\/[^\[\s]+)/', $sender->content, $matches)) 		{
  			foreach($matches[0] as $m) {
  				// New HTML5 embed code
  				$arr[] = "<iframe src=\"$m/embed\" width=\"100%\" height=\"180px\" scrolling=\"no\" frameborder=\"no\" marginheight=\"0\" marginwidth=\"0\"></iframe>";

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
