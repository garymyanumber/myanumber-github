<?php

class MessageObject {

    var $Id;
    var $Addresses;
    var $Text;
    var $Attachments;
    var $Subject;
    var $Group;
    var $Data;
    var $Boundary;
    var $Header;
    var $StartPart;

    public function __construct() {
        $this->Addresses = array();
        $this->Attachments = array();
        $this->Boundary = "----------------------------" . substr(md5(date("c")), 0, 10);
        $this->StartPart = "<startpart>";
    }

    public function AddAddress($address) {
        array_push($this->Addresses, "tel:$address");
    }

    public function AddAttachment($attachment) {
        if ($attachment['name'] != '') {
            $data = "--" . $this->Boundary . "\r\n";
            $data .= "Content-Disposition: form-data; name=\"file0\"; filename=\"" . $attachment['name'] . "\"\r\n"; //Attachment; Filename=\"".$file['name']."\"\r\n";
            $data .= "Content-Type:" . $attachment['type'] . "\r\n";
            $data .= "Content-ID:<" . $attachment['name'] . ">\r\n";
            $data .= "Content-Transfer-Encoding:binary\r\n\r\n";
            $data .= join("", file($attachment['tmp_name'])) . "\r\n";
            array_push($this->Attachments, $data);
        }
    }

    public function GetMessage() {
        if (count($this->Attachments) > 0) {
            $addresses_url = "";
            for ($i = 0; $i < count($this->Addresses); $i++) {
                if (($i + 1) == count($this->Addresses)) {
                    $addresses_url .= "Addresses=" . $this->Addresses[$i];
                } else {
                    $addresses_url .= "Addresses=" . $this->Addresses[$i] . "&";
                }
            }

            //Form the first part of MIME body containing address, subject in urlencided format
            $sendMMSData = $addresses_url . '&Subject=' . urlencode($this->Subject) . '&Text=' . urlencode($this->Text) . '&Group=' . urlencode($this->Group);

            //Form the MIME part with MIME message headers and MIME attachment
            $data = "";
            $data .= "--" . $this->Boundary . "\r\n";
            $data .= "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\nContent-Disposition: form-data; name=\"root-fields\"\r\nContent-ID: " . $this->StartPart . "\r\n\r\n" . $sendMMSData . "\r\n\r\n";

            foreach ($this->Attachments as $attachment) {
                $data .= $attachment;
            }

            $data .= "--" . $this->Boundary . "--\r\n";

            return $data;
        }
        else {
            $shortMessage = new ShortMessageObject();
            $shortMessage->Addresses = $this->Addresses;
            $shortMessage->Text = $this->Text;
            $shortMessage->Subject = $this->Subject;
            
            return json_encode($shortMessage);
        }
    }

}

class ShortMessageObject {

    var $Addresses;
    var $Text;
    var $Subject;

}

?>
