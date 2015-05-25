<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Vine"] = array(
	"name" => "Vine",
	"description" => "Autoembedding Vine Links",
	"version" => "1.0",
	"author" => "schnubor",
	"authorEmail" => "schnuppser@gmail.com",
	"authorURL" => "",
	"license" => "GPLv2"
);

class ETPlugin_Vine extends ETPlugin {

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

		$count = 0;
		if(preg_match_all('/(https?:\/\/vine\.co\/v\/[^\[\s]+)/', $sender->content, $matches)) {
  			foreach($matches[0] as $m) {
  			
  				// New HTML5 embed code
  				$n = str_replace("https://vine.co/v/", "", $m);
  				$arr[] = "<iframe src='https://vine.co/v/$n/embed/simple' width='600' height='600' frameborder='0'></iframe><script src='https://platform.vine.co/static/scripts/embed.js'></script>";
  				
  				// Old Flash embed code
				//$arr[] = "<object height='81' width='100%'><param name='movie' value='http://player.soundcloud.com/player.swf?url=$m&amp;g=bb'></param><param name='allowscriptaccess' value='always'></param><embed allowscriptaccess='always' height='81' src='http://player.soundcloud.com/player.swf?url=$m&amp;g=bb' type='application/x-shockwave-flash' width='100%'></embed></object> <a href='$m'>$m</a>";

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
