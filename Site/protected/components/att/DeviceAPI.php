<?php
class DeviceAPI extends AttRestBase {
    public function getDeviceInfo($token) {
        
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token
        );
        
        $url = $this->base . "rest/2/Devices/Info";
        
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($this->ch);
        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        if($responseCode != 200) {
            throw new Exception("Error: " . $responseCode . ". Message: " . $result);
        }
        
        $device = new DeviceObject($result);
        
        return $device;
    }
}

?>
