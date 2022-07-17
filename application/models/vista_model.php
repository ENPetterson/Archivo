<?php
class Vista_model extends CI_Model {
    public $id;
    public $nombre;
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getVista(){
        $vista = R::load('vista', $this->id);
        return $vista->export();
    }
    
    public function getVistas(){
        $vistas = R::getAll('select * from vista order by nombre');
        return $vistas;
    }
    
    public function saveVista(){
        $vista = R::load('vista', $this->id);
        $vista->nombre = $this->nombre;
        $this->id = R::store($vista);
        return $this->id;
    }
    
    public function delVista(){
        $vista = R::load('vista', $this->id);
        R::trash($vista);
    }
    
    public function findVista($vista){
        $vista = R::findOne('vista', 'nombre = ?', array($vista));
        return $vista->id;
    }
}