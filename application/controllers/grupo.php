<?php
class Grupo extends MY_AuthController{
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'grupo/grilla', 
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
            'grupo/editar', 
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
        $table = 'grupo';
        $fields = array('id', 'nombre');
        $datos = $this->grilla_model->datosGrilla($table, $fields, $pagenum, $pagesize, 
                $filterscount, $filtervalue, $filtercondition, $filterdatafield, 
                $filteroperator, $sortdatafield, $sortorder);
        echo json_encode($datos);
        
    }
    
    
    function saveGrupo(){
        $this->load->model('Grupo_model');
        $this->Grupo_model->id = $this->input->post('id');
        $this->Grupo_model->nombre = $this->input->post('nombre');
        $id = $this->Grupo_model->saveGrupo();
        $resultado = array('id'=>$id);
        echo json_encode($resultado);
    }
    
    function getGrupo(){
        $this->load->model('Grupo_model');
        $this->Grupo_model->id = $this->input->post('id');
        $grupo = $this->Grupo_model->getGrupo();
        echo json_encode($grupo);
    }
    
    function getGrupos(){
        $this->load->model('Grupo_model');
        $grupos = $this->Grupo_model->getGrupos();
        echo json_encode($grupos);
    }

    function delGrupo(){
        $this->load->model('Grupo_model');
        $this->Grupo_model->id = $this->input->post('id');
        $this->Grupo_model->delGrupo();
        echo json_encode(array('resultado'=>'Grupo borrado exitosamente'));
    }
}