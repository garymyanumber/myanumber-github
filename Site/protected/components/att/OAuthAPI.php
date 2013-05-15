<?php

class OAuthAPI extends AttRestBase
{
    var $base = "https://api.att.com/";
    
    public function __construct($client_id = NULL, $secret_id = NULL) {
        parent::__construct();
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
    
    public function getUserAuthorizationUrl($redirect = NULL) {
        if(!isset($this->client_id))
            throw new Exception("The client_id must be set.");
        
        $url = $this->base . "oauth/authorize" .
                "?client_id=" . $this->client_id .
                "&scope=TL,MIM,DC";
        
        if(isset($redirect))
            $url .= "&redirect_uri=" . $redirect;
        
        return $url;
    }
    
    public function getAccessToken($code) {
        if(!isset($this->client_id))
            throw new Exception("The client_id must be set in the constructor.");
        if(!isset($this->secret_id))
            throw new Exception("The secret_id must be set in the constructor.");
        
        $url = $this->base . "oauth/token";
        
        $accessTokenHeaders = array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            );
        
        $post = "client_id=" . $this->client_id . 
                "&client_secret=" . $this->secret_id . 
                "&code=" . $code . 
                "&grant_type=authorization_code";
        
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $accessTokenHeaders);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $post);
        
        $result = curl_exec($this->ch);
        $error = curl_error($this->ch);
        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        parent::__destruct();
        
        if($responseCode == 200) {
            $tokenObj = json_decode($result);
            $token = new TokenObject();
            $token->AccessToken = $tokenObj->{'access_token'};
            $token->RefreshToken = $tokenObj->{'refresh_token'};
            $token->Expires = $tokenObj->{'expires_in'};
            return $token;
        }
        else {
            throw new Exception("Error getting access token. Response code: " . $responseCode . ". Error: $result");
        }
    }
}
?>
