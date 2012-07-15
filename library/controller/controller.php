<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controlller
 *
 * @author Laptop
 */
require_once 'helper/flashMessage.php';
class controller {
    //put your code here
    public $_params;
    
    public function getNameModule(){
        
    }
    
    public function getNameController(){
        
    }
    
    function init(){
        $this->_params = array_merge($_GET,$_POST);
    }
    function isPost(){
        if($_SERVER['REQUEST_METHOD']=='POST'){
            return true;
        }
        return false;
    }
    function flashMessage($name){
        return new flashMessage($name);
    }
    function _redirect($uri){
        header('Location: '.$uri);
        exit;
    }
}

?>
