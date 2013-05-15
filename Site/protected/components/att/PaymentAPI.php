<?php
class PaymentAPI extends AttRestBase
{
    public function getTransactionUrl($amount, $category, $description, $merchantProductId, $redirect)
    {
        $transaction = new TransactionObject();
        $transaction->Amount = $amount;
        $transaction->Category = $category;
        $transaction->Description = $description;
        $transaction->MerchantProductId = $merchantProductId;
        $transaction->MerchantPaymentRedirectUrl = $redirect;
        $transaction->MerchantTransactionId = uniqid();
        $transaction->MerchantSubscriptionIdList = substr(uniqid(), 0, 12);
        
        $paymentString = json_encode($transaction);
        
        $notary = new NotaryAPI($this->client_id, $this->secret_id);
        
        $notaryObject = $notary->notarize($paymentString);
        
        $url = $this->base . "rest/3/Commerce/Payment/Transactions?clientid=" . $this->client_id . 
                "&SignedPaymentDetail=" . $notaryObject->SignedDocument .
                "&Signature=" . $notaryObject->Signature;
        
        return $url;
    }
    
    public function getSubscriptionUrl($amount, $category, $description, $merchantProductId, $redirect)
    {
        $subscription = new SubscriptionObject();
        $subscription->Amount = $amount;
        $subscription->Category = $category;
        $subscription->Description = $description;
        $subscription->MerchantProductId = $merchantProductId;
        $subscription->MerchantTransactionId = uniqid();
        $subscription->MerchantPaymentRedirectUrl = $redirect;
        $subscription->MerchantSubscriptionIdList = substr(uniqid(), 0, 12);
        
        $paymentString = json_encode($subscription);
        
        $notary = new NotaryAPI($this->client_id, $this->secret_id);
        
        $notaryObject = $notary->notarize($paymentString);
        
        $url = $this->base . "rest/3/Commerce/Payment/Subscriptions?clientid=" . $this->client_id . 
                "&SignedPaymentDetail=" . $notaryObject->SignedDocument .
                "&Signature=" . $notaryObject->Signature;
        
        return $url;
    }
    
    public function getSubscriptionStatus($token, $subscriptionAuthCode)
    {
        $url = $this->base . "rest/3/Commerce/Payment/Subscriptions/SubscriptionAuthCode/" . $subscriptionAuthCode;
        
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        );
        
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($this->ch);
        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        if($responseCode != 200)
        {
            throw new Exception("Response Code: " . $responseCode . ". Error: " . $result);
        }
        
        parent::__destruct();
        
        return json_decode($result);
    }
    
    public function getTransaction($token, $transactionAuthCode)
    {
        $url = $this->base . "rest/3/Commerce/Payment/Transactions/TransactionAuthCode/" . $transactionAuthCode;
        
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        );
        
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($this->ch);
        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        
        if($responseCode != 200)
        {
            throw new Exception("Response Code: " . $responseCode . ". Error: " . $result);
        }
        
        parent::__destruct();
        
        return json_decode($result);
    }
}
?>
