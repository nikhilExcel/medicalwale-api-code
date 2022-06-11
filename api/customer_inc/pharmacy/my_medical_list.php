<?php
require_once("config.php");

if(isset($_POST['user_id']))
{
$user_id = $_POST['user_id'];
$sql = "SELECT my_medical.user_id,my_medical.medical_id,medical_stores.id,medical_stores.medical_name,medical_stores.address1,medical_stores.address2,medical_stores.pincode,medical_stores.city,medical_stores.state,medical_stores.contact_no,medical_stores.whatsapp_no,medical_stores.email,medical_stores.website,medical_stores.delivery_till,medical_stores.delivery_time,medical_stores.reach_area,medical_stores.profile_pic FROM `medical_stores` INNER JOIN `my_medical` On my_medical.medical_id = medical_stores.id WHERE my_medical.user_id='$user_id'";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
$result1 = array();
$result2 = array(); 
if($count>0)
{
$true_false='true';

while($row = mysqli_fetch_array($res))
{

$true_false='true';
$medical_id=$row['id'];
$medical_name=$row['medical_name'];
$address1=$row['address1'];
$address2=$row['address2'];
$pincode=$row['pincode'];
$city=$row['city'];
$state=$row['state'];
$contact_no=$row['contact_no'];
$whatsapp_no=$row['whatsapp_no'];
$email=$row['email'];
$website=$row['website'];
$delivery_till=$row['delivery_till'];
$delivery_time=$row['delivery_time'];
$reach_area=$row['reach_area'];

if($row['profile_pic']!='')
{
$profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/images/'.$row['profile_pic'];
}
else
{
$profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/images/default.jpg';
}

array_push($result2,array('id'=>$medical_id,'medical_name'=>$medical_name,'address1'=>$address1,'address2'=>$address2,'pincode'=>$pincode,'city'=>$city,'state'=>$state,'contact_no'=>$contact_no,'whatsapp_no'=>$whatsapp_no,'email'=>$email,'website'=>$website,'delivery_till'=>$delivery_till,'delivery_time'=>$delivery_time,'reach_area'=>$reach_area,'profile_pic'=>$profile_pic));
}
array_push($result1,array('true_false'=>$true_false));
$arry = array(array('true_false'=>$true_false),$result2);
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$error_msg='No Medical List';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
}
else
{
$error_msg='No Medical List';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
?>