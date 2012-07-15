<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of requeri
 *
 * @author Laptop
 */
require_once 'validator.php';


class required extends validator {

    function required($param,$value){
        $this->_value = $value;
        $this->_param = $param;
        $this->_result = true;
        if($this->_value == ''){
            $this->_message = 'el valor de '.$param.' es requerido';
            $this->_result = false;
        }
    }
    function isValid() {
        return $this->_result;
    }
    
    
}

?>
