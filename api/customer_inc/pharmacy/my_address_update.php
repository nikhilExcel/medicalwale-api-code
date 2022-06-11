<?php 
require_once ("config.php"); 

if(isset($_POST['address_id']) && isset($_POST['user_id']) && isset($_POST['address1']) && isset($_POST['address2']) && isset($_POST['landmark']) && isset($_POST['mobile']))
{
$address_id = $_POST['address_id'];
$user_id = $_POST['user_id'];
$patient_name = $_POST['patient_name'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$landmark = $_POST['landmark'];
$mobile = $_POST['mobile'];

$result = array();

if($user_id!='' && $address_id!='')
{

$update= mysqli_query($connection,"UPDATE `oc_address` SET `firstname`='$patient_name',`address_1`='$address1',`address_2`='$address2',`landmark`='$landmark',`telephone`='$mobile' WHERE address_id='$address_id' AND customer_id='$user_id' "); 

$id =  mysqli_insert_id($connection);

$true_false='True';
$msg='Updated Successfully!';
array_push($result,array('true_false'=>$true_false,'msg'=>$msg));
echo json_encode($result); 
mysqli_close($connection);

}
else
{
$error_msg='Please enter all fields!';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
} 
}
else
{
$error_msg='Please enter all fields!';
$true_false='false';
array_push($result,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result); 
mysqli_close($connection);
}
?>