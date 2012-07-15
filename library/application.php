<?php
session_start();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of application
 *
 * @author Laptop
 */
class application {

    //put your code here
    public $_env;
    public $_file;
    public $_vars;

    function application($env, $file) {
        $this->_file = $file;
        $this->_env = $env;
        $vars = parse_ini_file($this->_file, true);
        $this->_vars = $vars[$this->_env];
    }

    function run() {

        $page = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'inicio';
        $page = str_replace('.', '', $page);
        if (strpos($page, '?') > 0)
            $page = substr($page, 0, strpos($page, '?'));
        $resultRequest = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
        if (strpos($resultRequest, '?') > 0)
            $resultRequest = substr($resultRequest, 0, strpos($resultRequest, '?'));
        if ($resultRequest != '') {
            $param = explode('/', $resultRequest);
        } else {
            $param = array();
            //$param[0]='';
        }
        $ismoduleUri = FALSE;
        if (isset($param[0]) && $param[0] != '' && file_exists(APPLICATION_PATH . '/module/' . $param[0])) {
            $module = $param[0] == '' ? 'default' : $param[0];
            $ismoduleUri = TRUE;
            
        } else {
            $ismoduleUri = FALSE;
            $module = 'default';
        }
        
        $countParam = count($param);
        switch ($countParam) {
            case 0:
                $page = $module . '/view/index/index';
                break;
            case 1:
                $page = substr($page, 1, strlen($page));
                $page = $module . '/view/' . $page . '/index';
                break;
            case 2:
                $page = substr($page, 1, strlen($page));
                if ($ismoduleUri) {
                    $page = $module . '/view/' . $param[1] . '/index';
                } else {
                    $page = $module . '/view/' . $page;
                }
                break;
            case 3:
                if ($ismoduleUri) {
                    $page = $module . '/view/' . $param[1] . '/' . $param[2];
                } else {
                    $page = $module . '/view/' . $param[0] . '/' . $param[1];
                }
                break;
            default:
                if ($ismoduleUri) {
                    $page = $module . '/view/' . $param[1] . '/' . $param[2];
                    $counts = 3;
                } else {
                    $page = $module . '/view/' . $param[0] . '/' . $param[1];
                    $counts = 2;
                }
                $count = 1;
                for ($i = $counts; $i < $countParam; $i++) {
                    if ($count % 2 != 0) {
                        $_GET[$param[$i]] = isset($param[$i + 1]) ? $param[$i + 1] : '';
                    }
                    $count++;
                }
                break;
        }

        $file = APPLICATION_PATH . '/module/' . $page . '.php';
        $_SESSION['_vars'] = $this->_vars;
        require_once($file);
    }
}

?>
