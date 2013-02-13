<?php

class Controller {
    
	protected $_controller;
	protected $_action;
	protected $_template;
	protected $_type = 'main';
	protected $_assets = array();
	protected $_authRequired;
	protected $_isAuth;

	public $renderHeadersAndFooters = true;
	
	public $render;

	function __construct($controller = null, $action = null, $auth = null) {
            
            if($controller != null && $action != null) {
				
                $this->_controller = ucfirst($controller);
                $this->_action = $action;
                $this->_isAuth = (!empty($auth) && $auth!=0)
                	? LoggedInUser::isLoggedIn()
                	: true;
                
                //Magic Modeling (naming convention: 
                $model = ucfirst(strtolower($controller)).'_Model';
                $this->render = true;
                $this->$model = new $model;
                $this->_template = new Template($controller,$action);
                
            } else {
            
                // is standalone instantiation and none of those dependencies are required.
                $this->render = false;
                
            }

	}
        

	public function asset($type, $path) {
		$this->_assets[] = array(
			'type'=>$type,
			'file'=>$path
		);
	}


	public function isHTTPVerb( $request_type ) {
		$verb = $_SERVER['REQUEST_METHOD'];
		return (strtoupper($verb) == strtoupper($request_type)) ? TRUE : FALSE;
	}
	
	
	public function HTTPVerb() {
		$verb = $_SERVER['REQUEST_METHOD'];
		return (strtoupper($verb));
	}




	/*
	 * @param boolean   $status
	 * @param string    $statusMessage
	 * @param mixed     $data
	 * 
	 * @returns         json object with status, statusMessage, data, timestamp
	 */
	public function renderAsJSON($status = false, $statusMessage = null, $data = null) {
		
		$this->render = false;
		
		$return = array(
			'status' => $status,
			'statusMessage' => $statusMessage,
			'data' => $data,
			'timestamp' => $_SERVER['REQUEST_TIME']
		);
		header('Content-type: application/json');
		print json_encode($return);
		
	}




	/*
	 * In order for something to authenticate properly:
	 *  -- auth must be required
	 *  -- user must be currently authenticated
	 *  -- this class must not be just a instance for utility purposes, rather than rendering a view
	 */
	public function authenticatePage() {
		if( $this->_isAuth === null && $this->_controller != null && $this->_action != null ) {
			URL::redirect('login');
		}
	}
	

	public function set_template_type( $template_type = 'application' ) {
		if(!is_object($this->_template)) {
			$this->_template = new Template( $this->_controller, $this->_action );
		}
		$this->_template->set_type( $this->_type );
	}
	
	/*
	 * Renders the view
	 */
	public function renderView( $file ) {
		if ($this->render) {
			$this->_template->_type = $this->_type;
			$this->_template->setAssets($this->_assets);
			$this->set('assets', $this->_template->getAssets($this->_assets));
			$this->_template->render( $file, $this->renderHeadersAndFooters );
		}
		
	}
	


	function set($name,$value) {
		$this->_template->set($name,$value);
	}

	function __destruct() {
            
	}
    
}

