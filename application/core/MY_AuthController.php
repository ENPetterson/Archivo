<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_AuthController extends CI_Controller {
    public function __construct() {
        parent::__construct();

        //print_r("sasasa"); die;

        
        $usuario = $this->session->userdata('usuario');



        if ($usuario['id'] == ''){
            redirect('/login', 'refresh');
        }
        
        $controladoresInternos = array(
            'principal/index', 
            'menu/getMenues', 
            'menu/getMenu',
            'menu/getNombre',
            'util',
            'permiso/getPermisosVista'
        );
        
        $controladores = $this->session->userdata('controlador');
        if (!in_array('*', $controladores)){ //Si tiene el controlador * es admin!!!
        
            $controlador = $this->router->class;
            $metodo = $this->router->class . '/' . $this->router->method;
            if (!in_array($controlador, $controladoresInternos) && !in_array($metodo, $controladoresInternos)){
                if (!in_array($controlador, $controladores)){
                    if (!in_array($metodo, $controladores)){
                        show_error('Permiso denegado', 505);
                    }
                }
            }
        }
        
        
    }
    
    
}

?>
