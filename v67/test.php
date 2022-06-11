<?php
$filename = $_FILES['pdf_received']['name'];
$filedata = $_FILES['pdf_received']['tmp_name'];

$data = array('name' => 'pdf_received', 'filename' => @$filedata);
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_PORT => "8009",
  CURLOPT_URL => "http://13.233.231.151:8009/ads/watermark/",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $data
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
?>
