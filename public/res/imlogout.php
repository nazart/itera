<?php
@require_once("x5engine.php");

$pa = new imPrivateArea();
$pa->logout();
header("Location: ../");

// End of file imlogout.php