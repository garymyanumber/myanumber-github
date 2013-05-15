<?php

class DeviceObject {
    public $Name;
    public $Vendor;
    public $Model;
    public $FirmwareVersion;
    public $UaProf;
    public $MmsCapable;
    public $AssistedGps;
    public $LocationTechnology;
    public $DeviceBrowser;
    public $WapPushCapable;
    
    public function __construct($json = NULL) {
        if(!$json)
            return;
        
        $rawData = json_decode($json);
        $capabilities = $rawData->DeviceInfo->Capabilities;
        $this->Name = $capabilities->Name;
        $this->Vendor = $capabilities->Vendor;
        $this->Model = $capabilities->Model;
        $this->FirmwareVersion = $capabilities->FirmwareVersion;
        $this->UaProf = $capabilities->UaProf;
        $this->MmsCapable = $capabilities->MmsCapable;
        $this->AssistedGps = $capabilities->AssistedGps;
        $this->LocationTechnology = $capabilities->LocationTechnology;
        $this->DeviceBrowser = $capabilities->DeviceBrowser;
        $this->WapPushCapable = $capabilities->WapPushCapable;
    }
}

?>
