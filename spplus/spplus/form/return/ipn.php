<?php
error_reporting(E_ALL ^ E_NOTICE);

define('WP_BK_RESPONSE_IPN_MODE', true);
// Load the main libraries
require_once (dirname(__FILE__) . '/../../../../wpbc-response.php');
/*
 * Set errors recording (Only in DEBUG mode) ///////////////////////////////////
 * Catch these fatal errors and log to the ipn_errors.log.
 * By default this file is not exist in the production version.
 * So if you are need to make debug, firstly you are need to create the ipn_errors.log
 * at te same folder as this file with correct permission.
 * After you are finish debug process, please delete ipn_errors.log file!!! /*
 */
define('WPDEV_BK_IPN_DEBUG_MODE', false);
if (WPDEV_BK_IPN_DEBUG_MODE) {
	ini_set('log_errors', true);
	ini_set('error_log', dirname(__FILE__) . '/ipn_errors.log');
}

if (isset($_GET["json"])) {
	$body = json_decode(file_get_contents("php://input"), true);
} else {
	$body = $_POST;
}

require_once (dirname(__FILE__) . "/../../../wpbc-gw-spplus.php");
$args = WPBC_Gateway_API_SPPlus::get_PaymentFormToolBox_args();

$toolbox = WPBC_Gateway_API_SPPlus::getPaymentFormToolbox($args);
$control = $toolbox->checkSignature($body);

if (! $control) {
	$err = "Signature check failed!";
	echo $err;
	error_log($err);
	die();
}

if (isset($_POST['vads_hash']) && $control) {
	$is_ok_transaction = true;

	$response = $toolbox->getIpn();

	$booking_id = $response["vads_order_id"];

	// 1. Check that payment amount matches with booking cost
	$payment_amount = $response["vads_effective_amount"];
	$booking_cost = apply_bk_filter('get_booking_cost_from_db', '', $booking_id);
	$booking_cost = floatval($booking_cost) * 100;
	if ($booking_cost != $payment_amount) {
		$is_ok_transaction = false;
		$transaction_error_description = 'The cost of the booking is different';
	}

	// 2. Check that currency is correct
	$payment_currency = $response["vads_currency"];
	// $spplus_currency = get_bk_option('booking_spplus_curency'); // this gives "EUR", but we expect ISO value 978
	if ($payment_currency != "978") {
		$is_ok_transaction = false;
		$transaction_error_description = 'The currency of payment for booking is different';
	}

	if ($is_ok_transaction) {
		// Update payment status
		$payment_status = $response["vads_trans_status"];
		if ($payment_status == 'AUTHORISED' || $payment_status == 'ACCEPTED') {
			$spplus_payment_status = 'SPPlus:' . $payment_status;
		}
		make_bk_action('wpdev_change_payment_status', $booking_id, $spplus_payment_status);

		sleep(5);
		if ($spplus_payment_status == 'SPPlus:AUTHORISED' || $spplus_payment_status == 'SPPlus:ACCEPTED') {
			wpbc_auto_approve_booking($booking_id);
		} else {
			wpbc_auto_cancel_booking($booking_id);
		}

		if (WPDEV_BK_IPN_DEBUG_MODE)
			error_log("Booking " . $booking_id . " updated.");
	} else {
		error_log($transaction_error_description);
	}
} else {
	/*
	 * An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
	 * a good idea to have a developer or sys admin manually investigate any invalid IPN.
	 */
	error_log("Potential fraud detected.");
}

?>