<?php

class Router {

    public $url = null;
    public $urlArray = array();

    
    public $controller;
    public $action;
    public $auth;
    public $queryParams = array();
    
    public $routing = array(

        'index' => array(
			'controller' 	=> 'main',
        	'action'		=> 'index',
        	'auth' 			=> 0
        ),
        'quote' => array(
        	'controller' 	=> 'quote',
        	'action'		=> 'quoteForm',
        	'auth' 			=> 0
        )
    );
    
    private $default = array(
		'controller'    => 'main',
		'action'        => 'index',
		'auth'          => null
	);
    
    
    public function __construct( $url, $debug = false ) {

		$this->debug = $debug;

		//prepare untreated url
		$url = !empty($url) ? $url : ( !empty($_GET['page']) ? $_GET['page'] : $_SERVER['REQUEST_URI'] );
        $this->setMVC( $url );
        
    }
    
    
    public function getDefaults() {
        return $this->default;
    }
    
    
    private function setMVC( $url ) {


		//skim traditional query params, if they exist
		$start	= substr( $url, 0 ) === '/' ? 1 : 0;
		$end	= strpos( $url, '?');
		$this->url = ($end!==false) ? substr( $url, $start, $end-1 ) : substr( $url, $start );


	    if( !array_key_exists( $this->url, $this->routing ) ) {
			
			$this->auth		= Permissions::$defaultPerm;
			$this->urlArray = explode("/",$this->url);

			
			if( is_array($this->urlArray) && count($this->urlArray)>0 ) {
			
				$count			= count($this->urlArray);
				$urlArray		= $this->urlArray; // mainpulate a copy
				
				switch( $count ) {
					case 1:
						$controller = array_shift($urlArray);
						$action		= $this->default['action'];
						$params		= array();
						
						break;
						
					case 2:
						$controller	= array_shift($urlArray);
						$action		= array_shift($urlArray);
						$params		= array();
						break;
						
					case 3:
					default:
						$controller	= array_shift($urlArray);
						$action		= array_shift($urlArray);
						$params		= $urlArray;
						break;
				}
				
				//now that we have our stuff, set it.
				$this->controller 	= $controller;
				$this->action		= $action;
				$this->queryParams	= $params;
			
			} else {
			
				$route				= $this->default;
				$this->controller 	= $route['controller'];
				$this->action		= $route['action'];
				
			}

    	} else {
    	
    		$route				= $this->routing[$this->url];
    		$this->controller 	= $route['controller'];
    		$this->action		= $route['action'];
    		$this->auth			= $route['auth'];
    	
    	}
    	
    	if($this->debug) {
    		echo '<pre>';
    		echo 'Controller: '.$this->controller.'<br>';
    		echo 'Action: '.$this->action.'<br>';
    		echo 'Auth: '.$this->auth.'<br>';
    		echo 'Params: '.'<br>';
    		print_r($this->queryParams);
    		
	    	echo '</pre>';
    	}
    	
    }
    
    

    /*
     *  Auth code cheat sheet.
     *      Not Set - public
     *      1 - logged in regular user
     *      2 - logged in admin user
     */
    public function routeURL( $url_string ) {
        
        $url = $url_string;
        return $url;       
    
    }


}