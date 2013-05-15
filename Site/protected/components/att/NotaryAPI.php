<?php
class NotaryAPI extends AttRestBase
{
    public function notarize($payload)
    {
        $headers = array(
            'Content-Type: application/json',
            'client_id: ' . $this->client_id,
            'client_secret: ' . $this->secret_id
        );
        
        $url = $this->base . "/Security/Notary/Rest/1/SignedPayload";
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($this->ch);
        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        parent::__destruct();
        
        $notaryObj = json_decode($result);
        
        $notary = new NotaryObject();
        $notary->SignedDocument = $notaryObj->{'SignedDocument'};
        $notary->Signature = $notaryObj->{'Signature'};
        
        return $notary;
    }
}
?>
