<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of flashMesagee
 *
 * @author Laptop
 */
require_once 'helper.php';

class flashMessage extends helper {

    public $_message;
    public $_nameFlash;

    function flashMessage($name) {
        $this->_nameFlash = $name;
    }

    function addMessage($message) {
        $this->_message[] = $message;
        $_SESSION['_flasMessage'][$this->_nameFlash] = $this->_message;
    }

    function getMessage() {
        if (isset($_SESSION['_flasMessage'][$this->_nameFlash])) {
            $this->_message = $_SESSION['_flasMessage'][$this->_nameFlash];
            unset($_SESSION['_flasMessage'][$this->_nameFlash]);
            return $this->_message;
        } else {
            return array();
        }
    }

}

?>
