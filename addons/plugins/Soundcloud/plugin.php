<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Soundcloud"] = array(
	"name" => "Soundcloud",
	"description" => "Enable [soundcloud] to embed soundcloud player",
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
		addToArrayString($controls, "soundcloud", "<a href='javascript:Soundcloud.embbed(\"$id\");void(0)' title='".T("Soundcloud")."' class='bbcode-soundcloud'><span>".T("Soundcloud")."</span></a>", 0);	
	}

	
	public function handler_memberController_renderBefore($sender){
		$this->handler_conversationController_renderBefore($sender);
	}
	
	public function handler_format_beforeFormat( $sender ){

		// Common BB regex, case insensitive, multiple lines
 		$regexp = "/\[soundcloud\](.*?)\[\/soundcloud\]/si";
 		//$regexp = "";
		//$regexp = '#(https?://[www\.]?soundcloud\.com/.*)#i';


		// replace like stated in:
		// http://blog.soundcloud.com/2009/07/28/soundcloud-player-in-forums-5-step-guide-for-soundcloud-bb-code/



		$count = 0;
		if(preg_match_all('/(https?:\/\/soundcloud\.com\/.+)/', $sender->content, $matches)) {
  			foreach($matches[0] as $m) {
				$arr[] = "<object height=\"81\" width=\"100%\"><param name=\"movie\" value=\"http://player.soundcloud.com/player.swf?url=$m&amp;g=bb\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed allowscriptaccess=\"always\" height=\"81\" src=\"http://player.soundcloud.com/player.swf?url=$m&amp;g=bb\" type=\"application/x-shockwave-flash\" width=\"100%\"></embed></object> <a href=\"$m\">$m</a>";

				$sender->content = str_replace($m, "###".$count, $sender->content);
				$count++;
  		}
}
		for($i = 0; $i < $count; $i++)
			$sender->content = str_replace("###".$i, array_shift($arr), $sender->content);
	}
}
?>
