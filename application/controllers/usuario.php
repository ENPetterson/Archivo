<?php
class Usuario extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    
    public function validarUsuario(){
        
        $nombreUsuario = $this->input->post('nombreUsuario');
        $clave = $this->input->post('clave');
        $dominio = $this->input->post('dominio');
        $this->load->model('Usuario_model');
        $resultado = $this->Usuario_model->validarUsuario($nombreUsuario, $clave, $dominio);
        echo json_encode($resultado);
    }
    
    function getUsuario(){
        $this->load->model('Usuario_model');
        $this->Usuario_model->id = $this->input->post('id');
        $usuario = $this->Usuario_model->getUsuario();
        
        echo json_encode($usuario);
    }

    public function closeSession(){
        $this->session->sess_destroy();
        redirect('/');
    }    

}