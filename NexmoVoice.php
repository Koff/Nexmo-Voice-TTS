<?php

/**mess
 * Class NexmoVoice handlesTTS the methods and properties of sending a Voice using TTS
 * 
 * Usage: $var = new NexoVoiceTTS ( $account_key, $account_password );
 * Methods:
 *     sendVoice ( $to, $from, $text, $lg = "US english", $voice = "female", $repeat = 1, $drop_if_machine = null, $callback = null, $callback_method = "GET" )
 *     displayOverview( $nexmo_response=null )
 *     
 *     reply ( $text )
 *     
 *
 */

class NexmoVoice {

	// Nexmo account credentials
	
	private $nx_key = '';
	private $nx_password = '';

	/**
	 * @var string Nexmo server URI
	 *
	 * We're sticking with the JSON interface here since json
	 * parsing is built into PHP and requires no extensions.
	 * This will also keep any debugging to a minimum due to
	 * not worrying about which parser is being used.
	 */
	var $nx_uri = 'https://api.nexmo.com/tts/json';


	/**
	 * @var array The most recent parsed Nexmo response.
	 */
	private $nexmo_response = '';


	// Current message
	public $to = '';
	public $from = '';
	public $text = '';
	public $lg = '';
    public $voice = '';
    public $repeat = '';
    public $drop_if_machine = '';
    public $callback = '';
    public $callback_method = '';
    

	// A few options
	public $ssl_verify = false; // Verify Nexmo SSL before sending any message

	function NexmoVoice ($nx_key, $nx_password) {
		$this->nx_key = $nx_key;
		$this->nx_password = $nx_password;
	}
	
	/**
	 * Prepare new voice message
	 *	 
	 */
	function sendVoice ( $to, $from, $text, $lg = 'US english', $voice = 'female', $repeat = 1, $drop_if_machine = null, $callback = null, $callback_method = 'GET'  ) {

		if ( !is_numeric($from) && !mb_check_encoding($from, 'UTF-8') ) {
			trigger_error('$from needs to be a valid UTF-8 encoded string');
			return false;
		}

		// Make sure $from is valid
		$from = $this->validateOriginator($from);

		// URL Encode
        $to= urlencode( $to );
		$from = urlencode( $from );
		$text = urlencode( $text );
        $lg = urlencode( $lg );
        $voice = urlencode( $voice );
        $repeat = urlencode( $repeat );
        $drop_if_machine = urlencode( $drop_if_machine );
        $callback = urlencode( $callback );
        $callback_method = urlencode( $callback_method );

		// Send away!
		 $post = array(
			'to' => $to,
            'from' => $from,
			'text' => $text,
			'lg' => $lg,
            'voice' => $voice,
            'repeat' => $repeat,
            'drop_if_machine' => $drop_if_machine,
            'callback' => $callback,
            'callback_method' => $callback_method
		);

		return $this->sendRequest ( $post );
	}

	/**
	 * Prepare and send a new message.
	 */
	private function sendRequest ( $data ) {
		// Build the post data
		$data = array_merge($data, array('api_key' => $this->nx_key, 'api_secret' => $this->nx_password));
		$post = '';
		foreach($data as $k => $v){
			$post .= "&$k=$v";
		}

		// If available, use CURL
		if (function_exists('curl_version')) {
			$to_nexmo = curl_init( $this->nx_uri );
			curl_setopt( $to_nexmo, CURLOPT_POST, true );
			curl_setopt( $to_nexmo, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $to_nexmo, CURLOPT_POSTFIELDS, $post );

			if (!$this->ssl_verify) {
				curl_setopt( $to_nexmo, CURLOPT_SSL_VERIFYPEER, true);
			}
			$from_nexmo = curl_exec( $to_nexmo );
			curl_close ( $to_nexmo );

		} elseif (ini_get('allow_url_fopen')) {
			// No CURL available so try the awesome file_get_contents

			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $post
				)
			);
			$context = stream_context_create($opts);
			$from_nexmo = file_get_contents($this->nx_uri, false, $context);

		} else {
			// No way of sending a HTTP post :(
			return false;
		}

		$from_nexmo = str_replace('-', '', $from_nexmo);

		return $this->nexmoParse( $from_nexmo );

	 }

	/**
	 * Parse server response.
	 */
	private function nexmoParse ( $from_nexmo ) {

		$response_obj = json_decode( $from_nexmo );

		if ($response_obj) {
			$this->nexmo_response = $response_obj;
		
			if (is_numeric(intval($response_obj->status))) {

                return $response_obj;
				
			}


		} else {
			// A malformed response
			$this->nexmo_response = array();
			return false;
		}

	}

	/**
	 * Validate an originator string
	 *
	 * If the originator ('from' field) is invalid, some networks may reject the network
	 * whilst stinging you with the financial cost! While this cannot correct them, it
	 * will try its best to correctly format them.
	 */
	private function validateOriginator($inp){
		// Remove any invalid characters
		$ret = preg_replace('/[^a-zA-Z0-9]/', '', (string)$inp);

		if(preg_match('/[a-zA-Z]/', $inp)){

			// Alphanumeric format so make sure it's < 11 chars
			$ret = substr($ret, 0, 11);

		} else {

			// Numerical, remove any prepending '00'
			if(substr($ret, 0, 2) == '00'){
				$ret = substr($ret, 2);
				$ret = substr($ret, 0, 15);
			}
		}

		return (string)$ret;
	}

	public function displayOverview( $nexmo_response=null ){
		$info = (!$nexmo_response) ? $this->nexmo_response : $nexmo_response;

		if (!$nexmo_response ) return '<p>Cannot display an overview of this response</p>';

		if ( intval($nexmo_response->status) == 0 ) {

			print ('Your voice message was sent successfully');
            print ('<br> </br>');
            print ('The message Id is: ');
            print ($nexmo_response->call_id);

		} else {

			return '<p>There was an error sending your voice message</p>' . $info->error-text;
            
		}
	}

}