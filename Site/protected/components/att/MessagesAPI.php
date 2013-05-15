<?php

class MessagesAPI extends AttRestBase {

    public function sendMessage($token, $message) {

        if (count($message->Attachments) > 0) {
            $host = str_replace("https", "ssl", $this->base);
            $host = chop($host, "/");
            $port = "443";

            $fp = fsockopen($host, $port, $errno, $errstr);

            $completeMessage = $message->GetMessage();

            $header = "POST " . $this->base . "rest/1/MyMessages? HTTP/1.0\r\n";
            $header .= "Authorization: BEARER " . $token . "\r\n";
            $header .= "Content-Type: multipart/form-data; type=\"application/x-www-form-urlencoded\"; start=\"" . $message->StartPart . "\"; boundary=\"" . $message->Boundary . "\"\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Host: " . chop(str_replace("https://", "", $this->base), "/") . "\r\n";
            $dc = strlen($completeMessage); //content length
            $header .= "Content-length: $dc\r\n\r\n";

            $httpRequest = $header . $completeMessage;

            fputs($fp, $httpRequest);

            $sendMMS_response = "";

            while (!feof($fp)) {
                $sendMMS_response .= fread($fp, 1024);
            }

            fclose($fp);

            $responseCode = trim(substr($sendMMS_response, 9, 4));

            $splitString = explode("{", $sendMMS_response);

            $joinString = "{" . implode("{", array($splitString[1]));
            //echo $joinString;
            $message->Id = substr($joinString, 11, -6);
            
            return $message;
            
        } else {
            $headers = array(
                "Accept: application/json",
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            );

            $url = $this->base . "rest/1/MyMessages";

            $json = $message->GetMessage();

            curl_setopt($this->ch, CURLOPT_URL, $url);
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
            curl_setopt($this->ch, CURLOPT_HEADER, 0);
            curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $json);

            $result = curl_exec($this->ch);

            $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

            if ($responseCode != 200) {
                throw new Exception("Error: " . $responseCode . ". Message: " . $result);
            }

            parent::__destruct();

            $id = json_decode($result);
            
            $message->Id = $id->Id;
            
            return $message;
        }
    }

    public function getMessages($token, $headerCount, $indexCursor = 'I:123:,t:2783:,r:1983:,u:1045:,p:885') {

        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token
        );

        $url = $this->base . "rest/1/MyMessages?" .
                "HeaderCount=" . $headerCount .
                "&IndexCursor=" . urlencode($indexCursor);

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($this->ch);

        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if ($responseCode != 200) {
            throw new Exception("Error: " . $responseCode . ". At URL: " . $url . ". Message: " . $result);
        }

        parent::__destruct();

        $messages = json_decode($result);

        return $messages;
    }

}

?>
