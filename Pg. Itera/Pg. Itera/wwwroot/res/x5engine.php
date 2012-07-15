<?php
/**
 * This file contains all the classes used by the PHP code created by WebSite X5
 * @author Incomedia Srl (http://www.incomedia.eu)
 * @version 1.0
 */

@session_start();

$imSettings = Array();
$l10n = Array();
$phpError = false;

@include("imemail.inc.php");		// Email class - Static
@include("x5settings.php");			// Basic settings - Dynamically created by WebSite X5
@include("blog.inc.php");			// Blog data - Dynamically created by WebSite X5
@include("cart.inc.php");			// E-commerce cart data - Dynamically created by WebSite X5
@include("access.inc.php");			// Private area data - Dynamically created by WebSite X5
@include("l10n.php");				// Localizations - Dynamically created by WebSite X5
@include("search.inc.php");			// Search engine data - Dynamically created by WebSite X5

/**
 * Contains the methods used to format and send emails
 * @access public
 */
class imSendEmail {

	/**
	 * Send the E-commerce cart order/email
	 * @access public
	 * @param post The data sent by the form of the e-commerce cart at step 4 (array)
	 */
	function sendCartEmail($post_data) {

		global $imSettings;
		global $l10n;

		$separationLine = "<tr><td colspan=\"2\" style=\"margin: 10px 0; height: 10px; font-size: 0.1px; border-bottom: 1px solid " . $imSettings['email']['email_background'] . ";\">&nbsp;</td></tr>\n";
		$PayMsg = $imSettings['cart']['confirmationEmail'];
		$ownerEmail = $imSettings['cart']['owner_email'];
		$imOpt = 0;
		$imVat = 0;
		$imOrderNo = $post_data["order_no"];
		$imUserData = $post_data["form"];
		$imShippingDataTxt = "";
		$imShippingDataHtml = "";
		$imUserDataTxt = "";
		$imUserDataHtml = "";
		$imUserDataCSVH = "";
		$imUserDataCSV = "";

		$i = 0;
		if(is_array($imUserData)) {
			foreach($imUserData as $key => $value) {
				// Is it an email?
				if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $value['imValue'])) {
					$f = "<td><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['name']) . ":</b></td><td><a href=\"mailto:" . $value['imValue'] . "\">". $value['imValue'] . "</a></td>";
				} else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $value['imValue'])) {
					// Is it an URL?
					$f = "<td><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['name']) . ":</b></td><td><a href=\"" . $value['imValue'] . "\">". $value['imValue'] . "</a></td>";
				} else {
					$f = "<td><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['name']) . ":</b></td><td>" . str_replace(array("\\'", '\\"'), array("'", '"'), $value['imValue']) . "</td>";
				}
				
				if (substr($key, -strlen("_shipping")) == "_shipping") {
					$imShippingDataTxt .= $value['name'] . ": " . $value['imValue'] . "\n";
					$imShippingDataHtml .= "\n\t\t\t\t<tr" . ($i%2 ? " bgcolor=\"" . $imSettings['email']['body_background_odd'] . "\"" : "") . ">" . $f . "</tr>";
					$imShippingDataCSVH[] = $value['name'];
					$imShippingDataCSV[] = $value['imValue'];
				} else {
					$imUserDataTxt .= $value['name'] . ": " . $value['imValue'] . "\n";
					$imUserDataHtml .= "\n\t\t\t\t<tr" . ($i%2 ? " bgcolor=\"" . $imSettings['email']['body_background_odd'] . "\"" : "") . ">" . $f . "</tr>";
					$imUserDataCSVH[] = $value['name'];
					$imUserDataCSV[] = $value['imValue'];
				}
				$i++;
			}
			if ($imUserDataHtml != "")
				$imUserDataHtml = "<table width=\"100%\" style=\"font: inherit;\">" . $imUserDataHtml . "</table>";

			if ($imShippingDataHtml != "")
				$imShippingDataHtml = "<table width=\"100%\" style=\"font: inherit;\">" . $imShippingDataHtml . "</table>";
		}
		$imUserDataCSV = @implode(";",$imUserDataCSVH) . "\n" . @implode(";",$imUserDataCSV);
		$imShippingDataCSV = @implode(";",$imShippingDataCSVH) . "\n" . @implode(";",$imShippingDataCSV);

		$imOrderData = $post_data["products"];
		$imOrderDataTxt = "";
		$imOrderDataHTML = "";
		$imOrderDataCSV = "";
		$i = 0;
		if(is_array($imOrderData)) {
		   foreach($imOrderData as $p) {
			  if($p["option"] != "null")
				$imOpt = 1;
			  if ($p["vat"] != "null" && $p["vat"] != 0)
				$imVat = 1;
		   }
		   $colspan = 3 + $imOpt + $imVat;
		   $imOrderDataHTML = "<table cellpadding=\"5\" width=\"100%\" style=\"font: inherit; border-collapse: collapse;\"><tr bgcolor=\"" . $imSettings['email']['body_background_odd'] . "\"><td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\"><b>" . $l10n["cart_name"] . "</b></td>" . ($imOpt ? "<td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\"><b>" . $l10n["product_option"] . "</b></td>" : "") . "<td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\"><b>" . $l10n["cart_qty"] . "</b></td><td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\"><b>" . $l10n["cart_price"] . "</b></td>" . ($imVat ? "<td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\"><b>" . $l10n["cart_VAT"] ."</b></td>" : "") . "<td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; width: " . $priceFieldWidth . "\"><b>" . $l10n["cart_subtot"] . "</b></td></tr>\n";
		   $imOrderDataCSV = $l10n["cart_name"] . ";" . $l10n["cart_descr"] . ";" . ($imOpt ? $l10n["product_option"] . ";" : "") . $l10n["cart_qty"] . ";" . $l10n["cart_price"] . ";" . ($imVat ? $l10n["cart_vat"] .";" : "") . $l10n["cart_subtot"];
		   foreach($imOrderData as $od) {
			$imOrderDataCSV .= "\n" . strip_tags(str_replace(array("\n", "\r"), "", $od["name"])) . ";" . strip_tags(str_replace(array("\n", "\r"), "", $od["description"])) . ";" . (($imOpt && $od["option"] != "null") ? $this->restoreSpecialChars(urldecode($od["option"])) . ";" : "") . $od["quantity"] . ";" . $od["single_price"] . ";" . ($imVat ? $od["price_vat"] .";" : "") . $od["price_vat"];
			$imOrderDataTxt .= strip_tags(str_replace(array("\n", "\r"), "", $od["name"])) . " - " . strip_tags(str_replace(array("\n", "\r"), "", $od["description"])) . (($imOpt && $od["option"] != "null") ? " " . $this->restoreSpecialChars(urldecode($od["option"])) . ";" : "") . "\n " . $od["quantity"] . " x " . $od["single_price"] . " " . ($imVat ? "+ " . $l10n["cart_vat"] . " " . $od["vat_f"] : "") . " = " . $od["price_vat"] . "\n\n";
			$imOrderDataHTML .= "\n\t\t\t\t<tr valign=\"top\" style=\"vertical-align: top\"" . ($i%2 ? " bgcolor=\"#EEEEEE\"" : "") . "><td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\">" . $od["name"] . "<br />" . $od["description"] . "</td>" . ($imOpt ? "<td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . ";\">" . (($od["option"] != "null") ? $this->restoreSpecialChars(urldecode($od["option"])) : "") . "</td>" : "") . "<td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $od["quantity"] . "</td><td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $od["single_price"] . "</td>" . ($imVat ? "<td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . (($od["vat"] != "null") ? $od["vat"]*100 . "% / " . $od["vat_f"] : "") ."</td>" : "") . "<td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right; width: " . $priceFieldWidth . "\">" . $od["price_vat"] . "</td></tr>\n";
			$i++;
		   }

			// Payment Price

			if (isset($post_data['payment']['price']) && $post_data['payment']['price'] != null && $post_data['payment']['price'] != "null") {
				$imOrderDataHTML .= "<tr><td colspan=\"" . $colspan . "\"  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $l10n['cart_payment'] . ": " . $post_data['payment']['name'] . "</td><td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $post_data['payment']['price'] . "</td></tr>";
				$imOrderDataTxt .= "\n" . $l10n['cart_payment'] . " - " . $post_data['payment']['name'] . ": " . $post_data['payment']['price'];
			}
			if (isset($post_data['shipping']['price']) && $post_data['shipping']['price'] != null && $post_data['shipping']['price'] != "null") {
				$imOrderDataHTML .= "<tr><td colspan=\"" . $colspan . "\"  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $l10n['cart_shipping'] . ": " . $post_data['shipping']['name'] . "</td><td  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $post_data['shipping']['price'] . "</td></tr>";
				$imOrderDataTxt .= "\n" . $l10n['cart_shipping'] . " - " . $post_data['shipping']['name'] . ": " . $post_data['shipping']['price'];
			}

			$imOrderDataHTML .= "<tr><td colspan=\"" . $colspan . "\"  style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $l10n['cart_total_vat'] . "</td><td style=\"border: 1px solid " . $imSettings['email']['body_background_border'] . "; text-align: right;\">" . $post_data['total'] . "</td></tr>";
			$imOrderDataTxt .= "\n" . $l10n['cart_total_vat']  . ": " . $post_data['total'];

			$imOrderDataHTML .= "</table>";
		}

		$htmlMsg = $imSettings['email']['header'];

		$htmlMsg .= "<table border=0 width=\"100%\" style=\"font: inherit;\">\n";
		// Opening message
		$htmlMsg .= "<tr><td colspan=\"2\">" . $imSettings['cart']['email_opening'] . "</td></tr>\n";
		$txtMsg = str_replace("<br />", "\n", $imSettings['cart']['email_opening']);

		// Order number
		$htmlMsg .= "<tr><td colspan=\"2\" style=\"text-align: center; font-weight: bold;\">" . $l10n['cart_order_no'] . ": " . $imOrderNo . "</td></tr>\n";
		$txtMsg .= "\n\n" . str_replace("<br />", "\n", $l10n['cart_order_no'] . ": " . $imOrderNo);

		// Vat data
		if ($imShippingDataHtml != "") {
			$htmlMsg .= "<tr style=\"vertical-align: top\" valign=\"top\"><td style=\"width: 50%; padding: 20px 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_vat_address'] . "</h3>" . $imUserDataHtml . "</td>";
			$htmlMsg .= "<td style=\"width: 50%; padding: 20px 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_shipping_address'] . "</h3>" . $imShippingDataHtml . "</td></tr>";

			$txtMsg .= "\n" . str_replace("<br />", "\n", $l10n['cart_vat_address'] . "\n" . $imUserDataTxt);
			$txtMsg .= "\n" . str_replace("<br />", "\n", $l10n['cart_shipping_address'] . "\n" . $imShippingDataTxt );
		} else {
			$htmlMsg .= "<tr><td colspan=\"2\" style=\"padding: 20px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_vat_address'] . "/" . $l10n['cart_shipping_address'] . "</h3>" . $imUserDataHtml . "</td></tr>";
			$txtMsg .= "\n" . str_replace("<br />", "\n", $l10n['cart_vat_address'] . "/" . $l10n['cart_shipping_address'] . "\n" . $imUserDataTxt);
		}

		$htmlMsg .= $separationLine;

		// Products
		$htmlMsg .=  "<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_product_list'] . "</h3>" . $imOrderDataHTML . "</td></tr>";
		$txtMsg .= "\n\n" . str_replace("<br />", "\n", $l10n['cart_product_list'] . "\n" . $imOrderDataTxt);

		$htmlMsg .= $separationLine;

		// Payment
		$htmlMsg .= "<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_payment'] . "</h3>" . str_replace(array("\\'", '\\"'), array("'", '"'), str_replace("\\\"", "\"",  preg_replace('/[\n\r\t]*/', "", nl2br($post_data['payment']['email']))));
		if ($post_data['payment']['html'] != null && $post_data['payment']['html'] != "" && $post_data['payment']['html'] != "null")
			$htmlMsg .= "<br /><br /><div style=\"text-align: center;\">" . str_replace("\\\"", "\"", $post_data['payment']['html']) . "</div>";
		$htmlMsg .= "</td></tr>";
		$txtMsg .= "\n\n" . str_replace("<br />", "\n", $l10n['cart_payment'] . "\n" . str_replace(array("\\'", '\\"'), array("'", '"'), str_replace("\\\"", "\"",  $post_data['payment']['email'])));

		$htmlMsg .= $separationLine;

		// Shipping
		$htmlMsg .= "<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_shipping'] . "</h3>" . str_replace(array("\\'", '\\"'), array("'", '"'), str_replace("\\\"", "\"",  preg_replace('/[\n\r\t]*/', "", nl2br($post_data['shipping']['email'])))) . "</td></tr>";
		$txtMsg .= "\n\n" . str_replace("<br />", "\n", $l10n['cart_shipping'] . "\n" . str_replace(array("\\'", '\\"'), array("'", '"'), str_replace("\\\"", "\"",  $post_data['shipping']['email'])));

		// Closing message
		$htmlMsg .= $separationLine;
		$htmlMsg .= "<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\">" . str_replace(array("\\'", '\\"'), array("'", '"'), str_replace("\\\"", "\"",  $imSettings['cart']['email_closing'])) . "</td></tr>\n";
		$txtMsg .= "\n\n" . str_replace(array("\\'", "\\\"", "<br />", "<br>"), array("'", "\"", "\n", "\n"), $imSettings['cart']['email_closing']);

		$htmlMsg .= "</table>\n";

		$htmlMsg .= $imSettings['email']['footer'];

		//Send email to user
		$oEmail = new imEMail($ownerEmail,$post_data["form"]["imCartEmail"]["imValue"],$l10n['cart_order_no'] . " " . $imOrderNo,"utf-8");
		$oEmail->setText($txtMsg);
		$oEmail->setHTML($htmlMsg);
		$oEmail->send();

		//Send email to owner
		$txtMsg = "";
		$htmlMsg = $imSettings['email']['header'];
		$htmlMsg .= "<table border=0 width=\"100%\" style=\"font: inherit;\">\n";
		// Order number
		$htmlMsg .= "<tr><td colspan=\"2\" style=\"text-align: center; font-weight: bold;\">" . $l10n['cart_order_no'] . ": " . $imOrderNo . "</td></tr>\n";
		$txtMsg .= "\n\n" . str_replace("<br />", "\n", $l10n['cart_order_no'] . ": " . $imOrderNo);

		// Vat data
		if ($imShippingDataHtml != "") {
			$htmlMsg .= "<tr style=\"vertical-align: top\" valign=\"top\"><td style=\"width: 50%; padding: 20px 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_vat_address'] . "</h3>" . $imUserDataHtml . "</td>";
			$htmlMsg .= "<td style=\"width: 50%; padding: 20px 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_shipping_address'] . "</h3>" . $imShippingDataHtml . "</td></tr>";

			$txtMsg .= "\n" . str_replace("<br />", "\n", $l10n['cart_vat_address'] . "\n" . $imUserDataTxt);
			$txtMsg .= "\n" . str_replace("<br />", "\n", $l10n['cart_shipping_address'] . "\n" . $imShippingDataTxt );
		} else {
			$htmlMsg .= "<tr><td colspan=\"2\" style=\"padding: 20px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_vat_address'] . "/" . $l10n['cart_shipping_address'] . "</h3>" . $imUserDataHtml . "</td></tr>";
			$txtMsg .= "\n" . str_replace("<br />", "\n", $l10n['cart_vat_address'] . "/" . $l10n['cart_shipping_address'] . "\n" . $imUserDataTxt);
		}

		$htmlMsg .= $separationLine;

		// Products
		$htmlMsg .=  "<tr><td colspan=\"2\" style=\"padding: 5px 0 0 0;\"><h3 style=\"font-size: 1.11em\">" . $l10n['cart_product_list'] . "</h3>" . $imOrderDataHTML . "</td></tr>";
		$txtMsg .= "\n\n" . str_replace("<br />", "\n", $l10n['cart_product_list'] . "\n" . $imOrderDataTxt);

		$oEmailO = new imEMail($ownerEmail,$ownerEmail,$l10n['cart_order_no'] . " " . $imOrderNo,"utf-8");
		if ($imSettings['cart']['useCSV']) {
			$txtMsg .= $imUserDataCSV . "\n" . $imOrderDataCSV;
			$oEmailO->attachFile("user_data.csv",$imUserDataCSV,"text/csv");
			$oEmailO->attachFile("order_data.csv",$imOrderDataCSV,"text/csv");
		}
		$oEmailO->setText($txtMsg);
		$oEmailO->setHTML($htmlMsg);
		return $oEmailO->send();
	}

	/**
	 * Send the email message sent by the Email Object form
	 * @access public
	 * @param form The form settings in an associative array (array)
	 * @param form_data The posted text data in an associative array (array)
	 * @param files_data The posted files data in an associative array (array)
	 * @param user_only Set TRUE to send the email only to the customer who filled the form. This is used when the data is stored in a DB and a confirmation email is sent too (bool)
	 */
	function sendFormEmail($form, $form_data, $files_data, $user_only = FALSE) {
		global $imSettings;
		if (!is_array($form))
			$settings = $imSettings['email_form'][$form_id];
		else
			$settings = $form;
		//Form Data
		$txtData = "";
		$htmData = "";
		$csvHeader = "";
		$csvData = "";
		$keys = array_keys($form_data);
		foreach ($keys as $key) {
			if (is_array($form_data[$key])) {
				$txtData .= $key . ": " . implode(", ", $form_data[$key]) . "\r\n";
				$htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . $key . ":</b></td><td>" . implode(", ", $form_data[$key]) . "</td></tr>";
				if ($settings['customer_csv'] || $settings['owner_csv']) {
					$csvHeader .= $key . ";";
					$csvData .= implode(", ", $form_data[$key]) . ";";
				}
			} else {
				$txtData .= $key . ": " . $form_data[$key] . "\r\n";
				// Is it an email?
				if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $form_data[$key])) {
					$htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $key) . ":</b></td><td><a href=\"mailto:" . $form_data[$key] . "\">". $form_data[$key] . "</a></td></tr>";
				} else if (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $form_data[$key])) {
					// Is it an URL?
					$htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $key) . ":</b></td><td><a href=\"" . $form_data[$key] . "\">". $form_data[$key] . "</a></td></tr>";
				} else {
					$htmData .= "<tr valign=\"top\"><td width=\"25%\"><b>" . str_replace(array("\\'", '\\"'), array("'", '"'), $key) . ":</b></td><td>" . str_replace(array("\\'", '\\"'), array("'", '"'), $form_data[$key]) . "</td></tr>";
				}
				if ($settings['customer_csv'] || $settings['owner_csv']) {
					$csvHeader .= str_replace(array("\\'", '\\"'), array("'", '"'), $key) . ";";
					$csvData .= str_replace(array("\\'", '\\"'), array("'", '"'), $form_data[$key]) . ";";
				}
			}
		}

		// Template
		$htmHead = $imSettings['email']['header'];
		$htmFoot = $imSettings['email']['footer'];

		//Send email to owner
		if (!$user_only && isset($settings['owner_email_to']) && $settings['owner_email_to'] != "") {
			$txtMsg = $settings['owner_message'];
			$htmMsg = nl2br($settings['owner_message']);
			if (strpos($settings['owner_email_from'], "@") === FALSE)
				$settings['owner_email_from'] = $form_data[$settings['owner_email_from']];
			if ($settings['owner_email_from'] == "")
				$settings['owner_email_from'] = $settings['owner_email_to'];
			$oEmail = new imEMail($settings['owner_email_from'],$settings['owner_email_to'],$settings['owner_subject'],"utf-8");
			$oEmail->setText($txtMsg . "\n\n" . $txtData);
			$oEmail->setHTML($htmHead . $htmMsg . "<br><br><table border=0 width=\"100%\" style=\"" . $imSettings['email']['email_content_style'] . "\">" . $htmData . "</table>" . $htmFoot);
			if ($settings['owner_csv'])
				$oEmail->attachFile("form_data.csv", $csvHeader . "\n" . $csvData,"text/csv");
			if (count($files_data) > 0) {
				foreach ($files_data as $file) {
					if (file_exists($file['tmp_name']))
						$oEmail->attachFile($file['name'], file_get_contents($file['tmp_name']), $file['type']);
				}
			}
			$oEmail->send();
		}

		//Send email to user
		if (isset($settings['customer_email_to']) && $settings['customer_email_to'] != "") {
			$txtMsg = $settings['customer_message'];
			$htmMsg = nl2br($settings['customer_message']);
			if ($settings['customer_email_from'] == "")
				$settings['customer_email_from'] = $settings['owner_email_to'];
			$oEmail = new imEMail($settings['customer_email_from'],$form_data[$settings['customer_email_to']],$settings['customer_subject'],"utf-8");
			if ($settings['customer_csv']) {
				$oEmail->setHTML($htmHead . $htmMsg . "<br><br><table border=0 width=\"100%\" style=\"" . $imSettings['email']['email_content_style'] . "\">" . $htmData . "</table>" . $htmFoot);
				$oEmail->setText($txtMsg . "\n\n" . $txtData);
			} else {
				$oEmail->setText($txtMsg);
				$oEmail->setHTML($htmHead . $htmMsg . $htmFoot);
			}
			$oEmail->send();
		}
	}

	/**
	 * Send the blog emails
	 * @access public
	 * @param post The comment data
	 */
	function sendBlogEmail($post) {
		global $imSettings;
		global $l10n;
		if (isset($post['post_id'])) {
			$e = new imEmail($imSettings['blog']['email'],$imSettings['blog']['email'],$l10n['blog_new_comment_object'],"utf-8");
			$text = $l10n['blog_new_comment_text'] . " \"" . $imSettings['blog']['posts'][$post['post_id']]['title'] . "\":\n\n";
			$text .= $l10n['blog_name'] . " " . stripslashes($post['name']) . "\n";
			$text .= $l10n['blog_email'] . " " . $post['email'] . "\n";
			$text .= $l10n['blog_website'] . " " . $post['url'] . "\n";
			$text .= $l10n['blog_message'] . " " . stripslashes($post['body']) . "\n\n";
			$text .= ($imBCommentsApproved ? $l10n['blog_unapprove_link'] : $l10n['blog_approve_link']) . ":\n" . $imSettings['general']['url'] . "/admin/blog.php?post_id=" . $post['post_id'];
			$e->setText($text);
			return $e->send();
		}

		return FALSE;
	}
	
	/**
	 * Send the guestbook emails
	 * @access public
	 * @param post The comment data
	 */
	function sendGuestbookEmail($id, $name, $email, $website, $body, $direct_approval, $owner_email) {
		global $imSettings;
		global $l10n;
		if (isset($id)) {
			$e = new imEmail($owner_email,$owner_email,str_replace(array("Blog", "blog"), array("Guestbook", "guestbook"), $l10n['blog_new_comment_object']),"utf-8");
			$text = str_replace(array("Blog", "blog"), array("Guestbook", "guestbook"), $l10n['blog_new_comment_text']) . " \"" . $id . "\":\n\n";
			$text .= $l10n['blog_name'] . " " . stripslashes($name) . "\n";
			$text .= $l10n['blog_email'] . " " . $email . "\n";
			$text .= $l10n['blog_website'] . " " . $website . "\n";
			$text .= $l10n['blog_message'] . " " . stripslashes($body) . "\n\n";
			$text .= ($direct_approval ? $l10n['blog_unapprove_link'] : $l10n['blog_approve_link']) . ":\n" . $imSettings['general']['url'] . "/admin/guestbook.php?post_id=" . $id;
			$e->setText($text);
			return $e->send();
		}

		return FALSE;
	}

	/**
	 * Restore some special chars escaped previously in WSX5
	 * @access public
	 * @param str The string to be restored
	 */
	function restoreSpecialChars($str) {
		$str = str_replace("{1}", "'", $str);
		$str = str_replace("{2}", "\"", $str);
		$str = str_replace("{3}", "\\", $str);
		$str = str_replace("{4}", "<", $str);
		$str = str_replace("{5}", ">", $str);
		return $str;
	}
}

/**
 * Contains the methods used by the search engine
 * @access public
 */
class imSearch {

	var $scope;
	var $page;
	var $results_per_page;

	function __construct() {
		$this->setScope();
		$this->results_per_page = 10;
	}

	function imSearch() {
		$this->setScope();
		$this->results_per_page = 10;
	}

	/**
	 * Loads the pages defined in search.inc.php  to the search scope
	 * @access public
	 */
	function setScope() {
		global $imSettings;
		$scope = $imSettings['search']['general']['defaultScope'];

		// Logged users can search in their private pages
		$pa = new imPrivateArea();
		if ($user = $pa->who_is_logged()) {
			foreach ($imSettings['search']['general']['extendedScope'] as $key => $value) {
				if (in_array($user['uid'], $imSettings['access']['pages'][$key]))
					$scope[] = $value;
			}
		}

		$this->scope = $scope;
	}

	/**
	 * Do the pages search
	 * @access public
	 * @param queries The search query (array)
	 */
	function searchPages($queries) {
		global $l10n;
		global $imSettings;

		$html = "";

		if (is_array($this->scope)) {
			foreach($this->scope as $filename) {
				$count = 0;
				$weight = 0;
				$file_content = @implode("\n",file($filename));

				// Remove the breadcrumbs
				while (stristr($file_content, "<div id=\"imBreadcrumb\"") !== FALSE) {
					$style_start = stripos($file_content, "<div id=\"imBreadcrumb\"");
					$style_end = stripos($file_content, "</div", $style_start);
					$style = substr($file_content, $style_start, $style_end - $style_start);
					$file_content = str_replace($style, "", $file_content);
				}

				// Remove CSS
				while (stristr($file_content, "<style") !== FALSE) {
					$style_start = stripos($file_content, "<style");
					$style_end = stripos($file_content, "</style", $style_start);
					$style = substr($file_content, $style_start, $style_end - $style_start);
					$file_content = str_replace($style, "", $file_content);
				}

				// Remove JS
				while (stristr($file_content, "<script") !== FALSE) {
					$style_start = stripos($file_content, "<script");
					$style_end = stripos($file_content, "</script", $style_start);
					$style = substr($file_content, $style_start, $style_end - $style_start);
					$file_content = str_replace($style, "", $file_content);
				}
				$file_title = "";

				// Get the title of the page
				preg_match('/\<title\>([^\<]*)\<\/title\>/', $file_content, $matches);
				if ($matches[1] != null)
					$file_title = $matches[1];
				else {
					preg_match('/\<h2\>([^\<]*)\<\/h2\>/', $file_content, $matches);
					if ($matches[1] != null)
						$file_title = $matches[1];
				}

				if($file_title != "") {
					foreach($queries as $query) {
						$title = strtolower($file_title);
						while (($title = stristr($title, $query)) !== FALSE) {
							$weight += 5;
							$count++;
							$title = substr($title,strlen($query));
						}
					}
				}

				// Get the keywords
				preg_match('/\<meta name\=\"keywords\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
				if ($matches[1] != null) {
					$keywords = $matches[1];
					foreach($queries as $query) {
						$tkeywords = strtolower($keywords);
						while (($tkeywords = stristr($tkeywords, $query)) !== FALSE) {
							$weight += 4;
							$count++;
							$tkeywords = substr($tkeywords,strlen($query));
						}
					}
				}

				// Get the description
				preg_match('/\<meta name\=\"description\" content\=\"([^\"]*)\" \/>/', $file_content, $matches);
				if ($matches[1] != null) {
					$keywords = $matches[1];
					foreach($queries as $query) {
						$tkeywords = strtolower($keywords);
						while (($tkeywords = stristr($tkeywords, $query)) !== FALSE) {
							$weight += 3;
							$count++;
							$tkeywords = substr($tkeywords,strlen($query));
						}
					}
				}

				// Remove the page title from the result
				while (stristr($file_content, "<h2") !== FALSE) {
					$style_start = strpos($file_content, "<h2");
					$style_end = strpos($file_content, "</h2", $style_start);
					$style = substr($file_content, $style_start, $style_end - $style_start);
					$file_content = str_replace($style, "", $file_content);
				}

				$page_pos = strpos($file_content,"<div id=\"imContent\">") + strlen("<div id=\"imContent\">");
				$page_end = strpos($file_content, "<div id=\"imBtMn\">");
				if ($page_end == FALSE)
					$page_end = strpos($file_content,"</body>");

				$file_content = strip_tags(substr($file_content,$page_pos, $page_end-$page_pos));
				$t_file_content = strtolower($file_content);

				foreach($queries as $query) {
					$file = $t_file_content;
					while (($file = stristr($file, $query)) !== FALSE) {
						$count++;
						$weight++;
						$file = substr($file,strlen($query));
					}
				}

				if($count > 0) {
					$found_count[$filename] = $count;
					$found_weight[$filename] = $weight;
					$found_content[$filename] = $file_content;
					if ($file_title == "")
						$found_title[$filename] = $filename;
					else
						$found_title[$filename] = $file_title;
				}
			}
		}

		if($found_count != null) {
			arsort($found_weight);
			$i = 0;
			$pagine = ceil(count($found_count)/$this->results_per_page);
			if(($this->page >= $pagine) || ($this->page < 0))
				$this->page = 0;
			foreach($found_weight as $name => $weight) {
				$count = $found_count[$name];
				$i++;
				if(($i > $this->page*$this->results_per_page) && ($i <= ($this->page+1)*$this->results_per_page)) {
					$title = strip_tags($found_title[$name]);
					$file = $found_content[$name];
					$file = strip_tags($file);
					$ap = 0;
					$filelen = strlen($file);
					$text = "";
					for($j=0;$j<($count > 6 ? 6 : $count);$j++) {
						$minpos = $filelen;
						foreach($queries as $query) {
							if(($pos = strpos(strtoupper($file),strtoupper($query),$ap)) !== FALSE) {
								if($pos < $minpos) {
									$minpos = $pos;
									$word = $query;
								}
							}
						}
						$prev = explode(" ",substr($file,$ap,$minpos-$ap));
						if(count($prev) > ($ap > 0 ? 9 : 8))
							$prev = ($ap > 0 ? implode(" ",array_slice($prev,0,8)) : "") . " ... " . implode(" ",array_slice($prev,-8));
						else
							$prev = implode(" ",$prev);
						$text .= $prev . "<strong>" . substr($file,$minpos,strlen($word)) . "</strong> ";
						$ap = $minpos + strlen($word);
					}
					$next = explode(" ",substr($file,$ap));
					if(count($next) > 9)
						$text .= implode(" ",array_slice($next,0,8)) . "...";
					else
						$text .= implode(" ",$next);
					$text = str_replace("|", "", $text);
					$text = str_replace("<br />", " ", $text);
					$text = str_replace("<br>", " ", $text);
					$text = str_replace("\n", " ", $text);
					$html .= "<div class=\"imSearchPageResult\"><h3><a href=\"" . $name . "\">" . $title . "</a></h3>" . $text . "<div class=\"imSearchLink\"><a href=\"" . $name . "\">" . $domain . $name . "</a></div></div>\n";
				}
			}
			$html = preg_replace("/\\s+/", " ", $html);
			$html .= "<div class=\"imSLabel\">&nbsp;</div>\n";
		}

		return array("content" => $html, "count" => count($found_content));
	}

	/**
	 * Start the site search
	 * @access public
	 * @param keys The search keys as string (string)
	 * @param page Page to show (integer)
	 */
	function search($keys, $page = "") {
		global $l10n;
		global $imSettings;

		$html = "";
		$content = "";

		$html .= "<h2 id=\"imPgTitle\" class=\"searchPageTitle\">" . $l10n['search_results'] . "</h2>\n";
		$html .= "<div class=\"searchPageContainer\">";
		$html .= "<div class=\"imPageSearchField\"><form method=\"get\" action=\"imsearch.php\">";
		$html .= "<input style=\"width: 200px; font: 8pt Tahoma; color: rgb(0, 0, 0); background-color: rgb(255, 255, 255); padding: 3px; border: 1px solid rgb(0, 0, 0); vertical-align: middle;\" class=\"search_field\" value=\"" . $keys . "\" type=\"text\" name=\"search\" />";
		$html .= "<input style=\"height: 21px; font: 8pt Tahoma; color: rgb(0, 0, 0); background-color: rgb(211, 211, 211); margin-left: 6px; padding: 3px 3px; border: 1px solid rgb(0, 0, 0); vertical-align: middle; cursor: pointer;\" type=\"submit\" value=\"" . $l10n['search_search'] . "\">";
		$html .= "</form></div>\n";

		if ($keys == "" || $keys == NULL) {
			$html .= "<div style=\"margin-top: 15px; text-align: center; font-weight: bold;\">" . $l10n['search_empty'] . "</div>\n";
			echo $html;
			return FALSE;
		}

		$domain = "";
		$search = trim(strtolower($keys));
		if($page == "" || $page == NULL)
			$page = 0;

		$this->page = $page;

		if($search != "") {
			$queries = explode(" ",$search);

			// Pages
			$pages = $this->searchPages($queries);
			if ($pages['count'] > 0) {
				$content .= "<div id=\"imSearchWebPages\">" . $pages['content'] . "</div>\n";
			}

			$results_count = max($pages['count'], $blog['count'], $products['count'], $images['count'], $videos['count']);

			if ($pages['count'] == 0 && $blog['count'] == 0 && $products['count'] == 0 && $images['count'] == 0 && $videos['count'] == 0) {
				$html .= "<div style=\"margin-top: 15px; text-align: center; font-weight: bold;\">" . $l10n['search_empty'] . "</div>\n";
			} else {
				$html .= "<div id=\"imSearchResults\">\n";
				$html .= "\t<div id=\"imSearchContent\">" . $content . "</div>\n";
				$html .= "</div>\n";
			}

			// Pagination
			if ($results_count > $this->results_per_page) {
				$html .= "<div style=\"text-align: center;\">";
				// Back
				if ($page > 0) {
					$html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page - 1) . "\">&lt;&lt;</a>&nbsp;";
				}

				// Central pages
				$start = max($page - 5, 0);
				$end = min($page + 10 - $start, ceil($results_count/$this->results_per_page));

				for ($i = $start; $i < $end; $i++) {
					if ($i != $this->page)
						$html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . $i . "\">" . ($i + 1) . "</a>&nbsp;";
					else
						$html .= ($i + 1) . "&nbsp;";
				}

				// Next
				if ($results_count > ($page + 1) * $this->results_per_page) {
					$html .= "<a href=\"imsearch.php?search=" . implode("+", $queries) . "&amp;page=" . ($page + 1) . "\">&gt;&gt;</a>";
				}
				$html .= "</div>";
			}

		} else
			$html .= "<div style=\"margin-top: 15px; text-align: center; font-weight: bold;\">" . $l10n['search_empty'] . "</div>\n";

		$html .= "</div>";

		echo $html;
	}
}

/**
 * Private area
 * @access public
 */
class imPrivateArea {

	var $session_uname;
	var $session_uid;
	var $session_page;
	var $cookie_name;

	// PHP 5
	function __contruct() {
		$this->session_uname = "im_access_uname";
		$this->session_real_name = "im_access_real_name";
		$this->session_page = "im_access_request_page";
		$this->session_uid = "im_access_uid";
		$this->cookie_name = "im_access_cookie_uid";
	}

	// PHP 4
	function imPrivateArea() {
		$this->session_uname = "im_access_uname";
		$this->session_real_name = "im_access_real_name";
		$this->session_page = "im_access_request_page";
		$this->session_uid = "im_access_uid";
		$this->cookie_name = "im_access_cookie_uid";
	}

	/**
	 * Login
	 * @access public
	 * @param uname Username (string)
	 * @param pwd Password (string)
	 */
	function login($uname, $pwd) {
		global $imSettings;

		// Check if the user exists
		if ($imSettings['access']['users'][$uname] != NULL && $imSettings['access']['users'][$uname]['password'] == $pwd) {
			// Save the session
			$_SESSION[$this->session_uid] = $imSettings['access']['users'][$uname]['id'];
			$_SESSION[$this->session_uname] = $uname;
			$_SESSION[$this->session_real_name] = $imSettings['access']['users'][$uname]['name'];
			setcookie($this->cookie_name, $imSettings['access']['users'][$uname]['id'], time() + 60 * 60 * 24 * 30, "/");
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Logout
	 * @access public
	 */
	function logout() {
		$_SESSION[$this->session_uname] = "";
		$_SESSION[$this->session_uid] = "";
		setcookie($this->cookie_name, "", time() - 3600, "/");
		$_COOKIE[$this->cookie_name] = "";
	}

	/**
	 * Save the referrer page
	 * @access public
	 */
	function save_page() {
		$_SESSION[$this->session_page] = basename($_SERVER['PHP_SELF']);
	}

	/**
	 * Return to the referrer page
	 * @access public
	 */
	function saved_page() {
		if ($_SESSION[$this->session_page] != "" && $_SESSION[$this->session_page] != null)
			return $_SESSION[$this->session_page];
		return FALSE;
	}

	/**
	 * Get an array of data about the logged user
	 * @access public
	 */
	function who_is_logged() {
		if ($_SESSION[$this->session_uname] != "" && $_SESSION[$this->session_uname] != null)
			return array(
				"username" => $_SESSION[$this->session_uname],
				"uid" => $_SESSION[$this->session_uid],
				"realname" => $_SESSION[$this->session_real_name],
			);
		return FALSE;
	}

	/**
	 * Check if the logged user can access to a page
	 * @access public
	 * @param page The page id (string)
	 */
	function checkAccess($page) {
		global $imSettings;

		if ($_SESSION[$this->session_uname] == null || $_SESSION[$this->session_uname] == '' || $_SESSION[$this->session_uid] == null || $_SESSION[$this->session_uid] == '')
			return -1; // Wrong login data

		$uid = $_SESSION[$this->session_uid];
		if (!@in_array($uid, $imSettings['access']['pages'][$page]) && !@in_array($uid, $imSettings['access']['admins']))
			return -2; // The user cannot access to this page

		return 0;
	}

	/**
	 * Get the user's landing page
	 * @access public
	 */
	function getLandingPage() {
		global $imSettings;
		if ($_SESSION[$this->session_uname] === null || $_SESSION[$this->session_uname] === '' || $_SESSION[$this->session_uid] === null || $_SESSION[$this->session_uid] === '')
			return FALSE;

		return $imSettings['access']['users'][$_SESSION[$this->session_uname]]['page'];
	}
}

/**
 * MySQL Storage class
 * @access public
 */
class imDatabase {

	var $host_name;
	var $db_name;
	var $user_name;
	var $password;
	var $table_name;
	var $file_storage;
	var $field_names;
	var $conn; //DB Connection handler

	/**
	 * PHP 5 Constuctor
	 * @param host_name (string)
	 * @param db_name (string)
	 * @param user_name (string)
	 * @param password (string)
	 * @param table_name (string)
	 * @param file_storage The folder in which the uploaded files are stored (string)
	 */
	function __construct($host_name, $db_name, $user_name, $password, $table_name, $file_storage) {
		$this->host_name = $host_name;
		$this->db_name = $db_name;
		$this->user_name = $user_name;
		$this->password = $password;
		$this->table_name = $table_name;
		if ($file_storage[strlen($file_storage) - 1] == "/");
			$file_storage = substr($file_storage, 0, strlen($file_storage) - 1);
		$this->file_storage = $file_storage;
		$this->field_names = array();
		$this->conn = @mysql_connect($this->host_name, $this->user_name, $this->password);
		if ($this->conn !== FALSE)
			@mysql_set_charset("utf8",$this->conn);
	}

	/**
	 * PHP 4 Constuctor
	 * @param host_name (string)
	 * @param db_name (string)
	 * @param user_name (string)
	 * @param password (string)
	 * @param table_name (string)
	 * @param file_storage The folder in which the uploaded files are stored (string)
	 */
	function imDatabase($host_name, $db_name, $user_name, $password, $table_name, $file_storage) {
		$this->host_name = $host_name;
		$this->db_name = $db_name;
		$this->user_name = $user_name;
		$this->password = $password;
		$this->table_name = $table_name;
		if ($file_storage[strlen($file_storage) - 1] == "/");
			$file_storage = substr($file_storage, 0, strlen($file_storage) - 1);
		$this->file_storage = $file_storage;
		$this->field_names = array();
		$this->conn = @mysql_connect($this->host_name, $this->user_name, $this->password);
		if ($this->conn !== FALSE)
			@mysql_set_charset("utf8",$this->conn);
	}

	function test_connection() {
		return $this->conn;
	}

	function __destruct() {
		if ($this->conn)
			mysql_close($this->conn);
	}

	/**
	 * Save the data to the DB
	 * @param post an associative array as "tablefield_id" => "data_to_save"
	 * @param files an associative array. Normally it's $_FILES
	 */
	function addData($post = null, $files = null) {
		if ($post == null && $files == null)
			return FALSE;
		if ($post == null)
			$post = array();
		if ($files == null)
			$files = array();
		$empty = true;
		foreach ($post as $field) {
			if ($field != "" && $field != null)
				$empty = FALSE;
		}
		if ($empty)
			return FALSE;
		$fields_name = array_merge(array_keys($post), array_keys($files));
		$fields_count = count($fields_name);
		if (count($fields_name) < $fields_count) {
			$d = $fields_count - count($fields_name);
			for ($i = 0; $i < $d; $i++)
				$field_name[] = "field_" + ($i + $d);
		}

		// If the table does not exists, create it
		$result = mysql_query("SHOW FULL TABLES FROM `" . $this->db_name . "` LIKE '" . $this->table_name . "'", $this->conn);
		if ($result && mysql_num_rows($result) == 0) {
			$query = "CREATE TABLE `" . $this->db_name . "`.`" . $this->table_name . "` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
			for ($i=0; $i<$fields_count; $i++) {
				$query .= "`" . $fields_name[$i] . "` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL";
				if ($i != $fields_count - 1)
					$query .= ",";
			}
			$query .= ") ENGINE = MYISAM ;";
			mysql_query($query, $this->conn);
		}
		else // If the table has not enough fields, update it
		{
			$result = mysql_query("SHOW COLUMNS FROM `" . $this->db_name . "`.`" . $this->table_name . "`", $this->conn);
			if ($result) {
				// Actual fields
				$row = mysql_fetch_array($result);
				$query = "ALTER TABLE `" . $this->db_name. "`.`" . $this->table_name . "`";
				$act_fields = array();
				while ($row = mysql_fetch_array($result))
					$act_fields[] = $row[0];
				// New fields
				$new_fields = array_diff($fields_name, $act_fields);
				$new_fields = array_merge($new_fields); //ordina gli indici
				if (count($new_fields) > 0) {
					for ($j = 0; $j < count($new_fields); $j++) {
						$query .= " ADD `" . $new_fields[$j] . "` TEXT CHARACTER SET utf8 COLLATE utf8_bin NOT NULL ";
						if ($j != count($new_fields) - 1)
							$query .= ",";
					}
					mysql_query($query, $this->conn);
				}
			}
		}

		// Save
		$query = "INSERT INTO `" . $this->db_name . "`.`" . $this->table_name . "` (";
		$fields = array();
		for ($i = 0; $i<$fields_count; $i++)
			array_push ($fields,  $fields_name[$i]);
		$query .= join(",", $fields);
		$query .= ") VALUES (";

		$data = array();
		$p_keys = array_keys($post);
		for ($i = 0; $i<count($post); $i++) {
			if (is_array($post[$p_keys[$i]])) {
				$s = "";
				for ($x = 0; $x < count($post[$p_keys[$i]]); $x++)
					$s .= (($x > 0) ? ", " : "") . $post[$p_keys[$i]][$x];
				array_push($data, "'" . mysql_real_escape_string(str_replace(array("\n", "\r"), array("<br />", ""), $s)). "'");
			} else {
				array_push($data, "'" . mysql_real_escape_string(str_replace(array("\n", "\r"), array("<br />", ""), $post[$p_keys[$i]])). "'");
			}
		}

		$p_keys = array_keys($files);
		$f = true;
		for ($i = 0; $i<count($files); $i++) {
			if ($files[$p_keys[$i]]['tmp_name'] != "") {
				// Upload files using an unique name
				$fname = $this->findFileName($files[$p_keys[$i]]['name']);
				if (@move_uploaded_file($files[$p_keys[$i]]['tmp_name'], ($this->file_storage != "" ? $this->file_storage . "/" : "../") . $fname)) {
				  $f = true;
				} else {
				  $f = FALSE;
				}
				array_push($data, "'" . mysql_real_escape_string($fname). "'");
			}
			else {
				array_push($data, "''");
			}
		}
		$query .= join(",", $data);
		$query .=")";

		$r = mysql_query($query, $this->conn);

		if ($r && $f)
			return true;
		return FALSE;
	}

	/**
	 * Set the field names
	 * @param array An array containing the field names (array)
	 */
	function setFieldNames($array) {
		$this->field_names = $array;
	}

	/**
	 * Show the current storage table (read only version)
	 * @param order ASC or DESC
	 */
	function showTable($order = "ASC") {
		$result = mysql_query("SHOW COLUMNS FROM `" . $this->db_name . "`.`" . $this->table_name . "`", $this->conn);
		if ($result && mysql_num_rows($result)>1) {
			echo "<table class=\"imDbTable\">\n";
			echo "	<tr>\n";
			$row = mysql_fetch_array($result);
		  for ($i=1; $i<mysql_num_rows($result); $i++) {
			$row = mysql_fetch_array($result);
			if (array_key_exists($i-1, $this->field_names)) $field = $this->field_names[$i - 1];
			else 	$field = $row[0];
			echo "		<td class='imDbTableHead'>" . $field . "</td>\n";
			}
			echo "	</tr>\n";
			$result = mysql_query("SELECT * FROM `" . $this->db_name. "`.`" . $this->table_name . "` ORDER BY id " . $order, $this->conn);
			while ($row = mysql_fetch_array($result)){
				echo "	<tr>\n";
					for ($i = 1; $i < mysql_num_fields($result); $i++) {
						if (file_exists($this->file_storage . "/" . $row[$i]))
							echo "		<td><a href=\""  . $this->file_storage . "/" . $row[$i] . "\" target=\"_blank\">" . $row[$i] . "</a></td>\n";
						else
							echo "		<td>" . $row[$i] . "</td>\n";
					}
				echo "	</tr>\n";
			}
			echo "</table>\n";
		}
	}

	/**
	 * Show the current storage table (r/w version)
	 * @param order ASC or DESC
	 */
	function showAdminTable($order = "ASC") {
		$form_id = "tt_form_" . rand(0, 10000000);
		$result = mysql_query("SHOW COLUMNS FROM `" . $this->db_name . "`.`" . $this->table_name . "`", $this->conn);
		if ($result && mysql_num_rows($result)>1) {
			echo "<form id=\"" . $form_id . "\" method=\"post\" action=\"" . $_SERVER['PHP_SELF'] . "?delete\" onsubmit=\"return confirm('Sei sicuro di voler confermare l\'operazione?');\">\n";
			echo "<table class=\"imDbTable\">\n";
			echo "	<tr>\n";
			$row = mysql_fetch_array($result);
			for ($i=1; $i<mysql_num_rows($result); $i++) {
				$row = mysql_fetch_array($result);
				if (array_key_exists($i-1, $this->field_names)) $field = $this->field_names[$i - 1];
				else 	$field = $row[0];
				echo "		<td class='imDbTableHead'>" . $field . "</td>\n";
			}
			echo "	</tr>\n";
			$result = mysql_query("SELECT * FROM `" . $this->db_name. "`.`" . $this->table_name . "` ORDER BY id " . $order, $this->conn);
			while ($row = mysql_fetch_array($result)){
				echo "	<tr>\n";
					for ($i = 1; $i < mysql_num_fields($result); $i++) {
						if (file_exists($this->file_storage . "/" . $row[$i]) && !is_dir($this->file_storage . "/" . $row[$i]))
							echo "		<td><a href=\""  . $this->file_storage . "/" . $row[$i] . "\" target=\"_blank\">" . $row[$i] . "</a></td>\n";
						else
							echo "		<td>" . $row[$i] . "</td>\n";
					}
				echo "		<td><input type=\"checkbox\" value=\"" . $row[0] . "\" name=\"" . $row[0] . "\"></td>\n";
				echo "	</tr>\n";
			}
			echo "	<tr>\n";
			echo "		<td colspan=\"" . $i . "\" style=\"text-align:right\">Se selezionati: <input type=\"submit\" value=\"Elimina\"></td>\n";
			echo "	</tr>\n";
			echo "</table></form>\n";
		}
	}

	/**
	 * Find a free file name
	 */
	function findFileName($tmp_name) {
		$ext = substr($tmp_name, strrpos($tmp_name, "."));
		$fname = basename($tmp_name, $ext);
		do {
			$rname = $fname . "_" . date("Ymdhisu") . $ext;
		} while (file_exists($this->file_storage . "/" . $rname));
		return $rname;
	}

	/**
	 * Delete a row from the storage table
	 * @param post The ids of the rows to deleted
	 */
	function deleteRow($post) {
		if ($post != null) {
			$results = mysql_query("SELECT * FROM `" . $this->db_name . "`.`" . $this->table_name . "` WHERE id IN (" . join(",", $post) . ")", $this->conn);
			//Segna come eliminati i file linkati
			while ($results && $row = mysql_fetch_array($results))
				for ($i = 1; $i < mysql_num_fields($results); $i++)
					if (file_exists($this->file_storage . "/" . $row[$i]) && !is_dir($this->file_storage . "/" . $row[$i]))
						rename($this->file_storage . "/" . $row[$i], $this->file_storage . "/" . $row[$i] . ".deleted");
			//Cancella i record
			mysql_query("DELETE FROM `" . $this->db_name . "`.`" . $this->table_name . "` WHERE id IN (" . join(",", $post) . ")", $this->conn);
		}
	}
}

/**
 * XML Handling class
 * @access public
 */
class imXML {
    var $tree = array();
    var $force_to_array = array();
    var $error = null;
	var $parser;

	// PHP 5
	function __construct($encoding = 'UTF-8') {
        $this->parser = xml_parser_create($encoding);
        xml_set_object($this->parser, $this); // $this was passed as reference &$this
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($this->parser, "startEl", "stopEl");
        xml_set_character_data_handler($this->parser, "charData");
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    }

	// PHP 4
    function imXML($encoding = 'UTF-8') {
        $this->parser = xml_parser_create($encoding);
        xml_set_object($this->parser, $this); // $this was passed as reference &$this
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
        xml_set_element_handler($this->parser, "startEl", "stopEl");
        xml_set_character_data_handler($this->parser, "charData");
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
    }

    function parse_file($file) {
		$fp = @fopen($file, "r");
		if (!$fp)
			return FALSE;
		while ($data = fread($fp, 4096)) {
			if (!xml_parse($this->parser, $data, feof($fp))) {
				return FALSE;
			}
		}
		fclose($fp);
		return $this->tree[0]["content"];
	}

	function parse_string($str) {
		if (!xml_parse($this->parser, $str))
			return FALSE;
		return $this->tree[0]["content"];
	}

    function startEl($parser, $name, $attrs) {
        array_unshift($this->tree, array("name" => $name));
    }

    function stopEl($parser, $name) {
        if ($name != $this->tree[0]["name"])
			return FALSE;
        if (count($this->tree) > 1) {
            $elem = array_shift($this->tree);
            if (isset($this->tree[0]["content"][$elem["name"]])) {
	            if (is_array($this->tree[0]["content"][$elem["name"]]) && isset($this->tree[0]["content"][$elem["name"]][0])) {
	                array_push($this->tree[0]["content"][$elem["name"]], $elem["content"]);
	            } else {
	                $this->tree[0]["content"][$elem["name"]] =
	                array($this->tree[0]["content"][$elem["name"]],$elem["content"]);
	            }
            } else {
	            if (in_array($elem["name"],$this->force_to_array)) {
	                $this->tree[0]["content"][$elem["name"]] = array($elem["content"]);
	            } else {
	                if (!isset($elem["content"])) $elem["content"] = "";
	                $this->tree[0]["content"][$elem["name"]] = $elem["content"];
	            }
            }
        }
    }

    function charData($parser, $data) {
        if (!is_string($this->tree[0]["content"]) && !preg_match("/\\S/", $data))
			return FALSE;
        $this->tree[0]["content"] .= $data;
    }
}


/**
 * Captcha handling class
 * @access public
 */
class imCaptcha {

	/**
	 * Show the captcha chars
	 */
	function show($sCode) {
		global $oNameList;
		global $oCharList;

		$text = "<!DOCTYPE HTML>
			<html>
		  <head>
		  <meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">
		  <meta http-equiv=\"pragma\" content=\"no-cache\">
		  <meta http-equiv=\"cache-control\" content=\"no-cache, must-revalidate\">
		  <meta http-equiv=\"expires\" content=\"0\">
		  <meta http-equiv=\"last-modified\" content=\"\">
		  </head>
		  <body style=\"margin: 0; padding: 0; border-collapse: collapse;\">";

		for ($i=0; $i<strlen($sCode); $i++)
			$text .= "<img style=\"margin:0; padding:0; border: 0; border-collapse: collapse; width: 24px; height: 24px; position: absolute; top: 0; left: " . (24 * $i) . "px;\" src=\"imcpa_".$oNameList[substr($sCode,$i,1)].".gif\" width=\"24\" height=\"24\">";

		$text .= "</body></html>";

		return $text;
	}

	/**
	 * Check the sent data
	 * @param sCode The correct code (string)
	 * @param dans The user's answer (string)
	 */
	function check($sCode, $ans) {
		global $oCharList;
		if ($ans == "")
			return '-1';
		for ($i=0; $i<strlen($sCode); $i++)
		  if ($oCharList[substr(strtoupper($sCode),$i,1)] != substr(strtoupper($ans), $i,1))
			return '-1';
		return '0';
	}
}

/**
 * Comments
 * @access public
 */
class imComment {

	/**
	 * Get the comments from a file
	 * @param file The source file path
	 */
	function getComments($file) {
		$xml = new imXML();
		$comments = $xml->parse_file($file);
		if (is_array($comments)) {
			if (!is_array($comments['comment'][0])) {
				return str_replace(array("\\'", '\\"'), array("'", '"'), array($comments['comment']));
			} else {
				for ($i = 0; $i < count($comments['comment']); $i++) {
					$comments['comment'][$i]['body'] = str_replace(array("\\'", '\\"'), array("'", '"'), $comments['comment'][$i]['body']);
					$comments['comment'][$i]['name'] = str_replace(array("\\'", '\\"'), array("'", '"'), $comments['comment'][$i]['name']);
					$comments['comment'][$i]['body'] = str_replace("\\\"", "\"", $comments['comment'][$i]['body']);
					$comments['comment'][$i]['name'] = str_replace("\\\"", "\"", $comments['comment'][$i]['name']);
				}
				return $comments['comment'];
			}
		}
		else
			return $this->getComments_old($file);
	}

	/**
	 * Get the comments from a v8 comments file
	 * @param file The source file path
	 */
	function getComments_old($file) {
		if(file_exists($file)) {
			$f = file_get_contents($file);
			$f = explode("\n",$f);
			for($i = 0;$i < count($f)-1; $i += 6) {
				$c[$i/6]['name'] = stripslashes($f[$i]);
				$c[$i/6]['email'] = $f[$i+1];
				$c[$i/6]['url'] = $f[$i+2];
				$c[$i/6]['body'] = stripslashes($f[$i+3]);
				$c[$i/6]['timestamp'] = $f[$i+4];
				$c[$i/6]['approved'] = $f[$i+5];
				$c[$i/6]['rating'] = 0;
			}
			return $c;
		}
		else
		  return -1;
	}

	/**
	 * Save the comments in a xml file
	 * @param file The destination file path
	 * @param comments An associative array containing the comments data
	 */
	function writeXML($file, $comments) {
		if (count($comments) > 0) {
			$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$xml .= "<comments>\n";
			foreach ($comments as $comment) {
				$xml .= "\t<comment>\n";
				$keys = array_keys($comment);
				foreach ($keys as $key)
					$xml .= "\t\t<" . $key . "><![CDATA[" . str_replace(array("\\'", '\\"'), array("'", '"'), str_replace("\\\"", "\"",  preg_replace('/[\n\r\t]*/', "", nl2br($comment[$key])))) . "]]></" . $key . ">\n";
				$xml .= "\t</comment>\n";
			}
			$xml .= "</comments>";
		} else $xml = "";

		if (is_writable($file) || !file_exists($file)) {
			if (!$f = fopen($file, 'w+'))
				return -3;
			else {
				if (flock($f, LOCK_EX))
					$locked = 1;
				if (fwrite($f, $xml) === FALSE)
					return -4;
				else {
					if($locked)
						flock($f, LOCK_UN);
					fclose($f);
					return 0;
				}
			}
		}
		else
		  return -2;
	}

	/**
	 * Add a comment to a file
	 * @param file The destination file path
	 * @param name The user's name
	 * @param email The user's email
	 * @param url The user's site url
	 * @param body The user's message
	 * @param abuse Set 1 to mark the message as an abuse
	 * @param approved Set 1 to mark the message as approved
	 */
	function addComment($file,$name,$email,$url,$body,$abuse = "0",$approved = 0) {
		global $imSettings;
		
		$name = filterCode($name);
		$email = filterCode($email);
		$url = filterCode($url);
		$body = filterCode($body);
		
		$locked = 0;
		$comments = $this->getComments($file);
		if (!is_array($comments))
			$comments = array();
		if ($url != "" && stripos($url, "http://") === FALSE)
			$url = "http://" . $url;
		$comments[] = array("name" => $name, "email" => $email, "url" => $url, "body" => $body, "abuse" => $abuse, "timestamp" => date("d-m-Y H:i:s"), "approved" => $approved);
		if($file != "" && trim($name) != "" && trim($email) != "" && trim($body) != "")
			return $this->writeXML($file, $comments);
		else
			return -1;
	}

	/**
	 * Add a comment to a file
	 * @param file The destination file
	 * @param n The comment number (0 is the first comment)
	 * @param approved Set 1 to approve the comment, 0 to unapprove
	 */
	function approveComment($file,$n,$approved) {
		$locked = 0;
		$fn = $file;
		if(!copy($fn,$fn . "_bak"))
		  return -1;
		$c = $this->getComments($file);
		if($c == -1)
		  return -2;
		if(!file_exists($fn))
		  return -3;
		if(!is_writable($fn))
		  return -4;
		$c[$n - 1]['approved'] = $approved;
		$this->writeXML($file, $c);
		return 0;
	}

	/**
	 * Delete a comment
	 * @param file The destination file
	 * @param n The comment number (0 is the first comment)
	 */
	function removeComment($file,$n) {
		$locked = 0;
		$fn = $file;
		if(!copy($fn,$fn . "_bak"))
		  return -1;
		$c = $this->getComments($file);
		if($c == -1)
		  return -2;
		if(!file_exists($fn))
		  return -3;
		if(!is_writable($fn))
		  return -4;
		for ($i = 0; $i < count($c); $i++) {
			if ($i != $n - 1)
				$comments[] = $c[$i];
		}
		$this->writeXML($file, $comments);
		return 0;
	}

	/**
	 * Set the abuse
	 * @param file The destination file
	 * @param n The comment number (0 is the first comment)
	 * @param abuse Set 1 to set as an abuse
	 */
	function setAbuse($file, $n, $abuse) {
		$locked = 0;
		$fn = $file;
		if(!copy($fn,$fn . "_bak"))
		  return -1;
		$c = $this->getComments($file);
		if($c == -1)
		  return -2;
		if(!file_exists($fn))
		  return -3;
		if(!is_writable($fn))
		  return -4;
		$c[$n - 1]['abuse'] = $abuse;
		$this->writeXML($file, $c);
		return 0;
	}
}

/**
 * Blog class
 * @access public
 */
class imBlog {

	var $comments; //Comments class

	// PHP 5
	function __construct() {
		$this->comments = new imComment();
	}

	// PHP 4
	function imBlog() {
		$this->comments = new imComment();
	}

	function formatTimestamp($ts) {
		return date("d/m/Y H:i:s", strtotime($ts));
	}

	/**
	 * Get the comments from a post
	 * @param post the post ID
	 */
	function getComments($post) {
		global $imSettings;
		return $this->comments->getComments($imSettings['general']['dir'] . $imSettings['blog']['file_prefix'] . 'pc' . $post);
	}

	/**
	 * Add a comment to a post
	 * @param post the post ID
	 * @param file The destination file path
	 * @param name The user's name
	 * @param email The user's email
	 * @param url The user's site url
	 * @param body The user's message
	 */
	function addComment($post,$name,$email,$url,$body) {
		global $imSettings;
		if (!file_exists($imSettings['general']['dir']) && $imSettings['general']['dir'] != "" && $imSettings['general']['dir'] != "./.")
			@mkdir($imSettings['general']['dir'], 0777, TRUE);
		return $this->comments->addComment($imSettings['general']['dir'] . $imSettings['blog']['file_prefix'] . 'pc' . $post,$name,$email,$url,$body, "0", ($imSettings['blog']['approve_comments'] == 1) ? 0 : 1);
	}

	/**
	 * Approve a comment
	 * @param post the post ID
	 * @param n The comment number
	 * @param approved Set 1 to approve
	 */
	function approveComment($post,$n,$approved) {
		global $imSettings;
		return $this->comments->approveComment($imSettings['general']['dir'] . $imSettings['blog']['file_prefix'] . 'pc' . $post,$n,$approved);
	}

	/**
	 * Remove a comment
	 * @param post the post ID
	 * @param n The comment number
	 */
	function removeComment($post,$n) {
		global $imSettings;
		return $this->comments->removeComment($imSettings['general']['dir'] . $imSettings['blog']['file_prefix'] . 'pc' . $post, $n);
	}

	/**
	 * Abuse a comment
	 * @param post the post ID
	 * @param n The comment number
	 * @param abuse Set 1 to abuse
	 */
	function setAbuse($post, $n, $abuse) {
		global $imSettings;
		return $this->comments->setAbuse($imSettings['general']['dir'] . $imSettings['blog']['file_prefix'] . 'pc' . $post, $n, $abuse);
	}

	/**
	 * Get the last update date
	 */
	function getLastModified() {
		global $imSettings;
		$c = $this->comments->getComments($_GET['id']);
		if($_GET['id'] != "" && $c != -1) {
		  return $this->formatTimestamp($c[count($c)-1]['timestamp']);
		}
		else {
		  $last_post = $imSettings['blog']['posts'];
		  $last_post = array_shift($last_post);
		  return $last_post['timestamp'];
		}
	}

	/**
	 * Show a post
	 * @param id the post id
	 * @param ext Set 1 to show as extended
	 * @param first Set 1 if this is the first post in the list
	 */
	function showPost($id,$ext=0,$first=0) {
		global $imSettings;
		global $l10n;
		$bp = $imSettings['blog']['posts'][$id];

		echo "<h2 id=\"imPgTitle\" style=\"display: block;\">" . $bp['title'] . "</h2>\n";
		echo "<div class=\"imBreadcrumb\" style=\"display: block;\">" . $l10n['blog_published_by'] . "<strong> " . $bp['author'] . " </strong>";
		echo $l10n['blog_in'] . " <a href=\"?category=" . $bp['category'] . "\" target=\"_blank\" rel=\"nofollow\">" . $bp['category'] . "</a> &#149; " . $bp['timestamp'];

		// Media audio/video
		if ($bp['media'] != null) {
			echo " &#149; <a href=\"" . $bp['media'] . "\">Download " . basename($bp['media']) . "</a>";
		}

		if (count($bp['tag']) > 0) {
			echo "<br />Tags: ";
			for ($i = 0; $i < count($bp['tag']); $i++) {
				echo "<a href=\"?tag=" . $bp['tag'][$i] . "\">" . $bp['tag'][$i] . "</a>";
				if ($i < count($bp['tag']) - 1)
					echo ",&nbsp;";
			}
		}
		echo "</div>";

		if($ext != 0 || $first != 0) {
			echo "<div class=\"imBlogPostBody\">";

			if ($bp['mediahtml'] != null) {
				echo $bp['mediahtml'];
			}

			echo $bp['body'];

			if (count($bp['sources']) > 0) {
				echo "<div class=\"imBlogSources\">";
				echo "<b>" . $l10n['blog_sources'] . "</b>:<br />";
				echo "<ul>";

				foreach ($bp['sources'] as $source) {
					echo "<li>" . $source . "</li>";
				}

				echo "</ul></div>";
			}
			echo (($imSettings['blog']['addThis'] != null) ? "<br />" . $imSettings['blog']['addThis'] : "") . "<br /><br /></div>";
		}
		else {
			echo "<div class=\"imBlogPostSummary\">" . $bp['summary'] . "</div>";
		}
		if($ext == 0) {
			echo "<div class=\"imBlogPostRead\"><a class=\"imCssLink\" href=\"?id=" . $id . "\">" . $l10n['blog_read_all'] ." &#187;</a></div>";
		}
		else {
			echo "<div class=\"imBlogPostFooHTML\">" . $bp['foo_html'] . "</div>";
		}

		if($ext != 0 && $bp['comments'] == true) { //&& @chdir($imSettings['general']['dir'])
			echo "<div class=\"imBlogPostComments\">";
			if(($c = $this->getComments($id)) != -1) {
				if(is_array($c))
					foreach($c as $comment)
						if($comment['approved'] == 1)
							$ca[] = $comment;
				echo "<div class=\"imBlogCommentsCount\">" . (count($ca) > 0 ? count($ca) . " " . (count($ca) > 1 ? $l10n['blog_comments'] : $l10n['blog_comment']) : $l10n['blog_no_comment']) . "</div>";
				for($i = 0;$i < count($ca); $i++) {
					echo "<div class=\"imBlogPostCommentUser\">" . (stristr($ca[$i]['url'],"http") ? "<a href=\"" . $ca[$i]['url'] . "\" target=\"_blank\">" . $ca[$i]['name'] . "</a>" : $ca[$i]['name']) . "</div>";
					echo "<div class=\"imBlogPostCommentDate imBreadcrumb\" style=\"display: block\">" . $this->formatTimestamp($ca[$i]['timestamp']) . "</div>";
					echo "<div class=\"imBlogPostCommentBody\">" . $ca[$i]['body'] . "</div>";
					echo "<div class=\"imBlogPostAbuse\"><a href=\"" . basename($_SERVER['PHP_SELF']) . "?id=" . $id . "&abuse=" . ($i + 1) . "\"><img src=\"../res/exclamation.png\" alt=\"" . $l10n['blog_abuse'] . "\" title=\"" . $l10n['blog_abuse'] . "\" /></a></div>";
				}
				echo "<br />";
			}
			else {
					echo "<div class=\"imBlogCommentsCount\">" . $l10n['blog_no_comment'] . "</div>";
			}
			if($_GET['ok'] == 1 && $imSettings['blog']['approve_comments']) {
				echo "<div class=\"imBlogCommentsMsgOK\">" . $l10n['blog_send_confirmation'] . "<br /></div>";
			}
			if($_GET['err'] != "") {
				echo "<div class=\"imBlogCommentsMsgErr\">" . $l10n['blog_send_error'] . "<br /></div>";
			}
			echo "<div class=\"imBlogCommentsForm\" style=\"width: 300px;\">
			  <form id=\"blogComment\" action=\"" . $_SERVER['PHP_SELF'] . "?id=" . $id . "\" method=\"post\" onsubmit=\"return x5engine.imForm.validate(this, {type: 'tip', showAll: true})\">
				<input type=\"hidden\" name=\"post_id\" value=\"" . $id . "\"/>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_name\" style=\"float: left;\">" . $l10n['blog_name'] . "*</label> <input type=\"text\" id=\"form_name\" name=\"name\" class=\"imfield mandatory\" style=\"float: right;\" />
				</div>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_email\" style=\"float: left;\">" . $l10n['blog_email'] . "*</label> <input type=\"text\" id=\"form_email\" name=\"email\" class=\"imfield mandatory valEmail\" style=\"float: right;\" />
				</div>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_url\" style=\"float: left;\">" . $l10n['blog_website'] . "</label> <input type=\"text\" id=\"form_url\" name=\"url\" style=\"float: right;\" class=\"imfield\" />
				</div>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_body\" style=\"clear: both;\">" . $l10n['blog_message'] . "*</label><textarea id=\"form_body\" name=\"body\" class=\"imfield mandatory\" style=\"width: 100%; height: 100px;\"></textarea>
				</div>";
				if ($imSettings['blog']['captcha'])
					echo "<div class=\"imBlogCommentRow\" style=\"text-align: center\">
							<label for=\"imCpt\" style=\"float: left;\">" . $l10n['form_captcha_title'] . "</label>&nbsp;<input type=\"text\" id=\"imCpt\" name=\"imCpt\" class=\"imfield imCpt[5,../]\" size=\"5\" style=\"width: 120px; margin: 0 auto;\" />
						</div>";
				echo "<div class=\"imBlogCommentRow\" style=\"text-align: center; margin-bottom: 15px;\"><input type=\"submit\" value=\"" . $l10n['blog_send'] . "\" class=\"imBlogCommentSubmitBtn\"/></div>
			  </form>
			</div>
			</div>";
		}
	}

	/**
	 * Find the posts tagged with tag
	 * @param tag The searched tag
	 */
	function showTag($tag) {
		global $imSettings;
		if (count($imSettings['blog']['posts']) > 0) {
			$i = 0;
			foreach ($imSettings['blog']['posts'] as $id => $post) {
				if (in_array($tag, $post['tag']))
					echo $this->showPost($id,0,(($i == 0) ? 1 : 0));
				if ($i > 0)
					echo x5engine.imBlog.separator;
				$i++;
			}
		}
		else {
			echo "<div class=\"imBlogEmpty\">Empty blog</div>";
		}
	}

	/**
	 * Find the post in a category
	 * @param category the category ID
	 */
	function showCategory($category) {
		global $imSettings;
		$bps = $imSettings['blog']['posts_cat'][$category];
		if(is_array($bps)) {
			$bpsc = count($bps);
			for($i = 0; $i < $bpsc; $i++)
				$this->showPost($bps[$i],0,($i == 0 ? 1 : 0));
		}
		else {
			echo "<div class=\"imBlogEmpty\">Empty category</div>";
		}
	}

	/**
	 * Find the posts of the month
	 * @param month
	 */
	function showMonth($month) {
		global $imSettings;
		$bps = $imSettings['blog']['posts_month'][$month];
		if(is_array($bps)) {
			$bpsc = count($bps);
			for($i = 0; $i < $bpsc; $i++)
				$this->showPost($bps[$i],0,($i == 0 ? 1 : 0));
		}
		else {
			echo "<div class=\"imBlogEmpty\">Empty month</div>";
		}
	}

	/**
	 * Show the last n posts
	 * @param count the number of posts to show
	 */
	function showLast($count) {
		global $imSettings;
		$bps = array_keys($imSettings['blog']['posts']);
		if(is_array($bps)) {
			$bpsc = count($bps);
			for($i = 0; $i < ($bpsc<$count ? $bpsc : $count); $i++)
				$this->showPost($bps[$i],0,($i == 0 ? 1 : 0));
		}
		else {
			echo "<div class=\"imBlogEmpty\">Empty blog</div>";
		}
	}

	/**
	 * Show the search results
	 * @param search the search query
	 */
	function showSearch($search) {
		global $imSettings;
		$bps = array_keys($imSettings['blog']['posts']);
		$j = 0;
		if(is_array($bps)) {
			$bpsc = count($bps);
			for($i = 0; $i < $bpsc; $i++) {
				if(stristr($imSettings['blog']['posts'][$bps[$i]]['title'],$search) || stristr($imSettings['blog']['posts'][$bps[$i]]['summary'],$search) || stristr($imSettings['blog']['posts'][$bps[$i]]['body'],$search)) {
					$this->showPost($bps[$i],0,($j == 0 ? 1 : 0));
					$j++;
				}
			}
			if($j == 0) {
				echo "<div class=\"imBlogEmpty\">Empty search</div>";
			}
		}
		else {
			echo "<div class=\"imBlogEmpty\">Empty blog</div>";
		}
	}

	/**
	 * Show the categories sideblock
	 * @param n The number of categories to show
	 */
	function showBlockCategories($n) {
		global $imSettings;

		if (is_array($imSettings['blog']['posts_cat'])) {
			$categories = array_keys($imSettings['blog']['posts_cat']);
			array_multisort($categories);
			echo "<ul>";
			for ($i = 0; $i < count($categories) && $i < $n; $i++) {
				$post = $imSettings['blog']['posts'][$imSettings['blog']['posts_cat'][$categories[$i]][0]];
				echo "<li><a href=\"?category=" . $categories[$i] . "\">" . $post['category'] . "</a></li>";
			}
			echo "</ul>";
		}
	}

	/**
	 * Show the cloud sideblock
	 * @param type TAGS or CATEGORY
	 */
	function showBlockCloud($type) {
		global $imSettings;

		$max = 0;
		if ($type == "tags") {
			$tags = array();
			foreach ($imSettings['blog']['posts'] as $id => $post) {
				foreach ($post['tag'] as $tag) {
					if ($tags[$tag] == null)
						$tags[$tag] = 1;
					else
						$tags[$tag] = $tags[$tag] + 1;
					if ($tags[$tag] > $max)
						$max = $tags[$tag];
				}
			}

			$tags = shuffle_assoc($tags);

			$min_em = 0.7;
			$max_em = 1.3;
			foreach ($tags as $name => $number) {
				$size = number_format(($number/$max * ($max_em - $min_em)) + $min_em, 2, '.', '');
				echo "<span class=\"imBlogCloudItem\" style=\"font-size: " . $size . "em;\"><a href=\"?tag=" . $name . "\">" . str_replace("_", " ", $name) . "</a></span>\n";
			}
		} else if ($type == "categories") {
			$categories = array();
			foreach ($imSettings['blog']['posts'] as $id => $post) {
				if ($categories[$post['category']] == null)
					$categories[$post['category']] = 1;
				else
					$categories[$post['category']] = $categories[$post['category']] + 1;
				if ($categories[$post['category']] > $max)
					$max = $categories[$post['category']];
			}

			$categories[$category] = shuffle_assoc($categories[$category]);

			$min_em = 0.7;
			$max_em = 1.3;
			foreach ($categories as $name => $number) {
				$size = number_format(($number/$max * ($max_em - $min_em)) + $min_em, 2, '.', '');
				echo "<span class=\"imBlogCloudItem\" style=\"font-size: " . $size . "em;\"><a href=\"?category=" . str_replace(" ", "_", $name) . "\">" . $name . "</a></span>\n";
			}
		}
	}

	/**
	 * Show the month sideblock
	 * @param n Number of entries
	 */
	function showBlockMonths($n) {
		global $imSettings;

		if (is_array($imSettings['blog']['posts_month'])) {
			$months = array_keys($imSettings['blog']['posts_month']);
			array_multisort($months);
			echo "<ul>";
			for ($i = 0; $i < count($months) && $i < $n; $i++) {
				$post = $imSettings['blog']['posts'][$imSettings['blog']['posts_cat'][$categories[$i]][0]];
				echo "<li><a href=\"?month=" . $months[$i] . "\">" . substr($months[$i], 4) . "/" . substr($months[$i], 0, 4) . "</a></li>";
			}
			echo "</ul>";
		}
	}

	/**
	 * Show the last posts block
	 * @param n The number of post to show
	 */
	function showBlockLast($n) {
		global $imSettings;

		if (is_array($imSettings['blog']['posts'])) {
			echo "<ul>";
			for ($i = 0; $i < count($imSettings['blog']['posts']) && $i < $n; $i++) {
				$post = array_keys($imSettings['blog']['posts']);
				$post = $imSettings['blog']['posts'][$post[$i]];
				echo "<li><a href=\"?id=" . $post['id'] . "\">" . $post['title'] . "</a></li>";
			}
			echo "</ul>";
		}
	}
}

/**
 * Guestbook class
 * @access public
 */
class imGuestBook {
	var $comments;
	var $path;
	var $email;
	var $direct_approval;

	// PHP 5
	function __construct($path, $email = '', $direct_approval = TRUE) {
		if ($direct_approval == TRUE)
			$this->direct_approval = 1;
		else
			$this->direct_approval = 0;
		if ($email != "" && $email != null)
			$this->email = $email;
		$this->comments = new imComment();
		if (substr($path, -1, 1) != "/" && $path != "")
			$path .= "/";
		if ($path != null)
			$this->path = $path;
		else
			$this->path = $imSettings['general']['dir'];
			
		if (!file_exists($this->path) && $this->path != "" && $this->path != "./.") {
			mkdir($this->path, 0777, TRUE);
		}
	}

	// PHP 4
	function imGuestBook($path, $email = '', $direct_approval = TRUE) {
		if ($direct_approval == TRUE)
			$this->direct_approval = 1;
		else
			$this->direct_approval = 0;
		if ($email != "" && $email != null)
			$this->email = $email;
		$this->comments = new imComment();
		if (substr($path, -1, 1) != "/" && $path != "")
			$path .= "/";
		if ($path != null)
			$this->path = $path;
		else
			$this->path = $imSettings['general']['dir'];
			
		if (!file_exists($this->path) && $this->path != "" && $this->path != "./.") {
			mkdir($this->path, 0777, TRUE);
		}
	}

	function formatTimestamp($ts) {
		return date("d/m/Y H:i:s", strtotime($ts));
	}

	/**
	 * Get the comments of the guestbook ID
	 * @param id The guestbook ID
	 */
	function getComments($id) {
		global $imSettings;
		return $this->comments->getComments($this->path . "gb" . $id);
	}

	/**
	 * Add a comment
	 * @param id The guestbook ID
	 * @param name The user's name
	 * @param email The user's email
	 * @param url The user's site url
	 * @param body The user's message
	 */
	function addComment($id,$name,$email,$url,$body) {
		global $imSettings;
		if (!file_exists($this->path) && $this->path != "" && $this->path != "./.")
			@mkdir($this->path, 0777, TRUE);
		$em = new imSendEmail();
		$em->sendGuestbookEmail($id, $name, $email, $url, $body, $this->direct_approval, $this->email);
		return $this->comments->addComment($this->path . "gb" . $id,$name,$email,$url,$body, "0", $this->direct_approval);
	}

	/**
	 * Approve a comment
	 * @param id the guestbook ID
	 * @param n The comment number
	 * @param approved Set 1 to approve
	 */
	function approveComment($id,$n,$approved) {
		global $imSettings;
		return $this->comments->approveComment($this->path . "gb" . $id,$n,$approved);
	}

	/**
	 * Delete a comment
	 * @param id the guestbook ID
	 * @param n The comment number
	 */
	function removeComment($id,$n) {
		global $imSettings;
		return $this->comments->removeComment($this->path . "gb" . $id, $n);
	}

	/**
	 * Abuse a comment
	 * @param post the post ID
	 * @param n The comment number
	 * @param abuse Set 1 to abuse
	 */
	function setAbuse($id, $n, $abuse) {
		global $imSettings;
		return $this->comments->setAbuse($this->path . "gb" . $id, $n, $abuse);
	}

	/**
	 * Show a guestbook
	 * @param id the guestbook ID
	 * @param captcha Set TRUE to show
	 */
	function showGuestBook($id, $captcha = TRUE) {
		global $imSettings;
		global $l10n;
		echo "<div class=\"imBlogPostComments\">";
		if(($c = $this->getComments($id)) != -1) {
			if(is_array($c))
				foreach($c as $comment)
					if($comment['approved'] == 1)
						$ca[] = $comment;
			echo "<div class=\"imBlogCommentsCount\">" . (count($ca) > 0 ? count($ca) . " " . (count($ca) > 1 ? $l10n['blog_comments'] : $l10n['blog_comment']) : $l10n['blog_no_comment']) . "</div>";
			for($i = 0;$i < count($ca); $i++) {
				echo "<div class=\"imBlogPostCommentUser\">" . (stristr($ca[$i]['url'],"http") ? "<a href=\"" . $ca[$i]['url'] . "\" target=\"_blank\">" . $ca[$i]['name'] . "</a>" : $ca[$i]['name']) . "</div>";
				echo "<div class=\"imBlogPostCommentDate imBreadcrumb\" style=\"display: block;\">" . $this->formatTimestamp($ca[$i]['timestamp']) . "</div>";
				echo "<div class=\"imBlogPostCommentBody\">" . $ca[$i]['body'] . "</div>";
				echo "<div class=\"imBlogPostAbuse\"><a href=\"" . basename($_SERVER['PHP_SELF']) . "?id=" . $id . "&abuse=" . ($i + 1) . "\"><img src=\"res/exclamation.png\" alt=\"" . $l10n['blog_abuse'] . "\" title=\"" . $l10n['blog_abuse'] . "\" /></a></div>";
			}
		}
		else {
			echo "<div class=\"imGuestbookCount\">" . $l10n['blog_no_comment'] . "</div>";
		}
		if($_GET['ok_' . $id] == 1 && $this->direct_approval == 0) {
			echo "<div class=\"imGuestbookMsgOK\">" . $l10n['blog_send_confirmation'] . "</div><br /><br />";
		}
		if($_GET['err_' . $id] != "") {
			echo "<div class=\"imGuestbookErr\">" . $l10n['blog_send_error'] . "</div><br /><br />";
		}

		echo "<div class=\"imBlogCommentsForm\">
			  <form id=\"guestBookComment_" . $id . "\" action=\"" . $_SERVER['PHP_SELF'] . "?id=" . $id . "\" method=\"post\" onsubmit=\"return x5engine.imForm.validate(this, {type: 'tip', showAll: true})\">
				<input type=\"hidden\" name=\"post_id\" value=\"" . $id . "\"/>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_name\" style=\"float: left; width: 100px;\">" . $l10n['blog_name'] . "*</label> <input type=\"text\" id=\"form_name\" name=\"name\" class=\"imfield mandatory\" />
				</div>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_email\" style=\"float: left; width: 100px;\">" . $l10n['blog_email'] . "*</label> <input type=\"text\" id=\"form_email\" name=\"email\" class=\"imfield mandatory valEmail\"/>
				</div>
				<div class=\"imBlogCommentRow\">
					<label for=\"form_url\" style=\"float: left; width: 100px;\">" . $l10n['blog_website'] . "</label> <input type=\"text\" id=\"form_url\" name=\"url\" class=\"imfield\" />
				</div>
				<div class=\"imBlogCommentRow\">
					<br /><label for=\"form_body\" style=\"clear: both; width: 100px;\">" . $l10n['blog_message'] . "*</label><textarea id=\"form_body\" name=\"body\" class=\"imfield mandatory\" style=\"width: 95%; height: 100px;\"></textarea>
				</div>";
				if ($captcha)
					echo "<div class=\"imBlogCommentRow\" style=\"text-align: center\">
							<label for=\"imGuestBookCpt" . $id . "_imCpt\" style=\"float: left;\">" . $l10n['form_captcha_title'] . "</label>&nbsp;<input type=\"text\" id=\"imGuestBookCpt" . $id . "_imCpt\" name=\"imCpt\" maxlength=\"5\" class=\"imfield imCpt[5]\" size=\"5\" style=\"width: 120px; margin: 0 auto;\" />
						</div>";
				echo "<div class=\"imBlogCommentRow\" style=\"text-align: center\">
						<input type=\"submit\" value=\"" . $l10n['form_submit'] . "\" />
						<input type=\"reset\" value=\"" . $l10n['form_reset'] . "\" />
					</div>
			  </form>
			  <script type=\"text/javascript\">x5engine.imForm.initForm('#guestBookComment_" . $id . "');</script>
			</div>
			</div>";
	}
}

/**
 * Star rating class
 * @access public
 */
class imStarRating {

	var $id;
	var $scale;

	// PHP 5
	function __construct($id, $scale) {
		$this->id = $id;
		$this->scale = $scale;
	}

	// PHP 4
	function imStarRating($id, $scale) {
		$this->id = $id;
		$this->scale = $scale;
	}

	/**
	 * Get the rating from a file
	 */
	function getRating() {
		global $imSettings;
		$xml = new imXML();
		$rating = $xml->parse_file($imSettings['general']['dir'] . "/" . $imSettings['guestbook']['file_prefix'] . "sr" . $this->id);
		if ($rating['scale'] == "" || $rating['scale'] == null)
			$rating['scale'] = $this->scale;
		if (is_array($rating))
			return $rating;
		else
			return FALSE;
	}

	/**
	 * Set a rating
	 * @param value The rating value
	 * @param scale The rating maximum value
	 */
	function setRating($value, $scale) {
		global $imSettings;
		$rating = $this->getRating();
		if (!$rating)
			$rating = array('scale' => $scale, 'vote_sum' => 0, 'count' => 0);
		$rating['vote_sum'] += $value;
		$rating['count'] += 1;

		$fp = fopen($imSettings['general']['dir'] . "/" . $imSettings['guestbook']['file_prefix'] . "sr" . $this->id, "w");
		if (!$fp)
			return FALSE;

		$xml =  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$xml .= "<rating>\n";
		$xml .= "\t<count>" . $rating['count'] . "</count>\n";
		$xml .= "\t<vote_sum>" . $rating['vote_sum'] . "</vote_sum>\n";
		$xml .= "\t<scale>" . $rating['scale'] . "</scale>\n";
		$xml .= "</rating>";

		fwrite($fp, $xml);
		fclose($fp);

		return TRUE;
	}

	/**
	 * Show the rating widget
	 */
	function showWidget() {
		$html =  "<div class=\"imStarRating\">\n";
		$avg_vote = 0;
		if (@$_COOKIE[$this->id] != "y" && @$_POST['id'] != "" && @$_POST['scale'] != "" && @$_POST['value'] != "" && @$_POST['id'] == $this->id) {
			$this->setRating(@$_POST['value'], @$_POST['scale']);
		}
		$rating = $this->getRating();
		if ($rating['count'] > 0)
			$avg_vote = round($rating['vote_sum'] / $rating['count'], 1);
		$html .= "Vote: " . $avg_vote . "/" . $this->scale . "<br />";
		if (@$_COOKIE[$this->id] != "y") {
			for ($i = 0; $i < $rating['scale']; $i++)
				$html .= "<img src=\"res/star_empty.png\" style=\"cursor: pointer;\" id=\"" . $this->id . "_star" . ($i + 1) . "\" class=\"imStarRating[" . ($i + 1) . "]\" />";
			$html .= "<script type=\"text/javascript\">x5engine.imQueue.push_init(\"x5engine.imStarRating('" . $this->id . "', " . $rating['scale'] . ", '" . basename($_SERVER['PHP_SELF']) . "')\");</script>\n";
		}
		$html .= "</div>\n";

		echo $html;
	}
}

/**
 * Server Test Class
 * @access public
 */
class imTest {
	
	/*
	 * Session check
	 */
	function session_test() {
		global $l10n;
		if (!isset($_SESSION))
			return array(FALSE, null);
		$_SESSION['imAdmin_test'] = "test_message";
		if ($_SESSION['imAdmin_test'] != "test_message")
			return array(FALSE, $l10n['admin_test_session_suggestion']);
		return array(TRUE);
	}

	/*
	 * Writable files check
	 */
	function writable_folder_test() {
		global $imSettings;
		global $l10n;
		
		$root = getcwd();
		$dir = $imSettings['general']['dir'];
		
		if (!file_exists($imSettings['general']['dir']) && $imSettings['general']['dir'] != "" && $imSettings['general']['dir'] != "./.")
			@mkdir($imSettings['general']['dir'], 0777, TRUE);

		if ($dir != "" && !@chdir($dir)) {
			@chdir($root);
			return array(FALSE, $l10n['admin_test_folder_suggestion']);
		}

		$fp = @fopen("imAdmin_test_file", "w");
		if (!$fp) {
			@chdir($root);
			return array(FALSE, $l10n['admin_test_folder_suggestion']);
		}
		if (fwrite($fp, "test") === FALSE) {
			@chdir($root);
			return array(FALSE, $l10n['admin_test_folder_suggestion']);
		}
		fclose($fp);
		if (file_exists("imAdmin_test_file")) {
			unlink("imAdmin_test_file");
			@chdir($root);
			return array(TRUE);
		}
		@chdir($root);
		return array(FALSE, $l10n['admin_test_folder_suggestion']);
	}

	/*
	 * PHP Version check
	 */
	function php_version_test() {
		global $l10n;
		$php_version = PHP_VERSION;
		if (version_compare($php_version, '4.0.0') < 0)
			return array(FALSE, $l10n['admin_test_version_suggestion']);

		return array(TRUE, $l10n['admin_test_version_suggestion']);
	}

	/*
	 * MySQL Connection check
	 */
	function mysql_test() {
		global $imSettings;
		global $l10n;
		$r = TRUE;
		$dir = "mail";
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== FALSE) {
					if ($file != ".." && $file != ".")
						include($dir . "/" . $file);
				}
				closedir($dh);
			}
		}
		
		if (is_array($settings)) {
			foreach ($settings as $form) {
				if ($form['db_host'] != NULL) {
					$test = new imDatabase($form['db_host'], $form['db_name'], $form['db_username'], $form['db_password'], "", "");
					if ($test->test_connection() === FALSE)
						$r = FALSE;
				}
			}
		}

		return array($r, $l10n['admin_test_database_suggestion']);
	}

	/*
	 * Do the test
	 */
	function doTest($name, $funct) {
		$result = $this->$funct();
		if ($result[0])
			echo "<div class=\"imTest pass\">" . $name . "<span>PASS</span></div>";
		else
			echo "<div class=\"imTest fail\">" . $name . "<span>FAIL</span><br /><img src=\"../res/info.gif\"/ style=\"vertical-align: middle;\"> " . $result[1] . "</div>";
	}
}

/**
 * Google Webmaster Tools Class
 * @access public
 */
class imGoogle {

	var $auth;
	var $err = false;

	// PHP 5
	function __construct($uname, $pwd, $service) {
		global $phpError;
		$curl = curl_init();
		if (curl_setopt($curl, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin") && !$phpError) {
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_POST, true);
			$post = array(
				'accountType' => 'HOSTED_OR_GOOGLE',
				'Email' => $uname,
				'Passwd' => $pwd,
				'service' => $service,
				'source' => ''
			);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			$output = curl_exec($curl);
			$info = curl_getinfo($curl);
			curl_close($curl);
			if($info['http_code'] == 200) {
				preg_match('/Auth=(.*)/', $output, $match);
				if(isset($match[1]))
					$this->auth = $match[1];
				else
					$this->auth = FALSE;
			}
		} else {
			$this->err = TRUE;
			return FALSE;
		}
	}

	// PHP 4
	function imGoogle($uname, $pwd, $service) {
		global $phpError;
		$curl = curl_init();
		if (curl_setopt($curl, CURLOPT_URL, "https://www.google.com/accounts/ClientLogin") && !$phpError) {
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($curl, CURLOPT_POST, true);
			$post = array(
				'accountType' => 'HOSTED_OR_GOOGLE',
				'Email' => $uname,
				'Passwd' => $pwd,
				'service' => $service,
				'source' => ''
			);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			$output = curl_exec($curl);
			$info = curl_getinfo($curl);
			curl_close($curl);
			if($info['http_code'] == 200) {
				preg_match('/Auth=(.*)/', $output, $match);
				if(isset($match[1]))
					$this->auth = $match[1];
				else
					$this->auth = FALSE;
			}
		} else {
			$this->err = TRUE;
			return FALSE;
		}
	}

	function urlencoding($site) {
		return str_replace(".", "%2E", urlencode($site));
	}

	/**
	 * Get an array with the data results from Google
	 * @param site The site url
	 * @param operation the operation id
	 */
	function readServiceData($site, $operation) {
		if ($this->auth === FALSE)
			return FALSE;

		if(strlen($site)>0)$request = $this->urlencoding($site) . "/" . $operation . "/";
		else $request = $operation."/";
		$url = "https://www.google.com/webmasters/tools/feeds/" . $request;
		$curl = curl_init();
		$head = array("Authorization: GoogleLogin auth=" . $this->auth,"GData-Version: 2");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $head);
		$result = curl_exec($curl);
		$info = curl_getinfo($curl);
		curl_close($curl);
		if ($info['http_code']!=200)
			return FALSE;

		$xml = new imXML();
		$xml_output = $xml->parse_string($result);

		return $xml_output;
	}

	function getKeywords($site) {
		return $this->readServiceData($site, "keywords");
	}

	function getSitemap($site) {
		return $this->readServiceData($site, "sitemaps");
	}

	function getMessages($site) {
		return $this->readServiceData($site, "messages");
	}

	function getCrawler($site) {
		return $this->readServiceData($site, "crawlissues");
	}
}

/**
* Set the error handler
*/

function imErrorHandler($errno, $errstr, $errfile, $errline)
{
	global $phpError;
	$phpError = true;
    return true;
}

set_error_handler("imErrorHandler");

/**
 * Useful functions
 */
 
function filterCode($str) {
	while (($start = strpos($str, "<script")) !== FALSE) {
		$end = strpos($str, "</script>") + strlen("</script>");
		$str = substr($str, 0, $start) . substr($str, $end);
	}
	
	while (($start = strpos($str, "<?")) !== FALSE) {
		$end = strpos($str, "?>") + strlen("?>");
		$str = substr($str, 0, $start) . substr($str, $end);
	}
	
	while (($start = strpos($str, "<%")) !== FALSE) {
		$end = strpos($str, "%>") + strlen("<%");
		$str = substr($str, 0, $start) . substr($str, $end);
	}
	
	$str = strip_tags($str, '<b><i><u>');
	
	$count = 1;
	while ($count) $str = preg_replace("/(<[\\s\\S]+) on.*\\=(['\"\"])[\\s\\S]+\\2/i", "\\1", $str, -1, $count);
		
	return $str;
}

function imPrintJsError() {
	global $l10n;
	$html = "<DOCTYPE><html><head><meta http-equiv=\"Refresh\" content=\"5;URL=" . $_SERVER['HTTP_REFERER'] . "\"></head><body>";
	$html .= $l10n['form_js_error'];
	$html .= "</body></html>";
	return $html;
}

function imCheckAccess($page) {
	$pa = new imPrivateArea();
	$stat = $pa->checkAccess($page);
	if ($stat == -1) {
		$pa->save_page();
		header("Location: imlogin.php");
	} else if ($stat == -2) {
		$pa->save_page();
		header("Location: imlogin.php?err=1");
	}
}

function showGuestBook($id, $path, $email, $captcha = TRUE, $direct_approval = TRUE) {
	$gb = new imGuestBook($path, $email, $direct_approval);

	if (isset($_GET['abuse']))
		$gb->setAbuse($id, $_GET['abuse'], 1);

	if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['body']) && isset($_POST['post_id']) && $_POST['post_id'] == $id) {
		$result = $gb->addComment($id,$_POST['name'],$_POST['email'],$_POST['url'],$_POST['body']);
		if ($result === 0)
			echo "<script type=\"text/javascript\">location.href='" . $_SERVER['PHP_SELF'] . "?ok_" . $id . "=1';</script>";
		else
			echo "<script type=\"text/javascript\">location.href='" . $_SERVER['PHP_SELF'] . "?err_" . $id . "=" . $result . "';</script>";
	} else
		$gb->showGuestBook($id, $captcha);
}

function showStarRating($id) {
	$rating = new imStarRating($id, 5);
	$rating->showWidget();
}

function imCurrency($amount,$from,$to) {
	$amount = urlencode($amount);
	$from = urlencode($from);
	$to = urlencode($to);
	$url = "http://www.google.com/ig/calculator?hl=en&q=" . $amount . $from . "=?" . $to;
	$curl = curl_init();
	$timeout = 0;
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$rawdata = curl_exec($curl);
	curl_close($curl);
	$data = explode('"', $rawdata);
	$data = explode(' ', $data['3']);
	$var = $data['0'];
	return "{ \"value\": " . $var . "}";
}

function imValidateVAT($vat, $country) {
	$url = "http://isvat.appspot.com/" . $country . "/" . $vat . "/?callback=?";
	$curl = curl_init();
	$timeout = 0;
	curl_setopt ($curl, CURLOPT_URL, $url);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	curl_setopt ($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
	return curl_exec($curl);
}

function shuffle_assoc($list) {
  if (!is_array($list)) return $list;

  $keys = array_keys($list);
  shuffle($keys);
  $random = array();
  foreach ($keys as $key)
    $random[$key] = $list[$key];

  return $random;
}

// End of file x5engine.php