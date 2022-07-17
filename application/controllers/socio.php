<?php
class Socio extends MY_AuthController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('socio/grilla');
        $this->load->view('template/pie');
    }
    
    public function editar(){
        $datos['id'] = $this->input->post('id');
        $datos['origen'] = $this->input->post('origen');
        $datos['cierre'] = $this->input->post('cierre');
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('socio/editar', $datos);
        $this->load->view('template/pie');
    }
    
    public function saveOrden(){
        $this->load->model('Socio_model');
        $this->Socio_model->id = $this->input->post('id');
        $this->Socio_model->descripcion = $this->input->post('descripcion');
        $this->Socio_model->anulado = $this->input->post('anulado');
        $this->Socio_model->tipoSocio = $this->input->post('tipoSocio');
        $orden = $this->Socio_model->saveOrden();
        echo json_encode($orden);
    }
    
    public function getSocioId(){
        $this->load->model('Socio_model');
        $this->Socio_model->id = $this->input->post('id');
        $socio = $this->Socio_model->getSocioId();
        echo json_encode($socio);
    }

    public function getSocio(){
        $this->load->model('Socio_model');
        $this->Socio_model->id = $this->input->post('id');
        $socio = $this->Socio_model->getSocio();
        echo json_encode($socio);
    }

    public function delOrden(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Socio_model');
        $this->Socio_model->ordenes = $ordenes;
        $this->Socio_model->delOrden();
        echo json_encode(array('resultado'=>'Socio borrado exitosamente'));
    }
    
    
    
    public function grilla(){
        
        
        $usuario = $this->session->userdata('usuario');
        $usuario_id = $usuario['id'];
        
        //$cierre_id = $this->input->post('cierre_id');
        
                
        $this->load->model('Socio_model');
        $this->Socio_model->usuario_id = $usuario_id;
        //$this->Socio_model->cierre_id = $cierre_id;
        $resultado = $this->Socio_model->grilla();
        
        echo json_encode($resultado);

        
    }
    
    public function enviarOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Socio_model');
        $this->Socio_model->ordenes = $ordenes;
        $resultado = $this->Socio_model->enviarOrdenes();
        echo json_encode($resultado);
    }
    
    public function anularOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Socio_model');
        $this->Socio_model->ordenes = $ordenes;
        $resultado = $this->Socio_model->anularOrdenes();
        echo json_encode($resultado);        
    }
    

 
    
    public function control(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('socio/control');
        $this->load->view('template/pie');
    }
    
    public function controlGrilla(){
        
        $cierre_id = $this->input->post('cierre_id');
        $this->load->model('Socio_model');
        /*
        if ($cierre_id > 0){
            $this->Registro_model->actualizarPosicionMonetaria();
        }
         * 
         */
        $this->Socio_model->cierre_id = $cierre_id;
        $resultado = $this->Socio_model->controlGrilla();
        
        echo json_encode($resultado);
    }
    
    public function getOrdenes(){
        $cierre_id = $this->input->post('cierre_id');
        $numComitente = $this->input->post('numComitente');
        $this->load->model('Socio_model');
        $this->Socio_model->cierre_id = $cierre_id;
        $this->Socio_model->numComitente = $numComitente;
        $resultado = $this->Socio_model->getOrdenes();
        
        echo json_encode($resultado);
    }    

    
    public function grillaResumen(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Socio_model');
        $this->Socio_model->ordenes = $ordenes;
        $resultado = $this->Socio_model->grillaResumen();
        echo json_encode($resultado);
    }
    
    
    
    public function grabarExcel(){
        
        $archivo = $this->input->post('file');

        $this->load->model('Socio_model');
        $this->Socio_model->archivo = $archivo;
        
        $resultado = $this->Socio_model->grabarExcel();
        echo json_encode($resultado);
        
    }
    
    
    
    
    
    
    
    
    
    
}
