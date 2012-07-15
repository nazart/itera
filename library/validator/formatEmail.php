<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of formatEmail
 *
 * @author Laptop
 */
class formatEmail extends validator {

    function formatEmail($param,$value){
        $this->_value = $value;
        $this->_param = $param;
        $this->_result = true;
        $pattern ='/^[^0-9][a-zA-A0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-A]{2,4}$/';
        if(!preg_match($pattern, $this->_value)){
            $this->_message = 'el formato de '.$param.' no es el adecuado';
            $this->_result = false;
        }
    }
    function isValid() {
        return $this->_result;
    }
}

?>
