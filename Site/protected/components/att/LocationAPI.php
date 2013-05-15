<?php

class LocationAPI extends AttRestBase
{
    public function getLocation($token, $requestedAccuracy = NULL, $tolerance = NULL, $acceptableAccuracy = NULL)
    {
        $url = $this->base . "2/devices/location";
        
        if(isset($requestedAccuracy))
            $url .= "?requestedAccuracy=" . $requestedAccuracy;
        
        if(isset($tolerance))
        {
            if(strstr($url, "?"))
                    $url .= "&Tolerance=" . $tolerance;
            else
                $url .= "?Tolerance=" . $tolerance;
        }
        
        if(isset($acceptableAccuracy))
        {
            if(strstr($url, "?"))
                    $url .= "&acceptableAccuracy=" . $acceptableAccuracy;
            else
                $url .= "?acceptableAccuracy=" . $acceptableAccuracy;
        }
        
        $headers = array(
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept-Type: application/json'
        );
        
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTPGET, true);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($this->ch);
        
        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        parent::__destruct();
        
        if($responseCode != 200)
            throw new Exception("Error: " . $responseCode . ". Message: " . $result);
        
        $locationObject = json_decode($result);
        
        return $locationObject;
    }
}

?>
