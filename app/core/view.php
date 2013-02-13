<?php

class View {
    
    private $controller;
    private $views_dir = 'views';
    
    private $vars = array();
    private $path;
    
    public function __construct( $view = null ) {
		$this->controller = $view;
		$this->getViewFile($view);
    }
    
    public function getViewFile( $view ) {
        $filename = 'view.'.$view.'.php';
        $path = __APP__;
        $file = $path.'/'. $this->views_dir .'/'.$filename;
        //echo $file;
        if(file_exists( $file )) {
            require_once( $file );
        }
    }
    
    public static function partial( $view, array $args = array() ) {
		extract($args);
        $path = __APP__;
        $file = $path.'/views/'.$view.'.php';
    	if(file_exists( $file )) {
            include( $file );
            error_log($file);
        }
    }
    
	
    
}