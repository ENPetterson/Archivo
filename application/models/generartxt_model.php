<?php

require_once APPPATH."/third_party/PHPExcel.php";

class Generartxt_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $tramo;
    public $numComitente;
    public $tipoInversor;
    public $moneda;
    public $cable;
    public $comision;
    public $cantidad;
    public $precio;
    public $comitente;
//    public $tipoPersona;
    public $oficial;
    public $cuit;
    public $ordenes;
    
    public $usuario_id;
    
    //public $cierregenerartxt_id;
    public $fechahora;

    
    public function saveOrden(){
                
        $usuarioParam = $this->session->userdata('usuario');
        $usuario = R::load('usuario', $usuarioParam['id']);

        $orden = R::load('generartxt', $this->id);
        
        $orden->tramo = $this->tramo;
        $orden->numcomitente = $this->numComitente;
        $orden->tipoInversor = $this->tipoInversor;
        $orden->moneda = $this->moneda;
        $orden->cable = $this->cable;
        
        //$orden->plazo = $this->plazo;
        $orden->especie = $this->especie;
        
        $orden->comision = (float)$this->comision;
        $orden->cantidad = (int)$this->cantidad;
        $orden->precio = (float)$this->precio;
        $orden->comitente = $this->comitente;
        $orden->tipopersona = $this->tipoPersona;
        $orden->oficial = $this->oficial;
        $orden->cuit = (int)$this->cuit;
        $orden->fhmodificacion =  R::isoDateTime();
        $this->id = R::store($orden);
        
        return $orden->export();
    }
    
    public function getOrden(){
        $orden = R::load('generartxt', $this->id);
        return $orden->export();
    }
    
    public function delOrden(){
        foreach ($this->ordenes as $id){
            $orden = R::load('generartxt', $id);
            R::trash($orden);
        }
    }
    
    
    public function grilla(){
        $sql = "select b.id,
                b.tramo,
                b.numeroComitente,
                b.moneda,
                b.especie,
                b.comision,
                b.cantidad,
                b.precio,                
                eo.estado,
                b.estado_id,
                b.fhmodificacion,
                b.usuario_id
         from   generartxt b
         join   estadoorden eo 
         on     b.estado_id = eo.id

         where  b.usuario_id = ?
         order by b.id desc"; 
        
        $resultado = R::getAll($sql, array($this->usuario_id));
        
        return $resultado;

    }
    
    public function enviarOrdenes(){        
        $estado = R::load('estadoorden', 2);
        $resultado = array('exito'=>1, 'resultado'=>'Ordenes Enviadas Correctamente');
        $ahora = new DateTime();
        foreach ($this->ordenes as $id) {
            $orden = R::load('generartxt', $id);
            $fechaCierre = new DateTime($orden->cierregenerartxt->fechahora);
            if ($fechaCierre < $ahora){
                $resultado = array('exito'=>0, 'resultado'=>'Algunas ordenes no se pudieron enviar porque ya estaban cerradas');
            } else {
                $orden->estado = $estado;
                R::store($orden);
            }
        }
        return $resultado;
    }
    
    public function retirarOrdenes(){        
        $estado = R::load('estadoorden', 5);
        $resultado = array('exito'=>1, 'resultado'=>'Ordenes Retiradas Correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('generartxt', $id);
            $orden->fechaRetiro = R::isoDateTime();
            $orden->estado = $estado;
            R::store($orden);
        }
        return $resultado;
    }
    
    public function anularOrdenes(){        
        $estado = R::load('estadoorden', 4);
        $resultado = array('exito'=>1, 'resultado'=>'Ordenes Anuladas Correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('generartxt', $id);
            $orden->estado = $estado;
            R::store($orden);
        }
        return $resultado;
    }
    
    /*public function getMonedas(){
        if ($this->cierregenerartxt_id > 0){
            $sql = "select distinct moneda from plazogenerartxt where cierregenerartxt_id = ? ";
            $monedas = R::getCol($sql, array($this->cierre_id));
        } else {
            $sql = "select distinct moneda from plazogenerartxt where cierregenerartxt_id = (SELECT id FROM cierregenerartxt where fechahora > NOW() order by fechahora limit 0,1)";
            $monedas = R::getCol($sql);
        }
        $sql = "select * from moneda where simbolo in (" . R::genSlots($monedas) . ")";
        $resultado = R::getAll($sql, $monedas);
        return $resultado;
    }*/
 
    public function grillaResumen(){        
        $ordenes_in = implode(',', $this->ordenes);
        $sql = "SELECT  moneda, 
                        count(*)      cantidadOrdenes, 
                        sum(cantidad) sumaCantidad
                        FROM    generartxt
                WHERE   id in ({$ordenes_in})
                GROUP BY moneda";
        $resultado = R::getAll($sql);
        return $resultado;
    }
    
    public function procesarGrilla(){
        $sql = "(select b.id,
                b.tramo,
                b.numComitente,
                b.moneda,
                b.cable,
                b.especie,
                b.comision,
                b.cantidad,
                b.precio,
                b.comitente,
                
                b.oficial,
                concat(u.apellido, ' ', u.nombre) as usuario,
                b.cuit,
                eo.estado,
                b.estado_id,
                b.fhmodificacion,
                b.envio,
                b.fhenvio
         from   generartxt b
         join   estadoorden eo 
         on     b.estado_id = eo.id
         join   cierregenerartxt c
         on     b.cierregenerartxt_id = c.id 

         join   usuario u
         on     b.usuario_id = u.id
         where  b.estado_id <> 1
         order by b.fhmodificacion desc)"; 
        
        $resultado = R::getAll($sql, array($this->cierregenerartxt_id));
        
        return $resultado;
    }
    
    /*public function previewSantander(){
        $ordenes_in = implode(',', $this->ordenes);
        $sql = "SELECT  b.cierre_id,
                        b.plazo, 
                        b.moneda, 
                        b.precio,
                        p.especie, 
                        sum(b.cantidad) sumaCantidad
                        FROM    generartxt b
                        left outer join    plazogenerartxt p 
                        ON (b.cierregenerartxt_id = p.cierregenerartxt_id AND b.plazo = p.plazo AND b.moneda = p.moneda)
                WHERE   b.id in ($ordenes_in)
                GROUP BY b.cierre_id, b.plazo, b.moneda, b.precio, p.especie";
        $resultado = R::getAll($sql);
        return $resultado;
    }*/
 
    public function enviarSantander(){        
        $estado = R::load('estadoorden', 3);
        $resultado = array('exito'=>1, 'resultado'=>'Ordenes Enviadas Correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('generartxt', $id);
            $orden->estado = $estado;
            $orden->envio = 'S';
            $orden->fhenvio = R::isoDateTime();
            R::store($orden);
        }
        return $resultado;
    }
    
    
    /*public function previewMercado(){
        $ordenes_in = implode(',', $this->ordenes);
        $sql = "SELECT  cierregenerartxt_id,
                        tramo,
                        moneda, 
                        cable,
                        tipopersona,
                        numcomitente,
                        comitente,
                        tipoInversor,
                        cantidad,
                        precio,
                        cuit,
                        comision
                        FROM    generartxt
                WHERE   id in ({$ordenes_in})
                ORDER BY cierregenerartxt_id, moneda, tramo ";
        $resultado = R::getAll($sql);
        
        
        
        $contenido = array();
        $colocacionAnterior = 0;
        $contenidoInd = 0;
        foreach ($resultado as $indice=>$fila){
            $plazo = R::findOne('plazogenerartxt', 'cierregenerartxt_id = ? and moneda = ?', array($fila['cierregenerartxt_id'], $fila['moneda']));
        
            

            
            //Esto se cambia.
            
            if ($fila['tramo'] == 'Competitiva'){
                $titulo = $plazo->tituloC;
                $precio = $fila['precio'];
                $colocacion = $plazo->colocacionC;
            } else {
                $precio = '';
                $titulo = $plazo->tituloNC;
                $colocacion = $plazo->colocacionNC;
            }
            
            switch ($fila['plazo']){
		case '30':
		    switch ($fila['moneda']){
		        case '$':
		            if ($fila['tramo'] == 'Competitiva'){
		                $titulo = utf8_decode('Letras 140 dias Vto. 17/01/2020 Tramo Competitivo  - Integracion Pesos');
		                $precio = $fila['precio'];
		            } else {
		                $titulo = utf8_decode('Letras 140 dias Vto. 17/01/2020 Tramo NO Competitivo  - Integracion Pesos');
		                $precio = '';
		            }
		            break;

		        case 'u$s':
		            if ($fila['tramo'] == 'Competitiva'){
		                $titulo = utf8_decode('Boncer en $ Vto. 2021 Tramo Competitivo - Int Dolares');
		                $precio = $fila['precio'];
		            } else {
		                $titulo = utf8_decode('Boncer en $ Vto. 2021 Tramo NO Competitivo - Int Dolares');
		                $precio = '';
		            }
		            break;
	    	    }
                break;
            }            
            
            if ($indice == 0){
                $colocacionAnterior = $colocacion;
            }
            
            if ($colocacion <> $colocacionAnterior){
                $contenidoInd++;
                $colocacionAnterior = $colocacion;
            }
            
                        
            if ($fila['tipopersona'] == 'JURIDICA'){
                $tipoPersona = 'Persona Juridica';
                $tipoDocumento = 'CUIT';
            } else {
                $tipoPersona = 'Persona Fisica';
                $tipoDocumento = 'CUIL';
            }
            
            
            
            
            if ($fila['moneda'] == '$'){
                $moneda = 'Pesos';
            } else {
                if ($fila['cable'] == 1) {
                    $moneda = 'Cable';
                } else {
                    $moneda = 'MEP';
                }
            }
            
            
            
            switch ($fila['tipoInversor']){
                
                case 'I': 
                    $tipoInversor = 'Institucional';
                    break;
                case 'M':
                    $tipoInversor = 'Inversor Minorista';
                    break;
                case 'P':
                    $tipoInversor = 'Cartera Propia';
                    break;
                case 'R':
                    $tipoInversor = 'No Residente';
                    break;
                case 'C':
                    $tipoInversor = 'Corporativo';
                    break;
                default :
                    $tipoInversor = '';
                    break;
            }
            
//            print_r("fila['numcomitente']");
//        
//            echo "<pre>";
//            print_r($fila['numcomitente']);
//            echo "<pre>";
//            
//            print_r("moneda");
//        
//            echo "<pre>";
//            print_r($moneda);
//            echo "<pre>";
//            
//            print_r("fila['cuit']");
//        
//            echo "<pre>";
//            print_r($fila['cuit']);
//            echo "<pre>";
//            

            
            
            
            $contenido[$contenidoInd]['colocacion'] = $colocacion;
//            $contenido[$contenidoInd]['datos'] .= $colocacion . "\t" . $titulo . "\r\n";
//            $contenido[$contenidoInd]['datos'] .= $colocacion . "\t" . $titulo . "\t" . $precio . "\t" . $fila['cantidad'] . "\t\t" . $this->formatearCuit($fila['cuit']) . "\t" . $fila['numcomitente'] . "\r\n"; 
//            $contenido[$contenidoInd]['datos'] .= $colocacion . "\t" . $titulo . "\t" . $precio . "\t" . $fila['cantidad'] . "\t\t" . $fila['numcomitente'] . "\t" . $moneda . "\t" . $tipoPersona . "\t" . $fila['cuit']  . "\t" . $fila['comitente'] . "\t\t". $tipoInversor . "\t\r\n";
//            $contenido[$contenidoInd]['datos'] .= $colocacion . "\t" . $titulo . "\t" . $precio . "\t" . $fila['cantidad'] . "\t" . $fila['numcomitente'] . "\t" . $fila['cuit'] . "\t" . $tipoPersona . "\t" . $tipoDocumento . "\t\r\n";
            $contenido[$contenidoInd]['datos'] .= $colocacion . "\t" . $titulo . "\t" . $precio . "\t" . $fila['cantidad'] . "\t\t" . $fila['numcomitente'] . "\t\t" . $fila['cuit']  . "\t\t\t\t" . $tipoPersona .  "\t\t\t".$tipoDocumento."\r\n";
            
            
            
        }
        $this->load->library('zip');
        foreach ($contenido as $data){
            $archivo = date('Y-m-d-H-i-s') . '-' . $data['colocacion'] . '-byma.txt';
            //$archivo = date('Y-m-d-H-i-s') . '-' . $data['colocacion'] . '-licitacions.txt';
            $this->zip->add_data($archivo, $data['datos']);
            //file_put_contents($archivo, $data['datos']);
            //array_push($uris, base_url() . 'generadas/' . basename($archivo));
        }
        $nombreZip = FCPATH . 'generadas/' . date('Y-m-d-H-i-s') . '-generartxts.zip';
        $this->zip->archive($nombreZip);
        
        return array('uris'=>array(base_url() . 'generadas/' . basename($nombreZip)));
    }
    */
    
    public function enviarMercado(){
        $estado = R::load('estadoorden', 3);
        $resultado = array('exito'=>1, 'resultado'=>'Ordenes Enviadas Correctamente');
        foreach ($this->ordenes as $id) {
            $orden = R::load('generartxt', $id);
            $orden->estado = $estado;
            $orden->envio = 'M';
            $orden->fhenvio = R::isoDateTime();
            R::store($orden);
        }
        return $resultado;
    }

    
    public function formatearCuit($cuit){
        return substr((string) $cuit, 0, 2) . '-' . substr((string) $cuit, 2, 8) . '-' . substr((string) $cuit, 10, 1);
    }
    
    private function nombreArchivo($nombrePlanilla){
        $resultado = "BONO ";
        $inicio  = strpos($nombrePlanilla, '-');
        $fin    = strpos($nombrePlanilla, '-', $inicio + 1);
        $largo = $fin - $inicio;
        //$plazo = substr($nombrePlanilla, $inicio + 1, $largo - 1);
        //$resultado .= $plazo;
        $resultado .= ' DIAS ';
        if (substr($nombrePlanilla, 1, 1) <> 'P'){
            $resultado .= 'USD ' . substr($nombrePlanilla, 1, 1) . ' ';
        }
        if (substr($nombrePlanilla, 0, 1) == 'C'){
            $resultado .= 'TC ';
        } else {
            $resultado .= 'TNC ';
        }
        if (substr($nombrePlanilla, 2, 1) == 'J'){
            $resultado .= 'PJ-';
        } else {
            $resultado .= 'PF-';
        }
        $numerador = substr($nombrePlanilla, $fin + 1);
        $resultado .= $numerador;
        
        return $resultado;
    }

    
    public function grabarExcel(){ 
                  

        $usuarioParam = $this->session->userdata('usuario');

        $orden = R::load('generartxt', $this->id);
        $cierre = R::load('cierregenerartxt', $this->cierre);
        $usuario = R::load('usuario', $usuarioParam['id']);
        $estadoorden = R::load('estadoorden', 1);        
//        $this->moneda = '$';


        //$plazos = $this->Generartxt_model->getPlazos();



               
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

        $sheetname = 'Sheet1';
        
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

            //Si están bien los nobres entonces aprobado vá a ser 1
            //Entonces imprimí el fuckin' aprobado            
            if($nombreHojas[0] == 'tramo' && $nombreHojas[1] == 'numerocomitente'){
                $aprobado = 1;
            }
        }
        //Que pelotuda, las mayúsculas        

        //Si sabés que lo primero que hace es fijarse los nombres de las primeras dos columnas
        //Fijate primero que haga eso.

        if($aprobado){



            $highestRow = $sheet->getHighestDataRow();
            
            $valido = true;
            $error = '';
            
            R::freeze(true);
            R::begin();
            
            //El debug te imprime todo lo que ejecutas en mysql y los errores que podes estar teniendo grabando en la base
            //R::debug(true);

            for ($row = 2; $row <= $highestRow; $row++){
                
                $tramo = $sheet->getCellByColumnAndRow(0,$row)->getFormattedValue();
                
                $numeroComitente = $sheet->getCellByColumnAndRow(1,$row)->getFormattedValue();                
                $numeroComitente = str_replace(',', '', $numeroComitente);
                $numeroComitente = str_replace('.', '', $numeroComitente);
                if (strlen(trim($numeroComitente)) > 0) {
                    
                    

                    // Comitente
                    //$this->load->model('Esco_model');
                    //$this->Esco_model->numComitente = $numeroComitente;
                    //$resultado = $this->Esco_model->getComitente();
                    
                    
                    /*if($resultado){
                        $orden->comitente = $resultado['comitente']; // Lo levanto del Esco
                        if ($resultado['esFisico'] == -1){
                            $orden->tipopersona = 'FISICA';
                        } else {
                            $orden->tipopersona = 'JURIDICA'; 
                        }
                        $orden->oficial = $resultado['oficial'];
                        $orden->cuit = $resultado['cuit'];    
                    } else {
                        $error.="Número de comitente inválido en fila {$row} <br>";
                        $valido = false;
                    }*/
                    
                    
                    
                    $moneda = $sheet->getCellByColumnAndRow(2,$row)->getFormattedValue();

                    //Plazo
                    
                    /*$plazoNombre = $sheet->getCellByColumnAndRow(3,$row)->getCalculatedValue();
                   
                    $this->Generartxt_model->plazoNombre = $plazoNombre;
                    $resultadoPlazo = $this->Generartxt_model->getPlazoPorNombre();
                    
                    $plazo = 0;
                    
                    if($resultadoPlazo){
                        $plazo = $resultadoPlazo['id'];
                    }else{
                        $error.="Plazo invalido en fila {$row} <br>";
                        $valido = false;                    
                    }*/
                    
                    
                    $especie = $sheet->getCellByColumnAndRow(4,$row)->getValue();     
                    
                    // Comision
                    $comision = $sheet->getCellByColumnAndRow(5,$row)->getOldCalculatedValue();     
                    if($comision == 0){
                      $comision = $sheet->getCellByColumnAndRow(5,$row)->getCalculatedValue();
                    }
                    $comision = (float)$comision;
                    
                    
                    // Cantidad
                    $cantidad = $sheet->getCellByColumnAndRow(6,$row)->getOldCalculatedValue();     
                    if($cantidad == 0){
                      $cantidad = $sheet->getCellByColumnAndRow(6,$row)->getCalculatedValue();
                    }
                    $cantidad = (int)$cantidad;
                    
                    
                    // Precio
                    $precio = $sheet->getCellByColumnAndRow(7,$row)->getOldCalculatedValue();     
                    if($precio == 0){
                      $precio = $sheet->getCellByColumnAndRow(7,$row)->getCalculatedValue();
                    }
                    $precio = (float)$precio;
                    

                    $orden = R::dispense('generartxt');
                    $orden->numeroComitente = $numeroComitente;
                    $orden->tramo = $tramo;
                    

                    
                    $orden->moneda = $moneda;
                    //$orden->plazo = $plazo;
                    $orden->especie = $especie;
                    $orden->comision = $comision;
                    $orden->cantidad = $cantidad;
                    $orden->precio = $precio;
                    
                    

                    //Esto siempre tiene que tener un campo que guarde la fecha de creado
                    //Se puede llamar fechaGenerado o algo así
                    //Por ahora se lo saco, después agregalo
                    $orden->fhmodificacion = R::isoDateTime();

                    $orden->usuario = $usuario;
                    $orden->cierregenerartxt = $cierre;
                    $orden->estado_id = 1;

                    
                    if ($valido){
                        $this->id = R::store($orden);    
                    }

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

    
    public function generarArchivo(){                  

        $usuarioParam = $this->session->userdata('usuario');

        $orden = R::load('generartxt', $this->id);
        $cierre = R::load('cierregenerartxt', $this->cierre);
        $usuario = R::load('usuario', $usuarioParam['id']);
        $estadoorden = R::load('estadoorden', 1);        
//        $this->moneda = '$';


        //$plazos = $this->Generartxt_model->getPlazos();



               
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

        $sheetname = 'Sheet1';
        
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

            //Si están bien los nobres entonces aprobado vá a ser 1
            //Entonces imprimí el fuckin' aprobado            
            if($nombreHojas[0] == 'tramo' && $nombreHojas[1] == 'numerocomitente'){
                $aprobado = 1;
            }
        }
        //Que pelotuda, las mayúsculas        

        //Si sabés que lo primero que hace es fijarse los nombres de las primeras dos columnas
        //Fijate primero que haga eso.

        if($aprobado){



            $highestRow = $sheet->getHighestDataRow();

            //Todo lo que sea guardar lo vamos a sacar, porque no queremos guardar nada.


            // Bueno, acá lo que tenemos entonces, es una función que levanta los datos de excel.
            // Hace un for y los vé, pero no los guarda en ningún lado.
            // El tema con los for, es que entra y dá vueltas, pero las variables las vá pisando, entonces cuando salís del for, la variable vale solamente el último dato.

            
            //Lo que queremos es hacer un archivo txt, y que tenga estos datos adentro
            //Así que antes del for vamos a hacer un txt

        //Estas tres fechas son en diferentes formatos
        //Después imprimí las tres
        //Y fijate como las imprime
        $fechaArchivo = new DateTime('NOW');
        $fechaArchivo = $fechaArchivo->format('Ymd'); 
        
        $fechaDMA = new DateTime('NOW');
        $fechaDMA = $fechaDMA->format('ymd');
                
        $fechaLimite = new DateTime('NOW');
        $fechaLimite = $fechaLimite->format('dm');
        
        
        $app_id = uniqid();//give each process a unique ID for differentiation, hace esto un id único. Por qué? No hay polqué
        $log_name = "elarchivotxt".$fechaArchivo.$app_id.".txt";//Este es el nombre
        //$log = fopen('/var/www/ordenes/application/downloads/'.$log_name,'a');
        $log = fopen("C:\\xampp\\htdocs\\ordenes\\application\\downloads\\".$log_name,'a');
        
        
        $tipoRegistro = 0;
        $fecha = $fechaDMA;
        $idArchivo = 'FTFAOT';
        $codigoParticipante = '0006';
//        $libre53 = (str_pad((string) $libre53,53," ",STR_PAD_LEFT));
        
        //00Aftfaot    20200821800001000000022
        $hora = '800001';
        $total = count($resultados);       
        $total = $total + 1;
        
        
        
        //$total = (str_pad((string) $total,9,"0",STR_PAD_LEFT));
        //$linea0 = '00Aftfaot    '.$fechaArchivo.$hora.$total;  
        $linea0 = '00Aftfaot    '.$fechaArchivo.$hora.$total;  

        //$linea99 = '99Aftfaot    '.$fechaArchivo.$hora.$total;  
        $linea99 = '99Aftfaot    '.$fechaArchivo.$hora.$total;  

        
        //0200820FTFAOT0006
        $linea1 = $tipoRegistro.$fecha.$idArchivo.$codigoParticipante;      
        





            for ($row = 2; $row <= $highestRow; $row++){
                
                $tramo = $sheet->getCellByColumnAndRow(0,$row)->getFormattedValue();
                
                $numeroComitente = $sheet->getCellByColumnAndRow(1,$row)->getFormattedValue();                
                $numeroComitente = str_replace(',', '', $numeroComitente);
                $numeroComitente = str_replace('.', '', $numeroComitente);
                //if (strlen(trim($numeroComitente)) > 0) {
                    
                    $moneda = $sheet->getCellByColumnAndRow(2,$row)->getFormattedValue();
                    
                    
                    $especie = $sheet->getCellByColumnAndRow(4,$row)->getValue();     
                    
                    // Comision
                    $comision = $sheet->getCellByColumnAndRow(5,$row)->getOldCalculatedValue();     
                    if($comision == 0){
                      $comision = $sheet->getCellByColumnAndRow(5,$row)->getCalculatedValue();
                    }
                    $comision = (float)$comision;
                    
                    
                    // Cantidad
                    $cantidad = $sheet->getCellByColumnAndRow(6,$row)->getOldCalculatedValue();     
                    if($cantidad == 0){
                      $cantidad = $sheet->getCellByColumnAndRow(6,$row)->getCalculatedValue();
                    }
                    $cantidad = (int)$cantidad;
                    
                    
                    // Precio
                    $precio = $sheet->getCellByColumnAndRow(7,$row)->getOldCalculatedValue();     
                    if($precio == 0){
                      $precio = $sheet->getCellByColumnAndRow(7,$row)->getCalculatedValue();
                    }
                    $precio = (float)$precio;
                    
                    //En cambio, si lo imprimís adentro del for, da una vuelta imprime, dá una vuelta, imprime, 
                    //pero el die lo tenes que dejar fuera del for, porque sino muere en la primera, imprime la primera nada más
                    //print_r('tramo: '); print_r($tramo);
                    //echo chr(13);
                    //print_r('numeroComitente: '); print_r($numeroComitente);
                    //echo chr(13);
                    //print_r('moneda: '); print_r($moneda);
                    //echo chr(13);



                    $lineas .= $tramo."'".$numeroComitente.chr(13).chr(10);

                    $datosEnArray[] = Array($tramo, $numeroComitente);

                    //"'".$moneda."'".$especie."'".$comision."'".$cantidad."'".$precio.chr(13).chr(10);


                    //echo chr(13);
                    //var_dump($datosEnArray);
                    //echo chr(13);

                   // die;
// Esto quería
// Eso? 
// Eso
                    

                //}
                
            }  //Acá termina el for


            //Siepre borrar los print, porque sino te devuelve un json sucio y se rompe todo.
            //Cuando se terminan las pruebs se borra o comenta.
            //echo('<pre>');
            //var_dump($datosEnArray);
            //echo('<pre>');

            //die;

            //O sea, acá donde termina el for quiero que imprimas el array $datosEnArray
            //Y que te imprima todo en forma de array. ok? Ok
            //Terminé haciendo todo yo
            //No era dificil
            //No entendi bien las instrucciones
            //OK


            //Ves, te imprime lo último nada mas, porque en cada vuelta pisa la variable anterior.
            //echo chr(13); //Esto es un enter, podés googlearlo, chr(10) es un carriege return, no me acuerdo cuál es cuál igual, googlealo
            //print_r('tramo: '); print_r($tramo);
            //echo chr(13);
            //print_r('numeroComitente: '); print_r($numeroComitente);
            //echo chr(13);
            //print_r('moneda: '); print_r($moneda);

            //die;

            //Por que va doble barra?
            //Porque la barra para allá \ es un caractér de escape, y anula lo que le ponés al lado, si yo pongo \" te dice que no le dés bola a la comilla de al lado, que es sólo un caracter al que no le dés bola, entonces la comilla no cierra
            //Entonces para que tome la \ le tenés que poner primero una \


            $log_line = join(array($linea0, chr(13).chr(10), $linea1, chr(13).chr(10), $lineas, $linea99 ) );
                
            fwrite($log, $log_line);
            fclose($log);
            //Siempre chequear que la carpeta tenga permisos para que la escriban, pero también no hay que ser boludo, si vés que dice var, eso es de linux, acá es c:\
            //$contenido = file_get_contents("/var/www/ordenes/application/downloads/" . $log_name);
            $contenido = file_get_contents("C:\\xampp\\htdocs\\ordenes\\application\\downloads\\" . $log_name);


            //Entendiste lo que se hizo acá?
            //Repasalo
            //Y como tarea del hogar, quiero que de ese row, viste que te mostré que cuando imprimis dentro del for imprime ok
            //Y cuando imprimis por fuera te imprime solo el ultimo dato porque pisa las variables, bueno, buscá cómo hacer un array y guardar los datos
                        
            return $log_name;
            
        } else {
            $error = 'Títulos inválidos.';
            $resultado = array('resultado'=>'Error', 'mensaje'=>$error);
            return $resultado;
        }
    }
     
    
}
// Así er esto originalmente?
//Si
//Ok

class Model_Generartxt extends RedBean_SimpleModel {
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
            $auditoria->table = 'generartxt';
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
        $auditoria->table = 'generartxt';
        $auditoria->tableId = $this->prev['id'];
        $auditoria->anterior = json_encode($this->prev);
        $auditoria->actual = json_encode(array('operacion'=>'Registro Borrado'));
        R::store($auditoria);        
    }    
}

class Model_Cierregenerartxt extends RedBean_SimpleModel {
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
            $auditoria->table = 'cierregenerartxt';
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
        $auditoria->table = 'cierregenerartxt';
        $auditoria->tableId = $this->prev['id'];
        $auditoria->anterior = json_encode($this->prev);
        $auditoria->actual = json_encode(array('operacion'=>'Registro Borrado'));
        R::store($auditoria);        
    }
}