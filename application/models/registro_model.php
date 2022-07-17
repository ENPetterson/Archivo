<?php

require_once APPPATH."/third_party/PHPExcel.php";

class Registro_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $numeroSocio;
    public $descripcionSocio;
    public $numeroProducto;
    public $descripcionProducto;
    public $cantidad;
    public $precio;
    public $fechaCarga;
    public $fhmodificacion;
    public $observaciones;
    public $descripcion;
    public $oficial;
    public $cuit;
    public $ordenes;
    
    public $usuario_id;
    
    public $cierre_id;
    public $fechahora;

    
    public function saveOrden(){
        $usuarioParam = $this->session->userdata('usuario');
        $usuario = R::load('usuario', $usuarioParam['id']);
        $orden = R::load('registro', $this->id);
        //$ordenAnterior = $orden;
        if ($orden->id == 0 ){

            $estado = R::load('estadoorden', 1);

            $cierreActual = $this->getCierreActual();
            if (isset($cierreActual['cerrado'])){
                return array('id'=>0);
            } else {
                $cierre = R::load('cierre', $cierreActual['id']);
                $orden->cierre = $cierre;
            }
            $orden->usuario = $usuario;
            $orden->estado = $estado;
            $orden->fechaCarga =  R::isoDateTime();
        }
        $orden->numeroSocio = $this->numeroSocio;
        $orden->descripcionSocio = $this->descripcionSocio;
        $orden->numeroProducto = $this->numeroProducto;
        $orden->descripcionProducto = $this->descripcionProducto;
        $orden->cantidad = $this->cantidad;
        $orden->precio = $this->precio;
        $orden->fhmodificacion =  R::isoDateTime();
        $orden->observaciones = $this->observaciones;
        $orden->descripcion = $this->descripcion;
        $this->id = R::store($orden);
        
        return $orden->export();
    }
    
    public function getOrden(){
        $orden = R::load('registro', $this->id);        
        return $orden->export();
    }
    
    public function delOrden(){
        foreach ($this->ordenes as $id){
            $registro = R::load('registro', $id);
            R::trash($registro);
        }
    }
    
    
    public function grilla(){
        $sql = "select l.id,
                l.numeroSocio,
                l.descripcionSocio,
                l.numeroProducto,
                l.descripcionProducto,
                l.cantidad,
                l.precio,
                eo.estado,
                l.estado_id,
                l.fechaCarga,
                l.fhmodificacion,
                l.observaciones,
                l.descripcion,
                c.fechahora as cierre
         from   registro l
         join   estadoorden eo 
         on     l.estado_id = eo.id
         join   cierre c
         on     l.cierre_id = c.id
         where  l.usuario_id = ?
         and    l.cierre_id = ?
         order by l.id"; 
        
        $resultado = R::getAll($sql, array($this->usuario_id, $this->cierre_id));
        
        return $resultado;

    }
    
    public function enviarOrdenes(){        
        $estado = R::load('estadoorden', 2);
        $resultado = array('exito'=>1, 'resultado'=>'Registros enviados correctamente');
        $ahora = new DateTime();
        foreach ($this->ordenes as $id) {
            $orden = R::load('registro', $id);
            $fechaCierre = new DateTime($orden->cierre->fechahora);
            if ($fechaCierre < $ahora){
                $resultado = array('exito'=>0, 'resultado'=>'Algunos registros no se pudieron enviar porque ya estaban cerrados');
            } else {
                $orden->estado = $estado;
                R::store($orden);
            }
        }
        return $resultado;
    }
    
    public function anularOrdenes(){        
        $estado = R::load('estadoorden', 4);
        $resultado = array('exito'=>1, 'resultado'=>'Registros anulados correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('registro', $id);
            $orden->estado = $estado;
            R::store($orden);
        }
        return $resultado;
    }
    
    
    public function getCierre(){
        
        $cierreBean = R::load('cierre', $this->cierre_id);
        $cierre = $cierreBean->export();
        //$sql = "select * from plazo where cierre_id = ? order by moneda, plazo";
        //$plazos = R::getAll($sql, array($cierreBean->id));
        //$cierre['plazos'] = $plazos;
        return $cierre;
    }
    
    public function saveCierre(){
                
        $cierre = R::load('cierre', $this->cierre_id);
        $cierre->fechahora = $this->fechahora;
        $cierre->pausarCierre = $this->pausarCierre;        
        
        R::store($cierre);
        
        return $cierre->export();
    }
    
   
    
    public function delCierre(){
        $cierre = R::load('cierre', $this->cierre_id);
        R::trash($cierre);
    }
    
    public function getCierreActual(){
        $cierreActual = R::findOne('cierre', 'fechahora > NOW() order by fechahora' );
        if (is_null($cierreActual)){
            return array('cerrado'=>true);
        } else {
            $this->cierre_id = $cierreActual->id;
            $cierre = $this->getCierre();
            return $cierre;
        }
    }
    
    
    public function comprobarEstadoCierre(){
        
        $sql = "SELECT pausarCierre FROM cierre where id = ?";
        $cierre = R::getRow($sql, array($this->cierre));
        $result = $cierre['pausarCierre'];
                
        return $result;
    }
    
    
    
    public function getCierres(){
        $cierres = R::getAll('select * from cierre order by fechahora desc');
        return $cierres;
    }
 
    public function grillaResumen(){        
        $ordenes_in = implode(',', $this->ordenes);
        $sql = "SELECT  plazo, 
                        moneda, 
                        count(*)      cantidadOrdenes, 
                        sum(cantidad) sumaCantidad
                        FROM    registro
                WHERE   id in ({$ordenes_in})
                GROUP BY plazo, moneda";
        $resultado = R::getAll($sql);
        return $resultado;
    }
    
    public function controlGrilla(){
        $sql = "select l.id,
                l.numeroSocio,
                l.descripcionSocio,
                l.numeroProducto,
                l.descripcionProducto,
                l.cantidad,       
                l.precio,
                concat(u.apellido, ' ', u.nombre) as usuario,
                eo.estado,
                l.estado_id,
                l.fechaCarga,
                l.fhmodificacion,
                l.observaciones,
                l.descripcion
         from   registro l
         join   estadoorden eo 
         on     l.estado_id = eo.id
         join   cierre c
         on     l.cierre_id = c.id
         join   usuario u
         on     l.usuario_id = u.id
         where  l.estado_id <> 1
         and    l.cierre_id = ?
         order by l.fhmodificacion desc"; 
        
        $resultado = R::getAll($sql, array($this->cierre_id));
        
        return $resultado;
    }
    
    public function getOrdenes(){
        
        if ($this->cierre_id == 0){
            $this->getCierreActual();
        }
        
        $sql = "(select l.id,
                l.moneda,
                l.plazo,
                l.cantidad,
                l.precio
         from   registro l
         join   estadoorden eo 
         on     l.estado_id = eo.id
         join   cierre c
         on     l.cierre_id = c.id
         join   usuario u
         on     l.usuario_id = u.id
         where  l.estado_id <> 4
         and    l.cierre_id = ?
         and    l.numComitente = ?
         order by l.fhmodificacion desc)"; 
        
        $resultado = R::getAll($sql, array($this->cierre_id, $this->numComitente));
        
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
 
        $orden = R::load('registro', $this->id);
        $cierre = R::load('cierre', $this->cierre);
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
                        
            if($nombreHojas[0] == 'numerosocio' && $nombreHojas[2] == 'numeroproducto'){
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
                $numeroSocio = $sheet->getCellByColumnAndRow(0,$row)->getCalculatedValue();
                $numeroSocio = str_replace(',', '', $numeroSocio);  

                $descripcionSocio = $sheet->getCellByColumnAndRow(1,$row)->getFormattedValue();               
                    
                $numeroProducto = $sheet->getCellByColumnAndRow(2,$row)->getCalculatedValue();     

                $descripcionProducto = $sheet->getCellByColumnAndRow(3,$row)->getFormattedValue();     
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

                $cantidad = $sheet->getCellByColumnAndRow(4,$row)->getFormattedValue();

                $precio = $sheet->getCellByColumnAndRow(5,$row)->getFormattedValue();

                $fechaCarga = $sheet->getCellByColumnAndRow(6,$row)->getFormattedValue();

                $fhmodificacion = $sheet->getCellByColumnAndRow(7,$row)->getFormattedValue();

                $observaciones = $sheet->getCellByColumnAndRow(8,$row)->getFormattedValue();

                $descripcion = $sheet->getCellByColumnAndRow(9,$row)->getFormattedValue();




                $orden = R::dispense('registro');
                $orden->numeroSocio = $numeroSocio;
                $orden->descripcionSocio = $descripcionSocio;
                $orden->numeroProducto = $numeroProducto;
                $orden->descripcionProducto = $descripcionProducto;
                $orden->cantidad = $cantidad;
                $orden->precio = $precio;
                $orden->estado = $estadoorden;
                $orden->usuario = $usuario;
                $orden->fechaCarga = $fechaCarga;
                $orden->fhmodificacion = R::isoDateTime();
                $orden->observaciones = $observaciones;
                $orden->descripcion = $descripcion;
                $orden->cierre = $cierre;

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

class Model_Registro extends RedBean_SimpleModel {
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
            $auditoria->table = 'registro';
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
        $auditoria->table = 'registro';
        $auditoria->tableId = $this->prev['id'];
        $auditoria->anterior = json_encode($this->prev);
        $auditoria->actual = json_encode(array('operacion'=>'Registro Borrado'));
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
        $auditoria->actual = json_encode(array('operacion'=>'Registro Borrado'));
        R::store($auditoria);        
    }    
}
