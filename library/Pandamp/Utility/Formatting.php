<?php
namespace Pandamp\Utility;

class Formatting
{
	public function teaser($word_limit, $content)
	{
		$content	= $this->postTagFilter($content);
		$content 	= $this->wpautop($content);
		$content	= $this->wptexturize($content);
		$content	= $this->postTagFilter($content);
		$teaser		= $this->cutArticleByWords($content, $word_limit);
	
		return $teaser;
	}
	
	public function cutArticleByWords($original_text, $how_many)
	{
		$word_cut = strtok($original_text," ");
	
		$return = '';
	
		for ($i=1;$i<=$how_many;$i++){
		  
			$return	.= $word_cut;
			$return	.= (" ");
			$word_cut = strtok(" ");
		}
	
		$return .= '';
		return $return;
	}
	
	function postTagFilter($source)
	{
		$replace_all_html = strip_tags($source);
		$bbc_tag		= array('/\[caption(.*?)]\[\/caption\]/is');
		$result			= preg_replace($bbc_tag, '', $replace_all_html);
	
		return $result;
	}
	
	function check_strpos($haystack, $needle) {
		if (is_array($needle)) {
			foreach ($needle as $need) {
				if (strpos($haystack, $need) !== false) {
					return true;
				}
			}
		}else {
			if (strpos($haystack, $need) !== false) {
				return true;
			}
		}
	
		return false;
	}
	
	public function wpautop($pee, $br = 1)
	{
		$pee = $pee . "\n"; // just to make things a little easier, pad the end
		$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	
		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
		$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
		$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
		$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
	
		if ( strpos($pee, '<object') !== false ) {
			$pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
			$pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
		}
	
		$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	
		/**
		 * Proses di bawah mengakibatkan ditulisan yg panjang tidak muncul.
		*/
		//$pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
		$pee = '<p>'.preg_replace('#(<br\s*?/?>\s*?){2,}#', '</p>'."\n".'<p>', nl2br($pee)).'</p>';
	
		$pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
		$pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
		$pee = preg_replace( '|<p>|', "$1<p>", $pee );
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
		$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
		$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
		$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
		$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
	
		if ($br) {
			$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
			$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
			$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
		}
	
		$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
		$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
	
		if (strpos($pee, '<pre') !== false)
			$pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', array($this, 'cleanPre'), $pee );
	
		$pee = preg_replace( "|\n</p>$|", '</p>', $pee );
		$pee = preg_replace('/<p>\s*?(' . (new Shortcodes)->getShortcodeRegex() . ')\s*<\/p>/s', '$1', $pee); // don't auto-p wrap shortcodes that stand alone
	
		return $pee;
	}
	
	public function wptexturize($text)
	{
		$next = true;
		$has_pre_parent = false;
		$output = '';
		$curl = '';
		$textarr = preg_split('/(<.*>|\[.*\])/Us', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		$stop = count($textarr);
	
		// if a plugin has provided an autocorrect array, use it
		if ( isset($wp_cockneyreplace) ) {
			$cockney = array_keys($wp_cockneyreplace);
			$cockneyreplace = array_values($wp_cockneyreplace);
		} else {
			$cockney = array("'tain't","'twere","'twas","'tis","'twill","'til","'bout","'nuff","'round","'cause");
			$cockneyreplace = array("&#8217;tain&#8217;t","&#8217;twere","&#8217;twas","&#8217;tis","&#8217;twill","&#8217;til","&#8217;bout","&#8217;nuff","&#8217;round","&#8217;cause");
		}
	
		$static_characters = array_merge(array('---', ' -- ', '--', 'xn&#8211;', '...', '``', '\'s', '\'\'', ' (tm)'), $cockney);
		$static_replacements = array_merge(array('&#8212;', ' &#8212; ', '&#8211;', 'xn--', '&#8230;', '&#8220;', '&#8217;s', '&#8221;', ' &#8482;'), $cockneyreplace);
	
		$dynamic_characters = array('/\'(\d\d(?:&#8217;|\')?s)/', '/(\s|\A|")\'/', '/(\d+)"/', '/(\d+)\'/', '/(\S)\'([^\'\s])/', '/(\s|\A)"(?!\s)/', '/"(\s|\S|\Z)/', '/\'([\s.]|\Z)/', '/(\d+)x(\d+)/');
		$dynamic_replacements = array('&#8217;$1','$1&#8216;', '$1&#8243;', '$1&#8242;', '$1&#8217;$2', '$1&#8220;$2', '&#8221;$1', '&#8217;$1', '$1&#215;$2');
	
		for ( $i = 0; $i < $stop; $i++ ) {
			$curl = $textarr[$i];
	
			if (isset($curl{0}) && '<' != $curl{0} && '[' != $curl{0} && $next && !$has_pre_parent) { // If it's not a tag
				// static strings
				$curl = str_replace($static_characters, $static_replacements, $curl);
				// regular expressions
				$curl = preg_replace($dynamic_characters, $dynamic_replacements, $curl);
			} elseif (strpos($curl, '<code') !== false || strpos($curl, '<kbd') !== false || strpos($curl, '<style') !== false || strpos($curl, '<script') !== false) {
				$next = false;
			} elseif (strpos($curl, '<pre') !== false) {
				$has_pre_parent = true;
			} elseif (strpos($curl, '</pre>') !== false) {
				$has_pre_parent = false;
			} else {
				$next = true;
			}
	
			$curl = preg_replace('/&([^#])(?![a-zA-Z1-4]{1,8};)/', '&#038;$1', $curl);
			$output .= $curl;
		}
	
		return $output;
	}
}

/**
 * Diambil dan dimodifikasi dari fungsi-fungsi shortcodes wordpress (2.6) wp-includes/shortcodes.php
 * Class ini berfungsi untuk 'merapihkan' tampilan pada detail post.
 *
 * WordPress API for creating bbcode like tags or what WordPress calls
 * "shortcodes." The tag and attribute parsing or regular expression code is
 * based on the Textpattern tag parser.
 *
 * A few examples are below:
 *
 * [shortcode /]
 * [shortcode foo="bar" baz="bing" /]
 * [shortcode foo="bar"]content[/shortcode]
 *
 * @author Wordpress Dev Team
 * @package WordPress
 * @subpackage Shortcodes
 * @since 2.5
 * @link http://codex.wordpress.org/Shortcode_API
 */
class Shortcodes
{
	public function __construct()
	{
		$this->shortcodeTags = array(
				'wp_caption' => 'imgCaptionShortcode',
				'caption' => 'imgCaptionShortcode',
		);
	
	}
	
	public function getShortcodeRegex()
	{
		$tagnames = array_keys($this->shortcodeTags);
		$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	
		return '\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\1\])?';
	}
}