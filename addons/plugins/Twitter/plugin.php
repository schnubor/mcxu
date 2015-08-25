<?php
//require_once 'Services/Soundcloud.php';

if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Twitter"] = array(
	"name" => "Twitter",
	"description" => "Autoembedding Twitter Links",
	"version" => "1.0",
	"author" => "schnubor",
	"authorEmail" => "info@chko.org",
	"authorURL" => "http://www.chko.org",
	"license" => "GPLv2"
);

class ETPlugin_Twitter extends ETPlugin {

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
		
		if(preg_match_all('/(https?:\/\/twitter\.com\/[^\[\s]+)/', $sender->content, $matches)) 		{
  			foreach($matches[0] as $m) {
  				// New HTML5 embed code
  				$arr[] = "<div id=\"autoembed-tweet$count\"></div><script>$.ajax({url: \"https://api.twitter.com/1/statuses/oembed.json?url=$m\", dataType: \"jsonp\", async: false, success: function(data){ $(\"#autoembed-tweet$count\").html(data.html) }});</script>";

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
