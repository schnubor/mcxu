<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Soundcloud"] = array(
	"name" => "Soundcloud",
	"description" => "Autoembedding Soundcloud Links"
	"version" => "1.0",
	"author" => "smoes",
	"authorEmail" => "smoesorino@gmail.com",
	"authorURL" => "",
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
 		
		// replace with emebdding like shown here
		// http://blog.soundcloud.com/2009/07/28/soundcloud-player-in-forums-5-step-guide-for-soundcloud-bb-code/

		$count = 0;
		if(preg_match_all('/(https?:\/\/soundcloud\.com\/.+)/', $sender->content, $matches)) {
  			foreach($matches[0] as $m) {
				$arr[] = "<object height=\"81\" width=\"100%\"><param name=\"movie\" value=\"http://player.soundcloud.com/player.swf?url=$m&amp;g=bb\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed allowscriptaccess=\"always\" height=\"81\" src=\"http://player.soundcloud.com/player.swf?url=$m&amp;g=bb\" type=\"application/x-shockwave-flash\" width=\"100%\"></embed></object> <a href=\"$m\">$m</a>";

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
