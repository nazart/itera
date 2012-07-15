<?php
	$settings['imEmailForm_12_2'] = array(
		"owner_email_from" => "Email:",
		"owner_email_to" => "servicio.cliente@itera.com.pe",
		"customer_email_from" => "servicio.cliente@itera.com.pe",
		"customer_email_to" => "Email:",
		"owner_message" => "",
		"customer_message" => "Gracias por escribirnos a ITERA S.A.C

Uno de Nuestros Gerentes de Cuentas Corporativas atenderá su requerimiento.

Atentamente,

ITERA S.A.C 
Central: (511) 652 - 4242 | Fax: (511) 638 - 1772
",
		"owner_subject" => "Consulta Web",
		"customer_subject" => "Consulta Web",
		"owner_csv" => False,
		"customer_csv" => False,
		"confirmation_page" => "../index.html"
	);

	if(substr(basename($_SERVER['PHP_SELF']), 0, 11) == "imEmailForm") {
		include "../res/x5engine.php";

		$answers = array(
		);

		$form_data = array(
			"Nombre y Apellidos:" => $_POST['imObjectForm_2_1'],
			"Empresa:" => $_POST['imObjectForm_2_2'],
			"Teléfono:" => $_POST['imObjectForm_2_3'],
			"Email:" => $_POST['imObjectForm_2_4'],
			"Producto:" => $_POST['imObjectForm_2_5'],
			"Cantidad de PCs:" => $_POST['imObjectForm_2_6']
		);

		$files_data = array(
		);

		if(@$_POST['action'] != "check_answer") {
			if(!isset($_POST['imJsCheck']) || $_POST['imJsCheck'] != "jsactive")
				die(imPrintJsError());
			if (isset($_POST['imCpt']) && !isset($_POST['imCptHdn']))
				die(imPrintJsError());
			if(isset($_POST['imSpProt']) && $_POST['imSpProt'] != "")
				die(imPrintJsError());
			$email = new imSendEmail();
			$email->sendFormEmail($settings['imEmailForm_12_2'], $form_data, $files_data);
			@header('Location: ' . $settings['imEmailForm_12_2']['confirmation_page']);
		} else {
			if(@$_POST['id'] == "" || @$_POST['answer'] == "" || strtolower(trim($answers[@$_POST['id']])) != strtolower(trim(@$_POST['answer'])))
				echo "0";
			else
				echo "1";
		}
	}

// End of file