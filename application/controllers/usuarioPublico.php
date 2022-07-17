<?php
class UsuarioPublico extends MY_AuthController{
    public function __construct() {
        parent::__construct();
    }

        public function index(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'usuario/grilla', 
            'template/pie'
            );
        foreach ($views as $value) {
            $this->load->view($value);
        }
    }
    
    public function editar(){
        $datos['id'] = $this->input->post('id');
        $views = array(
            'template/encabezado', 
            'template/menu',
            'usuario/editar', 
            'template/pie'
            );
        foreach ($views as $value) {
            $this->load->view($value, $datos);
        }
    }
    
    public function grilla(){
        $filtervalue = array();
        $filtercondition = array();
        $filterdatafield = array();
        $filteroperator = array();
        $pagenum = $this->input->get('pagenum');
        $pagesize = $this->input->get('pagesize');
        if ($pagenum == ''){
            $pagenum = 0;
        }
        if ($pagesize == ''){
            $pagesize = 20;
        }
        if ($this->input->get('filterscount')){
            $filterscount = $this->input->get('filterscount');
            if ($filterscount > 0){
                for ($i=0;$i<$filterscount;$i++){
                    $filtervalue[] = $_GET['filtervalue'.$i];
                    $filtercondition[] = $_GET['filtercondition'.$i];
                    $filterdatafield[] = $_GET['filterdatafield'.$i];
                    $filteroperator[] = $_GET['filteroperator'.$i];
                }
            }
        } else {
            $filterscount = 0;
        }
        $sortdatafield = $this->input->get('sortdatafield');
        $sortorder = $this->input->get('sortorder');
        $this->load->model('grilla_model');
        $table = 'usuario';
        $fields = array('id', 'nombreUsuario', 'nombre', 'apellido');
        $datos = $this->grilla_model->datosGrilla($table, $fields, $pagenum, $pagesize, 
                $filterscount, $filtervalue, $filtercondition, $filterdatafield, 
                $filteroperator, $sortdatafield, $sortorder);
        echo json_encode($datos);
        
    }
    
    
    function saveUsuario(){
        $this->load->model('Usuario_model');
        $this->Usuario_model->id = $this->input->post('id');
        $this->Usuario_model->nombreUsuario = $this->input->post('nombreUsuario');
        $this->Usuario_model->dominio = $this->input->post('dominio');
        $this->Usuario_model->nombre = $this->input->post('nombre');
        $this->Usuario_model->apellido = $this->input->post('apellido');
        $this->Usuario_model->email = $this->input->post('email');
        $this->Usuario_model->grupos = $this->input->post('grupos');
        $id = $this->Usuario_model->saveUsuario();
        $resultado = array('id'=>$id);
        echo json_encode($resultado);
    }
    
    function delUsuario(){
        $this->load->model('Usuario_model');
        $this->Usuario_model->id = $this->input->post('id');
        $this->Usuario_model->delUsuario();
        echo json_encode(array('resultado'=>'Usuario borrado exitosamente'));
    }    
    
    function getUsuarios(){
        $this->load->model('Usuario_model');
        $usuarios = $this->Usuario_model->getUsuarios();
        echo json_encode($usuarios);
    }
    
}