<?php
require_once("checkaccess.php");
require_once("header.php");
?>
<div id="imAdminPage">
	<div id="imBody">
		<div class="imSectionTitle"><?php echo $l10n['admin_test_title']; ?></div>
		<div class="imContent">
			<?php
			chdir("../");
			$test = new imTest();
			$test->doTest($l10n['admin_test_version'] . ": " . PHP_VERSION, "php_version_test");
			$test->doTest($l10n['admin_test_session'], "session_test");
			$test->doTest($l10n['admin_test_folder'] . (($imSettings['general']['dir'] != null && $imSettings['general']['dir'] != "") ? " (" . $imSettings['general']['dir'] . ")" : ""), "writable_folder_test");
			if (file_exists("mail") && is_dir("mail"))
				$test->doTest($l10n['admin_test_database'], "mysql_test");
			?>
		</div>
	</div>
</div>
<?php
require_once("footer.php");
?>