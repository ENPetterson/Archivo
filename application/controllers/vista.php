<?php
class Vista extends MY_AuthController{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'vista/grilla', 
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
            'vista/editar', 
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
        $table = 'vista';
        $fields = array('id', 'nombre');
        $datos = $this->grilla_model->datosGrilla($table, $fields, $pagenum, $pagesize, 
                $filterscount, $filtervalue, $filtercondition, $filterdatafield, 
                $filteroperator, $sortdatafield, $sortorder);
        echo json_encode($datos);
        
    }
    
    
    function saveVista(){
        $this->load->model('Vista_model');
        $this->Vista_model->id = $this->input->post('id');
        $this->Vista_model->nombre = $this->input->post('nombre');
        $id = $this->Vista_model->saveVista();
        $resultado = array('id'=>$id);
        echo json_encode($resultado);
    }
    
    function getVista(){
        $this->load->model('Vista_model');
        $this->Vista_model->id = $this->input->post('id');
        $vista = $this->Vista_model->getVista();
        echo json_encode($vista);
    }
    
    function getVistas(){
        $this->load->model('Vista_model');
        $vistas = $this->Vista_model->getVistas();
        echo json_encode($vistas);
    }

    function delVista(){
        $this->load->model('Vista_model');
        $this->Vista_model->id = $this->input->post('id');
        $this->Vista_model->delVista();
        echo json_encode(array('resultado'=>'Vista borrada exitosamente'));
    }
    
    
}