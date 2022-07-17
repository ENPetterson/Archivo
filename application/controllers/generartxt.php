<?php
class Generartxt extends MY_AuthController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('generartxt/grilla');
        $this->load->view('generartxt/pie');
        $this->load->view('template/pie');
    }
    
    public function editar(){
        $datos['id'] = $this->input->post('id');
        $datos['origen'] = $this->input->post('origen');
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('generartxt/editar', $datos);
        $this->load->view('template/pie');
    }
    
    public function saveOrden(){
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->id = $this->input->post('id');
        $this->Generartxt_model->tramo = $this->input->post('tramo');
        $this->Generartxt_model->numComitente = $this->input->post('numComitente');
        $this->Generartxt_model->moneda = $this->input->post('moneda');
        $this->Generartxt_model->cable = $this->input->post('cable');
        //$this->Generartxt_model->plazo = $this->input->post('plazo');
        $this->Generartxt_model->especie = $this->input->post('especie');
        $this->Generartxt_model->comision = $this->input->post('comision');
        $this->Generartxt_model->cantidad = $this->input->post('cantidad');
        $this->Generartxt_model->precio = $this->input->post('precio');
        $this->Generartxt_model->comitente = $this->input->post('comitente');
        $this->Generartxt_model->tipoInversor = $this->input->post('tipoInversor');
        $this->Generartxt_model->tipoPersona = $this->input->post('tipoPersona');
        $this->Generartxt_model->oficial = $this->input->post('oficial');
        $this->Generartxt_model->cuit = $this->input->post('cuit');
        $orden = $this->Generartxt_model->saveOrden();
        echo json_encode($orden);
    }
    
    public function getOrden(){
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->id = $this->input->post('id');
        $orden = $this->Generartxt_model->getOrden();
        echo json_encode($orden);
    }
    
    public function delOrden(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $this->Generartxt_model->delOrden();
        echo json_encode(array('resultado'=>'Ordenes borradas exitosamente'));
    }
    
    public function grilla(){
        
        
        $usuario = $this->session->userdata('usuario');
        $usuario_id = $usuario['id'];
        
                
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->usuario_id = $usuario_id;
        $resultado = $this->Generartxt_model->grilla();
        
        echo json_encode($resultado);

        
    }
    
    public function enviarOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->enviarOrdenes();
        echo json_encode($resultado);
    }
    
    public function retirarOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->retirarOrdenes();
        echo json_encode($resultado);
    }
    
    
    public function anularOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->anularOrdenes();
        echo json_encode($resultado);        
    }

    
    public function getMonedas(){
        $this->load->model('Generartxt_model');
        $resultado = $this->Generartxt_model->getMonedas();
        echo json_encode($resultado);
    }
    
    
//    public function getCierreActual($cierreId){
//        
//        print_r($cierreActual); die;
//        
//        $this->load->model('Licitacion_model');
//        $cierreActual = $this->Licitacion_model->getCierreActual($cierreId);
//        echo json_encode($cierreActual);
//    }
       

    public function procesar(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('generartxt/procesar');
        $this->load->view('template/pie');
    }
    
    public function procesarGrilla(){
        
        $this->load->model('Generartxt_model');
        $resultado = $this->Generartxt_model->procesarGrilla();
        
        echo json_encode($resultado);
    }
    
    
    public function grillaResumen(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->grillaResumen();
        echo json_encode($resultado);
    }
    
    public function previewSantander(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->previewSantander();
        echo json_encode($resultado);
    }
    
    public function enviarSantander(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->enviarSantander();
        echo json_encode($resultado);
    }
    
    public function previewMercado(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->previewMercado();
        echo json_encode($resultado);
    }

    public function enviarMercado(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->enviarMercado();
        echo json_encode($resultado);
    }

    public function rutaEscritorio(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->ordenes = $ordenes;
        $resultado = $this->Generartxt_model->rutaEscritorio();
        echo json_encode($resultado);
    }
    
    public function grabarExcel(){
        

        $archivo = $this->input->post('file');
        
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->archivo = $archivo;

        $resultado = $this->Generartxt_model->grabarExcel();
        echo json_encode($resultado);
        
    }


    public function getDescargarAchivo(){
        $logName = $this->input->post('archivo');
        $this->load->helper('download');
        $contenido = file_get_contents("C:\\xampp\\htdocs\\ordenes\\application\\downloads\\" . $logName);
        force_download($logName, $contenido);
    }

    
    public function generarArchivo(){
        

        $archivo = $this->input->post('file');
        
        $this->load->model('Generartxt_model');
        $this->Generartxt_model->archivo = $archivo;

        $resultado = $this->Generartxt_model->generarArchivo();
        echo json_encode($resultado);
        
    }
}

