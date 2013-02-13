<?php


class URL {
    
    public $applicationUrl = __APP__;
    private static $LOCAL_DIR = '';
    public static $url;
    public static $full_url;
    
    public static $clean_urls = array(
        'home' => '/',
        'quote' => '/quote'
    );
    
    public function __construct() {
        //$this->url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
    }
    


    public static function isPage( $page ) {
        
        $router = new Router();
        $controller = $router->getController();
        $action = $router->getControllerAction();
        $id = $router->getQueryString();
        $currentPage = !empty($id) ? substr($controller, 0, -1) : $controller;
        
        
        if(is_array($page)) {
            
            foreach($page as $p) {
                if($p == $currentPage) {
                    return TRUE;
                }
                
                if(in_array($currentPage, $rewrites)) {
                    if($action == $p) {
                        return TRUE;
                    }
                }
            }
            return FALSE;
            
        } else if(is_string($page)) {
            if($page == $currentPage) {
                return TRUE;
            }
            
            if(in_array($currentPage, $rewrites)) {
                if($action == $page) {
                    return TRUE;
                }
            }
            
            return FALSE;
            
        } else {
            return FALSE;
            
        }
        
    }


	

    /*
     * URL Director
     */
    public static function redirect( $page, $args = '' ) {
    	if( !empty($page) && array_key_exists($page, self::$clean_urls) ) {
    		$location = self::$clean_urls[$page];
    		$location .= !empty( $args ) ? '?='.$args : '';
			return header('Location: ' . $location );
		} else {
			return;
		}
    }
    
    public static function getBaseUrl() {
        return "http://" . $_SERVER['HTTP_HOST']; // add . $_SERVER['REQUEST_URI'] for full url
    }

    public function getThemeUrl() {
        $base = $this->getBaseUrl();
        $app = $this->applicationUrl;
        return $base.$app;
    }
    
    public static function getCSSUrl() {
    	return self::getBaseUrl().'/public/assets/css';
    }
    
    public static function getJavascriptUrl() {
	    return self::getBaseUrl().'/public/assets/js';
    }
    
    public static function getImagesUrl() {
	    return self::getBaseUrl().'/public/assets/images';
    }

    public static function link( $linkName ) {
        $member_id = LoggedInUser::get_user_id();
        return (array_key_exists($linkName, self::$clean_urls)) ? self::$clean_urls[$linkName] : null;
    }
    
}

