<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it" lang="it" dir="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="it" />
	<meta http-equiv="Content-Type-Script" content="text/javascript" />
	<meta http-equiv="ImageToolbar" content="False" />
	<meta name="MSSmartTagsPreventParsing" content="True" />
	<script type="text/javascript" src="../res/jquery.js"></script>
	<script type="text/javascript" src="../res/x5engine.js"></script>
	<link rel="stylesheet" type="text/css" href="template.css" media="screen" />
  <title>WebSite X5 Manager</title>
</head>
<body>
<?php
	if (isset($logged) && $logged) {
?>
		<div id="imNavBar">
			<?php require_once("menu.php"); ?>
		</div>
<?php
	}
?>