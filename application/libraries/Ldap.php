<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap {

    public function validar($usuario,$password,$dominio) {
        
        if ($dominio == 'alfa'){
            $servidor = '172.20.7.233';
        }elseif ($dominio == 'casa') {
            return 'OK';
        } else {
            $servidor = '172.20.6.15';
        } 
        $ldaprdn =  $dominio . "\\" . $usuario;
        $ldappass = $password;
        $ldapconn = ldap_connect($servidor )
            or die("Could not connect to LDAP server.");

        if ($ldapconn)  {
            $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass);

            if (($ldapbind) and ($password != '')) {
                $estado = 'OK';
            } else {
                $estado = 'ERROR';
            }
        }
        ldap_close($ldapconn);
        return $estado;  
    }
}
