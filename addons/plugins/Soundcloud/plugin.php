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

		// Common BB regex, case insensitive, multiple lines
 		$regexp = "/\[soundcloud\](.*?)\[\/soundcloud\]/si";


		// replace like stated in:
		// http://blog.soundcloud.com/2009/07/28/soundcloud-player-in-forums-5-step-guide-for-soundcloud-bb-code/

                while (preg_match($regexp, $sender->content)) {
			
                        $sender->content = preg_replace($regexp,
                                "<object height=\"81\" width=\"100%\"><param name=\"movie\" value=\"http://player.soundcloud.com/player.swf?url=$1&amp;g=bb\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed allowscriptaccess=\"always\" height=\"81\" src=\"http://player.soundcloud.com/player.swf?url=$1&amp;g=bb\" type=\"application/x-shockwave-flash\" width=\"100%\"></embed></object> <a href=\"$1\">$1</a>", $sender->content);
                }
	}
}

?>
