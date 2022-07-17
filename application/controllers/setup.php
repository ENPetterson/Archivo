<?php
class Setup extends CI_Controller {

    function index() {
 
        /*
        //Eliminando las tablas
        
        $tables = array('usuario', 'grupo', 'menu', 'grupo_usuario', 'grupo_menu','controlador', 
            'controlador_grupo', 'estadoorden');

        R::exec('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $value) {
            R::exec("drop table if exists {$value}");
        }
        R::exec('SET FOREIGN_KEY_CHECKS = 1;');
        
        //Creando los objetos
        $this->load->model('Usuario_model');
        $this->Usuario_model->nombreUsuario = "jleis";
        $this->Usuario_model->nombre = "Javier";
        $this->Usuario_model->apellido = "Leis";
        $this->Usuario_model->email = "jleis@allaria.com.ar";
        $usuario_id = $this->Usuario_model->saveUsuario();

        echo "Dado de alta el usuario con id {$usuario_id} <br />";

        $this->load->model('grupo_model');
        $this->grupo_model->nombre = 'Administradores';
        $this->grupo_model->id = 0;
        $grupo_id = $this->grupo_model->saveGrupo();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->usuario_id = $usuario_id;
        $this->grupo_model->assocUsuario();

        echo "Dado de alta el grupo con el id {$grupo_id} <br />";

        $padre_id = -1;

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Ordenes';
        $this->menu_model->accion = null;
        $menu_id = $this->menu_model->saveMenu();
        $padre_id = $menu_id;

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Lebac';
        $this->menu_model->accion = 'lebac';
        $menu_id = $this->menu_model->saveMenu();
        $padre_id = $menu_id;

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";
        
        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Procesar';
        $this->menu_model->accion = 'lebac/procesar';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Cierres';
        $this->menu_model->accion = 'lebac/cierre';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";
        
        
        
        $padre_id = -1;

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Seguridad';
        $this->menu_model->accion = null;
        $menu_id = $this->menu_model->saveMenu();
        $padre_id = $menu_id;

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";


        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Usuarios';
        $this->menu_model->accion = 'usuarioPublico';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->id = 0;
        $this->menu_model->nombre = 'Grupos';
        $this->menu_model->accion = 'grupo';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->id = 0;
        $this->menu_model->nombre = 'Menues';
        $this->menu_model->accion = 'menu';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Controladores';
        $this->menu_model->accion = 'controlador';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Vistas';
        $this->menu_model->accion = 'vista';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";
        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Permisos';
        $this->menu_model->accion = null;
        $menu_id = $this->menu_model->saveMenu();
        $padre_id = $menu_id;

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Menues';
        $this->menu_model->accion = 'permiso/menu';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Controladores';
        $this->menu_model->accion = 'permiso/controlador';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";

        $this->load->model('menu_model');
        $this->menu_model->id = 0;
        $this->menu_model->padre_id = $padre_id;
        $this->menu_model->nombre = 'Vistas';
        $this->menu_model->accion = 'permiso/vista';
        $menu_id = $this->menu_model->saveMenu();

        $this->grupo_model->id = $grupo_id;
        $this->grupo_model->menu_id = $menu_id;
        $this->grupo_model->assocMenu();

        echo "Dado de alta el menu con el id {$menu_id} <br />";
        
        $controladores = array('*', 'grupo', 'menu', 'permiso', 'principal', 'usuarioPublico', 'vista',
            'controlador');

        foreach ($controladores as $controlador) {
            
            echo "Controlador : $controlador <br>";
            
            $this->load->model('Controlador_model');
            $this->Controlador_model->id = 0;
            $this->Controlador_model->nombre = $controlador;
            $controlador_id = $this->Controlador_model->saveControlador();
            
            echo "Dado de alta el controlador con el id {$controlador_id} <br>";

            $this->load->model('Grupo_model');
            $this->Grupo_model->id = $grupo_id;
            $this->Grupo_model->controlador_id = $controlador_id;
            $this->Grupo_model->assocControlador();
        }
        
        $this->load->model('Estadoorden_model');
        $this->Estadoorden_model->id = 0;
        $this->Estadoorden_model->estado = 'Pendiente';
        $estadoOrden = $this->Estadoorden_model->saveEstadoOrden();
        
        echo "Dado de alta el Estado de Orden con el id {$estadoOrden['id']} <br>";
        
        $this->Estadoorden_model->id = 0;
        $this->Estadoorden_model->estado = 'Enviada';
        $estadoOrden = $this->Estadoorden_model->saveEstadoOrden();
        
        echo "Dado de alta el Estado de Orden con el id {$estadoOrden['id']} <br>";
        
        $this->Estadoorden_model->id = 0;
        $this->Estadoorden_model->estado = 'Procesada';
        $estadoOrden = $this->Estadoorden_model->saveEstadoOrden();
        
        echo "Dado de alta el Estado de Orden con el id {$estadoOrden['id']} <br>";
         * 
         */
        
        $cierres = R::find('cierre', 'plazospesos is not null');
        foreach ($cierres as $cierre) {
            $plazosPesos = explode(',', $cierre->plazospesos);
            $especiesPesos = explode(',', $cierre->especiespesos);
            $plazosDolares = explode(',', $cierre->plazosdolares);
            $especiesDolares = explode(',', $cierre->especiesdolares);
            $segmentosDolares = explode(',', $cierre->segmentosdolares);
            
            $colocacion = 100;
            
            foreach ($plazosPesos as $indice => $plazoPesos) {
                $plazo = R::dispense('plazo');
                $plazo->moneda = '$';
                $plazo->plazo = $plazoPesos;
                $plazo->especie = $especiesPesos[$indice];
                $plazo->colocacion = $colocacion;
                $plazo->tituloC = 'Lebac Vto. ' . $plazoPesos . ' $ Competitivo';
                $plazo->tituloNCJ = 'Lebac Vto. ' . $plazoPesos . ' $ No Competitivo Juridica';
                $plazo->tituloNCF = 'Lebac Vto. ' . $plazoPesos . ' $ No Competitivo Fisica';
                $plazo->cierre = $cierre;
                R::store($plazo);
                
                $colocacion++;
            }
            
            foreach ($plazosDolares as $indice => $plazoDolar) {
                $plazo = R::dispense('plazo');
                $plazo->moneda = 'u$s';
                $plazo->plazo = $plazoDolar;
                $plazo->especie = $especiesDolares[$indice];
                $plazo->segmento = $segmentosDolares[$indice];
                $plazo->colocacion = $colocacion;
                $plazo->tituloC = 'Lebac Vto. ' . $plazoDolar . ' u$s Competitivo';
                $plazo->tituloNCJ = 'Lebac Vto. ' . $plazoDolar . ' u$s No Competitivo Juridica';
                $plazo->tituloNCF = 'Lebac Vto. ' . $plazoDolar . ' u$s No Competitivo Fisica';
                $plazo->cierre = $cierre;
                R::store($plazo);
                
                $colocacion++;
            }
        }
        
        
    }
    

}
