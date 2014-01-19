<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["Vimeo"] = array(
	"name" => "Vimeo",
	"description" => "Autoembedding vimeo links"
	"version" => "1.0",
	"author" => "smoes",
	"authorEmail" => "smoesorino@gmail.com",
	"authorURL" => "",
	"license" => "GPLv2"
);

class ETPlugin_Soundcloud extends ETPlugin {

	public function handler_conversationController_renderBefore($sender){
			$sender->addCSSFile($this->getResource("vimeo.css"));
			$sender->addJSFile($this->getResource("vimeo.js"));
	}

	public function handler_conversationController_getEditControls($sender, &$controls, $id)
	{
	}

	
	public function handler_memberController_renderBefore($sender){
		$this->handler_conversationController_renderBefore($sender);
	}
	
        public function handler_format_beforeFormat( $sender ){

                $regexp = '/vimeo\.com\/([0-9]+)/';
                
                // replace with emebdding like shown here
                // http://blog.soundcloud.com/2009/07/28/soundcloud-player-in-forums-5-step-guide-for-soundcloud-bb-code/

  		while (preg_match($regexp, $sender->content)) {
                        $sender->content = preg_replace($regexp,
                                '<div style="width: 600px; margin: 0 auto;"><iframe src="http://player.vimeo.com/video/' . $1 . '" width="600" height="400" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>', $sender->content);
		}


	}
}
?>
