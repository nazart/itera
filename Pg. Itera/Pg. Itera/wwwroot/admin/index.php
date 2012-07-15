<?php
//Effettua il routing delle pagine al primo accesso in base ai file creati
if (file_exists("blog.php"))
	header("Location: blog.php");
else if (file_exists("guestbook.php"))
	header("Location: guestbook.php");
else if (file_exists("website_test.php"))
	header("Location: website_test.php");
else
	header("Location: login.php");
?>