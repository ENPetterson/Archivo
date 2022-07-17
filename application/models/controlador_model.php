<?php
class Controlador_model extends CI_Model {
    public $id;
    public $nombre;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getControlador(){
        $controlador = R::load('controlador', $this->id);
        return $controlador->export();
    }
    
    public function getControladores(){
        //obteniendo los grupos del usuario
        $usuario = $this->session->userdata('usuario');
        $usuario_id = $usuario['id'];
        $grupos = R::getCol('select grupo_id from grupo_usuario where usuario_id = ?',
                array($usuario_id));
        $controlador_in = R::getCol('select controlador_id from controlador_grupo where grupo_id in (' .
                R::genSlots($grupos). ')', $grupos);
        if(count($controlador_in)>0){
            $controladores = R::getCol('select nombre from controlador where id in ('.R::genSlots($controlador_in).') ', $controlador_in);
        } else {
            $controladores = array();
        }
        return $controladores;
    }    
    
    public function getAllControladores(){
        $controladores = R::getAll('select * from controlador order by nombre');
        return $controladores;
    }
    
    public function saveControlador(){
        $controlador = R::load('controlador', $this->id);
        $controlador->nombre = $this->nombre;
        $this->id = R::store($controlador);
        return $this->id;
    }
    
    public function delControlador(){
        $controlador = R::load('controlador', $this->id);
        R::trash($controlador);
    }
    
    public function getGrupoControlador(){
        $grupo_id = $this->id;
        $controlador = R::getCol('select controlador_id from controlador_grupo where grupo_id = ? ', array($grupo_id));
        return $controlador;
    }

}