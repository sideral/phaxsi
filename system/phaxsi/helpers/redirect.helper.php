<?php

/**
 * Helps with redirections.
 * 

 * Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2008-2014, Alejandro Zuleta (http://phplab.co)
 * @link          http://phaxsi.net Phaxsi PHP Framework
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @package       Phaxsi.Helpers
 * @since         Phaxsi v 0.1
 */

class RedirectHelper{

	static function ifDifferentHost(){

		/**
		 * When the site should always be seen from the same domain,
		 * this performs the redirect (ie. example.org to www.example.org
		 */
		if(AppConfig::REDIRECT_IF_DIFFERENT_HOST){

			if(AppConfig::HTTP_HOST != $_SERVER['HTTP_HOST']){

				$url = 'http://' . AppConfig::HTTP_HOST . $_SERVER['REQUEST_URI'];

				if($_SERVER['QUERY_STRING']){
					$url .=  '?' . $_SERVER['QUERY_STRING'];
				}

				self::to($url, true);
			}
		}
	}

	static function flash($url, $key, $value){
		Session::setFlash($key, $value);
		self::to($url);
	}

	static function to($url, $permanent = false){

		$_POST = null;

		//Generalize this
		if(!preg_match("/^https?:/", $url)){
			$url = UrlHelper::localized($url);
		}

		if($permanent){
			header("HTTP/1.1 301 Moved Permanently");
		}

		PluginManager::getInstance()->onRedirect($url);

		Session::end(true);
		header('Location: '.$url);
		exit;

	}
	
	/**
	 *	http://co.php.net/manual/en/function.headers-sent.php#60450
	 * @param string $url 
	 */
	static function force($url){
		if (!headers_sent()){
			self::to($url);
		}
		else {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.$url.'";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
			echo '</noscript>';
		}
	}

}
