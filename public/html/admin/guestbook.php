<?php
require_once("checkaccess.php");
require_once("header.php");

chdir('../.');

$guestbook_ids = array();
$guestbook_files = array();

// Load the files of the guestbook
if (count($imSettings['guestbook']['public_folders']) > 0) {
	foreach ($imSettings['guestbook']['public_folders'] as $folder) {
	  if ($dh = @opendir($folder)) {
	      if ($folder == "./.")
	        $folder = "";
				else
					$folder .= "/";
	      while (($file = @readdir($dh)) !== false) {
					if (!is_dir($folder . $file) && preg_match("/^gb([0-9a-z]+)$/", $file, $out) > 0) {
	        	$guestbook_ids[] = $out[1];
	        	$guestbook_files[$out[1]] = $folder;
				 	}
	      }
	      @closedir($dh);
	  }
  }
}

if (count($guestbook_ids) == 0) {
?>
<div id="imAdminPage">
	<div id="imBody">
		<div class="imContent">
  			<div class="imBlogPostComment">
					<?php echo $l10n['blog_no_comment']; ?>
				</div>
		</div>
	</div>
</div>
<?php
} else {
if($_POST['guestbook_id'] != "") {
  $guestbook = new imGuestBook($guestbook_files[$_POST['guestbook_id']]);
	if($_POST['approved'] != "")
		$r = $guestbook->approveComment($_POST['guestbook_id'],$_POST['comment_id'],$_POST['approved']);
	else
		$r = $guestbook->removeComment($_POST['guestbook_id'],$_POST['comment_id']);
}
?>
<div id="imAdminPage">
	<div id="imBody">
		<div class="imContent">
			<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">
				<fieldset class="imFilter">
					<input type="hidden" name="pwd" value="<?php echo $_POST['pwd']?>" />
					<select name="guestbook_id" onchange="form.submit();">
<?php
	if($_POST['guestbook_id'] == "")
			$_POST['guestbook_id'] = $guestbook_ids[0];
	foreach ($guestbook_ids as $id) {
?>
						<option value="<?php echo $id?>"<?php echo ($_POST['guestbook_id'] == $id ? " selected=\"selected\"" : "")?>><?php echo $id?></option>
<?php
	}
?>
					</select>
				</fieldset>
			</form>
<?php
    if($_POST['guestbook_id'] != "") {
      $guestbook = new imGuestBook($guestbook_files[$_POST['guestbook_id']]);
      $c = $guestbook->getComments($_POST['guestbook_id']);
      if (count($c) > 0) {
      	for($i = count($c)-1;$i >= 0 && $c != -1; $i--) {
?>
			<div class="imBlogPostComment">
				<?php
					if ($c[$i]['abuse'] == "1") {
						echo "<div class=\"imBlogAbuse\">" . $l10n['admin_comment_abuse'] . "</div>";
					}
				?>
				<div class="imBlogPostCommentAction">
					<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return confirm('<?php echo $l10n['blog_delete_question']?>')">
						<fieldset>
							<input type="hidden" name="pwd" value="<?php echo $_POST['pwd']?>" />
							<input type="hidden" name="guestbook_id" value="<?php echo $_POST['guestbook_id']?>" />
							<input type="hidden" name="comment_id" value="<?php echo $i+1?>" />
							<input type="submit" value="<?php echo $l10n['blog_delete']?>" />
						</fieldset>
					</form>
<?php
				if($c[$i]['approved'] == 0) {
?>
					<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return confirm('<?php echo $l10n['blog_approve_question']?>')">
						<fieldset>
							<input type="hidden" name="pwd" value="<?php echo $_POST['pwd']?>" />
							<input type="hidden" name="guestbook_id" value="<?php echo $_POST['guestbook_id']?>" />
							<input type="hidden" name="comment_id" value="<?php echo $i+1?>" />
							<input type="hidden" name="approved" value="1" />
							<input type="submit" class="imBlogPostCommentActionApprove" value="<?php echo $l10n['blog_approve']?>" />
						</fieldset>
					</form>
<?php
				}
				else {
?>
					<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post" onsubmit="return confirm('<?php echo $l10n['blog_unapprove_question']?>')">
						<fieldset>
							<input type="hidden" name="pwd" value="<?php echo $_POST['pwd']?>" />
							<input type="hidden" name="guestbook_id" value="<?php echo $_POST['guestbook_id']?>" />
							<input type="hidden" name="comment_id" value="<?php echo $i+1?>" />
							<input type="hidden" name="approved" value="0" />
							<input type="submit" value="<?php echo $l10n['blog_unapprove']?>" />
						</fieldset>
					</form>
<?php
				}
?>
				</div>
				<div class="imBlogPostCommentUser"><?php echo (stristr($c[$i]['url'],"http") ? "<a href=\"" . $c[$i]['url'] . "\" target=\"_blank\">" . $c[$i]['name'] . "</a>" : $c[$i]['name']) . " (" . $c[$i]['email'] . ")"?></div>
				<div class="imBlogPostCommentBody"><?php echo $c[$i]['body']?></div>
				<div class="imBlogPostCommentDate"><?php echo $guestbook->formatTimestamp($c[$i]['timestamp'])?></div>
			</div>
<?php
      }
    } else {
?>
<div class="imBlogPostComment">
	<?php echo $l10n['blog_no_comment']; ?>
</div>
<?php
		}
  }
}
?>
		</div>
	</div>
</div>
<?php
	require_once("footer.php");
?>