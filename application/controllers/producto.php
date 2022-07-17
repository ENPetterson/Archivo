<?php
class Producto extends MY_AuthController {
    public function __construct() {
        parent::__construct();
    }
    
    public function index(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('producto/grilla');
        $this->load->view('template/pie');
    }
    
    public function editar(){
        $datos['id'] = $this->input->post('id');
        $datos['origen'] = $this->input->post('origen');
        $datos['cierre'] = $this->input->post('cierre');
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('producto/editar', $datos);
        $this->load->view('template/pie');
    }
    
    public function saveOrden(){
        $this->load->model('Producto_model');
        $this->Producto_model->id = $this->input->post('id');
        $this->Producto_model->descripcion = $this->input->post('descripcion');
        $this->Producto_model->tipoProducto = $this->input->post('tipoProducto');
        $this->Producto_model->anulado = $this->input->post('anulado');
        $this->Producto_model->precio = $this->input->post('precio');
        $orden = $this->Producto_model->saveOrden();
        echo json_encode($orden);
    }
    

    //Si no usás este getOrden borralo
    /*
    public function getOrden(){
        $this->load->model('Producto_model');
        $this->Producto_model->id = $this->input->post('id');
        $orden = $this->Producto_model->getOrden();
        echo json_encode($orden);
    }
    */

    public function getProductoId(){
        $this->load->model('Producto_model');
        $this->Producto_model->id = $this->input->post('id');
        $producto = $this->Producto_model->getProductoId();
        echo json_encode($producto);
    }

    public function getProducto(){
        $this->load->model('Producto_model');
        $this->Producto_model->id = $this->input->post('id');
        $producto = $this->Producto_model->getProducto();
        echo json_encode($producto);
    }

    function getTipoProducto(){
        $this->load->model('Producto_model');
        $this->Producto_model->id = $this->input->post('id');
        $tipoProducto = $this->Producto_model->getTipoProducto();
        echo json_encode($tipoProducto);
    }

    function getTipoProductos(){
        $this->load->model('Producto_model');
        $tipoProducto = $this->Producto_model->getTipoProductos();
        echo json_encode($tipoProducto);
    }

    public function delOrden(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Producto_model');
        $this->Producto_model->ordenes = $ordenes;
        $this->Producto_model->delOrden();
        echo json_encode(array('resultado'=>'Producto borrado exitosamente'));
    }
    
    
    public function grilla(){
        
        
        $usuario = $this->session->userdata('usuario');
        $usuario_id = $usuario['id'];
        
                
        $this->load->model('Producto_model');
        $this->Producto_model->usuario_id = $usuario_id;
        $resultado = $this->Producto_model->grilla();
        
        echo json_encode($resultado);

        
    }
    
    public function enviarOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Producto_model');
        $this->Producto_model->ordenes = $ordenes;
        $resultado = $this->Producto_model->enviarOrdenes();
        echo json_encode($resultado);
    }
    
    public function anularOrdenes(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Producto_model');
        $this->Producto_model->ordenes = $ordenes;
        $resultado = $this->Producto_model->anularOrdenes();
        echo json_encode($resultado);        
    }
    
    
    public function control(){
        $this->load->view('template/encabezado');
        $this->load->view('template/menu');
        $this->load->view('producto/control');
        $this->load->view('template/pie');
    }
    
    public function controlGrilla(){
        
        $cierre_id = $this->input->post('cierre_id');
        $this->load->model('Producto_model');
        /*
        if ($cierre_id > 0){
            $this->Registro_model->actualizarPosicionMonetaria();
        }
         * 
         */
        $this->Producto_model->cierre_id = $cierre_id;
        $resultado = $this->Producto_model->controlGrilla();
        
        echo json_encode($resultado);
    }
    
    public function getOrdenes(){
        $cierre_id = $this->input->post('cierre_id');
        $numComitente = $this->input->post('numComitente');
        $this->load->model('Producto_model');
        $this->Producto_model->cierre_id = $cierre_id;
        $this->Producto_model->numComitente = $numComitente;
        $resultado = $this->Producto_model->getOrdenes();
        
        echo json_encode($resultado);
    }    
    
    public function grillaResumen(){
        $ordenes = $this->input->post('ordenes');
        $this->load->model('Producto_model');
        $this->Producto_model->ordenes = $ordenes;
        $resultado = $this->Producto_model->grillaResumen();
        echo json_encode($resultado);
    }
    
    
    
    public function grabarExcel(){
        
        $archivo = $this->input->post('file');

        $this->load->model('Producto_model');
        $this->Producto_model->archivo = $archivo;
        
        $resultado = $this->Producto_model->grabarExcel();
        echo json_encode($resultado);
        
    }
    
    
    
    
    
    
    
    
    
    
}
