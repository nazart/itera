<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of validator
 *
 * @author Laptop
 */
class validator {
    protected $_message;
    protected $_value;
    public $_values;
    public $_error;
    protected $_result;
    protected $_param;
    private $_validators;
    
    function validator(){
        $this->_validators = array();
    }
    function addValidator($validator) {
        if(is_object($validator)){
            $this->_validators[]=$validator;
        }
    }
    
    function isValid(){
        $result = true;
        $error = array();
        $value = array();
        foreach($this->_validators as $index){
            if(is_object($index)){
                if(!$index->isValid()){
                    $error[$index->_param] = array('message'=>$index->_message,'value'=>$index->_value);
                }
                $value[$index->_param] = $index->_value;
                $this->_values = $value;
            }
        }
        
        $this->_error = $error;
        if(empty($this->_error)){
            $result = false;
        }
        return $result;
    }
    //put your code here
}

?>
