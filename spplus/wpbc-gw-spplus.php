<?php
/**
 * @package  SPPlus Checkout Server  Integration
 * @category Payment Gateway for Booking Calendar
 * @author jmurgia
 * @version 1.0
 * @email julian.murgia@gmail.com
 *
 * @modified 2020-04-06
 *
 * Integration based on SPPlus Javascript embedded form
 * Based on guide: https://paiement.systempay.fr/doc/fr-FR/rest/V4.0/javascript/
 */
if (! defined('ABSPATH'))
	exit(); // Exit if accessed directly

if (! defined('WPBC_SPPLUS_GATEWAY_ID'))
	define('WPBC_SPPLUS_GATEWAY_ID', 'spplus');

if (! defined('WPBC_PLUGIN_URL'))
	define('WPBC_PLUGIN_URL', untrailingslashit(plugins_url('', WPBC_FILE)));

// <editor-fold defaultstate="collapsed" desc=" Gateway API " >

/**
 * API for Payment Gateway
 */
class WPBC_Gateway_API_SPPlus extends WPBC_Gateway_API
{

	/**
	 * Display a nice error code block
	 */
	function display_error($response)
	{
		$error = $response['answer'];
		?>
        <p
        	style="font-family: Verdana, sans-serif; font-size: 18px; color: #a52828; font-weight: BOLD;">web-service
        	call returns an error:</p>
        <table
        	style="border: 1px solid black; border-collapse: collapse; font-family: Verdana, sans-serif; line-height: 1.5; text-align: left; padding: 8px;">
        	<tr style="background-color: #a52828; color: white;">
        		<th style="padding: 8px;">Field</th>
        		<th style="padding: 8px;">value</th>
        	</tr>
        	<tr style="background-color: #f2f2f2">
        		<td style="padding: 8px;">web service:</td>
        		<td style="padding: 8px;"><?php

		echo $response['webService'];
		?></td>
        	</tr>
        	<tr>
        		<td style="padding: 8px;">errorCode:</td>
        		<td style="padding: 8px;"><?php

		echo $error['errorCode'];
		?></td>
        	</tr>
        	<tr style="background-color: #f2f2f2">
        		<td style="padding: 8px;">errorMessage:</td>
        		<td style="padding: 8px;"><?php

		echo $error['errorMessage'];
		?></td>
        	</tr>
        	<tr>
        		<td style="padding: 8px;">detailedErrorCode:</td>
        		<td style="padding: 8px;"><?php

		echo $error['detailedErrorCode'];
		?></td>
        	</tr>
        	<tr style="background-color: #f2f2f2">
        		<td style="padding: 8px;">detailedErrorMessage:</td>
        		<td style="padding: 8px;"><?php

		echo $error['detailedErrorMessage'];
		?></td>
        	</tr>
        	<tdody>
        	</tbody>

        </table>
        <?php
	}

	/**
	 * Display a nice signature error
	 */
	function signature_error($tr_uuid, $sha_key, $expected, $received)
	{
		?>
        <p style="font-family: Verdana, sans-serif; font-size: 18px; color: #a52828; font-weight: BOLD;">SHA256
        	validation failed</p>
        <table
        	style="border: 1px solid black; border-collapse: collapse; font-family: Verdana, sans-serif; line-height: 1.5; text-align: left; padding: 8px;">
        	<tr style="background-color: #a52828; color: white;">
        		<th style="padding: 8px;">Field</th>
        		<th style="padding: 8px;">value</th>
        	</tr>
        	<tr style="background-color: #f2f2f2">
        		<td style="padding: 8px;">transaction uuid:</td>
        		<td style="padding: 8px;"><?php

		echo $tr_uuid;
		?></td>
        	</tr>
        	<tr>
        		<td style="padding: 8px;">sha key:</td>
        		<td style="padding: 8px;"><?php

		echo $sha_key;
		?></td>
        	</tr>
        	<tr style="background-color: #f2f2f2">
        		<td style="padding: 8px;">expected value (calculated):</td>
        		<td style="padding: 8px;"><?php

		echo $expected;
		?></td>
        	</tr>
        	<tr>
        		<td style="padding: 8px;">recieved value (from POST):</td>
        		<td style="padding: 8px;"><?php

		echo $received;
		?></td>
        	</tr>
        	<tdody>
        	</tbody>

        </table>
        <?php
	}

	/**
	 * Get payment Form
	 *
	 * @param string $output
	 *        	- other active payment forms
	 * @param array $params
	 *        	- input params array (
	 *        	[id] => 514
	 *        	[days_input_format] => 24.05.2019
	 *        	[days_only_sql] => 2019-05-24
	 *        	[dates_sql] => 2019-05-24 00:00:00
	 *        	[check_in_date_sql] => 2019-05-24 00:00:00
	 *        	[check_out_date_sql] => 2019-05-24 00:00:00
	 *        	[dates] => 05/24/2019
	 *        	[check_in_date] => 05/24/2019
	 *        	[check_out_date] => 05/24/2019
	 *        	[check_out_plus1day] => 05/25/2019
	 *        	[dates_count] => 1
	 *        	[days_count] => 1
	 *        	[nights_count] => 1
	 *        	[check_in_date_hint] => 05/24/2019
	 *        	[check_out_date_hint] => 05/24/2019
	 *        	[start_time_hint] => 00:00
	 *        	[end_time_hint] => 00:00
	 *        	[selected_dates_hint] => 05/24/2019
	 *        	[selected_timedates_hint] => 05/24/2019
	 *        	[selected_short_dates_hint] => 05/24/2019
	 *        	[selected_short_timedates_hint] => 05/24/2019
	 *        	[days_number_hint] => 1
	 *        	[nights_number_hint] => 1
	 *        	[siteurl] => http://beta
	 *        	[resource_title] => Apartment#2
	 *        	[bookingtype] => Apartment#2
	 *        	[remote_ip] => 127.0.0.1
	 *        	[user_agent] => Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0
	 *        	[request_url] => http://beta/resource-id3/
	 *        	[current_date] => 04/24/2019
	 *        	[current_time] => 10:19
	 *        	[cost_hint] => CURRENCY_SYMBOL140.00
	 *        	[name] => John
	 *        	[secondname] => Smith
	 *        	[email] => user@beta.com
	 *        	[phone] => test
	 *        	[visitors] => 1
	 *        	[children] => 0
	 *        	[details] => test
	 *        	[term_and_condition] => I Accept term and conditions
	 *        	[booking_resource_id] => 3
	 *        	[resource_id] => 3
	 *        	[type_id] => 3
	 *        	[type] => 3
	 *        	[resource] => 3
	 *        	[content] =>
	 *        	First Name:John
	 *        	Last Name:Smith
	 *        	Email:user@beta.com
	 *        	Phone:test
	 *        	Adults: 1
	 *        	Details:
	 *        	test
	 *        	[moderatelink] => http://beta/wp-admin/admin.php?page=wpbc&view_mode=vm_listing&tab=actions&wh_booking_id=514
	 *        	[visitorbookingediturl] => http://beta/edit/?booking_hash=d4e19e315f8ed7903e38d1c8b2210356
	 *        	[visitorbookingslisting] => http://beta/list-customer-bookings/?booking_hash=d4e19e315f8ed7903e38d1c8b2210356
	 *        	[visitorbookingcancelurl] => http://beta/edit/?booking_hash=d4e19e315f8ed7903e38d1c8b2210356&booking_cancel=1
	 *        	[visitorbookingpayurl] => http://beta/edit/?booking_hash=d4e19e315f8ed7903e38d1c8b2210356&booking_pay=1
	 *        	[bookinghash] => d4e19e315f8ed7903e38d1c8b2210356
	 *        	[db_cost] => 140.00
	 *        	[db_cost_hint] => CURRENCY_SYMBOL140.00
	 *        	[modification_date] => 2019-04-24 10:19:23
	 *        	[modification_year] => 2019
	 *        	[modification_month] => 04
	 *        	[modification_day] => 24
	 *        	[modification_hour] => 10
	 *        	[modification_minutes] => 19
	 *        	[modification_seconds] => 23
	 *        	[__form] => text^selected_short_timedates_hint3^05/24/2019~text^nights_number_hint3^1~text^cost_hint3^CURRENCY_SYMBOL140.00~text^name3^John~text^secondname3^Smith~email^email3^user@beta.com~text^phone3^test~select-one^visitors3^1~select-one^children3^0~textarea^details3^test~checkbox^term_and_condition3[]^I Accept term and conditions
	 *        	[__nonce] => 155609396391.65
	 *        	[__payment_type] => payment_form
	 *        	[__is_deposit] =>
	 *        	[__additional_calendars] => Array ()
	 *        	[__booking_form_type] => standard
	 *        	[additional_description] =>
	 *        	[payment_cost] => 140.00
	 *        	[payment_cost_hint] => CURRENCY_SYMBOL140.00
	 *        	[calc_total_cost] => 140.00
	 *        	[calc_cost_hint] => CURRENCY_SYMBOL140.00
	 *        	[calc_total_cost_hint] => CURRENCY_SYMBOL140.00
	 *        	[calc_deposit_cost] => 140.00
	 *        	[calc_deposit_hint] => CURRENCY_SYMBOL140.00
	 *        	[calc_deposit_cost_hint] => CURRENCY_SYMBOL140.00
	 *        	[calc_balance_cost] => 0.00
	 *        	[calc_balance_hint] => CURRENCY_SYMBOL0.00
	 *        	[calc_balance_cost_hint] => CURRENCY_SYMBOL0.00
	 *        	[calc_original_cost] => 100.00
	 *        	[calc_original_cost_hint] => CURRENCY_SYMBOL100.00
	 *        	[calc_additional_cost] => 40.00
	 *        	[calc_additional_cost_hint] => CURRENCY_SYMBOL40.00
	 *        	[calc_coupon_discount] => 0.00
	 *        	[calc_coupon_discount_hint] => CURRENCY_SYMBOL0.00
	 *        	[payment_form_target] =>
	 *        	[cost_in_gateway] => 140.00
	 *        	[cost_in_gateway_hint] => CURRENCY_SYMBOL140.00
	 *        	[is_deposit] =>
	 *        	[gateway_hint] => Total
	 *        	)
	 * @return string - you must return in format: return $output . $your_payment_form_content
	 */
	public function get_payment_form($output, $params, $gateway_id = '')
	{
		// print_r($params);

		// Check if currently is showing this Gateway
		if (((! empty($gateway_id)) && ($gateway_id !== $this->get_id())) || (! $this->is_gateway_on()))
			return $output;

		// //////////////////////////////////////////////////////////////////////
		// Payment Options
		// //////////////////////////////////////////////////////////////////////
		$payment_options = array();
		$payment_options['subject'] = get_bk_option('booking_spplus_subject'); // 'Payment for booking %s on these day(s): %s'
		$payment_options['subject'] = apply_bk_filter('wpdev_check_for_active_language', $payment_options['subject']);
		$payment_options['subject'] = wpbc_replace_booking_shortcodes($payment_options['subject'], $params);
		$payment_options['subject'] = substr($payment_options['subject'], 0, 499);

		$payment_options['account_mode'] = get_bk_option('booking_spplus_account_mode');
		if ("test" == $payment_options['account_mode']) {
			$mode = "TEST";
		} else {
			$mode = "PRODUCTION";
		}
		$payment_options['shop_id'] = get_bk_option('booking_spplus_shop_id');
		$payment_options['test_key'] = get_bk_option('booking_spplus_test_key');
		$payment_options['production_key'] = get_bk_option('booking_spplus_production_key');
		$payment_options['platform_url'] = get_bk_option('booking_spplus_platform_url');
		$payment_options['curency'] = get_bk_option('booking_spplus_curency');

		$return_link = WPBC_PLUGIN_URL . '/inc/gateways/wpbc-response.php' . '?payed_booking=' . $params['booking_id'] . '&wp_nonce=' . $params['__nonce'] . '&pay_sys=spplus';
		// $payment_options['url_return'] = get_option('siteurl') . "/index.php/reserver/";
		// $payment_options['url_return'] = WPBC_PLUGIN_URL . '/inc/gateways/spplus/spplus/form/return/form-return.php';
		// $payment_options['url_return'] = get_option('siteurl') . get_bk_option('booking_spplus_order_successful');
		$payment_options['url_return'] = $return_link;

		// //////////////////////////////////////////////////////////////////////
		// Check about not correct configuration of settings:
		// //////////////////////////////////////////////////////////////////////
		if (empty($payment_options['curency']))
			return 'Wrong configuration in gateway settings.' . ' <em>Empty: "Currency" option</em>';

		include 'spplus/form/lib/locale/fr_FR/messages.php';
		require_once (__DIR__ . "/spplus/form/lib/class-payment-form-toolbox.php");
		$args = array(
			'shopID' => $payment_options['shop_id'], // shopID
			'certTest' => $payment_options['test_key'], // certificate, TEST-version
			'certProd' => $payment_options['production_key'], // certificate, PRODUCTION-version
			'ctxMode' => $mode, // PRODUCTION || TEST
			'platform' => $payment_options['platform_url'], // Platform URL
			'algorithm' => 'sha256', // the signature algorithm chosen in the shop configuration
			'debug' => true
		);

		// print_r($args);
		$toolbox = new paymentFormToolbox($args);

		$amount = floatval($params['cost_in_gateway']);

		// Multiply amount by 100 to have it in cents
		$amount = $amount * 100;

		$args = array(
			'vads_amount' => array(
				'value' => strval($amount), // The amount of the transaction presented in the smallest unit of the currency (cents for Euro).
				'label' => $i18n['price'],
				'type' => 'text',
				'class' => 'vads-field',
				'wrapper_class' => 'vads-wrapper',
				'readonly' => true,
				'help' => $i18n['displayrealprice']
			),
			"vads_currency" => "978",
			"vads_url_return" => $payment_options['url_return']
		);

		// Retrieve FORM DATA
		$formData = $toolbox->getFormData($args);

		// Output the form in html
		ob_start();
		?><div style="width:100%;clear:both;margin-top:20px;"></div><?php
		?><div class="spplus_div wpbc-payment-form" style="text-align:left;clear:both;"><?php

		$form = '<form action="' . $formData['form']['action'] . '" method="' . $formData['form']['method'] . '" accept-charset="' . $formData['form']['accept-charset'] . '" class="form-horizontal">';

		foreach ($formData['fields'] as $name => $value) {

			$display_value = (isset($value['value']) && is_array($value)) ? $value['value'] : $value;
			$label = (isset($value['label']) && is_array($value)) ? $value['label'] : $name;
			$class = (isset($value['class']) && is_array($value)) ? $value['class'] : '';
			$help = (isset($value['help']) && $value['help'] !== '' && is_array($value)) ? ' ' . $value['help'] : '';
			$wrapper_class = (isset($value['wrapper_class']) && is_array($value)) ? $value['wrapper_class'] : 'hidden';
			$type = (isset($value['type']) && is_array($value)) ? $value['type'] : 'text';
			$help_link = '<small id="helpBlock" class="help-block">' . $help . '</small>';
			$addon = '';
			$addon_end = '';
			$hidden_field = '';

			if ($name == 'vads_amount') {
				$hidden_field = '<input type="hidden" value="' . $display_value . '" name="vads_amount"/>';
				$cents = substr($display_value, - 2);
				$amount = substr($display_value, 0, - 2);
				$display_value = $amount . ',' . $cents;
				$addon = '<div class="input-group">';
				$addon_end = '<span class="glyphicon glyphicon-euro form-control-feedback" aria-hidden="true"></span></div>';
			}

			// $form .= $hidden_field;
			$form .= '<div class="form-group ' . $wrapper_class . '">';
			$form .= '<label for="' . $name . '" class="col-sm-2 control-label">' . $label . '</label>';
			$form .= '<div class="col-sm-10">';
			$form .= $addon;
			$form .= $hidden_field;
			$form .= '<input type="' . $type . '" readonly="readonly"  class="form-control ' . $class . '"  name="' . $name . '" value="' . $display_value . '" />';
			$form .= $help_link;
			$form .= $addon_end;
			$form .= '</div></div>';
		}

		$form .= '<button type="submit" class="btn btn-default">' . $i18n['pay'] . '</button>';
		$form .= '</form>';
		echo $form;
		?>

        </div>

        <?php
		$payment_form = ob_get_clean();
		return $output . $payment_form;
	}

	/**
	 * Define settings Fields
	 */
	public function init_settings_fields()
	{
		$this->fields = array();

		// On | Off
		$this->fields['is_active'] = array(
			'type' => 'checkbox',
			'default' => 'On',
			'title' => __('Enable / Disable', 'booking'),
			'label' => __('Enable this payment gateway', 'booking'),
			'description' => '',
			'group' => 'general'
		);

		// Switcher accounts - Test | Live
		$this->fields['account_mode'] = array(
			'type' => 'radio',
			'default' => 'test',
			'title' => __('Chose payment account', 'booking'),
			'description' => '', // __( 'Select TEST for the Test Server and LIVE in the live environment', 'booking' )
			'description_tag' => 'span',
			'css' => '',
			'options' => array(
				'test' => array(
					'title' => __('TEST', 'booking'),
					'attr' => array(
						'id' => 'spplus_mode_test'
					)
				),
				'production' => array(
					'title' => __('PRODUCTION', 'booking'),
					'attr' => array(
						'id' => 'spplus_mode_live'
					)
				)
			),
			'group' => 'general'
		);

		// shopId
		$this->fields['shop_id'] = array(
			'type' => 'text',
			'default' => '<change me>',
			'title' => __('Shop ID', 'booking'),
			'description' => __('Required', 'booking') . '.<br/>' . sprintf(__('This parameter have to assigned to you by %s', 'booking'), 'SPPlus') . '. Check SystemPay back office > Settings > Shop',
			'description_tag' => 'span',
			'css' => '', // 'width:100%'
			'group' => 'general',
			'tr_class' => 'wpbc_sub_settings_grayed wpbc_sub_settings_mode_live',
			'validate_as' => array(
				'required'
			)
		);

		// Test Key
		$this->fields['test_key'] = array(
			'type' => 'text',
			'default' => '<change me>',
			'title' => __('Test key', 'booking'),
			'description' => __('Required', 'booking') . '.<br/>' . sprintf(__('This parameter have to assigned to you by %s', 'booking'), 'SPPlus') . '. Check SystemPay back office > Settings > Shop',
			'description_tag' => 'span',
			'css' => '', // 'width:100%'
			'group' => 'general',
			'tr_class' => 'wpbc_sub_settings_grayed wpbc_sub_settings_mode_live',
			'validate_as' => array(
				'required'
			)
		);

		// Production Key
		$this->fields['production_key'] = array(
			'type' => 'text',
			'default' => '<change me>',
			'title' => __('Production key', 'booking'),
			'description' => '<br />' . sprintf(__('This parameter have to assigned to you by %s', 'booking'), 'SPPlus') . '. Check SystemPay back office > Settings > Shop',
			'description_tag' => 'span',
			'css' => '', // 'width:100%'
			'group' => 'general',
			'tr_class' => 'wpbc_sub_settings_grayed wpbc_sub_settings_mode_live'
			// 'validate_as' => array( 'required' )
		);

		// Platform url
		$this->fields['platform_url'] = array(
			'type' => 'text',
			'default' => 'https://secure.payzen.eu/vads-payment/',
			'title' => __('Platform URL', 'booking'),
			'description' => __('Required', 'booking') . '.<br/>' . sprintf(__('This parameter have to assigned to you by %s', 'booking'), 'SPPlus') . '. Check SystemPay back office > Settings > Shop',
			'description_tag' => 'span',
			'css' => '', // 'width:100%'
			'group' => 'general',
			'tr_class' => 'wpbc_sub_settings_grayed wpbc_sub_settings_mode_live',
			'validate_as' => array(
				'required'
			)
		);

		// Currency
		$currency_list = array(
			'EUR' => __('Euros', 'booking')
		);

		$this->fields['curency'] = array(
			'type' => 'select',
			'default' => 'EUR',
			'title' => __('Accepted Currency', 'booking'),
			'description' => __('The currency code that gateway will process the payment in.', 'booking'),
			// . '<div class="wpbc-settings-notice notice-info" style="text-align:left;"><strong>'
			// . __('Note:' ,'booking') . '</strong> '
			// . __('Setting the currency that is not supported by the payment processor will result in an error.' ,'booking')
			// . '<br/><strong>' . __( 'For more information:' ) . '</strong> '
			// . '<a href="https://stripe.com/docs/currencies#charge-currencies">Stripe Docs</a>'
			// . '<ul style="list-style: inside disc;">'
			// . ' <li>' . 'JCB, Discover, and Diners Club cards can only be charged in USD' . '</li>'
			// . ' <li>' . 'Currencies marked with * are not supported by American Express' . '</li>'
			// . ' <li>' . 'Brazilian Stripe accounts (currently in Preview) can only charge in Brazilian Real' . '</li>'
			// . ' <li>' . 'Mexican Stripe accounts (currently in Preview) can only charge in Mexican Peso' . '</li>'
			// . '</ul>'
			// . '</div>'
			'description_tag' => 'span',
			'css' => '',
			'options' => $currency_list,
			'group' => 'general'
		);
		// $this->fields['description_hr'] = array( 'type' => 'hr' );

		// Additional settings /////////////////////////////////////////////////
		$this->fields['subject'] = array(
			'type' => 'textarea',
			'default' => sprintf(__('Payment for booking %s on these day(s): %s', 'booking'), '[resource_title]', '[dates]'),
			'placeholder' => sprintf(__('Payment for booking %s on these day(s): %s', 'booking'), '[resource_title]', '[dates]'),
			'title' => __('Payment description at gateway website', 'booking'),
			'description' => sprintf(__('Enter the service name or the reason for the payment here.', 'booking'), '<br/>', '</b>') . '<br/>' . __('You can use any shortcodes, which you have used in content of booking fields data form.', 'booking'),
			// . '<div class="wpbc-settings-notice notice-info" style="text-align:left;"><strong>'
			// . __('Note:' ,'booking') . '</strong> '
			// . sprintf( __('This field support only up to %s characters by payment system.' ,'booking'), '255' )
			// . '</div>'
			'description_tag' => 'p',
			'css' => 'width:100%',
			'rows' => 2,
			'group' => 'general',
			'tr_class' => 'wpbc_sub_settings_is_description_show wpbc_sub_settings_grayedNO'
		);

		// //////////////////////////////////////////////////////////////////
		// Return URL & Auto approve | decline
		// //////////////////////////////////////////////////////////////////

		// Success URL
		$this->fields['order_successful_prefix'] = array(
			'type' => 'pure_html',
			'group' => 'auto_approve_cancel',
			'html' => '<tr valign="top" class="wpbc_tr_spplus_order_successful">
						  <th scope="row">' . WPBC_Settings_API::label_static('spplus_order_successful', array(
				'title' => __('Return URL after Successful order', 'booking'),
				'label_css' => ''
			)) . '</th>
				<td><fieldset>' . '<code style="font-size:14px;">' . get_option('siteurl') . '</code>',
			'tr_class' => 'relay_response_sub_class'
		);
		$this->fields['order_successful'] = array(
			'type' => 'text',
			'default' => '/successful',
			'placeholder' => '/successful',
			'css' => 'width:75%',
			'group' => 'auto_approve_cancel',
			'only_field' => true,
			'tr_class' => 'relay_response_sub_class'
		);
		$this->fields['order_successful_sufix'] = array(
			'type' => 'pure_html',
			'group' => 'auto_approve_cancel',
			'html' => '<p class="description" style="line-height: 1.7em;margin: 0;">' . __('The URL where visitor will be redirected after completing payment.', 'booking') . '<br/>' . sprintf(__('For example, a URL to your site that displays a %s"Thank you for the payment"%s.', 'booking'), '<b>', '</b>') . '</p>
														   </fieldset>
														</td>
													</tr>',
			'tr_class' => 'relay_response_sub_class'
		);

		// Failed URL
		$this->fields['order_failed_prefix'] = array(
			'type' => 'pure_html',
			'group' => 'auto_approve_cancel',
			'html' => '<tr valign="top" class="wpbc_tr_spplus_order_failed">
														<th scope="row">' . WPBC_Settings_API::label_static('spplus_order_failed', array(
				'title' => __('Return URL after Failed order', 'booking'),
				'label_css' => ''
			)) . '</th>
														<td><fieldset>' . '<code style="font-size:14px;">' . get_option('siteurl') . '</code>',
			'tr_class' => 'relay_response_sub_class'
		);
		$this->fields['order_failed'] = array(
			'type' => 'text',
			'default' => '/failed',
			'placeholder' => '/failed',
			'css' => 'width:75%',
			'group' => 'auto_approve_cancel',
			'only_field' => true,
			'tr_class' => 'relay_response_sub_class'
		);
		$this->fields['order_failed_sufix'] = array(
			'type' => 'pure_html',
			'group' => 'auto_approve_cancel',
			'html' => '<p class="description" style="line-height: 1.7em;margin: 0;">' . __('The URL where the visitor will be redirected after completing payment.', 'booking') . '<br/>' . sprintf(__('For example, the URL to your website that displays a %s"Payment Canceled"%s page.', 'booking'), '<b>', '</b>') . '</p>
														   </fieldset>
														</td>
													</tr>',
			'tr_class' => 'relay_response_sub_class'
		);
		// Auto Approve / Cancel
		$this->fields['is_auto_approve_cancell_booking'] = array(
			'type' => 'checkbox',
			'default' => 'Off',
			'title' => __('Automatically approve/cancel booking', 'booking'),
			'label' => __('Check this box to automatically approve bookings, when visitor makes a successful payment, or automatically cancel the booking, when visitor makes a payment cancellation.', 'booking'),
			'description' => '<div class="wpbc-settings-notice notice-warning" style="text-align:left;">' . '<strong>' . __('Warning', 'booking') . '!</strong> ' . __('This will not work, if the visitor leaves the payment page.', 'booking') . '</div>',
			'description_tag' => 'p',
			'group' => 'auto_approve_cancel',
			'tr_class' => 'relay_response_sub_class'
		);
	}

	// Support /////////////////////////////////////////////////////////////////

	/**
	 * Return info about Gateway
	 *
	 * @return array Example: array(
	 *         'id' => 'spplus
	 *         , 'title' => 'Stripe'
	 *         , 'currency' => 'USD'
	 *         , 'enabled' => true
	 *         );
	 */
	public function get_gateway_info()
	{
		$gateway_info = array(
			'id' => $this->get_id(),
			'title' => 'SPPlus',
			'currency' => get_bk_option('booking_' . $this->get_id() . '_' . 'curency'),
			'enabled' => $this->is_gateway_on()
		);
		return $gateway_info;
	}

	/**
	 * Get payment Statuses of gateway
	 *
	 * @return array
	 */
	public function get_payment_status_array()
	{
		return array(
			'ok' => array(
				'SPPlus:ACCEPTED',
				'SPPlus:AUTHORISED'
			),
			'pending' => array(
				'SPPlus:AUTHORISED_TO_VALIDATE',
				'SPPlus:CAPTURED',
				'SPPlus:UNDER_VERIFICATION',
				'SPPlus:WAITING_AUTHORISATION_TO_VALIDATE',
				'SPPlus:WAITING_AUTHORISATION'
			),
			'unknown' => array(
				'SPPlus:UNKNOWN'
			),
			'error' => array(
				'SPPlus:ABANDONED',
				'SPPlus:CANCELLED',
				'SPPlus:CAPTURE_FAILED',
				'SPPlus:EXPIRED',
				'SPPlus:INITIAL',
				'SPPlus:NOT_CREATED',
				'SPPlus:REFUSED',
				'SPPlus:SUSPENDED'
			)
		);
	}

	/**
	 * If activated "Auto approve|decline" and then Redirect to "Success" or "Failed" payment page.
	 *
	 * @param string $pay_system
	 * @param string $status
	 * @param type $booking_id
	 */
	public function auto_approve_or_cancell_and_redirect($pay_system, $status, $booking_id)
	{
		if ($pay_system == WPBC_SPPLUS_GATEWAY_ID) {

			$auto_approve = get_bk_option('booking_spplus_is_auto_approve_cancell_booking');

			if (in_array($status, $this->get_payment_status_array()['ok'])) {
				if ($auto_approve == 'On')
					wpbc_auto_approve_booking($booking_id);
				wpbc_redirect(get_option('siteurl') . get_bk_option('booking_spplus_order_successful'));
			} else {
				if ($auto_approve == 'On')
					wpbc_auto_cancel_booking($booking_id);
				wpbc_redirect(get_option('siteurl') . get_bk_option('booking_spplus_order_failed'));
			}
		}
	}

	private function get_PaymentFormToolBox_args()
	{
		$payment_options = array();
		$payment_options['account_mode'] = get_bk_option('booking_spplus_account_mode');
		$payment_options['shop_id'] = get_bk_option('booking_spplus_shop_id');
		$payment_options['test_key'] = get_bk_option('booking_spplus_test_key');
		$payment_options['production_key'] = get_bk_option('booking_spplus_production_key');
		$payment_options['platform_url'] = get_bk_option('booking_spplus_platform_url');

		$args = array(
			'shopID' => $payment_options['shop_id'], // shopID
			'certTest' => $payment_options['test_key'], // certificate, TEST-version
			'certProd' => $payment_options['production_key'], // certificate, PRODUCTION-version
			'ctxMode' => ("test" == $payment_options['account_mode']) ? "TEST" : "PRODUCTION", // PRODUCTION || TEST
			'platform' => $payment_options['platform_url'], // Platform URL
			'algorithm' => 'sha256', // the signature algorithm chosen in the shop configuration
			'debug' => true
		);
		return $args;
	}

	/**
	 * Update Payment Status after Response from specific Payment system website.
	 *
	 * @param type $response_status
	 * @param type $pay_system
	 * @param type $status
	 * @param type $booking_id
	 * @param type $wp_nonce
	 *
	 * @return string - $response_status
	 */
	public function update_payment_status__after_response($response_status, $pay_system, $status, $booking_id, $wp_nonce)
	{
		// debuge($_REQUEST);
		if ($pay_system == WPBC_SPPLUS_GATEWAY_ID) {

			include 'spplus/form/lib/locale/fr_FR/messages.php';
			require_once (__DIR__ . "/spplus/form/lib/class-payment-form-toolbox.php");
			$args = $this->get_PaymentFormToolBox_args();
			$toolbox = new paymentFormToolbox($args);
			$control = $toolbox->checkSignature($_POST);

			if ($control && $toolbox->debug == true) {
				$response = $toolbox->getIpn();
				$trans_status = (isset($response['vads_trans_status']) && is_array($response)) ? $response['vads_trans_status'] : 'undefined';
				// $vads_warranty_result = (isset($response['vads_warranty_result'])) ? $response['vads_warranty_result'] : 'undefined';
				// $vads_threeds_status = (isset($response['vads_threeds_status'])) ? $response['vads_threeds_status'] : 'undefined';
				// $vads_auth_result = (isset($response['vads_auth_result'])) ? $response['vads_auth_result'] : 'undefined';
				// $vads_capture_delay = (isset($response['vads_capture_delay'])) ? $response['vads_capture_delay'] : 'undefined';
				// $vads_validation_mode = (isset($response['vads_validation_mode'])) ? $response['vads_validation_mode'] : 'undefined';

				if ($trans_status == "undefined") {
					return "SPPlus:UNKNOWN";
				}

				// CF. https://paiement.systempay.fr/doc/en-EN/form-payment/standard-payment/vads-trans-status.html
				return 'SPPlus:' . $trans_status;
			} elseif ($control && $toolbox->debug == false) {
				$response = $toolbox->getIpn();
				$trans_status = (isset($response['vads_trans_status']) && is_array($response)) ? $response['vads_trans_status'] : 'undefined';

				// CF. https://paiement.systempay.fr/doc/en-EN/form-payment/standard-payment/vads-trans-status.html
				return 'SPPlus:' . $trans_status;
			} else {
				return 'SPPlus:REFUSED';
			}
		}
		return $response_status;
	}
}

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" Settings Page " >

/**
 * Settings Page
 */
class WPBC_Settings_Page_Gateway_SPPlus extends WPBC_Page_Structure
{

	public $gateway_api = false;

	/**
	 * Define interface for Gateway API
	 *
	 * @param string $selected_email_name
	 *        	- name of Email template
	 * @param array $init_fields_values
	 *        	- array of init form fields data - this array can ovveride "default" fields and loaded data.
	 * @return object Email API
	 */
	public function get_api($init_fields_values = array())
	{
		if ($this->gateway_api === false) {
			$this->gateway_api = new WPBC_Gateway_API_SPPlus(WPBC_SPPLUS_GATEWAY_ID, $init_fields_values);
		}

		return $this->gateway_api;
	}

	public function in_page()
	{ // P a g e t a g
		return 'wpbc-settings';
	}

	public function tabs()
	{ // T a b s A r r a y
		$tabs = array();

		$subtabs = array();

		// Checkbox Icon, for showing in toolbar panel does this payment system active
		$is_data_exist = get_bk_option('booking_' . WPBC_SPPLUS_GATEWAY_ID . '_is_active');
		if ((! empty($is_data_exist)) && ($is_data_exist == 'On'))
			$icon = '<i class="menu_icon icon-1x glyphicon glyphicon-check"></i> &nbsp; ';
		else
			$icon = '<i class="menu_icon icon-1x glyphicon glyphicon-unchecked"></i> &nbsp; ';

		$subtabs[WPBC_SPPLUS_GATEWAY_ID] = array(
			'type' => 'subtab', // Required| Possible values: 'subtab' | 'separator' | 'button' | 'goto-link' | 'html'
			'title' => $icon . 'SPPlus', // Title of TAB
			'page_title' => sprintf(__('%s Settings', 'booking'), 'SPPlus'), // Title of Page
			'hint' => sprintf(__('Integration of %s payment system', 'booking'), 'SPPlus'), // Hint
			'link' => '', // link
			'position' => '', // 'left' || 'right' || ''
			'css_classes' => '', // CSS class(es)
			                      // , 'icon' => 'http://.../icon.png' // Icon - link to the real PNG img
			                      // , 'font_icon' => 'glyphicon glyphicon-envelope' // CSS definition of Font Icon
			'default' => false, // Is this sub tab activated by default or not: true || false.
			'disabled' => false, // Is this sub tab deactivated: true || false.
			'checkbox' => false, // or definition array for specific checkbox: array( 'checked' => true, 'name' => 'feature1_active_status' ) //, 'checkbox' => array( 'checked' => $is_checked, 'name' => 'enabled_active_status' )
			'content' => 'content' // Function to load as conten of this TAB
		);

		$tabs['payment']['subtabs'] = $subtabs;

		return $tabs;
	}

	/**
	 * Show Content of Settings page
	 */
	public function content()
	{
		$this->css();

		// //////////////////////////////////////////////////////////////////////
		// Checking
		// //////////////////////////////////////////////////////////////////////

		do_action('wpbc_hook_settings_page_header', 'gateway_settings'); // Define Notices Section and show some static messages, if needed
		do_action('wpbc_hook_settings_page_header', 'gateway_settings_' . WPBC_SPPLUS_GATEWAY_ID);

		if (! wpbc_is_mu_user_can_be_here('activated_user'))
			return false; // Check if MU user activated, otherwise show Warning message.

		// if ( ! wpbc_is_mu_user_can_be_here('only_super_admin') ) return false; // User is not Super admin, so exit. Basically its was already checked at the bottom of the PHP file, just in case.

		// //////////////////////////////////////////////////////////////////////
		// Load Data
		// //////////////////////////////////////////////////////////////////////

		// $this->check_compatibility_with_older_7_ver();

		$init_fields_values = array();

		$this->get_api($init_fields_values);

		// //////////////////////////////////////////////////////////////////////
		// S u b m i t Main Form
		// //////////////////////////////////////////////////////////////////////

		$submit_form_name = 'wpbc_gateway_' . WPBC_SPPLUS_GATEWAY_ID; // Define form name

		$this->get_api()->validated_form_id = $submit_form_name; // Define ID of Form for ability to validate fields (like required field) before submit.

		if (isset($_POST['is_form_sbmitted_' . $submit_form_name])) {

			// Nonce checking {Return false if invalid, 1 if generated between, 0-12 hours ago, 2 if generated between 12-24 hours ago. }
			$nonce_gen_time = check_admin_referer('wpbc_settings_page_' . $submit_form_name); // Its stop show anything on submiting, if its not refear to the original page

			// Save Changes
			$this->update();
		}

		// //////////////////////////////////////////////////////////////////////
		// JavaScript: Tooltips, Popover, Datepick (js & css)
		// //////////////////////////////////////////////////////////////////////

		echo '<span class="wpdevelop">';

		wpbc_js_for_bookings_page();

		echo '</span>';

		// //////////////////////////////////////////////////////////////////////
		// Content
		// //////////////////////////////////////////////////////////////////////
		?>
        <div class="clear" style="margin-bottom: 10px;"></div>

        <span class="metabox-holder">
        	<form name="<?php

		echo $submit_form_name;
		?>" id="<?php

		echo $submit_form_name;
		?>" action="" method="post"
        		autocomplete="off">
				<?php
		// N o n c e field, and key for checking S u b m i t
		wp_nonce_field('wpbc_settings_page_' . $submit_form_name);
		?>
                <input type="hidden"
        			name="is_form_sbmitted_<?php

		echo $submit_form_name;
		?>"
        			id="is_form_sbmitted_<?php

		echo $submit_form_name;
		?>" value="1" />

        		<div class="clear" style="height: 10px;"></div>
				<?php

		$edit_url_for_visitors = get_bk_option('booking_url_bookings_edit_by_visitors');

		if (site_url() == $edit_url_for_visitors) {
			$message_type = 'error';
		} else {
			$message_type = 'warning';
		}

		?>
        		<div class="wpbc-settings-notice notice-<?php

		echo $message_type?>" style="text-align: left;">
    			<strong><?php

		echo (('error' == $message_type) ? __('Error', 'booking') : __('Note', 'booking'));
		?></strong>!
    			<?php
		echo 'SPPlus ';
		printf(__('require correct  configuration of this option: %sURL to edit bookings%s', 'booking'), '<strong><a href="' . wpbc_get_settings_url() . '#url_booking_edit">', '</a></strong>');
		?>
				</div>

        		<div class="clear" style=""></div>
				<?php

		if (version_compare(PHP_VERSION, '5.4') < 0) {
			echo '';
			?>
    				<div class="wpbc-settings-notice notice-error"
            			style="text-align: left;">
            			<strong><?php

			_e('Error', 'booking');
			?></strong>!
            			<?php
			echo 'SPPlus';
			printf(__('require PHP version %s or newer!', 'booking'), '<strong>5.4</strong>');
			?>
    				</div>

            		<div class="clear" style="height: 10px;"></div>
    				<?php
		}

		if ((! function_exists('curl_init')) && (! wpbc_is_this_demo())) { // FixIn: 8.1.1.1
			?>
        			<div class="wpbc-settings-notice notice-error" style="text-align: left;">
        			<strong><?php

			_e('Error', 'booking');
			?></strong>!
        			<?php
			echo 'SPPlus ';
			printf('require CURL library in your PHP!', '<strong>' . PHP_VERSION . '</strong>');
			?>
					</div>

        			<div class="clear" style="height: 10px;"></div>
        			<?php
		}
		?>

	            <!--div class="clear" style="height:5px;"></div>
        		<div class="wpbc-settings-notice notice-warning" style="text-align:left;">
        		<strong><?php

		_e('Important!', 'booking');
		?></strong>
                <?php
		printf(__('Please configure all fields inside the %sBilling form fields%s section at %sPayments General%s tab.', 'booking'), '<strong>', '</strong>', '<strong>', '</strong>');
		?>
        		</div-->

        		<div class="clear" style="height: 10px;"></div>

        		<div class="clear"></div>
        		<div class="metabox-holder">
        			<div class="wpbc_settings_row wpbc_settings_row_left_NO">
    				<?php
		wpbc_open_meta_box_section($submit_form_name . 'general', 'SPPlus');
		$this->get_api()->show('general');
		wpbc_close_meta_box_section();
		?>
    				</div>
        			<div class="clear"></div>


        			<div class="wpbc_settings_row wpbc_settings_row_left_NO">
    				<?php
		wpbc_open_meta_box_section($submit_form_name . 'auto_approve_cancel', __('Advanced', 'booking'));
		$this->get_api()->show('auto_approve_cancel');
		wpbc_close_meta_box_section();
		?>
				</div>
        		<div class="clear"></div>

    		</div>

    		<input type="submit" value="<?php

		_e('Save Changes', 'booking');
		?>" class="button button-primary" />
        	</form>
        </span>
        <?php

		$this->enqueue_js();
	}

	/**
	 * Update Email template to DB
	 */
	public function update()
	{

		// Get Validated Email fields
		$validated_fields = $this->get_api()->validate_post();

		$validated_fields = apply_filters('wpbc_gateway_spplus_validate_fields_before_saving', $validated_fields); // Hook for validated fields.

		$this->get_api()->save_to_db($validated_fields);

		wpbc_show_message(__('Settings saved.', 'booking'), 5); // Show Save message
	}

	// <editor-fold defaultstate="collapsed" desc=" CSS & JS " >

	/**
	 * CSS for this page
	 */
	private function css()
	{
		?>
        <style type="text/css">
        .wpbc-help-message {
        	border: none;
        	margin: 0 !important;
        	padding: 0 !important;
        }

        @media ( max-width : 399px) {
        }
        </style>
    <?php
	}

	/**
	 * Add Custon JavaScript - for some specific settings options
	 * Executed After post content, after initial definition of settings, and possible definition after POST request.
	 *
	 * @param type $menu_slug
	 */
	private function enqueue_js()
	{
		$js_script = '';

		// Eneque JS to the footer of the page
		wpbc_enqueue_js($js_script);
	}

	// </editor-fold>
}
add_action('wpbc_menu_created', array(
	new WPBC_Settings_Page_Gateway_SPPLUS(),
	'__construct'
));

// Executed after creation of Menu

/**
 * Override VALIDATED fields BEFORE saving to DB
 * Description:
 * Check "Return URLs" and "SPPLUS Email"m, etc...
 *
 * @param array $validated_fields
 */
function wpbc_gateway_spplus_validate_fields_before_saving__all($validated_fields)
{
	if ('On' == $validated_fields['is_active']) {
		// Only one instance of SPPlus integration can be active !
		update_bk_option('booking_spplus_is_active', 'Off');
	}

	$validated_fields['order_successful'] = wpbc_make_link_relative($validated_fields['order_successful']);
	$validated_fields['order_failed'] = wpbc_make_link_relative($validated_fields['order_failed']);

	if (wpbc_is_this_demo()) {
		$validated_fields['publishable_key'] = 'pk_test_6pRNASCoBOKtIshFeQd4XMUh';
		$validated_fields['secret_key'] = 'sk_test_BQokikJOvBiI2HlWgH4olfQ2';
		$validated_fields['publishable_key_test'] = 'pk_test_6pRNASCoBOKtIshFeQd4XMUh';
		$validated_fields['secret_key_test'] = 'sk_test_BQokikJOvBiI2HlWgH4olfQ2';
		$validated_fields['account_mode'] = 'test';
	}

	return $validated_fields;
}
add_filter('wpbc_gateway_spplus_validate_fields_before_saving', 'wpbc_gateway_spplus_validate_fields_before_saving__all', 10, 1);

// Hook for validated fields.

// </editor-fold>

// <editor-fold defaultstate="collapsed" desc=" Activate | Deactivate " >

// //////////////////////////////////////////////////////////////////////////////
// Activate | Deactivate
// //////////////////////////////////////////////////////////////////////////////

/**
 * A c t i v a t e
 */
function wpbc_booking_activate_SPPLUS()
{
	$op_prefix = 'booking_' . WPBC_SPPLUS_GATEWAY_ID . '_';

	add_bk_option($op_prefix . 'is_active', (wpbc_is_this_demo() ? 'On' : 'Off'));
	add_bk_option($op_prefix . 'account_mode', 'test');
	add_bk_option($op_prefix . 'shop_id', '12345678');
	add_bk_option($op_prefix . 'test_key', (wpbc_is_this_demo() ? 'tk_test_BQokikJOvBiI2HlWgH4olfQ2' : ''));
	add_bk_option($op_prefix . 'production_key', (wpbc_is_this_demo() ? 'pk_test_6pRNASCoBOKtIshFeQd4XMUh' : ''));
	add_bk_option($op_prefix . 'platform_url', 'https://secure.payzen.eu/vads-payment/');
	add_bk_option($op_prefix . 'curency', 'EUR');
	add_bk_option($op_prefix . 'subject', sprintf(__('Payment for booking %s on these day(s): %s', 'booking'), '[resource_title]', '[dates]'));
	add_bk_option($op_prefix . 'order_successful', '/successful');
	add_bk_option($op_prefix . 'order_failed', '/failed');
	add_bk_option($op_prefix . 'is_auto_approve_cancell_booking', 'Off');
}
add_bk_action('wpbc_other_versions_activation', 'wpbc_booking_activate_SPPLUS');

/**
 * D e a c t i v a t e
 */
function wpbc_booking_deactivate_SPPLUS()
{
	$op_prefix = 'booking_' . WPBC_SPPLUS_GATEWAY_ID . '_';

	delete_bk_option($op_prefix . 'is_active');
	delete_bk_option($op_prefix . 'account_mode');
	delete_bk_option($op_prefix . 'shop_id');
	delete_bk_option($op_prefix . 'test_key');
	delete_bk_option($op_prefix . 'production_key');
	delete_bk_option($op_prefix . 'platform_url');
	delete_bk_option($op_prefix . 'curency');
	delete_bk_option($op_prefix . 'order_successful');
	delete_bk_option($op_prefix . 'order_failed');
	delete_bk_option($op_prefix . 'is_auto_approve_cancell_booking');
}
add_bk_action('wpbc_other_versions_deactivation', 'wpbc_booking_deactivate_SPPLUS');

// </editor-fold>

// Hook for getting gateway payment form to show it after booking process, or for "payment request" after clicking on link in email.
// Note, here we generate new Object for correctly getting payment fields data of specific WP User in WPBC MU version.
add_filter('wpbc_get_gateway_payment_form', array(
	new WPBC_Gateway_API_SPPLUS(WPBC_SPPLUS_GATEWAY_ID),
	'get_payment_form'
), 10, 3);

// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// RESPONSE
// //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Update Payment status of booking
 *
 * @param
 *        	$booking_id
 * @param
 *        	$status
 *
 * @return bool
 */
function wpbc_spplus_update_payment_status($booking_id, $status)
{
	global $wpdb;

	// Update payment status
	$update_sql = $wpdb->prepare("UPDATE {$wpdb->prefix}booking AS bk SET bk.pay_status = %s WHERE bk.booking_id = %d;", $status, $booking_id);

	if (false === $wpdb->query($update_sql)) {
		return false;
	}

	return true;
}

/**
 * Auto cancel booking and redirect
 *
 * @param
 *        	$booking_id
 * @param
 *        	$spplus_error_code
 */
function wpbc_spplus_auto_cancel_booking($booking_id, $spplus_error_code)
{

	// Lets check whether the user wanted auto-approve or cancel
	$auto_approve = get_bk_option('booking_spplus_is_auto_approve_cancell_booking');
	if ($auto_approve == 'On')
		wpbc_auto_cancel_booking($booking_id);

	$spplus_error_url = get_bk_option('booking_spplus_order_failed');

	$spplus_error_url = wpbc_make_link_absolute($spplus_error_url);

	// if relay is active, this will point to some valid url the user entered. If not, it will point to the original gateway url
	// header ("Location: ". $spplus_error_url ."?error=".$spplus_error_code);

	wpbc_redirect($spplus_error_url . "?error=" . $spplus_error_code);
}

/**
 * Auto approve booking and redirect
 *
 * @param
 *        	$booking_id
 */
function wpbc_spplus_auto_approve_booking($booking_id)
{

	// Lets check whether the user wanted auto-approve or cancel
	$auto_approve = get_bk_option('booking_spplus_is_auto_approve_cancell_booking');
	if ($auto_approve == 'On') {
		wpbc_auto_approve_booking($booking_id);
	}

	$spplus_success_url = get_bk_option('booking_spplus_order_successful');
	if (empty($spplus_success_url)) {
		$spplus_success_url = get_bk_option('booking_thank_you_page_URL');
	}

	$spplus_success_url = wpbc_make_link_absolute($spplus_success_url);

	// if relay is active, this will point to some valid url the user entered. If not, it will point to the original gateway url
	// header ("Location: ". $spplus_success_url );
	wpbc_redirect($spplus_success_url);
}

/**
 * Parse 1 way secret HASH, usually after redirection from payment system
 * and make approve / decline specific booking.
 *
 * @param $parsed_response Array
 *        	(
 *        	[0] => payment
 *        	[1] => spplus
 *        	[2] => ec1f2c35728603edee9bde65ff3ba665
 *        	[3] => approve
 *        	)
 */
function wpbc_payment_response__spplus($parsed_response)
{
	list ($response_type, $response_source, $booking_hash, $response_action) = $parsed_response;
}

add_bk_action('wpbc_payment_response', 'wpbc_payment_response__spplus');
