<?php

class arr {

	/**
     * Sorts multidimensional arrays by a specific key.  Useful when you have an array of associative arrays that you want to sort
     * by one of the keys in the associative array.
     *
     * @param   array     The array should be an array of associative arrays. Ex. $users = array( array( 'UserName' => 'todd' ), array( 'UserName' => 'mary' ) )
     * @param   string    the key of the assiciative array that you want to sort by
     * @param   bool      whether to use the natural order algorithm.  Useful for filenames and mailboxes to sort the way you would expect them to be.
     * @return  void
     */
    public static function sortByArrayKey( array &$arr, $arrayKey, $useNaturalSort = FALSE )
    {
        if( $useNaturalSort )
            usort( $arr, create_function( '$a, $b', "return strnatcasecmp(\$a['$arrayKey'], \$b['$arrayKey']);" ) );
        else
            usort( $arr, create_function( '$a, $b', "return strcmp(\$a['$arrayKey'], \$b['$arrayKey']);" ) );
    }

}