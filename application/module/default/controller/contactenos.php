<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of contactenos
 *
 * @author Laptop
 */
require_once 'controller/controller.php';
class contactenos extends controller {
    function init(){
        parent::init();
    }
    function contactenos(){
        $this->init();
    }
    
    function validarContacto(){
        require_once '/validator/required.php';
        require_once '/validator/formatEmail.php';
        $validator = new validator();
        $params = $this->_params;
        $validatorRequired = new formatEmail('Email',$params['Email']);
        $validator->addValidator($validatorRequired);
        $validatorRequired = new required('Email',$params['Email']);
        $validator->addValidator($validatorRequired);
        $validatorRequired = new required('Nombres',$params['Nombres']);
        $validator->addValidator($validatorRequired);
        $validatorRequired = new required('Empresa',$params['Empresa']);
        $validator->addValidator($validatorRequired);
        $validatorRequired = new required('Telefono',$params['Telefono']);
        $validator->addValidator($validatorRequired);
        $validator->isValid();
        return $validator->_error;
    }
    
    function registrarContacto(){
        require_once APPLICATION_PATH.'/model/modelContacto.php';
        require_once APPLICATION_PATH.'/model/modelArea.php';
        $contacto = new modelContacto();
        $area = new modelArea();
        $params = $this->_params;
        $dato['NombresContacto'] = $params['Nombres'];
        $dato['Email'] = $params['Email'];
        $dato['TelefonoContacto'] = $params['Telefono'];
        $dato['FechaRegistro'] = date('Y-m-d H:s:i');
        $dato['Ip'] = $_SERVER['REMOTE_ADDR'];
        if($contacto->insertContacto($dato)){
            $this->flashMessage('mensajeContacto')->addMessage('El usuario se registro correctamente');
            $area->listarUsuariosEnvioCorreoArea($params['IdProducto']);
        }else{
            return false;
        }
    }
    function listarProductos(){
        require_once APPLICATION_PATH.'/model/modelProductos.php';
        $productos = new modelProductos();
        return $productos->listarProductos();
    }
    //put your code here
}

?>
