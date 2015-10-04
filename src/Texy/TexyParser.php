<?php

/**
 * This file is part of the Texy! (http://texy.info)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */


/**
 * Texy parser base class.
 *
 * @author     David Grudl
 */
class TexyParser extends TexyObject
{
	/** @var Texy */
	protected $texy;

	/** @var TexyHtml */
	protected $element;

	/** @var array */
	public $patterns;


	/**
	 * @return Texy
	 */
	public function getTexy()
	{
		return $this->texy;
	}

}
