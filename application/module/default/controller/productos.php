<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of productos
 *
 * @author Laptop
 */

require_once 'controller/controller.php';
require_once APPLICATION_PATH.'/model/productos.php';
class productos extends controller {
    function init(){
        parent::init();
    }
    function productos(){
        $this->init();
    }
    function listarProductos(){
        $productos = new modelProductos();
        $productos->listarProductos();
    }
    
}


?>
