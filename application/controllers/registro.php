<?php
class Registro extends MY_AuthController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('registro/grilla');
        $this->load->view('registro/pie');
        $this->load->view('template/pie');
    }
    
    public function editar(){
        $datos['id'] = $this->input->post('id');
        $datos['origen'] = $this->input->post('origen');
        $datos['cierre'] = $this->input->post('cierre');
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('registro/editar', $datos);
        $this->load->view('template/pie');
    }
    
    public function saveOrden(){
        $this->load->model('Registro_model');
        $this->Registro_model->id = $this->input->post('id');
        $this->Registro_model->numeroSocio = $this->input->post('numeroSocio');
        $this->Registro_model->descripcionSocio = $this->input->post('descripcionSocio');
        $this->Registro_model->numeroProducto = $this->input->post('numeroProducto');
        $this->Registro_model->descripcionProducto = $this->input->post('descripcionProducto');
        $this->Registro_model->cantidad = $this->input->post('cantidad');
        $this->Registro_model->precio = $this->input->post('precio');
        $this->Registro_model->observaciones = $this->input->post('observaciones');
        $this->Registro_model->descripcion = $this->input->post('descripcion');
        $orden = $this->Registro_model->saveOrden();
        echo json_encode($orden);
    }
    
    public function getOrden(){
        $this->load->model('Registro_model');
        $this->Registro_model->id = $this->input->post('id');
        $orden = $this->Registro_model->getOrden();
        echo json_encode($orden);
    }
    
    public function delOrden(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Registro_model');
        $this->Registro_model->ordenes = $ordenes;
        $this->Registro_model->delOrden();
        echo json_encode(array('resultado'=>'Registro borrado exitosamente'));
    }
    
    public function comprobarEstadoCierre(){
        $cierre = $this->input->post('cierre');
        $this->load->model('Registro_model');
        $this->Registro_model->cierre = $cierre;
        $resultado = $this->Registro_model->comprobarEstadoCierre();
        echo json_encode($resultado);
    }
    
    
    public function grilla(){
        
        
        $usuario = $this->session->userdata('usuario');
        $usuario_id = $usuario['id'];
        
        $cierre_id = $this->input->post('cierre_id');
        
                
        $this->load->model('Registro_model');
        $this->Registro_model->usuario_id = $usuario_id;
        $this->Registro_model->cierre_id = $cierre_id;
        $resultado = $this->Registro_model->grilla();
        
        echo json_encode($resultado);

        
    }
    
    public function enviarOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Registro_model');
        $this->Registro_model->ordenes = $ordenes;
        $resultado = $this->Registro_model->enviarOrdenes();
        echo json_encode($resultado);
    }
    
    public function anularOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Registro_model');
        $this->Registro_model->ordenes = $ordenes;
        $resultado = $this->Registro_model->anularOrdenes();
        echo json_encode($resultado);        
    }
    
    public function cierreEditar(){
        $data['id'] = $this->input->post('id');
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('registro/cierreEditar', $data);
        $this->load->view('template/pie');
    }
    
    public function getCierre(){
        $cierre_id = $this->input->post('cierre_id');
        $this->load->model('Registro_model');
        $this->Registro_model->cierre_id = $cierre_id;
        $resultado = $this->Registro_model->getCierre();
        echo json_encode($resultado);
    }
    
    
    public function saveCierre(){
        $cierre_id = $this->input->post('cierre_id');
        $fechaHora = $this->input->post('fechahora');
        $plazos = $this->input->post('plazos');
        $plazosBorrar = $this->input->post('plazosBorrar');
        $instrumento = $this->input->post('instrumento');
        $pausarCierre = $this->input->post('pausarCierre');
        $this->load->model('Registro_model');
        $this->Registro_model->cierre_id = $cierre_id;
        $this->Registro_model->fechahora = $fechaHora;
        $this->Registro_model->plazos = $plazos;
        $this->Registro_model->plazosBorrar = $plazosBorrar;
        $this->Registro_model->instrumento = $instrumento;
        $this->Registro_model->pausarCierre = $pausarCierre;
        $cierre = $this->Registro_model->saveCierre();
        echo json_encode($cierre);
    }
    
    public function delCierre(){
        $cierre_id = $this->input->post('id');
        $this->load->model('Registro_model');
        $this->Registro_model->cierre_id = $cierre_id;
        $this->Registro_model->delCierre();
        echo json_encode(array('resultado'=>'Cierre borrado exitosamente'));
    }
    
    public function cierreGrilla(){
        
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
        $table = "(select * from cierre order by fechahora desc) as cierre";
        $fields = array('id','fechahora');
        $datos = $this->grilla_model->datosGrilla($table, $fields, $pagenum, $pagesize, 
                $filterscount, $filtervalue, $filtercondition, $filterdatafield, 
                $filteroperator, $sortdatafield, $sortorder);
        echo json_encode($datos);
    }

    public function cierre(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('registro/cierreGrilla');
        $this->load->view('template/pie');
    }
    
    public function getCierreActual(){
        $this->load->model('Registro_model');
        $cierreActual = $this->Registro_model->getCierreActual();
        echo json_encode($cierreActual);
    }
    
    
    public function control(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('registro/control');
        $this->load->view('template/pie');
    }
    
    public function controlGrilla(){
        
        $cierre_id = $this->input->post('cierre_id');
        $this->load->model('Registro_model');
        /*
        if ($cierre_id > 0){
            $this->Registro_model->actualizarPosicionMonetaria();
        }
         * 
         */
        $this->Registro_model->cierre_id = $cierre_id;
        $resultado = $this->Registro_model->controlGrilla();
        
        echo json_encode($resultado);
    }
    
    public function getOrdenes(){
        $cierre_id = $this->input->post('cierre_id');
        $numComitente = $this->input->post('numComitente');
        $this->load->model('Registro_model');
        $this->Registro_model->cierre_id = $cierre_id;
        $this->Registro_model->numComitente = $numComitente;
        $resultado = $this->Registro_model->getOrdenes();
        
        echo json_encode($resultado);
    }    
    public function getCierres(){
        $this->load->model('Registro_model');
        $cierres = $this->Registro_model->getCierres();
        echo json_encode($cierres);
    }
    
    public function grillaResumen(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Registro_model');
        $this->Registro_model->ordenes = $ordenes;
        $resultado = $this->Registro_model->grillaResumen();
        echo json_encode($resultado);
    }
    
    
    
    public function grabarExcel(){
        
        $archivo = $this->input->post('file');
        $cierre = $this->input->post('cierre');

        $this->load->model('Registro_model');
        $this->Registro_model->archivo = $archivo;
        $this->Registro_model->cierre = $cierre;
        
        $resultado = $this->Registro_model->grabarExcel();
        echo json_encode($resultado);
        
    }
    
    
    
    
    
    
    
    
    
    
}
