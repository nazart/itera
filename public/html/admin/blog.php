<?php
require_once("checkaccess.php");
require_once("header.php");
	
chdir('../.');

if (!file_exists($imSettings['general']['dir']) && $imSettings['general']['dir'] != "") {
?>
	<div id="imAdminPage">
		<div id="imBody">
			<div class="imContent">
				<?php echo $l10n['blog_folder_error']; ?>
			</div>
		</div>
	</div>
<?php
} else {
	$blog = new imBlog();
	if (!isset($_POST['post_id']) && isset($_GET['post_id']))
		$_POST['post_id'] = $_GET['post_id'];
	if($_POST['post_id'] != "" && $_POST['comment_id'] != "") {
		if($_POST['approved'] != "")
			$r = $blog->approveComment($_POST['post_id'],$_POST['comment_id'],$_POST['approved']);
		else
			$r = $blog->removeComment($_POST['post_id'],$_POST['comment_id']);
	}
?>
<div id="imAdminPage">
	<div id="imBody">
		<div class="imContent">
		<?php if (count($imSettings['blog']['posts']) == 0) { ?>
		<div class="imBlogPostComment">
			<?php echo $l10n['blog_no_comment']; ?>
		</div>
		<?php } else { ?>
			<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">
				<fieldset class="imFilter">
					<input type="hidden" name="pwd" value="<?php echo $_POST['pwd']?>" />
					<select name="category_id" onchange="form.submit();">
<?php
	foreach ($imSettings['blog']['categories'] as $value) {
		$id = str_replace(" ", "_", $value);
	  if($_POST['category_id'] == "")
				$_POST['category_id'] = $id;
?>
						<option value="<?php echo $id?>"<?php echo ($_POST['category_id'] == $id ? " selected=\"selected\"" : "")?>><?php echo $value?></option>
<?php
	}
?>
					</select>
					<select name="post_id" onchange="form.submit();">
<?php
    foreach($imSettings['blog']['posts'] as $id => $value) {
    	if(str_replace(" ", "_", $value['category']) == str_replace(" ", "_", $_POST['category_id'])) {
    		if($first_post == "")
    			$first_post = $id;
    		if($_POST['post_id'] == $id)
    			$first_post = $id;
?>
						<option value="<?php echo $id?>"<?php echo ($id == $_POST['post_id'] ? " selected=\"selected\"" : "")?>><?php echo $value['title']?></option>
<?php
		}
  }
  $_POST['post_id'] = $first_post;
?>
					</select>
				</fieldset>
			</form>
<?php
    if($_POST['post_id'] != "") {
      $c = $blog->getComments($_POST['post_id']);
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
							<input type="hidden" name="category_id" value="<?php echo str_replace(" ", "_", $_POST['category_id'])?>" />
							<input type="hidden" name="post_id" value="<?php echo ($_POST['post_id'] != "" ? $_POST['post_id'] : $first_post)?>" />
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
							<input type="hidden" name="category_id" value="<?php echo str_replace(" ", "_", $_POST['category_id'])?>" />
							<input type="hidden" name="post_id" value="<?php echo ($_POST['post_id'] != "" ? $_POST['post_id'] : $first_post)?>" />
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
							<input type="hidden" name="category_id" value="<?php echo str_replace(" ", "_", $_POST['category_id'])?>" />
							<input type="hidden" name="post_id" value="<?php echo ($_POST['post_id'] != "" ? $_POST['post_id'] : $first_post)?>" />
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
				<div class="imBlogPostCommentDate"><?php echo $blog->formatTimestamp($c[$i]['timestamp'])?></div>
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
}
?>
		</div>
	</div>
</div>
<?php
	require_once("footer.php");
?>