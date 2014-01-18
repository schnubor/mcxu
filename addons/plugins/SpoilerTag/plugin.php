<?php
if (!defined("IN_ESOTALK")) exit;
 
 ET::$pluginInfo["SpoilerTag"] = array(
	"name" => "SpoilerTag",
	"description" => "Enable [spoiler] and [nsfw] tag to hide spoilers",
	"version" => "1.0.2",
	"author" => "Ramouch0",
	"authorEmail" => "me@Ramouch0.org",
	"authorURL" => "http://Ramouch0.org",
	"license" => "GPLv2"
);

class ETPlugin_SpoilerTag extends ETPlugin {

	public function handler_conversationController_renderBefore($sender){
			$sender->addCSSFile($this->getResource("spoiler.css"));
			$sender->addJSFile($this->getResource("spoiler.js"));
	}
	
	public function handler_memberController_renderBefore($sender){
		$this->handler_conversationController_renderBefore($sender);
	}
	
	public function handler_format_format( $sender ){
		// Spoiler - [spoiler:title]text[/spoiler]
		$regexp = "/(.*?)\n?\[spoiler(?:(?::|=)(.*?)(]?))?\]\n?(.*?)\n?\[\/spoiler\]\n{0,2}/is";
		while (preg_match($regexp, $sender->content)) {
			$sender->content = preg_replace($regexp,
				"$1</p><div class=\"spoiler\">-<span>".T("Spoiler")."</span> <span class=\"title\">$2$3</span>-<div class=\"content\">$4</div></div><p>", $sender->content);
		}
		
		// NSFW - [nsfw:title]text[/nsfw]
		$regexp = "/(.*?)\n?\[nsfw(?:(?::|=)(.*?)(]?))?\]\n?(.*?)\n?\[\/nsfw\]\n{0,2}/is";
		while (preg_match($regexp, $sender->content)) {
			$sender->content = preg_replace($regexp,
				"$1</p><div class=\"nsfw\">-<span>".T("NSFW")."</span> <span class=\"title\">$2$3</span>-<div class=\"content\">$4</div></div><p>", $sender->content);
		}
	}
}

?>