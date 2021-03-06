<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

ET::$pluginInfo["BBCode"] = array(
	"name" => "BBCode",
	"description" => "Formats BBCode within posts, allowing users to style their text.",
	"version" => ESOTALK_VERSION,
	"author" => "esoTalk Team",
	"authorEmail" => "support@esotalk.org",
	"authorURL" => "http://esotalk.org",
	"license" => "GPLv2"
);


/**
 * BBCode Formatter Plugin
 *
 * Interprets BBCode in posts and converts it to HTML formatting when rendered. Also adds BBCode formatting
 * buttons to the post editing/reply area.
 */
class ETPlugin_BBCode extends ETPlugin {


/**
 * Add an event handler to the initialization of the conversation controller to add BBCode CSS and JavaScript
 * resources.
 *
 * @return void
 */
public function handler_conversationController_renderBefore($sender)
{
	$sender->addJSFile($this->getResource("bbcode.js"));
	$sender->addCSSFile($this->getResource("bbcode.css"));
	$sender->addJSFile($this->getResource("jquery.gifplayer.js"));
	$sender->addCSSFile($this->getResource("gifplayer.min.css"));
}


/**
 * Add an event handler to the "getEditControls" method of the conversation controller to add BBCode
 * formatting buttons to the edit controls.
 *
 * @return void
 */
public function handler_conversationController_getEditControls($sender, &$controls, $id)
{
	addToArrayString($controls, "fixed", "<a href='javascript:BBCode.fixed(\"$id\");void(0)' title='".T("Code")."' class='bbcode-fixed'><span class='icon-code'></span></a>", 0);
	addToArrayString($controls, "image", "<a href='javascript:BBCode.image(\"$id\");void(0)' title='".T("Image")."' class='bbcode-img'><span class='icon-picture'></span></a>", 0);
	addToArrayString($controls, "link", "<a href='javascript:BBCode.link(\"$id\");void(0)' title='".T("Link")."' class='bbcode-link'><span class='icon-link'></span></a>", 0);
	addToArrayString($controls, "strike", "<a href='javascript:BBCode.strikethrough(\"$id\");void(0)' title='".T("Strike")."' class='bbcode-s'><span class='icon-strikethrough'></span></a>", 0);
	addToArrayString($controls, "header", "<a href='javascript:BBCode.header(\"$id\");void(0)' title='".T("Header")."' class='bbcode-h'><span class='icon-align-justify'></span></a>", 0);
	addToArrayString($controls, "italic", "<a href='javascript:BBCode.italic(\"$id\");void(0)' title='".T("Italic")."' class='bbcode-i'><span class='icon-italic'></span></a>", 0);
	addToArrayString($controls, "bold", "<a href='javascript:BBCode.bold(\"$id\");void(0)' title='".T("Bold")."' class='bbcode-b'><span class='icon-bold'></span></a>", 0);
}


/**
 * Add an event handler to the formatter to take out and store code blocks before formatting takes place.
 *
 * @return void
 */
public function handler_format_beforeFormat($sender)
{
	$hideBlock = create_function('&$blockFixedContents, $contents', '
		$blockFixedContents[] = $contents;
		return "</p><pre></pre><p>";');
	$hideInline = create_function('&$inlineFixedContents, $contents', '
		$inlineFixedContents[] = $contents;
		return "<code></code>";');

	$this->blockFixedContents = array();
	$this->inlineFixedContents = array();

	$regexp = "/(.*)^\s*\[code\]\n?(.*?)\n?\[\/code]$/imse";
	while (preg_match($regexp, $sender->content)) {
		if ($sender->inline) $sender->content = preg_replace($regexp, "'$1' . \$hideInline(\$this->inlineFixedContents, '$2')", $sender->content);
		else $sender->content = preg_replace($regexp, "'$1' . \$hideBlock(\$this->blockFixedContents, '$2')", $sender->content);
	}

	// Inline-level [fixed] tags will become <code>.
	$sender->content = preg_replace("/\[code\]\n?(.*?)\n?\[\/code]/ise", "\$hideInline(\$this->inlineFixedContents, '$1')", $sender->content);
}


/**
 * Add an event handler to the formatter to parse BBCode and format it into HTML.
 *
 * @return void
 */
public function handler_format_format($sender)
{
	// TODO: Rewrite BBCode parser to use the method found here:
	// http://stackoverflow.com/questions/1799454/is-there-a-solid-bb-code-parser-for-php-that-doesnt-have-any-dependancies/1799788#1799788
	// Remove control characters from the post.
	//$sender->content = preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $sender->content);
	// \[ (i|b|color|url|somethingelse) \=? ([^]]+)? \] (?: ([^]]*) \[\/\1\] )

	// Images: [img]url[/img]
	$replacement = $sender->inline ? "[image]" : "<a href='$1' target='_blank'><img src='$1' alt='-image-'/></a>";
	$sender->content = preg_replace_callback("/\[img\](.*?)\[\/img\]/i", array($this, "imagesCallback"), $sender->content);

	// Links with display text: [url=http://url]text[/url]
	$sender->content = preg_replace_callback("/\[url=(\w{2,6}:\/\/)?([^\]]*?)\](.*?)\[\/url\]/i", array($this, "linksCallback"), $sender->content);

	// Bold: [b]bold text[/b]
	$sender->content = preg_replace("|\[b\](.*?)\[/b\]|si", "<b>$1</b>", $sender->content);

	// Italics: [i]italic text[/i]
	$sender->content = preg_replace("/\[i\](.*?)\[\/i\]/si", "<i>$1</i>", $sender->content);

	// Strikethrough: [s]strikethrough[/s]
	$sender->content = preg_replace("/\[s\](.*?)\[\/s\]/si", "<del>$1</del>", $sender->content);

	// Headers: [h]header[/h]
	$replacement = $sender->inline ? "<b>$1</b>" : "</p><h4>$1</h4><p>";
	$sender->content = preg_replace("/\[h\](.*?)\[\/h\]/", $replacement, $sender->content);
}


/**
 * The callback function used to replace URL BBCode with HTML anchor tags.
 *
 * @param array $matches An array of matches from the regular expression.
 * @return string The replacement HTML anchor tag.
 */
public function linksCallback($matches)
{
	// If this is an internal link...
	$url = ($matches[1] ? $matches[1] : "http://").$matches[2];
	$baseURL = C("esoTalk.baseURL");
	if (substr($url, 0, strlen($baseURL)) == $baseURL) {
		return "<a href='".$url."' target='_blank' class='link-internal'>".$matches[3]."</a>";
	}

	// Otherwise, return an external HTML anchor tag.
	return "<a href='".$url."' rel='nofollow external' target='_blank' class='link-external'>".$matches[3]." <i class='icon-external-link'></i></a>";
}

/**
 * The callback function used to replace IMG BBCode with HTML img tags.
 *
 * @param array $matches An array of matches from the regular expression.
 * @return string The replacement HTML img tag.
 */
public function imagesCallback($matches)
{
	// If this is an internal link...
	$url = $matches[1];
	$ext = pathinfo($url, PATHINFO_EXTENSION);
	
	if($ext == 'gif') {
		return "<img src='/addons/plugins/BBCode/resources/placeholder.png' data-gif='".$url."' class='gifs gifplayer' onload='$(this).gifplayer()'/>";
	}

	if($ext == 'gifv') {
		preg_match('~/\K\w+(?=[^/]*$)~m', $url, $id);
		return '<video poster="https://i.imgur.com/'.$id[0].'.jpg" preload="auto" autoplay="autoplay" muted="muted" loop="loop" width="100%" height="auto"><source src="https://i.imgur.com/'.$id[0].'.webm" type="video/webm"><source src="https://i.imgur.com/'.$id[0].'.mp4" type="video/mp4"></video>';
	}

	// Otherwise, return an external HTML anchor tag.
	return "<a href='".$url."' target='_blank' class='normal'><img src='".$url."' alt='-image-'/></a>";
}


/**
 * Add an event handler to the formatter to put code blocks back in after formatting has taken place.
 *
 * @return void
 */
public function handler_format_afterFormat($sender)
{
	// Retrieve the contents of the inline <code> tags from the array in which they are stored.
	$sender->content = preg_replace("/<code><\/code>/ie", "'<code>' . array_shift(\$this->inlineFixedContents) . '</code>'", $sender->content);

	// Retrieve the contents of the block <pre> tags from the array in which they are stored.
	if (!$sender->inline) $sender->content = preg_replace("/<pre><\/pre>/ie", "'<pre>' . array_pop(\$this->blockFixedContents) . '</pre>'", $sender->content);
}

}
