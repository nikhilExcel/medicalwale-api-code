<?php
header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires: 0");


require_once("./encdec_paytm.php");
$checkSum = "";
// below code snippet is mandatory, so that no one can use your checksumgeneration url for other purpose .
$findme   = 'REFUND';
$findmepipe = '|';
$paramList = array();


//$paramList["MID"] = 'Techea83643642560567';
//Sandbox
//$paramList["MID"] = 'AEGISH99513189108451';
//$paramList["WEBSITE"] = 'APPSTAGING';
//Live
$paramList["MID"] = 'AEGISH62390902390804';
$paramList["WEBSITE"] = 'APPPROD';
$paramList["ORDER_ID"] = $_POST['ORDER_ID'];
$paramList["CUST_ID"] = $_POST['CUST_ID'];
$paramList["INDUSTRY_TYPE_ID"] = 'Retail109';
$paramList["CHANNEL_ID"] = 'WAP';
$paramList["TXN_AMOUNT"] = $_POST['TXN_AMOUNT'];

// $paramList["MID"] = 'AEGISH99513189108451';
// $paramList["ORDER_ID"] = 'order1234erte4566703810';
// $paramList["CUST_ID"] = 'cus1232567';
// $paramList["INDUSTRY_TYPE_ID"] = 'Retail';
// $paramList["CHANNEL_ID"] = 'WAP';
// $paramList["TXN_AMOUNT"] = '1.00';
// $paramList["WEBSITE"] = 'APPSTAGING';

//Sandbox
//$paramList["CALLBACK_URL"] = 'https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID='.$_POST['ORDER_ID'] ;

// Live
$paramList["CALLBACK_URL"] = 'https://securegw.paytm.in/theia/paytmCallback?ORDER_ID='.$_POST['ORDER_ID'] ;



// foreach($_POST as $key=>$value)
// {  
//   $pos = strpos($value, $findme);
//   $pospipe = strpos($value, $findmepipe);
//   if ($pos === false || $pospipe === false) 
//     {
//         $paramList[$key] = $value;
//     }
// }
  
//Here checksum string will return by getChecksumFromArray() function.
//sandbox
//$checkSum = getChecksumFromArray($paramList,'2eYq1XbYo@69%WYn');

//Live
$checkSum = getChecksumFromArray($paramList,'9sxfuPto3oLoUr8o');


  
  echo json_encode(array("CHECKSUMHASH" => $checkSum,"ORDER_ID" => $_POST['ORDER_ID'] ,'PARAMETERS'=>$paramList, "payt_STATUS" => "1"),JSON_UNESCAPED_SLASHES);
  
  
 

 
?> 