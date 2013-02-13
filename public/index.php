<?php

// this file is the bootstrap
function is_production_server() {
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    //if QA or OL is in the url, its not production.
    $testEnvironments = array(
    	'localhost',
    	'127.0.0.1'
    );
    $matchCriteria = '('.implode('|',$testEnvironments).')';
    return (!preg_match( $matchCriteria , $url)) ? TRUE : FALSE;
}


/**
 * Sets error reporting standard for production environment.
 * If production, errors are supressed, otherwise its all visible.
 */
if(!is_production_server()) {
    ini_set('display_errors', '1');
    error_reporting(-1);
} else {
    ini_set('display_errors', '0');     # don't show any errors...
    error_reporting(E_ALL | E_STRICT);  # ...but do log them
}




// Set Time Zone
date_default_timezone_set('America/Los_Angeles');

define( '__ROOT__'          , dirname(dirname((__FILE__))) ); //double dirname when using clean urls
define( '__APP__'           , __ROOT__.'/app' );
define( '__CONTENT__'       , __APP__.'/content' );


function stripSlashesDeep($value) {
    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
    return $value;
}


function removeMagicQuotes() {
    if ( get_magic_quotes_gpc() ) {
        $_GET    = stripSlashesDeep($_GET   );
        $_POST   = stripSlashesDeep($_POST  );
        $_COOKIE = stripSlashesDeep($_COOKIE);
    }
}


/*
 * Check register globals and remove them
 */
function unregisterGlobals() {
    if (ini_get('register_globals')) {
        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}
unregisterGlobals();


// get test version
define( 'DEFAULT_CONTENT'   , 'default' );
define( 'TEST_VERSION'      , !empty($_GET['t']) ? $_GET['t'] : DEFAULT_CONTENT );

// get page
$pageParam		= !empty($_GET['page']) ? $_GET['page'] : 'index';


require_once( __APP__ . '/core/routing.php' );
$router = new Router( $pageParam );


function callHook() {

	global $router;
	


    $controller		= $router->controller;
    $action			= $router->action;
    $queryString	= $router->queryParams;
    $authCode		= $router->auth;
    
    $pageIsAllowed 	= Permissions::validateAuth( $authCode );
    
    //sanitize controller name to avoid traversal attack
    $controller		= strtolower(preg_replace("/[^a-zA-Z\s]/","",$controller));
	$controllerName = ucfirst($controller).'_Controller';


	if( class_exists($controllerName) )
	{
		// we require a controller naming convention of Cname_Controller

		$dispatch		= new $controllerName($controller,$action,$authCode);
		
		if (method_exists($controllerName, $action))
		{
		
			if($pageIsAllowed)
			{    
				call_user_func_array(array($dispatch,$action),$queryString);   
			}
			else
			{
				$url = $_SERVER[REQUEST_URI];
				URL::redirect('login', "?unid=".md5(time().$url)."&ts=".time()."&loc=$url");
			}
		   
		}
		else
		{
			Template::render404('Method doesnt exist');
		}

	}
	else
	{
		Template::render404('Controller doesnt exist');
	}


}


// Gets written content. If none exists for the current AB test, then get default content
$content    	= array();
$contentFile    = file_exists( __CONTENT__ . '/' . TEST_VERSION . '.php')
					? __CONTENT__ . '/' . TEST_VERSION . '.php'
					: __CONTENT__ . '/' . DEFAULT_CONTENT . '.php';
require_once( $contentFile );


// autoloader of class files and beyond, eventually use [spl_autoload_register]
function __autoload($class) {
    
	$us		= strpos($class,"_");
	$name	= ($us!==false) ? substr($class,0,$us) : $class;
	
	
	$classFile	= strtolower($name);
	
	$in_controllers = __APP__ . '/controllers/'. $classFile . '.php';
	$in_models		= __APP__ . '/models/'. $classFile.'.php';	
	$in_library		= __APP__ . '/library/' . $classFile . '.php';
	$in_core		= __APP__ . '/core/' . $classFile . '.php';
		
//	echo '<br>['.$class.'] '.$classFile.'<br>';

    if (file_exists( $in_models )) {
    
    	if( preg_match( '/model/i', $class ) ) {
	    	require_once( $in_models );
	    	return;
    	}
    }
    
    if (file_exists( $in_controllers )) {
        require_once( $in_controllers );
        
    } else if (file_exists( $in_library )) {
        require_once( $in_library );    
        
    } else if (file_exists( $in_core )) {
    	require_once( $in_core );
    	
    } else {
        /* Class not found. */
    }
    

    
}

// Load the page.
callHook();