<?php
/**
 * This file contains PHP classes that can be used to interact with the Tropo REST API/
 * @see https://www.tropo.com/docs/rest/rest_api.htm
 * 
 * @copyright 2010 Mark J. Headd (http://www.voiceingov.org)
 * @package TropoPHP
 * @author Mark Headd
 */

class SessionAPI extends RestBase {
	
	// URL for the Tropo session API.
	var $base = 'https://api.tropo.com/1.0/';
	
	// Success response from Tropo Session API.
	const SessionResponse = '<success>true</success>';
	
	public function __construct() {
		parent::__construct();
	}
	
		
	public function setBaseURL($url) {
	  $this->base = $url;
	}
	
	protected function getBaseURL() {
	  return $this->base;
	}
	
	/**
	 * Launch a new Tropo session.
	 *
	 * @param string $token
	 * @param array $params
	 * @return boolean
	 */
	public function createSession($token, Array $params = null) {
		
		$querystring = '';
		if(isset($params)) {
			foreach ($params as $key=>$value) {
		    	@ $querystring .= '&'. urlencode($key) . '=' . urlencode($value);
		    }	
		}
		
	  curl_setopt($this->ch, CURLOPT_URL, $this->base . 'sessions?action=create&token=' . $token . $querystring);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($this->ch);
    $error = curl_error($this->ch);
		parent::__destruct();

		//check result and parse
		if($result === false OR !($xml = new SimpleXMLElement($result))) {
	    	throw new Exception('An error occurred: '.$error);
		} else {
		  if(!($xml->success == 'true')){
		    throw new Exception('An error occurred: Tropo session launch failed.');
		  }
		  return trim((string) $xml->id);
	    }		 
	}	
}

class EventAPI extends RestBase {
	
	// URL for the Tropo session API.
	var $base = 'https://api.tropo.com/1.0/';
		
	// Success response from Tropo Session API.
	const EventResponse = '<signal><status>QUEUED</status></signal>';
	
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Send an event into a running Tropo session.
	 *
	 * @param string $token
	 * @param array $params
	 * @return boolean
	 */
	public function sendEvent($session_id, $event) {
		
		$url = $this->base . '%session_id%/signals?action=signal&value=%value%';
		$url = str_replace(array('%session_id%', '%value%'), array($session_id, $event), $url);
		
	    curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($this->ch);
	    $error = curl_error($this->ch);
		parent::__destruct();
	    
		if($result === false) {
	    	throw new Exception('An error occurred: '.$error);
		 } else {
		   if (strpos($result, self::EventResponse) === false) {
		     throw new Exception('An error occurred: Tropo event injection failed.');
		   }
		  return true;
		 }		 
	}	
}

class ProvisioningAPI extends RestBase {
	
	// URLs for the Tropo provisioning API.
	//const ApplicationProvisioningURLBase = 'https://api.tropo.com/v1/';
	var $base = 'https://api.tropo.com/v1/';
		
	public function __construct($userid, $password) {
		parent::__construct($userid, $password);
	}
	
	public function setBaseURL($url) {
	  $this->base = $url;
	}
	
	protected function getBaseURL() {
	  return $this->base;
	}
	
	/**
	 * Create a new Tropo application.
	 *
	 * @param string $href
	 * @param string $name
	 * @param string $voiceUrl
	 * @param string $messagingUrl
	 * @param string $platform
	 * @param string $partition
	 * @return string JSON
	 */
	public function createApplication($href, $name, $voiceUrl, $messagingUrl, $platform, $partition) {
		
		$payload = json_encode(new Application($href, $name, $voiceUrl, $messagingUrl, $platform, $partition));
		$url = $this->base . 'applications';
		return self::makeAPICall('POST', $url, $payload);
		
	}
	
	/**
	 * Update an existing Tropo application to add an address.
	 *
	 * @param string $applicationID
	 * @param string $type
	 * @param string $prefix
	 * @param string $number
	 * @param string $city
	 * @param string $state
	 * @param string $channel
	 * @param string $username
	 * @param string $password
	 * @param string $token
	 * @return string JSON
	 */
	public function updateApplicationAddress($applicationID, $type, $prefix=NULL, $number=NULL, $city=NULL, $state=NULL, $channel=NULL, $username=NULL, $password=NULL, $token=NULL) {
		
		$payload = json_encode(new Address($type, $prefix, $number, $city, $state, $channel, $username, $password, $token));
		$url = $this->base . 'applications/'.$applicationID.'/addresses';		
		return self::makeAPICall('POST', $url, $payload);
		
	}
	
	/**
	 * Update an application property.
	 *
	 * @param string $applicationID
	 * @param string $href
	 * @param string $name
	 * @param string $voiceUrl
	 * @param string $messagingUrl
	 * @param string $platform
	 * @param string $partition
	 * @return string JSON
	 */
	public function updateApplicationProperty($applicationID, $href=NULL, $name=NULL, $voiceUrl=NULL, $messagingUrl=NULL, $platform=NULL, $partition=NULL) {
		
		$payload = json_encode(new Application($href, $name, $voiceUrl, $messagingUrl, $platform, $partition));
		$url = $this->base . 'applications/'.$applicationID;		
		return self::makeAPICall('PUT', $url, $payload);
		
	}
	
	/**
	 * Delete an existing Tropo application.
	 *
	 * @param string $applicationID
	 * @return string JSON
	 */
	public function deleteApplication($applicationID) {
		
		$url = $this->base . 'applications/'.$applicationID;
		return self::makeAPICall('DELETE', $url);
		
	}
	
	/**
	 * Delete an application address.
	 *
	 * @param string $applicationID
	 * @param string $type
	 * @param string $address
	 * @return string JSON
	 */
	public function deleteApplicationAddress($applicationID, $type, $address) {
		
		$url = $this->base . 'applications/'.$applicationID.'/addresses/'.$type.'/'.$address;
		return self::makeAPICall('DELETE', $url);
		
	}
	
	/**
	 * View all applications for an account.
	 *
	 * @return string JSON
	 */
	public function viewApplications() {
		
		$url = $this->base . 'applications';
		return self::makeAPICall('GET', $url);
		
	}
	
	/**
	 * View the details of a specific application.
	 *
	 * @param string $applicationID
	 * @return string JSON
	 */
	public function viewSpecificApplication($applicationID) {
		
		$url = $this->base . 'applications/'.$applicationID;
		return self::makeAPICall('GET', $url);
		
	}
	
	/**
	 * View all of the addreses for an application.
	 *
	 * @param string $applicationID
	 * @return string JSON
	 */
	public function viewAddresses($applicationID) {
		
		$url = $this->base . 'applications/'.$applicationID.'/addresses';
		return self::makeAPICall('GET', $url);
		
	}
	
	/**
	 * View a list of availalbe exchanges
	 *
	 * @return string JSON
	 */
	public function viewExchanges() {
		
		$url = $this->base . 'exchanges';
		return self::makeAPICall('GET', $url);

	}
	
	/**
	 * Method to make REST API call.
	 *
	 * @param string $method
	 * @param string $url
	 * @param string $payload
	 * @return string JSON
	 */
	protected function makeAPICall($method, $url, $payload=NULL) {
		
		if(($method == 'POST' || $method == 'PUT') && !isset($payload)) {
			throw new Exception("Method $method requires payload for request body.");
		}
		
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
		
		switch($method) {
			
			case 'POST':
				curl_setopt($this->ch, CURLOPT_POST, true);
				curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($payload))); 
			    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
			    break;
			    
			case 'PUT':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($payload))); 
			    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
			    break;
			    
			case 'DELETE':
				curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				
			default:
				curl_setopt($this->ch, CURLOPT_HTTPGET, true);			
			
		}
		
		$this->result = curl_exec($this->ch);
		$this->error = curl_error($this->ch);
		$this->curl_info = curl_getinfo($this->ch);
		$this->curl_http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		
		if($this->result === false) {
	    	throw new Exception('An error occurred: '.$this->error);
		 } else {
		   if (substr($this->curl_http_code, 0, 1) != '2') {
		     $body = json_decode($this->result);
         throw new Exception($body->error, $this->curl_http_code);
		   }
		  return $this->result;
		 }		
	}
	
	public function getResult() {
		return $this->result;
	}
	
	public function getInfo() {
		return $this->curl_info;
	}
	
	public function getHTTPCode() {
		return $this->curl_http_code;
	}
	
	public function __destruct() {
		parent::__destruct();
	}
	
}

/**
 * Base class for all REST classes.
 *
 */
class RestBase {
	
	protected $ch;
	protected $result;
	protected $error;
	protected $curl_info;
	protected $curl_http_code;
	
	public function __construct($userid=NULL, $password=NULL) {
		if (!function_exists('curl_init')) {
		  throw new Exception('PHP curl not installed.');
		}
		$this->ch = curl_init();
		if(isset($userid) && isset($password)) {
			curl_setopt($this->ch, CURLOPT_USERPWD, "$userid:$password");
		}
	}
	
	public function __destruct() {
		@ curl_close($this->ch);	
	}	
	
	
}

/**
 * Application class. Represents a Tropo application.
 *
 */
class Application {
	
	public function __construct($href=NULL, $name=NULL, $voiceUrl=NULL, $messagingUrl=NULL, $platform=NULL, $partition=NULL) {
		if(isset($href)) { $this->href = $href; }
		if(isset($name)) { $this->name = $name; }
		if(isset($voiceUrl)) { $this->voiceUrl = $voiceUrl; }
		if(isset($messagingUrl)) { $this->messagingUrl = $messagingUrl; }
		if(isset($platform)) { $this->platform = $platform; }
		if(isset($partition)) { $this->partition = $partition; }
	}
	
	public function __set($attribute, $value) {
		$this->$attribute= $value;
	}
}

/**
 * Address class. Represents an address assigned to a Tropo application.
 *
 */
class Address {
	
	public function __construct($type=NULL, $prefix=NULL, $number=NULL, $city=NULL, $state=NULL, $channel=NULL, $username=NULL, $password=NULL, $token=NULL) {
		if(isset($type)) { $this->type = $type; }
		if(isset($prefix)) { $this->prefix = $prefix; }
		if(isset($number)) { $this->number = $number; }
		if(isset($city)) { $this->type = $type; }
		if(isset($state)) { $this->state = $state; }
		if(isset($channel)) { $this->channel = $channel; }
		if(isset($username)) { $this->username = $username; }
		if(isset($password)) { $this->password = $password; }
		if(isset($token)) { $this->token = $token; }		
	}
	
	public function __set($attribute, $value) {
		$this->$attribute= $value;
	}
}

/**
 * Exchange class. Represents an exchange.
 *
 */
class Exchange {
	
	public function __construct($prefix=NULL, $city=NULL, $state=NULL, $country=NULL) {
		if(isset($prefix)) { $this->prefix = $prefix; }
		if(isset($city)) { $this->city = $city; }
		if(isset($state)) { $this->state = $state; }
		if(isset($country)) { $this->country = $country; }
		if(isset($description)) { $this->description = $description; }
	}
	
	public function __set($attribute, $value) {
		$this->$attribute= $value;
	}
}
/**
 * Helper class listing the type of addresses available to use with Tropo applications.
 *
 */

class AddressType {
	public static $number = "number";
	public static $token = "token";
	public static $aim = "aim";
	public static $gtalk = "gtalk";
	public static $jabber = "jabber";
	public static $msn = "msn";
	public static $yahoo = "yahoo";	
	public static $skype = "skype";
}

?>