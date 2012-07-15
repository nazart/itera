<?php
require_once('/../../controller/productos.php');
$producto = new productos();
print_r($producto->_params);
$producto->listarProductos();
?>
