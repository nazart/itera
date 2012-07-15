<?php
require_once("checkaccess.php");
require_once("header.php");
?>
<div id="imAdminPage">
	<div id="imBody">
		<div class="imSectionTitle"><?php echo $l10n['admin_seo_home']; ?></div>
		<div class="imContent">
			<div style="padding: 5px">
				<?php
				
					if (isset($_GET['login'])) {
						$_SESSION['imAdmin_goog_uname'] = $_POST['email'];
						$_SESSION['imAdmin_goog_pwd'] = $_POST['pwd'];
					}
					
					if (@$_SESSION['imAdmin_goog_uname'] != "" && @$_SESSION['imAdmin_goog_pwd'] != "") {
						$goog = new imGoogle($_SESSION['imAdmin_goog_uname'],$_SESSION['imAdmin_goog_pwd'],"sitemaps");
							if ($goog->err) {
							  echo "Your server does not support this feature.";
							} else {
								if ($goog->auth === FALSE) {
									$_SESSION['imAdmin_goog_uname'] = "";
									echo "<script type=\"text/javascript\">window.location.href='seo.php?login_error'</scrip>";
								} else {
									//Sitemap
									$sitemap = $goog->getSitemap($imSettings['general']['url']);
									if ($sitemap) {
										echo "<b>" . $l10n['admin_seo_sitemap'] . ":</b><br />";
										echo "<table width=\"100%\"><tr class=\"imHead\"><td>URL</td><td>Status</td><td>Updated</td><td>URL Count</td></tr>";
										$smap = $sitemap['entry'];
										$date = date("M d Y H:i:s", strtotime($smap['wt:sitemap-last-downloaded']));
										echo "<tr><td>" . $sitemap['title'] . "</td><td>" . $smap['wt:sitemap-status'] . "</td><td>" . $date . "</td><td>" . $smap['wt:sitemap-url-count'] . "</td></tr>";
										echo "</table><br /><br />";
									}

									//Visualizzazione chiavi di ricerca
									$keywords = $goog->getKeywords($imSettings['general']['url']);
									if ($keywords && is_array($keywords['wt:keyword'])) {
										$keys = array();
										foreach ($keywords['wt:keyword'] as $key) {
											$keys[] = "<a href=\"http://www.google.com/#sclient=psy&site=&source=hp&q=site:" . urlencode($imSettings['general']['url']) . "+" . urlencode($key) . "&aq=f&aqi=&aql=&oq=&gs_rfai=&pbx=1&fp=51c17303eaadb892\" target=\"_blank\">" . $key . "</a>";
										}

										if (count($keys) > 0) {
											$date = date("M d Y H:i:s", strtotime($keywords['updated']));
											echo "<b>" . $l10n['admin_seo_keys'] . " ($date):</b><br />" . implode(" ", $keys) . "<br />";
										}
									}

									//Google Messages
									$msgs = $goog->getMessages($imSettings['general']['url']);
									if ($msgs && is_array($msgs['entry'])){
										echo "<b>" . $l10n['admin_seo_messages'] . ":</b><br />";
										foreach ($msgs['entry'] as $msg) {
											$date = date("M d Y H:i:s", strtotime($msg['wt:date']));
											echo "<table width=\"100%\"><tr><td><b>Date:</b></td><td>$date</td></tr>";
											echo "<tr><td>Subject:</td><td>" . $msg['wt:subject'] . "</td></tr>";
											echo "<tr><td colspan=\"2\">" . $msg['wt:body'] . "</td></tr></table><br /><br />";
										}
									}

									//Crawl issues
									$crawl = $goog->getCrawler($imSettings['general']['url']);
									if ($crawl && is_array($crawl['entry'])) {
										  echo "<b>" . $l10n['admin_seo_crawl_mex'] . ":</b><br />";
										  echo "<table width=\"100%\"><tr class=\"imHead\"><td>Crawl Type</td><td>Issue Type</td><td>Details</td><td>From</td><td>Date</td></tr>";
											foreach ($crawl['entry'] as $issue) {
												$date = $date = date("M d Y H:i:s", strtotime($crawl['wt:date-detected']));
												echo "<tr><td>" . $issue['wt:crawl-type'] . "</td><td>" . $issue['wt:issue-type'] . "</td><td>" . $issue['wt:issue-detail'] . "</td><td>" . $issue['wt:linked-from'] . "</td><td>" . $date . "</td></tr>";
											}
											echo "</table>";
									}
								}
						}
					} else {
						if (isset($_GET['login_error']))
							echo $l10n['private_area_login_error'];
							
						echo "<b>" . $l10n['admin_seo_auth'] . "</b>:<br />";
					?>
					<form action="<?php echo basename($_SERVER['PHP_SELF']); ?>?login" method="post">
						<table class="no-border" align="center">
							<tr>
						 		<td>Email:</td>
								<td><input type="text" name="email" /></td>
							</tr>
							<tr>
						 		<td>Password:</td>
								<td><input type="password" name="pwd" /></td>
							</tr>
							<tr align="center">
								<td colspan="2"><input type="submit" value="Login"></td>
							</tr>
						</table>
					</form>
					<?php
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php
require_once("footer.php");
?>