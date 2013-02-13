<?php

class Model {
	
	public function __construct() {
	
	}
	
	/**
    * Shortcut for all Models for calling appserver functions.  Automatically instantiates an AppServerClient
    * if there isn't a connection
    *
    * @param   string  function to call in the AppServer
    * @param   array   array of data to pass to the given function
    * @param   bool    whether to print information to the error_log
    * @param   int     function version.  almost always 1
    * @return  array
    */
    public function call( $functionName, array &$input, $functionVersion = 1 )
    {
        
        $input['FunctionName'] = $functionName;

		$appclient 	= new AppServerClient_Core();
		
		try {

        	$result 	= $appclient->call( $functionName, $input, $functionVersion );
        	
        	//disconnect after use
        	$appclient->disconnect();
        	
        } catch (Exception $e) {
        	
        	//$this->log_result( $functionName, $input, $e ); //log appserver failures
        	return $e;
        	
        }

        return $result;
    }
	
	public static function log_result($functionName, $input, $result)
    {
        
        //The message to be recorded
        $message = <<<MESSAGE
==== START APPSERVER CALL (%s -- %s) ====
Input
=====
%s
Output
======
%s
==== END APPSERVER CALL (%s -- %s) ====

MESSAGE;
        
        //Enumerates the arrays
        $inStr = print_r($input, TRUE);
        $outStr = print_r($result, TRUE);
        reset($input);
        reset($result);
        
        
        $message = sprintf($message, $functionName, date("Y-m-d H:i:s"), $inStr, $outStr, $functionName, date("Y-m-d H:i:s"));
        $hostInfo = $_SERVER['SERVER_NAME'].' ('.$_SERVER['SERVER_ADDR'].')';
        $currentPath = url::current();
        
        
        //pages not to send to logging
        $ignorePaths = array(
            'main/ping'
        );
        
        /* 
         * If we haven't specifically specified the current page 
         * to be omitted from the logs, then log it!
         */
        if(!in_array( $currentPath, $ignorePaths )) {
        
            $functionTitle = 'AppServer Call'.(isset($input['FunctionName']) ? ' ['.$input['FunctionName'].']' : '' );    
            
            $gmessage = new GELFMessage();
            $gmessage->setHost( $hostInfo );
            $gmessage->setLevel(1);
            $gmessage->setShortMessage('AppServer Call');
            $gmessage->setFullMessage( $message );

            //message details
            $gmessage->setAdditional( 'Timestamp', time() );
            
            //INPUT App server details, for searchability
            $gmessage->setAdditional( 'RoleID', isset($input['RoleID']) ? $input['RoleID'] : '' );
            $gmessage->setAdditional( 'Application', isset($input['Application']) ? $input['Application'] : '' );
            $gmessage->setAdditional( 'UserName', isset($input['UserName']) ? $input['UserName'] : '' );
            $gmessage->setAdditional( 'FunctionName', isset($input['FunctionName']) ? $input['FunctionName'] : '' );
            $gmessage->setAdditional( 'RemoteAddress', isset($input['_Remote_Address']) ? $input['_Remote_Address'] : '' );
            $gmessage->setAdditional( 'Identity', isset($input['Identity']) ? $input['Identity'] : '' );
            
            //OUTPUT App Server details
            $gmessage->setAdditional( 'OperationTimer', isset($result['_OperationTimer']) ? $result['_OperationTimer'] : '' );
            $gmessage->setAdditional( 'AppServerHost', isset($result['_AppServerHost']) ? $result['_AppServerHost'] : '' );
            $gmessage->setAdditional( 'OperationalResult', isset($result['OperationalResult']) ? $result['OperationalResult'] : '' );
            $gmessage->setAdditional( 'MaintainerEmail', isset($result['MaintainerEmail']) ? $result['MaintainerEmail'] : '' );

            try {
                //Publish the log
                $publisher = new GELFMessagePublisher('graylog.fvoffice');
                $publisher->publish($gmessage);
            } catch (Exception $e) {
                //if publishing to graylog fails, write an error
                error_log('Graylog Failed to publish: '.$e);
            }
        
        }
        
    }

	
}