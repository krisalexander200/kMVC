<?php

class Permissions {
    
    public static $defaultPerm = 0;
    
    /*
     * @var array $memberRoles 
     */
    private $memberRoles = array(
        'regular'       => 0,
        'administrator' => 1,
        'loggedin'      => 2 
    );
    
    
    /**
     * Validate Page
     *
     * @param type $authCode 
     */
    
    public static function validateAuth( $authCode, $isAPI = false ) {
        
        //echo 'AUTH CODE: '.$authCode;
        
        if( $authCode === 3 ) {
            /*
             * Figure out what the fuck to do with this. lol
             */
            $loggedIn = LoggedInUser::isLoggedIn();
            return $loggedIn;
            
        } else if( $authCode === 2) { // denotes simple logged in state
            $loggedIn = LoggedInUser::isLoggedIn();
            return $loggedIn ? TRUE : FALSE;
            
        } else if ($authCode === 1) { 
            $loggedIn = LoggedInUser::isLoggedIn();
            $isAdmin = LoggedInUser::getMemberRole();
            return ($loggedIn && $isAdmin) ? TRUE : FALSE;
            
        } else if ( $authCode === 0 ) {
            return TRUE;
            
        } else {
            return ( $authCode === self::$defaultPerm ) ? TRUE : FALSE;
            
        }
    }
    
}