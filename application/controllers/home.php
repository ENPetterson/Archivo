<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_AuthController{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('home/home');
        $this->load->view('template/pie');
    }
}
