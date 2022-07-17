<?php


class Usuario_model extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    public $id;
    public $nombreUsuario;
    public $dominio;
    public $nombre;
    public $apellido;
    public $email;
    public $grupos;
    
    
    public function saveUsuario(){
        $usuario = R::load('usuario', $this->id);
        $usuario->nombreUsuario = $this->nombreUsuario;
        $usuario->dominio = $this->dominio;
        $usuario->nombre = $this->nombre;
        $usuario->apellido = $this->apellido;
        $usuario->email = $this->email;
        $this->id =R::store($usuario);
        
        R::clearRelations($usuario, 'grupo');
       
        if (is_array($this->grupos)){
            foreach ($this->grupos as $grupo) {
                $this->load->model('Grupo_model');
                $this->Grupo_model->id = $grupo;
                $this->Grupo_model->usuario_id = $this->id;
                $this->Grupo_model->assocUsuario();
            }
        }
        return $this->id;
    }
    
    public function getUsuario(){
        $usuario = R::load('usuario', $this->id);
        $resultado = $usuario->export();
        $grupos = R::getCol('select grupo_id from grupo_usuario where usuario_id = ?',
                array($this->id));
        $resultado['grupos'] = $grupos;
        return $resultado;
    }
    
    public function getUsuarios(){
        $usuarios = R::getAll("select * from v_usuario order by nombre");
        return $usuarios;
    }
    
    public function delUsuario(){
        $usuario = R::load('usuario', $this->id);
        R::trash($usuario);
    }    
    
    public function validarUsuario($nombreUsuario, $clave, $dominio){


        //Carga la libreria ldap
        //Carga un helper llamado coockie       
        //$this->load->library('ldap');
        //$this->load->helper('cookie');


        //Y acá te dice que se vá a esta librería ldap y usa una funciona de ahí que se llama "validar"
        //Se va a esa función que está en libraries/ldap funcion validar
        //Y se fija en el dominio, si ese user va con esa pass y con ese dominio, ok?
        //Entonces comebntas ese if, y te vás directo al else como si esa pate hubiera dado bien.
        //No era dificil

        //Pero no me habias pedido que lo haga si

        //Te dije que te fijes de hacerlo andar
        //No sos un robot XD XD

        //Vás y lo hacés andar
        //Ok

        //if( $this->ldap->validar($nombreUsuario,$clave,$dominio) == "ERROR"){
        //    return array('resultado'=>'Usuario o Contraseña Invalida');
        //} else {
            $usuario = R::findOne('usuario', 'nombreUsuario = ?', 
                    array($nombreUsuario));
            if (is_null($usuario)){
                return array('resultado'=>'Usuario no encontrado, por favor comuniquese a sistemas');
            } else {
                $this->session->set_userdata('usuario', $usuario->export());
                $this->load->model('Controlador_model');
                $controladores = $this->Controlador_model->getControladores();
                $this->session->set_userdata('controlador', $controladores);
                //$cookie =  array('name'=>'dominio',
                //   'value'=>$dominio,
                //   'expire' => time() + (10 * 365 * 24 * 60 * 60)
                //   );
                //set_cookie($cookie);
                return array('resultado'=>'OK');
            }
        //}
    }
    
}
