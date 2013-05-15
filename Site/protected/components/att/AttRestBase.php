<?php
class AttRestBase {
    
    protected $ch;
    protected $result;
    protected $error;
    protected $curl_info;
    protected $curl_http_code;
    protected $client_id;
    protected $secret_id;
    
    var $base = "https://api.att.com/";
    
    public function __construct($client_id = NULL, $secret_id = NULL) {
        if(!function_exists("curl_init")) {
            throw new Exception("PHP curl not installed.");
        }
        
        $this->ch = curl_init();
        
        if(isset($client_id))
            $this->client_id = $client_id;
        if(isset($secret_id))
            $this->secret_id = $secret_id;
    }
    
    public function setBaseUrl($url) {
        $this->base = $url;
    }
    
    protected function getBaseUrl() {
        return $this->base;
    }
    
    public function __destruct() {
        @ curl_close($this->ch);
    }
}
?>
