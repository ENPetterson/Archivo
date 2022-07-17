<?php

class Grupo_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $nombre;
    public $usuario_id;
    public $menu_id;
    public $controlador_id;
    
    public function saveGrupo(){
        $grupo = R::load('grupo', $this->id);
        $grupo->nombre = $this->nombre;
        $this->id = R::store($grupo);
        return $this->id;
    }
    
    public function getGrupo(){
        $grupo = R::load('grupo', $this->id);
        return $grupo->export();
    }
    
    public function getGruposUsuario(){
        $grupos = R::getCol('select grupo_id from grupo_usuario where usuario_id = ?', array($this->usuario_id));
        return $grupos;
    }
    
    public function getGrupos(){
        $grupos = R::getAll('select * from grupo order by nombre');
        return $grupos;
    }
    
    public function delGrupo(){
        $grupo = R::load('grupo', $this->id);
        R::trash($grupo);
    }
    
    public function assocUsuario(){
        $grupo = R::load('grupo', $this->id);
        $usuario = R::load('usuario', $this->usuario_id);
        R::associate($grupo, $usuario);
    }
    
    public function clearRelMenu(){
        $grupo = R::load('grupo', $this->id);
        R::clearRelations($grupo, 'menu');
    }
    
    public function assocMenu(){
        $grupo = R::load('grupo', $this->id);
        $menu = R::load('menu', $this->menu_id);
        R::associate($grupo, $menu);
    }
    
    public function clearRelControlador(){
        $grupo = R::load('grupo', $this->id);
        R::clearRelations($grupo, 'controlador');
    }
    
    public function assocControlador(){
        $grupo = R::load('grupo', $this->id);
        $controlador = R::load('controlador', $this->controlador_id);
        R::associate($grupo, $controlador);
    }
    
    
}