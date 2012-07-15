<?php
	require_once("../res/x5engine.php");
	$login_error = "";
	if (isset($_GET['logout'])) {
		$login = new imPrivateArea();
		$login->logout();
		@header("Location: ../");
	}
	if (isset($_GET['error']))
		$login_error = $l10n['private_area_login_error'];
	if (isset($_POST['uname']) && $_POST['uname'] != "" && isset($_POST['pwd']) && $_POST['pwd'] != "") {
		$login = new imPrivateArea();
		if ($login->login($_POST['uname'], $_POST['pwd']))
			header("Location: index.php");
		else
			$login_error = $l10n['private_area_login_error'];
	}
	require_once("header.php");
?>
<div id="imLoginPage">
	<div id="imHeader">
		<img src="logo.png" alt="WebSite X5 Admin" />
	</div>
	<div id="imBody">
		<div class="imContent">
			<div class="imBlogAdminLoginForm">
				<form action="<?php echo basename($_SERVER['PHP_SELF']) ?>" method="post">
					<?php echo ($login_error != "") ? $login_error . "<br />" : ""; ?>
					<fieldset>
						<label><?php echo $l10n['admin_home']; ?></label><br />
						Username: <input type="text" name="uname" id="uname"/>
						Password: <input type="password" name="pwd" />
						<div style="text-align: right; width: 255px; margin: 0; padding: 2px 0;"><input type="submit" value="<?php echo $l10n['blog_login'] ?>" /></div>
					</fieldset>
				</form>
				<script>
					$("#uname").focus();
				</script>
			</div>
		</div>
	</div>
</div>		
<?php
	require_once("footer.php");
?>