<?php

class KMVC {

	public function __construct() {}

	public static function config( $property ) {
		global $config;
		return (array_key_exists($property, $config)) 
			? $config[$property] 
			: null;
	}

}