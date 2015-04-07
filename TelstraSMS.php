<?php

class TelstraSMS {

	//change these with your creds from https://dev.telstra.com
	private $telstra_key = "";
	private $telstra_secret = "";

	private function telstra_token() {
		$url = "https://api.telstra.com/v1/oauth/token?client_id=" . $this->telstra_key . "&client_secret=" . $this->telstra_secret . "&grant_type=client_credentials&scope=SMS";

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$token = json_decode($response, true);

		if ($token['access_token']) {
			return $token['access_token'];
		} else {
			return false;
		}

	}

	public function telstra_sms($number, $message) {

		$token = $this->telstra_token();

		if ($token) {

			$url = "https://api.telstra.com/v1/sms/messages";

			$ch = curl_init($url);

			$headers = array('Authorization: Bearer ' . $token);
			$msg = array('to' => $number, 'body' => $message);

			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$response = curl_exec($ch);
			curl_close($ch);

			$response = json_decode($response, true);

			return $response['messageId'];

		} else {
			return false;
		}

	}

}

?>