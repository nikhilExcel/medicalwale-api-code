<?php
$patient_name=$_GET['patient_name'];
$address1=$_GET['address1'];
$address2=$_GET['address2'];
$landmark=$_GET['landmark'];
$mobile=$_GET['mobile'];
$area=$_GET['area'];

echo "<iframe src='http://111.118.178.221/api/smsapi.aspx?username=medicalwale&password=medicalwale@123&to=8898929759&from=Medwal&message=Patient Name: $patient_name, Address 1: $address1, Address 2: $address2, Landmark: $landmark, Mobile Number: $mobile, Area: $area' frameborder='0' scrolling='yes' style='height:0px;width:0px;display:none;'></iframe>";

?>