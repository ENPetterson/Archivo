<?php

class Menu_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $padre_id;
    public $nombre;
    public $accion;
    
    public function saveMenu(){
        $menu = R::load('menu', $this->id);
        $menu->padre_id = $this->padre_id;
        $menu->nombre = $this->nombre;
        $menu->accion = $this->accion;
        $this->id = R::store($menu);
        return $this->id;
    }
    
    public function getMenues(){
        //obteniendo los grupos del usuario
        $usuario = $this->session->userdata('usuario');
        $usuario_id = $usuario['id'];
        $grupos = R::getCol('select grupo_id from grupo_usuario where usuario_id = ?',
                array($usuario_id));
        $menu_in = R::getCol('select menu_id from grupo_menu where grupo_id in (' .
                R::genSlots($grupos). ')', $grupos);
        $menu = R::getAll('select * from menu where id in ('.R::genSlots($menu_in).') ', 
                $menu_in);
        return $menu;
    }
    
    public function getGrupoMenu(){
        $grupo_id = $this->id;
        $menu = R::getCol('select menu_id from grupo_menu where grupo_id = ? ', array($grupo_id));
        return $menu;
    }
    
    public function getAllMenues(){
        $menues = R::getAll('select * from menu order by padre_id, id');
        return $menues;
    }
    
    public function getMenu(){
        $menu = R::load('menu', $this->id);
        return $menu->export();
    }
    
    public function delMenu(){
        $menu = R::load('menu', $this->id);
        R::trash($menu);
    }
    
    
}