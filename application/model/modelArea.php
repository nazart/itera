<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of modelArea
 *
 * @author Laptop
 */
require_once '/model.php';
class modelArea extends model {
    //put your code here
    function listarUsuariosEnvioCorreoArea($idProducto){
        
        return $this->_db
                ->select('area',array('NombreUsuario','Correo','NombreArea','NombreProducto'))
                ->join('usuarioareacorreocontactenos',
                        'area.IdArea = usuarioareacorreocontactenos.IdArea')
                ->join('usuariosistema',
                        'usuarioareacorreocontactenos.IdUsuario = usuariosistema.IdUsuario')
                ->join('producto',
                        'producto.IdArea = area.IdArea')
                ->where('where IdProducto = '.$idProducto)
                ->fetchArray();
        
    }
}

?>
