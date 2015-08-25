<?php
//require_once 'Services/Soundcloud.php';

if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Mixcloud"] = array(
	"name" => "Mixcloud",
	"description" => "Autoembedding Mixcloud Links",
	"version" => "1.0",
	"author" => "schnubor",
	"authorEmail" => "info@chko.org",
	"authorURL" => "http://www.chko.org",
	"license" => "GPLv2"
);

class ETPlugin_Mixcloud extends ETPlugin {

	
	public function handler_format_beforeFormat( $sender ){

		$count = 0;
		
		if(preg_match_all('/(https?:\/\/www.mixcloud\.com\/[^\[\s]+)/', $sender->content, $matches)) 		{
  			foreach($matches[0] as $m) {
  				// New HTML5 embed code
  				$arr[] = "<iframe width='100%' height='180' src='https://www.mixcloud.com/widget/iframe/?embed_type=widget_standard&amp;embed_uuid=86354d28-61bd-420c-a86c-fb1bb8eb0f6a&amp;feed=$m&amp;hide_cover=1&amp;hide_tracklist=1&amp;light=1&amp;replace=0' frameborder='0'></iframe>";

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
