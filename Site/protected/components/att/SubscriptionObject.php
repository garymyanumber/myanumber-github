<?php

class SubscriptionObject extends TransactionObject
{
    public $MerchantSubscriptionIdList;
    public $IsPurchaseOnNoActiveSubscription = "false";
    public $SubscriptionRecurrences = 99999;
    public $SubscriptionPeriod = "MONTHLY";
    public $SubscriptionPeriodAmount = 1;
}

?>
