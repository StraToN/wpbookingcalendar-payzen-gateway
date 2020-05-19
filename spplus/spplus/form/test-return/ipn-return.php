<?php

/**
 * You can set the return URL in your back office for IPN
 * this file serves the Instant Payment Notification URL first
 *
 * IPN
 * URL of the page that analyzes the payment outcome must be specified in the Back Office
 * The merchant has to make sure that this URL is available from the payment gateway without redirection.
 * Redirection leads to losing data presented in POST.
 *
 * vads_hash
 * A unique key sent only to the IPN.
 */

/**
 * saveIpn
 * a simple example to save data in a log file
 * usually data will be saved in your Database
 *
 * @param null $data
 *
 * @throws Exception
 */
function saveIpn($data = null)
{
	// get file
	$filename = dirname(__FILE__) . "/log"; // filePath
	$handle = fopen($filename, 'a+') or die("Unable to open file!");
	if (! file_exists($filename) || ! $handle) {
		throw new Exception('Log file can not be opened or does not exist');
	} else {
		flock($handle, LOCK_EX);
		$msg = "\n" . date('d-m-Y h:i:s') . "\n" . $data;
		fwrite($handle, $msg);
		flock($handle, LOCK_UN);
		fclose($handle);
	}
}

// require_once ("../lib/class-payment-form-toolbox.php");
require_once ("../../../wpbc-gw-spplus.php");
$args = WPBC_Gateway_API_SPPlus::get_PaymentFormToolBox_args();

// print_r($args);
$toolbox = new paymentFormToolbox($args);
$control = $toolbox->checkSignature($_POST);

if (! $control) {
	$err = "Signature check failed!";
	echo $err;
	error_log($err);
}

if (isset($_POST['vads_hash']) && $control) {
	$response = $toolbox->getIpn();
	// error_log(print_r($response, true));
	saveIpn($response);
}

