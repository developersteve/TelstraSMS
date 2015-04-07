<?php

if (isset($_POST['amt']) && isset($_POST['nonce'])) {
	require_once 'Braintree.php';
	require_once "TelstraSMS.php";

	Braintree_Configuration::environment('sandbox');
	//get these creds from https://sandbox.braintreegateway.com/
	Braintree_Configuration::merchantId('');
	Braintree_Configuration::publicKey('');
	Braintree_Configuration::privateKey('');

	$result = Braintree_Transaction::sale(array(
		'amount' => $_POST['amt'],
		'paymentMethodNonce' => $_POST['nonce'],
		'customFields' => array(
			'cart' => $_POST['cart'],
			'msgid' => $_POST['msgid'],
		),
	));

	if ($result->success == 1) {

		if (isset($_POST['msgid'])) {
			$sms = new TelstraSMS();
			$sms->telstra_sms($_POST['msgid'], 'Your order has been placed, the transaction id is ' . $result->transaction->id);
		}

		echo $result->transaction->id;
	} else {
		echo "failed";
	}

} elseif (isset($_POST['mobile'])) {

	require_once "TelstraSMS.php";

	$url = (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

	$sms = new TelstraSMS();
	echo $sms->telstra_sms($_POST['mobile'], 'Hello there, to complete your checkout click here ' . $url . '?msgid=' . $_POST['mobile']);

}

?>
