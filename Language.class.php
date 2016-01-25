<?php
	class Language
	{
		/** Give the Language after the code **/
		public $lang;
		
		/** Give the Urls to change language manualy (Keep page and GET param) **/
		public $Urls;
		
		/** Use on class for define scheme (http/https) **/
		private $REQUEST_SCHEME;
		
		/** Time the language cooke stay (here 3 Months) **/
		private $COOKIE_TIME;
		
		/** 
		* Liste of language existing on website 
		* Define the default language to use  
		**/
		private $Languages = array(
								"All"=>array("fr"=>array(),"en"=>array(),"it"=>array()),
								"Default"=>"en"
							);
							
		/**
		* Construct make All the work
		**/
		public function __construct()
		{
			$this->COOKIE_TIME = (60*60*24*30*3);
			$this->REQUEST_SCHEME = $this->RequestScheme()."://";
			$this->getLang();
			$this->getRedirectUrl();
		}
		
		/**
		* Verify if the website use https for menu creation
		**/
		private function RequestScheme()
		{
			if(
				(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
				OR ($_SERVER['REQUEST_SCHEME']=="https")
				OR ($_SERVER['SERVER_PORT']=="443"))
			{
				return "https";
			}
			else
			{
				return "http";
			}
		}
		
		/**
		* Check if the language is given by POST or GET
		* if not check if cookie already create
		* if not check the language of the browser
		* 
		* Verify if the language exist on the website, if not use Default
		**/
		private function getLang()
		{
			if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				if (isset($_POST['lang'])) {
					$this->lang = $_POST['lang'];
				} else if (isset($_GET['lang'])) {
					$this->lang = $_GET['lang'];
				} else if (isset($_COOKIE['lang'])) {
					$this->lang = $_COOKIE['lang'];
				} else {
					$this->lang = self::getBrowserLang();
				}
			}else{
				$this->lang = $_COOKIE['lang'];
			}
			
			if (!array_key_exists($this->lang,$this->Languages["All"])) {
				$this->lang = $this->Languages["Default"];
			}
			
			
			$this->setLang();
		}
		
		/**
		* Verify the language of the browser
		* 
		* Verify if the language exist on the website, if not use Default
		**/
		private static function getBrowserLang()
		{
			$browser_lang = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : $this->Languages["Default"];
			return $browser_lang;
		}
		
		/**
		* Create COOKIE and define with the language of the website
		**/
		private function setLang()
		{
			setcookie('lang', $this->lang, time() + $this->COOKIE_TIME, '/', $_SERVER['HTTP_HOST']);
			define('LANG', $this->lang);
		}
		
		/**
		* Generate Redirection Link to change language manualy
		* Keep the same page exactly
		**/
		private function getRedirectUrl()
		{
			$Url = array();
			if((isset($_SERVER['QUERY_STRING'])) AND (!empty($_SERVER['QUERY_STRING'])) AND ($_SERVER['QUERY_STRING']!="")){
				if(isset($_GET['lang'])){
					$AddUrl = str_replace("lang=".$this->lang, "lang=%LANG%", $_SERVER['REQUEST_URI']);
				}
				else{
					$AddUrl = $_SERVER['REQUEST_URI']."&lang=%LANG%";
				}
			}
			else{
				$AddUrl = $_SERVER['REQUEST_URI']."?lang=%LANG%";
			}
			$BaseUrl = $this->REQUEST_SCHEME.$_SERVER['HTTP_HOST'].$AddUrl;
			foreach($this->Languages["All"] AS $Languages=>$Datas){
				$Url[$Languages] = str_replace("%LANG%", $Languages, $BaseUrl);
			}
			$this->Urls = $Url;
		}
	}
