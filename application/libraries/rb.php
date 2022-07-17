<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rb {
	
	function __construct() {
		// Include database configuration
		if (file_exists(APPPATH.'/config/' . ENVIRONMENT . '/database.php')){
                    include(APPPATH.'/config/' . ENVIRONMENT . '/database.php');
                } else {
                    include(APPPATH.'/config/database.php');
                }
		// Get Redbean
		include(APPPATH.'/third_party/rb/rb.php');
                
                $CI =& get_instance();
                $CI->load->database();
		
		// Database data
		$host = $CI->db->hostname;
		$user = $CI->db->username;
		$pass = $CI->db->password;
		$db = $CI->db->database;
		
		// Setup DB connection
		R::setup("mysql:host=$host;dbname=$db", $user, $pass);
                RedBean_OODBBean::setFlagBeautifulColumnNames(false);
                
                //R::freeze(array('tenencialebac', 'posicionmonetaria'));
                //  R::debug(true);
	} //end __contruct()
} //end Rb