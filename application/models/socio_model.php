<?php

require_once APPPATH."/third_party/PHPExcel.php";

class Socio_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $descripcion;
    public $anulado;
    public $tipoSocio;
    public $fechaCarga;
    public $fhmodificacion;
    public $oficial;
    public $cuit;
    public $ordenes;
    public $socio;
    
    
    public $fechahora;

    
    public function saveOrden(){
        $usuarioParam = $this->session->userdata('usuario');
        $usuario = R::load('usuario', $usuarioParam['id']);

        $orden = R::load('socio', $this->id);

     
        $orden->usuario = $usuario;

        $orden->descripcion = $this->descripcion;
        $orden->anulado = $this->anulado;
        $orden->tipoSocio = $this->tipoSocio;
        $orden->fechaCarga =  R::isoDateTime();
        $orden->fhmodificacion =  R::isoDateTime();
        $this->id = R::store($orden);
        
        return $orden->export();
    }
    
    public function getSocioId(){
        $sql = "select *
         from   socio
         WHERE id = ?"; 
        
        $resultado = R::getAll($sql, array($this->id));
        
        return $resultado;
    }
    

    public function getSocio(){
        $socio = R::load('socio', $this->id);        
        return $socio->export();
    }
    
    public function delOrden(){
        foreach ($this->ordenes as $id){
            $socio = R::load('socio', $id);
            R::trash($socio);
        }
    }
    
    
    public function grilla(){
        $sql = "select l.id,
                l.descripcion,
                CASE WHEN anulado = 'true' THEN 'Si' ELSE 'No' END as anulado,
                l.tipoSocio,
                l.fechaCarga,
                l.fhmodificacion
         from   socio l
         order by l.id"; 
        
        $resultado = R::getAll($sql);
        
        return $resultado;

    }
    
    public function enviarOrdenes(){        
        $estado = R::load('estadoorden', 2);
        $resultado = array('exito'=>1, 'resultado'=>'Socios enviados correctamente');
        $ahora = new DateTime();
        foreach ($this->ordenes as $id) {
            $orden = R::load('socio', $id);
            
        }
        return $resultado;
    }
    
    public function anularOrdenes(){        
        $estado = R::load('estadoorden', 4);
        $resultado = array('exito'=>1, 'resultado'=>'Socios anulados correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('socio', $id);
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
                        FROM    socio
                WHERE   id in ({$ordenes_in})
                GROUP BY plazo, moneda";
        $resultado = R::getAll($sql);
        return $resultado;
    }
    
    public function controlGrilla(){
        $sql = "select l.id,
                l.descripcion,
                l.anulado,
                l.tipoSocio,       
                concat(u.apellido, ' ', u.nombre) as usuario,
                eo.estado,
                l.estado_id,
                l.fechaCarga,
                l.fhmodificacion
         from   socio l
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
         from   socio l
         join   estadoorden eo 
         on     l.estado_id = eo.id
         join   usuario u
         on     l.usuario_id = u.id
         where  l.estado_id <> 4    
         and    l.numComitente = ?
         order by l.fhmodificacion desc)"; 
        
        $resultado = R::getAll($sql, array( $this->numComitente));
        
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
 
        $orden = R::load('socio', $this->id);
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
                        
            if($nombreHojas[0] == 'descripcion' && $nombreHojas[1] == 'anulado'){
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
                    
                $anulado = $sheet->getCellByColumnAndRow(1,$row)->getCalculatedValue();     
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

                $tipoSocio = $sheet->getCellByColumnAndRow(2,$row)->getFormattedValue();

                $fechaCarga = $sheet->getCellByColumnAndRow(3,$row)->getFormattedValue();


                $orden = R::dispense('socio');
                $orden->descripcion = $descripcion;
                $orden->anulado = $anulado;
                $orden->tipoSocio = $tipoSocio;
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

class Model_Socio extends RedBean_SimpleModel {
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
            $auditoria->table = 'socio';
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
        $auditoria->table = 'socio';
        $auditoria->tableId = $this->prev['id'];
        $auditoria->anterior = json_encode($this->prev);
        $auditoria->actual = json_encode(array('operacion'=>'Socio Borrado'));
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
        $auditoria->actual = json_encode(array('operacion'=>'Socio Borrado'));
        R::store($auditoria);        
    }    
}
