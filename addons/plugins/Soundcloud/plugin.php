<?php
require_once 'Services/Soundcloud.php';

if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Soundcloud"] = array(
	"name" => "Soundcloud",
	"description" => "Autoembedding Soundcloud Links",
	"version" => "1.2",
	"author" => "smoes, schnubor (html5)",
	"authorEmail" => "smoesorino@gmail.com, schnuppser@gmail.com",
	"authorURL" => "http://www.mcxuc.com",
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
		if(preg_match_all('/(https?:\/\/soundcloud\.com\/[^\[\s]+)/', $sender->content, $matches)) {
  			foreach($matches[0] as $m) {
  				// create a client object with your app credentials
  				$soundcloud = new Services_Soundcloud('12c138ffb184282ce1729781bfb13fb0', '85cdbcd5c044289ab8311a196413baf9');
  				$soundcloud->setCurlOptions(array(CURLOPT_FOLLOWLOCATION => 1));

  				// a permalink to a track
				$track_url = $m;
				
				// resolve track URL into track resource
				//$track = json_decode($soundcloud->get('resolve', array('url' => $track_url))); // <- doesn't work, gives fatal error in esotalk, no idea why
				//$track_id = $track->id;
  			
  				// New HTML5 embed code
  				//$n = $track->id;	// test ID: 138230328
  				$n = str_replace("https://soundcloud.com/", "", $m);

  				$arr[] = "<iframe class='sc-iframe' onload='var t=this; SC.resolveLink(\"$track_url\",t)' width='100%' height='166' scrolling='no' frameborder='no' src='https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/$n&amp;color=ff6600&amp;auto_play=false&amp;show_artwork=true'></iframe><a href='$m'>$m</a>";
  				
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
