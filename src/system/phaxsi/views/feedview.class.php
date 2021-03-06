<?php

/**
 * View for RSS feeds.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Views
 * @since         Phaxsi v 0.1
 */

class FeedView extends View{

	private $feed;

	function __construct($context){
		Loader::includeLibrary('feedwriter/FeedWriter.php');
		$this->feed = new FeedWriter(RSS2);		
		parent::__construct($context);
	}

	function render(){
		header('Content-Type: application/rss+xml');
		return $this->feed->genarateFeed();
	}

	function getFeed(){
		return $this->feed;
	}

}

