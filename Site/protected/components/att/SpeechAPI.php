<?php

class SpeechAPI extends AttRestBase {

    public function getGenericText($file) {
        $headers = array(
            'Accept: application/json',
            'Content-Type: audio/wav',
            'X-Speech-Context: Generic'
        );

        $url = $this->base . 'rest/2/SpeechToText';

        $data = file_get_contents($file);

        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_HTTPGET, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($this->ch);

        $responseCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        if ($responseCode != 200) {
            throw new Exception("Error: " . $responseCode . ". Message: " . $result);
        }

        parent::__destruct();

        $responseObj = json_decode($result);

        $speechResponse = new SpeechResponseObject();
        $speechResponse->ResponseId = $responseObj->Recognition->ResponseId;
        $speechResponse->Status = $responseObj->Recognition->Status;

        foreach ($responseObj->Recognition->NBest as $speech) {
            $speechObject = new SpeechObject();
            $speechObject->Confidence = $speech->Confidence;
            $speechObject->Grade = $speech->Grade;
            $speechObject->Hypothesis = $speech->Hypothesis;
            $speechObject->LanguageId = $speech->LanguageId;
            $speechObject->ResultText = $speech->ResultText;
            for ($i = 0; $i < count($speech->WordScores); $i++) {
                $word = new WordObject();
                $word->Score = $speech->WordScores[$i];
                $word->Word = $speech->Words[$i];
                array_push($speechObject->Words, $word);
            }
            array_push($speechResponse->Results, $speechObject);
        }
        
        return $speechResponse;
    }

}

?>
