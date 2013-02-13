<?php

class Template {

    protected $variables = array();
    protected $_controller;
    public $_type;
    protected $_action;
    protected $template_dir = '/app/templates/';
    protected $views_dir = '/app/views/';
    protected $_assets;
    protected $pathJavascript = '/public/assets/js/';




    public function __construct($controller,$action) {
        $this->_controller = $controller;
        $this->_action = $action;
    }

    /** Set Variables **/

    public function set($name,$value) {
        $this->variables[$name] = $value;
    }

    
    public function set_type( $type ) {
        $this->_type = $type;
        $this->template_dir = ($this->_type == 'application') ? 'application/' : $this->_type.'/';
    }


    public static function Element( $template_path, $available_vars = array()) {
		
		$element_dir = __APP__ . '/templates/';
		
        if(file_exists( $element_dir . $template_path . '.php')) {
            $filename = $element_dir . $template_path . '.php';
            
        } else if(file_exists( $element_dir . '/general/' . $template_path . '.php')) {
            $filename = $element_dir . '/general/' . $template_path . '.php';
            
        } else if (file_exists( $element_dir . $template_path . '.php')) {
            $filename = $element_dir . $template_path . '.php';
            
        }
        $outputData = $available_vars;
        require_once $filename;
    }
    
    
    public function setAssets( array $assets ) {
        $this->_assets = $assets;
    }


    public function getAssets() {
        $assets = $this->_assets;
        $scripts = '';
        $styles = '';
        $url = new URL();
        
        foreach($assets as $asset) {
            $file = $this->pathJavascript.$asset['file'].'.js';
            if($asset['type']=='javascript') {
                $scripts .= "\t".'<script type="text/javascript" src="'.$url->getBaseUrl().$file.'"></script>'."\n";
            }
            
            if($asset['type']=='css') {
                $styles .= '';
            }
        }
        
        $output = $styles . "\n" . $scripts;
        return $output;
    }
    

    /** Display Template **/
    public function render( $file, $renderHeadersAndFooters = true ) {

        extract($this->variables);
        
        $template_dir 	= $this->template_dir;
        $file_base 		= __ROOT__ . $this->views_dir . $this->_controller . '.php';

		// header
		$header 		= __ROOT__ . $this->template_dir . $this->_type . '/header.php';
		$alt_header 	= __ROOT__ . $this->template_dir . 'header.php';
		if ($renderHeadersAndFooters && file_exists( $header )) {
			include ( $header );
			
		} else if($renderHeadersAndFooters && file_exists($alt_header)) {
			include ( $alt_header );
		}

        // view
        $view_url 		= __ROOT__ . $this->views_dir . $file . '.php';
        if(file_exists( $view_url )) {
            include $view_url;

        } else if(file_exists($file_base)) {
            include $file_base;
        }

        // footer
        $footer 		= __ROOT__ . $this->template_dir . $this->_type . '/footer.php';
        $alt_footer 	= __ROOT__ . $this->template_dir . '/footer.php';
		if ($renderHeadersAndFooters && file_exists( $footer )) { 
			include ( $footer );

		} else if($renderHeadersAndFooters && file_exists($alt_footer)) {
			include ( $alt_footer );
		}
		
    }
    
    public static function render404( $reason ) {
    
    	// if custom 404 exists, load it
		$file = __APP__ . '/templates/404.php';
		if( file_exists( $file ) ) {
			include $file;
		} else {
			//otherwise, load our ugly built-in 404
			self::renderCore404();
		}
	}

	public static function renderCore404( $reason ) {
		$file = __APP__ . '/core/templates/404.php';
		include $file;
	}

    public static function partial( $view, array $args = array() ) {
		extract($args);
        $path = __APP__;
        $file = $path.'/templates/'.$view.'.php'; error_log($file);
    	if(file_exists( $file )) {
            include( $file );
        }
    }

}

?>