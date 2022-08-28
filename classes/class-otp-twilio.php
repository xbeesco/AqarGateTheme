<?php
require AG_DIR . 'libs/twilio/Twilio/autoload.php';
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;
use Twilio\Http\CurlClient;

class AQ_WP_Twilio{

	protected static $_instance = null;
	private $account_sid, $auth_token, $senders_number;

	public function __construct(){
		$this->set_credentials();
	}

	public static function get_instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	private function set_credentials(){	
		$this->account_sid 		= carbon_get_theme_option('twilio-account-sid');
		$this->auth_token 		= carbon_get_theme_option('twilio-auth-token');
		$this->senders_number 	= carbon_get_theme_option('twilio-sender-number');	
	}

	public function sendSMS( $phone, $message ){

		$client = new Client(
			$this->account_sid,
			$this->auth_token,		
		);

		try {
		    $client->messages->create(
		    // Where to send a text message (your cell phone?)
			    $phone,
			    array(
			        'from' => $this->senders_number,
			        'body' => $message
			    )
			);
		} catch (Exception $e) {
		    // output error message if fails
		    return new WP_Error( 'operator-error', $e->getMessage() );
		}

	}

	public function Add_Caller_ID( $phone, $name ){

		$client = new Client(
			$this->account_sid,
			$this->auth_token,		
		);

		try {
		    $client->validationRequests->create(
		    // Where to send a text message (your cell phone?)
			    $phone,
			    [ "friendlyName" => $name ]
			);
		} catch (Exception $e) {
		    // output error message if fails
		    return new WP_Error( 'operator-error', $e->getMessage() );
		}

	}

}

function aq_wp_twilio(){
	return AQ_WP_Twilio::get_instance();
}