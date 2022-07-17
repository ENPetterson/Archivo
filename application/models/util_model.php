<?php
class Util_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    function buscarDuplicado($tabla, $campo, $valor, $id){
        $registro = R::findOne($tabla, " {$campo} = :valor and id <> :id  ", array(':valor'=>$valor, ':id'=>$id));
        if (is_null($registro)){
            $duplicado = false;
        } else {
            $duplicado = true;
        }
        return $duplicado;
    }
}