<?php

// A simple FAST parser to convert BBCode to HTML
// Trade-in more restrictive grammar for speed and simplicty
//
// Syntax Sample:
// --------------
// [img]http://elouai.com/images/star.gif[/img]
// [url="http://elouai.com"]eLouai[/url]
// [mail="webmaster@elouai.com"]Webmaster[/mail]
// [size="25"]HUGE[/size]
// [color="red"]RED[/color]
// [b]bold[/b]
// [i]italic[/i]
// [u]underline[/u]
// [list][*]item[*]item[*]item[/list]
// [code]value="123";[/code]
// [quote]John said yadda yadda yadda[/quote]
//
//
// (please do not remove credit)
// author: Louai Munajim
// website: http://elouai.com
// date: 2004/Apr/18

// Modified by Eric Blade to use Blockquote/italics instead of an ugly Table for the [quote] func

function bb2html($text)
{
  $bbcode = array(
                "[list]", "[*]", "[/list]", 
                "[img]", "[/img]", 
                "[b]", "[/b]", 
                "[u]", "[/u]", 
                "[i]", "[/i]",
                '[color="', "[/color]",
                "[size=\"", "[/size]",
                '[url="', "[/url]",
                "[mail=\"", "[/mail]",
                "[code]", "[/code]",
                "[quote]", "[/quote]",
                '"]');
  $htmlcode = array(
                "<ul>", "<li>", "</ul>", 
                "<img src=\"", "\">", 
                "<b>", "</b>", 
                "<u>", "</u>", 
                "<i>", "</i>",
                "<span style=\"color:", "</span>",
                "<span style=\"font-size:", "</span>",
                '<a target="_blank" href="', "</a>",
                "<a href=\"mailto:", "</a>",
                "<code>", "</code>",
                "<blockquote><i>", "</i></blockquote>",
                '">');
  $newtext = str_replace($bbcode, $htmlcode, $text);
  //$newtext = nl2br($newtext);//second pass
  return $newtext;
}
?>