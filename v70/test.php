<?php
$filename = $_FILES['pdf_received']['name'];
$filedata = $_FILES['pdf_received']['tmp_name'];
$filesize = $_FILES['pdf_received']['size'];

            
$file = new \CURLFile('@$filedata'); //<-- Path could be relative
$data = array('name' => 'pdf_received', 'filename' => $file);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://13.233.231.151:8009/ads/watermark/');
curl_setopt($ch, CURLOPT_POST, 1);
//CURLOPT_SAFE_UPLOAD defaulted to true in 5.6.0
//So next line is required as of php >= 5.6.0
curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);         
 print_r($response);          


?>