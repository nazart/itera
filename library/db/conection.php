<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of coneccion
 *
 * @author Laptop
 */

class conection {
    //put your code here
   private $_host;
   private $_bd;
   private $_user;
   private $_password;
   private $_adapter;
   public $_conect;
   
   function conection($options = array()){
       $this->setOption($_SESSION['_vars']['db']);
       switch ($this->_adapter) {
           case 'MYSQLI':
               $this->_conect = $this->adapterMysqli();
               break;
           default:
               $this->_conect = $this->adapterMysqli();
               break;
       }
       return $this->_conect;
   }
   function setOption($option){
       $this->_host = $option['host'];
       $this->_bd = $option['bd'];
       $this->_user = $option['user'];
       $this->_password = $option['password'];
   }
   function adapterMysqli(){
       $mysqli = new mysqli(
               $this->_host, 
               $this->_user, 
               $this->_password, 
               $this->_bd);
       if($mysqli->error){
           echo $mysqli->error;
           exit;
       }else {
           return $mysqli;
       }
   }
}

?>
