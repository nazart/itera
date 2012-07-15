<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model
 *
 * @author Laptop
 */

require_once '/model.php';

class modelProductos extends model {

    function listarProductos() {
        $db = new db();
        return $this->_db->select('producto', array('IdProducto', 'NombreProducto'))->fetchArray();
        
    }

    //put your code here
}

?>
