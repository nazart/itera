<?php
	require_once("../res/x5engine.php");
	$login = new imPrivateArea();
	if ($login->checkAccess("admin/" . basename($_SERVER['PHP_SELF'])) !== 0) {
		if (basename($_SERVER['HTTP_REFERER']) == "login.php")
			header("Location: login.php?error");
		else
			header("Location: login.php");
	}
	else
		$logged = TRUE;

// End of file checkaccess.php