<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author Laptop
 */
require_once 'conection.php';

class db {

    //put your code here
    public $_adapter;
    public $_sql;

    function db() {
        $adapter = new conection();
        $this->_adapter = $adapter->_conect;
    }

    function select($table, $rows = array(), $where='') {
        $campos = implode(',', $rows);
        if ($campos != '') {
            $sql = 'select ' . $campos . ' from ' . $table;
        } else {
            $sql = 'select * from ' . $table;
        }
        
        $this->_sql = $sql;
        return $this;
    }
    
    function join($table,$option){
        $this->_sql = $this->_sql.' INNER JOIN '.$table.' on ('.$option.')';
        return $this;
    }
    function where ($where){
        $this->_sql = $this->_sql.' '.$where;
        return $this;
    }
    function fetchArray(){
        $resultQuery = $this->_adapter->query($this->_sql);
        while ($row = $resultQuery->fetch_array(MYSQLI_ASSOC)) {
            $result[] = $row;
        }
        $resultQuery->free();
        return $result;        
    }
    
    function insert($table, $bind) {
        foreach ($bind as $col => $vals) {
            //$cols[] = $this->_quoteIdentifier($col, true);
            $val[] = $this->_quoteIdentifier($vals, true);
        }
        $sql = 'INSERT INTO ' . $table
                . ' (' . implode(', ', array_keys($bind)) . ') '
                . 'VALUES (' . implode(', ', $val) . ')';
        return $this->_adapter->query($sql);
    }

    protected function _quoteIdentifier($value) {
        $q = $this->getQuoteIdentifierSymbol();
        return ($q . str_replace("$q", "$q$q", $value) . $q);
    }

    public function getQuoteIdentifierSymbol() {
        return '"';
    }

    function delete() {
        
    }

    function update() {
        
    }

}

?>
