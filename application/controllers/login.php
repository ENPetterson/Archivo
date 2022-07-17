<?php
class Login extends CI_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->helper('cookie');
        $datos['dominio'] = get_cookie('dominio');
        $this->load->view('template/encabezado');
        $this->load->view('login/login', $datos);
        $this->load->view('template/pie');
    }
    
}
