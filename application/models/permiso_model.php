<?php

class Permiso_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $grupo_id;
    public $vista_id;
    public $tipo;
    public $elemento;
    public $permisos;
    public $grupos;
    
    public function getPermisos(){
        $permisos = R::getAll('select tipo, elemento from permiso where grupo_id = ? and vista_id = ?', 
                 array($this->grupo_id, $this->vista_id));
        return $permisos;
    }
    
    public function savePermiso(){
        $grupo = R::load('grupo', $this->grupo_id);
        $vista = R::load('vista', $this->vista_id);
        $permisos = R::find('permiso', 'grupo_id = ? and vista_id = ?', array($this->grupo_id, $this->vista_id));
        R::trashAll($permisos);
        if (is_array($this->permisos)){
            foreach ($this->permisos as $value) {
                $permiso = R::dispense('permiso');
                $permiso->grupo = $grupo;
                $permiso->vista = $vista;
                $permiso->tipo = $value['tipo'];
                $permiso->elemento = $value['elemento'];
                $this->id = R::store($permiso);
            }
        };
        return $this->id;
    }
    
    public function getPermisosVista(){
        $permisos = R::getAll("select tipo, elemento from permiso where vista_id = {$this->vista_id} and grupo_id in (".
                R::genSlots($this->grupos)." ) ", $this->grupos);
        return $permisos;
    }
}