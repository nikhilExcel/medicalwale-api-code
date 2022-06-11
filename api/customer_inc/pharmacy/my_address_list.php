<?php
require_once("config.php");

if(isset($_POST['user_id']))
{
$user_id = $_POST['user_id'];

$sql = "SELECT * FROM `oc_address` WHERE customer_id='$user_id'";
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
$id=$row['address_id'];
$firstname=$row['firstname'];
$lastname=$row['lastname'];
$patient_name=$firstname.' '.$lastname;
$address1=$row['address_1'];
$address2=$row['address_2'];
$landmark=$row['landmark'];
$mobile=$row['telephone'];

array_push($result2,array('id'=>$id,'patient_name'=>$patient_name,'address1'=>$address1,'address2'=>$address2,'landmark'=>$landmark,'mobile'=>$mobile));
}
array_push($result1,array('true_false'=>$true_false));
$arry = array(array('true_false'=>$true_false),$result2);
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$error_msg='No Address List';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
}
else
{
$error_msg='No Address List';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
?>