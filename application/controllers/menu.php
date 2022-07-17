<?php
class Menu extends MY_AuthController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'menu/grilla', 
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
            'menu/editar', 
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
        $table = 'v_menu';
        $fields = array('id', 'padre_id', 'padre', 'nombre', 'accion');
        $datos = $this->grilla_model->datosGrilla($table, $fields, $pagenum, $pagesize, 
                $filterscount, $filtervalue, $filtercondition, $filterdatafield, 
                $filteroperator, $sortdatafield, $sortorder);
        echo json_encode($datos);
        
    }
    
    function getMenues(){
        $this->load->model('Menu_model');        
        $menu = $this->Menu_model->getMenues();
        echo json_encode($menu);
    }
    
    function getAllMenues(){
        $this->load->model('Menu_model');
        $menues = $this->Menu_model->getAllMenues();
        echo json_encode($menues);
    }
    
    
    function getMenu(){
        $this->load->model('Menu_model');        
        $menu_id = $this->input->post('menu_id');
        $this->Menu_model->id = $menu_id;
        $menu = $this->Menu_model->getMenu();
        echo json_encode($menu);
    }
    
    function getNombre(){
        $usuario = $this->session->userdata('usuario');
        $nombre = $usuario['nombre'] . ' ' . $usuario['apellido'];
        echo json_encode(array('nombre'=>$nombre));
    }
    
    function saveMenu(){
        $this->load->model('Menu_model');
        $this->Menu_model->id = $this->input->post('id');
        $this->Menu_model->padre_id = $this->input->post('padre_id');
        $this->Menu_model->nombre = $this->input->post('nombre');
        $this->Menu_model->accion = $this->input->post('accion');
        $id = $this->Menu_model->saveMenu();
        $resultado = array('id'=>$id);
        echo json_encode($resultado);
    }
    
    function delMenu(){
        $this->load->model('Menu_model');
        $this->Menu_model->id = $this->input->post('id');
        $this->Menu_model->delMenu();
        echo json_encode(array('resultado'=>'Menu borrado exitosamente'));
    }    
    
} 