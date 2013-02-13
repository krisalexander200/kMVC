<?php

/**
 * @package    FVSLibrary-Helpers
 */

class format
{
    static public function balance( $balance, $includeDollarSign = TRUE )
    {
        if( $includeDollarSign )
            return ( $balance > 0 ) ? '$' . $balance : '(' . format::dollar( 0 - $balance, $includeDollarSign ) . ')';
        return ( $balance > 0 ) ? $balance : '(' . format::dollar( 0 - $balance, $includeDollarSign ) . ')';
    }


    /**
    * Formats a phone number in (xxx) xxx-xxxx format
    *
    * @param  string  value The phone number to format
    */
    static public function phoneNumber( $value )
    {
        // Clean the number first
        $value = preg_replace( '/\D+/', '', $value );

        if( isset( $value ) && strlen( $value ) < 10 )
            return $value;

        // Check for initial '1' and 11 digit number
        if( strlen( $value ) == 11 && substr( $value, 0, 1 ) == '1' )
            $value = substr( $value, 1 );

        if( strlen( $value ) == 10 )
            return '(' . substr( $value, 0, 3 ) . ') ' . substr( $value, 3, 3 ) . '-' . substr( $value, 6, 4 );
        return $value;
    }


    /**
    * Replaces all letters with number
    *
    * @param   string  value The phone number to convert
    * @return  string
    */
    static public function letterToNumber( $value )
    {
        $value = preg_replace( '/[^0-9a-zA-Z]/', '', $value );
        $value = strtolower( $value );

        $arrayTmp = array('a' => 2, 'b' => 2, 'c' => 2,
                          'd' => 3, 'e' => 3, 'f' => 3,
                          'g' => 4, 'h' => 4, 'i' => 4,
                          'j' => 5, 'k' => 5, 'l' => 5,
                          'm' => 6, 'n' => 6,
                          'o' => 6, 'p' => 7, 'q' => 7,
                          'r' => 7, 's' => 7, 't' => 8,
                          'u' => 8, 'v' => 8, 'w' => 9,
                          'x' => 9, 'y' => 9, 'z' => 9);
        $value = strtr( $value, $arrayTmp );
        return $value;
    }


    /**
     * If a phoneNumber is 11 digits and starts with a 1, this function will clean it up and make it a 10 digit number
     *
     * @param   string
     * @return  string
     */
    static public function normalizePhoneNumber( $value )
    {
        // Clean it if it starts with a '1' and is 11 digits
        if( strlen( $value ) == 11 && text::hasPrefix( $value, '1' ) )
            $value = substr( $value, 1 );

        return $value;
    }


    /**
     * Converts the bool 'strings' returned from the appserver to a true bool.  'TRUE' = TRUE
     *
     * @param   string
     * @return  mixed
     */
    static public function stringToBool( $value )
    {
        $val = strtolower( $value );
        switch( $val ) {
            case 'true':
            case 't':
                return TRUE;
                break;
            case 'false':
            case 'f':
                return FALSE;
                break;
            default:
                return $value;
                break;
        }

    }


    /**
     * Converts a bool to a string.  This is useful for appserver calls that take boolean arguments
     *
     * @param   boolean
     * @return  string
     */
    static public function boolToString( $bool )
    {
        return ( $bool ) ? 'TRUE' : 'FALSE';
    }


    /**
    * Formats a dollar amount
    *
    * @param   string  the dollar amount to format
    * @param   bool    whether to includd the dollar sign in the return value
    * @return  string
    */
    static public function dollar( $value, $includeDollarSign = TRUE )
    {
        if( $includeDollarSign )
            return '$' . sprintf( '%.2f', $value );

        return sprintf( '%.2f', $value );
    }


    /**
    * Formats an input from a user into a legit dollar amount x.xx
    *
    * @param   string  the dollar amount to format
    * @return  string
    */
    static public function cleanInputDollarAmount( $str )
    {
        $abc = preg_replace( '/[^0-9\.]/', '', $str );
        return number_format( $abc, 2, '.', '' );
    }


    /**
    * Converts seconds to hour:min:sec
    *
    * @param   int     seconds to format
    * @param   bool    whether to pad the hours to 2 digits or not
    * @return  string
    */
    static function sec2hms( $sec, $padHours = FALSE )
    {
        // holds formatted string
        $hms = '';

        // there are 3600 seconds in an hour, so if we divide total seconds by 3600 and throw away
        // the remainder, we've got the number of hours
        $hours = intval( intval( $sec ) / 3600 );

        // add to $hms, with a leading 0 if asked for
        if( $hours > 0 )
        {
            $hms .= ( $padHours ) ? str_pad( $hours, 2, '0', STR_PAD_LEFT ) . ':' : $hours. ':';
        }

        // dividing the total seconds by 60 will give us the number of minutes, but we're interested in
        // minutes past the hour: to get that, we need to divide by 60 again and keep the remainder
        $minutes = intval( ( $sec / 60 ) % 60 );

        if( $hours > 0 )
        {
            // then add to $hms (with a leading 0 if needed)
            $hms .= str_pad( $minutes, 2, '0', STR_PAD_LEFT ) . ':';
        }
        else
        {
            $hms .= $minutes . ':';
        }

        // seconds are simple - just divide the total seconds by 60 and keep the remainder
        $seconds = intval( $sec % 60 );

        // add to $hms, again with a leading 0 if needed
        $hms .= str_pad( $seconds, 2, '0', STR_PAD_LEFT );

        // done!
        return $hms;
    }


    /**
     * @desc Returns a human-friendly version of the number given.  If less than 1 minute, returns
     *
     * @param   int
     * @return  string
     */
    static public function seconds( $seconds )
    {
        $seconds = intval( $seconds );

        // Less than 60 seconds and we just return the number of seconds
        if( $seconds < 60 )
            return $seconds . ' seconds';

        $minutes = intval( ( $seconds / 60 ) % 60 );
        $hours = intval( intval( $seconds ) / 3600 );
        if( $hours > 0 )
        {
            return sprintf( '%s hours', number_format( $hours + ( $minutes / 60 ), 1 ) );
        }
        else
        {
            $seconds = intval( $seconds % 60 );
            return sprintf( '%s minutes', number_format( $minutes + ( $seconds / 60 ), 1 ) );
        }
    }


    /**
     * Strip all characters that are not digits
     *
     * @param   string
     * @return  string
     */
    static public function stripNonDigits( $value )
    {
        return preg_replace( '/\D+/', '', $value );
    }


    /**
     * Strip all characters that are not digits or x.  Used to filter a phone number plus mailbox entry. Ex. 8004771477x834
     *
     * @param   string
     * @return  string
     */
    static public function stripNonDigitsExceptX( $value )
    {
        return preg_replace( '/[^0-9x]+/', '', $value );
    }


    /**
     * Limits the length of the displayed string to $maxCharacters and replaces them with $endDelimiter
     *
     * @param   string
     * @param   int      Maxiumum amount of characters to display, including the delimeter
     * @return  string
     */
    static public function limitDisplayLength( $str, $maxCharacters = 13, $endDelimiter = '...' )
    {
        // If the string is too long, strip the last N chars and add the endDelimiter
        if( strlen( $str ) > $maxCharacters )
        {
            return substr( $str, 0, ( $maxCharacters - strlen( $endDelimiter ) ) ) . $endDelimiter;
        }

        return $str;
    }


    /**
     * Returns either the given string (if one is given) or empty string if none is given.  Can be used to echo
     * values that may or may not be there.  If you are passing in an array, you need to make sure $root is your array and
     * $key is the key of the value you want to check for
     *
     * @param   mixed
     * @param   string   If $key is passed in, $root must be an array and $key will be the array index/key that will be checked and returned
     * @return  string
     */
    static public function ifExists( $root, $key = NULL, $defaultValue = '' )
    {
        if( isset($key) && is_array( $root ) )
            return ( isset( $root[$key] ) ) ? $root[$key] : $defaultValue;

        return ( isset( $root ) ) ? $root : $defaultValue;
    }


    /**
     * Clamps a value between $min and $max
     *
     * @param   number
     * @param   number
     * @param   number
     * @return  number
     */
    static public function clamp( $value, $min, $max )
    {
        return ( ( $value < $min ) ? $min : ( ( $value > $max ) ? $max : $value ) );
    }


    /**
     * Takes an arbitrary number of arguments and creates a string of them imploded with '.'s between them
     *
     * @param   string
     * @return  string
     */
    static public function makeKey( $item1, $item2 )
    {
        $args = func_get_args();

        // Set our key
        return implode( '.', $args );
    }


    /**
     * Takes an arbitrary string and converts underscores _ to hyphens -
     *
     * @param   string
     * @return  string
     */
    static public function hyphenate( $string )
    {
		return str_replace( '_', '-', $string );
    }


    /**
     * Takes an arbitrary string and converts hyphens - to underscores _
     *
     * @param   string
     * @return  string
     */
    static public function deHyphenate( $string )
    {
		return str_replace( '-', '_', $string );
    }


    /**
     * Takes in a SQL timestamp and formats it according to the given formatter
     *
     * @param   string    SQL timestamp
     * @param   string    format to be sent to the date() function
     * @return  string
     */
    public static function SQLDate( $date, $format = 'D m-d-Y g:i a' )
    {
        $year = substr( $date, 0, 4 );
        $month = substr( $date, 4, 2 );
        $day = substr( $date, 6, 2 );
        $hour = substr( $date, 8, 2 );
        $minute = substr( $date, 10, 2 );
        $second = substr( $date, 12, 2 );

        return date( $format, mktime( $hour, $minute, $second, $month, $day, $year ) );
    }


    /**
     * Takes in a SQL timestamp and formats it
     *
     * @param   string    SQL timestamp
     * @return  string
     */
    public static function SQLDayDefault( $date )
    {
        return self::SQLDay( $date );
    }


    /**
     * Takes in a SQL timestamp and formats it according to the given formatter
     *
     * @param   string    SQL timestamp
     * @param   string    format to be sent to the date() function
     * @return  string
     */
    public static function SQLDay( $date, $format = 'm/d/Y' )
    {
        if( empty( $date ) )
            return $date;

        $year = substr( $date, 0, 4 );
        $month = substr( $date, 4, 2 );
        $day = substr( $date, 6, 2 );
        $hour = substr( $date, 8, 2 );
        $minute = substr( $date, 10, 2 );
        $second = substr( $date, 12, 2 );

        return date( $format, mktime( $hour, $minute, $second, $month, $day, $year ) );
    }


    /**
     * Takes in seconds since epoch and outputs int he given format
     *
     * @param   int      seconds since epoch
     * @param   string   format to be sent to the date() function
     * @return  string
     */
    public static function epochDate( $seconds, $format = 'm/d g:i a' )
    {
        return date( $format, $seconds );
    }


    /**
     * Takes in milli-seconds since epoch and outputs in the default format
     *
     * @param   int      milli-seconds since epoch
     * @return  string
     */
    public static function epochDateFromMillisecondsDefault( $milliSeconds )
    {
        return self::epochDateFromMilliseconds( $milliSeconds );
    }


    /**
     * Takes in milli-seconds since epoch and outputs int he given format
     *
     * @param   int      milli-seconds since epoch
     * @param   string   format to be sent to the date() function
     * @return  string
     */
    public static function epochDateFromMilliseconds( $milliSeconds, $format = 'm/d g:i a' )
    {
        return date( $format, $milliSeconds / 1000 );
    }


    /**
     * @desc converts a unix time stamp to .NET ticks
     *
     * @param   int
     * @return  string
     */
    public static function dateToTicks( $secondsSinceEpoch )
    {
        // Convert to UTC (add 8 hours)
        $daylightSavingsSeconds = 3600;
        $secondsSinceEpoch += $daylightSavingsSeconds;
        $secondsSinceEpoch -= 28790;

        // Grab our ticks
        $ticks = $secondsSinceEpoch + ( 621355968000000000 / 10000000 );
        $stringTicks = sprintf( '%s0000000', $ticks );

        return $stringTicks;
    }


    /**
     * @desc Returns a link to the AppServer log viewer
     *
     * @param   string
     * @return  string
     */
    public function appServerLogLink( $functionName )
    {
        $logUrl = 'http://webapps.freedomvoice.com/CustView/Logs/AppServerNETLogs.aspx?SearchType=NameAndLastNSeconds&FunctionName=%s&NSeconds=300&NTime=300&BaseDateTime=%s';
        $ticks = self::dateToTicks( time() );

        return sprintf( $logUrl, $functionName, $ticks );
    }


    /**
     * @desc Formats a mac address in xx-xx-xx-xx-xx style
     *
     * @param   string
     * @return  string
     */
    static public function macAddress( $address )
    {
        return sprintf( '%s:%s:%s:%s:%s:%s', substr( $address, 0, 2 ), substr( $address, 2, 2 ), substr( $address, 4, 2 ), substr( $address, 6, 2 ), substr( $address, 8, 2 ), substr( $address, 10, 2 ) );
    }


	/**
	 * Transforms a prefix into an AppServer compatible phone number filter.
	 *
	 * @param	mixed
	 * @return	string|null
	 */
	public static function normalizePhoneNumberFilter( $numberFilter = NULL )
	{
		// Avoid double encoding a filter
		if ( is_string( $numberFilter ) AND substr( $numberFilter, -1 ) === '%' )
			return $numberFilter;
		
		// We expect a valid phone number prefix e.g. "800"
		if( is_numeric( $numberFilter ) )
			return $numberFilter .= '%';
		
		// Anything else should default to standard toll free numbers
		return NULL;
	}

}
