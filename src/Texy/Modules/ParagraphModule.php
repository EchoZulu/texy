<?php

/**
 * This file is part of the Texy! (http://texy.info)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Texy\Modules;

use Texy;
use Texy\Regexp;


/**
 * Paragraph module.
 */
final class ParagraphModule extends Texy\Module
{

	public function __construct($texy)
	{
		$this->texy = $texy;
		$texy->addHandler('paragraph', [$this, 'solve']);
	}


	/**
	 * @return void
	 */
	public function process(Texy\BlockParser $parser, $content, Texy\HtmlElement $el)
	{
		$tx = $this->texy;

		if ($parser->isIndented()) {
			$parts = preg_split('#(\n(?! )|\n{2,})#', $content, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$parts = preg_split('#(\n{2,})#', $content, -1, PREG_SPLIT_NO_EMPTY);
		}

		foreach ($parts as $s) {
			$s = trim($s);
			if ($s === '') {
				continue;
			}

			// try to find modifier
			$mod = NULL;
			if ($mx = Regexp::match($s, '#'.Texy\Patterns::MODIFIER_H.'(?=\n|\z)#sUm', Regexp::OFFSET_CAPTURE)) {
				list($mMod) = $mx[1];
				$s = trim(substr_replace($s, '', $mx[0][1], strlen($mx[0][0])));
				if ($s === '') {
					continue;
				}
				$mod = new Texy\Modifier;
				$mod->setProperties($mMod);
			}

			$res = $tx->invokeAroundHandlers('paragraph', $parser, [$s, $mod]);
			if ($res) {
				$el->insert(NULL, $res);
			}
		}
	}


	/**
	 * Finish invocation.
	 * @return Texy\HtmlElement|FALSE
	 */
	public function solve(Texy\HandlerInvocation $invocation, $content, Texy\Modifier $mod = NULL)
	{
		$tx = $this->texy;

		// find hard linebreaks
		if ($tx->mergeLines) {
			// ....
			// ... => \r means break line
			$content = Regexp::replace($content, '#\n +(?=\S)#', "\r");
		} else {
			$content = Regexp::replace($content, '#\n#', "\r");
		}

		$el = new Texy\HtmlElement('p');
		$el->parseLine($tx, $content);
		$content = $el->getText(); // string

		// check content type
		// block contains block tag
		if (strpos($content, Texy\Texy::CONTENT_BLOCK) !== FALSE) {
			$el->setName(NULL); // ignores modifier!

		// block contains text (protected)
		} elseif (strpos($content, Texy\Texy::CONTENT_TEXTUAL) !== FALSE) {
			// leave element p

		// block contains text
		} elseif (preg_match('#[^\s'.Texy\Patterns::MARK.']#u', $content)) {
			// leave element p

		// block contains only replaced element
		} elseif (strpos($content, Texy\Texy::CONTENT_REPLACED) !== FALSE) {
			$el->setName($tx->nontextParagraph);

		// block contains only markup tags or spaces or nothing
		} else {
			// if {ignoreEmptyStuff} return FALSE;
			if (!$mod) {
				$el->setName(NULL);
			}
		}

		if ($el->getName()) {
			// apply modifier
			if ($mod) {
				$mod->decorate($tx, $el);
			}

			// add <br />
			if (strpos($content, "\r") !== FALSE) {
				$key = $tx->protect('<br />', Texy\Texy::CONTENT_REPLACED);
				$content = str_replace("\r", $key, $content);
			};
		}

		$content = strtr($content, "\r\n", '  ');
		$el->setText($content);

		return $el;
	}

}
