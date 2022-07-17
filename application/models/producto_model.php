<?php

require_once APPPATH."/third_party/PHPExcel.php";

class Producto_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $descripcion;
    public $tipoProducto;
    public $anulado;
    public $precio;
    public $fechaCarga;
    public $fhmodificacion;
    public $oficial;
    public $cuit;
    public $ordenes;
    public $producto;
    
    public $fechahora;

    
    public function saveOrden(){
        $usuarioParam = $this->session->userdata('usuario');
        $usuario = R::load('usuario', $usuarioParam['id']);

        $orden = R::load('producto', $this->id);
        

        $orden->usuario = $usuario;

        $orden->descripcion = $this->descripcion;
        $orden->tipoProducto = $this->tipoProducto;
        $orden->anulado = $this->anulado;
        $orden->precio = $this->precio;
        $orden->fechaCarga =  R::isoDateTime();
        $orden->fhmodificacion =  R::isoDateTime();
        $this->id = R::store($orden);
        
        return $orden->export();
    }
    
    
    public function getProductoId(){
        $sql = "select *
         from   producto
         WHERE id = ?"; 
        
        $resultado = R::getAll($sql, array($this->id));
        
        return $resultado;
    }
    

    public function getProducto(){
        $producto = R::load('producto', $this->id);        
        return $producto->export();
    }


    public function getTipoProducto(){
        $tipoProducto = R::load('tipoProducto', $this->id);
        return $tipoProducto->export();
    }


    public function getTipoProductos(){
        $tipoProducto = R::getAll('select * from tipoProducto');
        return $tipoProducto;
    }

    public function getTipoProductoPorNombre(){
        $sql = "select id, descripcion from tipoProducto WHERE descripcion = ?";
        $tipoProducto = R::getRow($sql, array($this->tipoProducto)); 

        return $tipoProducto;
    }


    
    public function delOrden(){
        foreach ($this->ordenes as $id){
            $producto = R::load('producto', $id);
            R::trash($producto);
        }
    }
    



    
    public function grilla(){
        $sql = "select l.id,
                l.descripcion,
                l.tipoProducto,
                CASE WHEN anulado = 'true' THEN 'Si' ELSE 'No' END as anulado,
                l.precio,                
                l.fechaCarga,
                l.fhmodificacion
         from   producto l        
         order by l.id"; 
        
        $resultado = R::getAll($sql);
        
        return $resultado;

    }
    
    public function enviarOrdenes(){        
        $estado = R::load('estadoorden', 2);
        $resultado = array('exito'=>1, 'resultado'=>'Productos enviados correctamente');
        $ahora = new DateTime();
        foreach ($this->ordenes as $id) {
            $orden = R::load('producto', $id);
        }
        return $resultado;
    }
    
    public function anularOrdenes(){        
        $estado = R::load('estadoorden', 4);
        $resultado = array('exito'=>1, 'resultado'=>'Productos anulados correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('productos', $id);
            $orden->estado = $estado;
            R::store($orden);
        }
        return $resultado;
    }
    
 
    public function grillaResumen(){        
        $ordenes_in = implode(',', $this->ordenes);
        $sql = "SELECT  plazo, 
                        moneda, 
                        count(*)      cantidadOrdenes, 
                        sum(cantidad) sumaCantidad
                        FROM    producto
                WHERE   id in ({$ordenes_in})
                GROUP BY plazo, moneda";
        $resultado = R::getAll($sql);
        return $resultado;
    }
    
    public function controlGrilla(){
        $sql = "select l.id,
                l.descripcion,
                l.tipoProducto,
                l.anulado,       
                l.precio,
                concat(u.apellido, ' ', u.nombre) as usuario,
                eo.estado,
                l.estado_id,
                l.fechaCarga,
                l.fhmodificacion
         from   producto l
         join   estadoorden eo 
         on     l.estado_id = eo.id
         join   usuario u
         on     l.usuario_id = u.id
         where  l.estado_id <> 1
         order by l.fhmodificacion desc"; 
        
        $resultado = R::getAll($sql);
        
        return $resultado;
    }
    
    public function getOrdenes(){
        
        $sql = "(select l.id,
                l.moneda,
                l.plazo,
                l.cantidad,
                l.precio
         from   producto l
         join   estadoorden eo 
         on     l.estado_id = eo.id
         join   usuario u
         on     l.usuario_id = u.id
         where  l.estado_id <> 4
         and    l.numComitente = ?
         order by l.fhmodificacion desc)"; 
        
        $resultado = R::getAll($sql, array($this->numComitente));
        
        return $resultado;
    }
    
    /*
    public function generarTxt(){        
        $estado = R::load('estadoorden', 3);
        $resultado = array('exito'=>1, 'resultado'=>'Ordenes Enviadas Correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('registro', $id);
            $orden->estado = $estado;
            $orden->envio = 'M';
            $orden->fhenvio = R::isoDateTime();
            R::store($orden);
        }
        return $resultado;
    }
*/

    
    
    
    
    public function grabarExcel(){
                  
        $usuarioParam = $this->session->userdata('usuario');
 
        $orden = R::load('producto', $this->id);
        $usuario = R::load('usuario', $usuarioParam['id']);
        $estadoorden = R::load('estadoorden', 1);        
               
        $this->load->helper('file');
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/tmp/';
       
        try {
            $inputFileName = $uploadDir . $this->archivo;
            $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFileName);
        } catch(Exception $e) {
            die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
        }

        $sheetname = 'Hoja1';
        
        $sheet = $objPHPExcel->getSheetByName($sheetname);
        
        if($sheet){
            for ($row = 1; $row < 2; $row++){
                for($column = 0; $column < 11; $column++){
                    
                    $nombreHoja = str_replace(
                                            array('á','é','í','ó','ú'),
                                            array('a','e','i','o','u'),
                                            $sheet->getCellByColumnAndRow($column,$row)->getFormattedValue()
                                        );
                    
                    $nombreHoja = strtolower($nombreHoja);                    
                    $nombreHojas[] = $nombreHoja;                                    
                }
            }
                        
            if($nombreHojas[0] == 'descripcion' && $nombreHojas[1] == 'tipoProducto'){
                $aprobado = 1;
            }
        }


        if($aprobado){
            $highestRow = $sheet->getHighestDataRow();
            
            $valido = true;
            $error = '';
            
            R::freeze(true);
            R::begin();

            //R::debug();
            
            for ($row = 2; $row <= $highestRow; $row++){

                $descripcion = $sheet->getCellByColumnAndRow(0,$row)->getCalculatedValue();
             
                    
                $tipoProducto = $sheet->getCellByColumnAndRow(1,$row)->getCalculatedValue();     
                /*if($numeroProducto == 0){
                $numeroProducto = $sheet->getCellByColumnAndRow(1,$row)->getCalculatedValue();
                }
                $numeroSocio = (int)$cantidad;*/



                    /*if(!is_int($cantidad)){
                        $error.="Cantidad inválida en fila {$row} <br>";
                        $valido = false;
                    }*/


                    /*if(!is_int($precio)){
                        $error.="Precio inválido en fila {$row} <br>";
                        $valido = false;
                    }*/

                $cantidad = $sheet->getCellByColumnAndRow(2,$row)->getFormattedValue();

                $anulado = $sheet->getCellByColumnAndRow(3,$row)->getFormattedValue();

                $fechaCarga = $sheet->getCellByColumnAndRow(4,$row)->getFormattedValue();

                $fhmodificacion = $sheet->getCellByColumnAndRow(5,$row)->getFormattedValue();



                $orden = R::dispense('producto');
                $orden->descripcion = $descripcion;
                $orden->tipoProducto = $tipoProducto;
                $orden->anulado = $anulado;
                $orden->precio = $precio;
                $orden->estado = $estadoorden;
                $orden->usuario = $usuario;
                $orden->fechaCarga = $fechaCarga;
                $orden->fhmodificacion = R::isoDateTime();

                if ($valido){
                        $this->id = R::store($orden);    
                }
                
                
                
            }           
                        
            if ($valido){
                R::commit();
                $resultado = array('resultado'=>'OK');
            } else {
                R::rollback();
                $resultado = array('resultado'=>'Error', 'mensaje'=>$error);
            }
            
            R::freeze(false); 

            return $resultado;
            
        } else {
            
            $error = 'Títulos inválidos.';
            $resultado = array('resultado'=>'Error', 'mensaje'=>$error);
            return $resultado;
        }
    }        
}

class Model_Producto extends RedBean_SimpleModel {
    private $prev;
    
    function open(){
        $this->prev = $this->bean->export();
    }
    
    function after_update(){
        if (json_encode($this->prev) != json_encode($this->bean->export())){
            $CI =& get_instance();
            $usuarioParam = $CI->session->userdata('usuario');
            $usuario = R::load('usuario', $usuarioParam['id']);
            $auditoria = R::dispense('auditoria');
            $auditoria->usuario = $usuario;
            $auditoria->table = 'producto';
            $auditoria->tableId = $this->bean->id;
            $auditoria->anterior = json_encode($this->prev);
            $auditoria->actual = json_encode($this->bean->export());
            R::store($auditoria);
        }
    }

    function after_delete(){
        $CI =& get_instance();
        $usuarioParam = $CI->session->userdata('usuario');
        $usuario = R::load('usuario', $usuarioParam['id']);
        $auditoria = R::dispense('auditoria');
        $auditoria->usuario = $usuario;
        $auditoria->table = 'producto';
        $auditoria->tableId = $this->prev['id'];
        $auditoria->anterior = json_encode($this->prev);
        $auditoria->actual = json_encode(array('operacion'=>'Producto Borrado'));
        R::store($auditoria);        
    } 
        
    
}

class Model_Cierre extends RedBean_SimpleModel {
    private $prev;
    
    function open(){
        $this->prev = $this->bean->export();
    }
    
    function after_update(){
        if (json_encode($this->prev) != json_encode($this->bean->export())){
            $CI =& get_instance();
            $usuarioParam = $CI->session->userdata('usuario');
            $usuario = R::load('usuario', $usuarioParam['id']);
            $auditoria = R::dispense('auditoria');
            $auditoria->usuario = $usuario;
            $auditoria->table = 'cierre';
            $auditoria->tableId = $this->bean->id;
            $auditoria->anterior = json_encode($this->prev);
            $auditoria->actual = json_encode($this->bean->export());
            R::store($auditoria);
        }
    }
    
    function after_delete(){
        $CI =& get_instance();
        $usuarioParam = $CI->session->userdata('usuario');
        $usuario = R::load('usuario', $usuarioParam['id']);
        $auditoria = R::dispense('auditoria');
        $auditoria->usuario = $usuario;
        $auditoria->table = 'cierre';
        $auditoria->tableId = $this->prev['id'];
        $auditoria->anterior = json_encode($this->prev);
        $auditoria->actual = json_encode(array('operacion'=>'Producto Borrado'));
        R::store($auditoria);        
    }    
}
