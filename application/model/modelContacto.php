<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of modelContacto
 *
 * @author Laptop
 */

require_once '/model.php';

class modelContacto extends model {
    
    function insertContacto($param){
        
        return $this->_db->insert('contacto',$param);
    }
    //put your code here
}

?>
