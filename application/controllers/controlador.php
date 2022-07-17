<?php
class Controlador extends MY_AuthController{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'controlador/grilla', 
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
            'controlador/editar', 
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
        $table = 'controlador';
        $fields = array('id', 'nombre');
        $datos = $this->grilla_model->datosGrilla($table, $fields, $pagenum, $pagesize, 
                $filterscount, $filtervalue, $filtercondition, $filterdatafield, 
                $filteroperator, $sortdatafield, $sortorder);
        echo json_encode($datos);
        
    }
    
    
    function saveControlador(){
        $this->load->model('Controlador_model');
        $this->Controlador_model->id = $this->input->post('id');
        $this->Controlador_model->nombre = $this->input->post('nombre');
        $id = $this->Controlador_model->saveControlador();
        $resultado = array('id'=>$id);
        echo json_encode($resultado);
    }
    
    function getControlador(){
        $this->load->model('Controlador_model');
        $this->Controlador_model->id = $this->input->post('id');
        $controlador = $this->Controlador_model->getControlador();
        echo json_encode($controlador);
    }
    
    function getAllControladores(){
        $this->load->model('Controlador_model');
        $controladores = $this->Controlador_model->getAllControladores();
        echo json_encode($controladores);
    }

    function delControlador(){
        $this->load->model('Controlador_model');
        $this->Controlador_model->id = $this->input->post('id');
        $this->Controlador_model->delControlador();
        echo json_encode(array('resultado'=>'Controlador borrado exitosamente'));
    }
    
}