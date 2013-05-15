<?php

class Attfoundry {
    public function getAuthUrl($clientid, $redirect = NULL) {
        $url = 'https://auth.tfoundry.com/oauth/authorize?client_id=' . $clientid .
                '&response_type=code' . 
                '&scope=AccountDetails,AddressBook,Geo,Locker,Messages,SpeechAlpha,Translation,Voice,WebRTC'. 
                '&redirect_uri=' . $redirect;
        return $url;
    }
    
    public function getToken($client_id, $client_secret, $code, $redirect) {
        
        $url = 'https://auth.tfoundry.com/oauth/token';
        
        $ch = curl_init();
        
        $post = "client_id=" . $client_id . 
                "&client_secret=" . $client_secret . 
                "&redirect_uri=" . $redirect .
                "&grant_type=authorization_code" . 
                "&code=" . $code;
                    
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $tokenObject = json_decode($result);
        
        return $tokenObject->access_token;
    }
}

?>
