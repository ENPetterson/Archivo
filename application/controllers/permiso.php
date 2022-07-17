<?php
class Permiso extends MY_AuthController {
    public function __construct() {
        parent::__construct();
    }
    
    public function menu(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'permiso/menu', 
            'template/pie'
            );
        foreach ($views as $value) {
            $this->load->view($value);
        }
    }
    
    public function getGrupoMenu(){
        $id = $this->input->post('id');
        $this->load->model('Menu_model');
        $this->Menu_model->id = $id;
        $menues = $this->Menu_model->getGrupoMenu();
        echo json_encode($menues);
    }
    
    public function saveGrupoMenu(){
        $grupo_id = $this->input->post('id');
        $menues = $this->input->post('menues');
        $this->load->model('Grupo_model');
        $this->Grupo_model->id = $grupo_id;
        $this->Grupo_model->clearRelMenu();
        if (is_array($menues)){
            foreach ($menues as $menu_id) {
                $this->Grupo_model->menu_id = $menu_id;
                $this->Grupo_model->assocMenu();
            }
        }
       echo json_encode(array('resultado'=>true));
    }
    
    public function controlador(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'permiso/controlador', 
            'template/pie'
            );
        foreach ($views as $value) {
            $this->load->view($value);
        }
    }
    
    public function getGrupoControlador(){
        $id = $this->input->post('id');
        $this->load->model('Controlador_model');
        $this->Controlador_model->id = $id;
        $controladores = $this->Controlador_model->getGrupoControlador();
        echo json_encode($controladores);
    }    
    
    public function saveGrupoControlador(){
        $grupo_id = $this->input->post('id');
        $controladores = $this->input->post('controladores');
        $this->load->model('Grupo_model');
        $this->Grupo_model->id = $grupo_id;
        $this->Grupo_model->clearRelControlador();
        if (is_array($controladores)){
            foreach ($controladores as $controlador_id) {
                $this->Grupo_model->controlador_id = $controlador_id;
                $this->Grupo_model->assocControlador();
            }
        }
        echo json_encode(array('resultado'=>true));
    }
    
    public function vista(){
        $views = array(
            'template/encabezado', 
            'template/menu',
            'permiso/vista', 
            'template/pie'
            );
        foreach ($views as $value) {
            $this->load->view($value);
        }
    }
    
    public function getPermisos(){
        $grupo_id = $this->input->post('grupo_id');
        $vista_id = $this->input->post('vista_id');
        $this->load->model('Permiso_model');
        $this->Permiso_model->grupo_id = $grupo_id;
        $this->Permiso_model->vista_id = $vista_id;
        $permisos = $this->Permiso_model->getPermisos();
        echo json_encode($permisos);
    }
    
    public function savePermiso(){
        $grupo_id = $this->input->post('grupo_id');
        $vista_id = $this->input->post('vista_id');
        $permisos = $this->input->post('permisos');
        
        $this->load->model('Permiso_model');
        $this->Permiso_model->grupo_id = $grupo_id;
        $this->Permiso_model->vista_id = $vista_id;
        $this->Permiso_model->permisos = $permisos;
        $id = $this->Permiso_model->savePermiso();
        echo json_encode(array('resultado'=>true));
    }
    
    public function getPermisosVista(){
        $usuario = $this->session->userdata('usuario');
        $metodo = $this->input->post('vista');
        if ($metodo == "" || $metodo == "index.php"){
            $metodo = $this->router->routes['default_controller'];
        }
        $this->load->model('Vista_model');
        $vista_id = $this->Vista_model->findVista($metodo);
        $this->load->model('Grupo_model');
        $this->Grupo_model->usuario_id = $usuario['id'];
        $grupos = $this->Grupo_model->getGruposUsuario();
        $this->load->model('Permiso_model');
        $this->Permiso_model->vista_id = $vista_id;
        $this->Permiso_model->grupos = $grupos;
        $permisos = $this->Permiso_model->getPermisosVista();
        echo json_encode($permisos);
    }
}