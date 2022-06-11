<?php 
if(isset($_POST['name']) && isset($_POST['address1']) && isset($_POST['address2']) && isset($_POST['mobile']) && isset($_POST['area']) && isset($_POST['id']))
{
require_once ("config.php"); 
$patient_name = $_POST['name'];
$address1 = $_POST['address1'];
$address2 = $_POST['address2'];
$landmark = $_POST['landmark'];
$mobile = $_POST['mobile'];
$area = $_POST['area'];
$user_id=$_POST['id'];
$order_status="pending";
$order_date=date('Y-m-d');
$result = array();
if($patient_name!='' && $address1!='' && $address2!='' && $mobile!='' && $area!='' && $user_id!='')
{
define('UPLOAD_DIR', 'images/');
$img1 = '';
$img2 = '';
$img3 = '';
$img4 = '';

$img1 = $_POST['image1'];
$img2 = $_POST['image2'];
$img3 = $_POST['image3'];
$img4 = $_POST['image4'];
if($img1!='')
{
$img1 = str_replace('data:image/jpeg;base64,', '', $img1);
$img1 = str_replace(' ', '+', $img1);
$data1 = base64_decode($img1);
$file1 = UPLOAD_DIR . uniqid() . '.png';
$success = file_put_contents($file1, $data1);
}
if($img2!='')
{
$img2 = str_replace('data:image/jpeg;base64,', '', $img2);
$img2 = str_replace(' ', '+', $img2);
$data2 = base64_decode($img2);
$file2 = UPLOAD_DIR . uniqid() . '.png';
$success2 = file_put_contents($file2, $data2);
}
if($img3!='')
{
$img3 = str_replace('data:image/jpeg;base64,', '', $img3);
$img3 = str_replace(' ', '+', $img3);
$data3 = base64_decode($img3);
$file3 = UPLOAD_DIR . uniqid() . '.png';
$success3 = file_put_contents($file3, $data3);
}
if($img4!='')
{
$img4 = str_replace('data:image/jpeg;base64,', '', $img4);
$img4 = str_replace(' ', '+', $img4);
$data4 = base64_decode($img4);
$file4 = UPLOAD_DIR . uniqid() . '.png';
$success4 = file_put_contents($file4, $data4);
}



$insert= mysqli_query($connection,"INSERT INTO `user_patient`(`id`, `user_id`, `patient_name`, `address_1`, `address_2`, `land_mark`, `city`, `area`, `pincode`, `mobile_number`, `details`, `status`, `date` ,`image1` ,`image2` ,`image3` ,`image4`) VALUES ('', '$user_id', '$patient_name', '$address1', '$address2', '$landmark', '', '$area', '', '$mobile', '$details', '$order_status', '$order_date', '$file1', '$file2', '$file3', '$file4')"); 
$order_id =  mysqli_insert_id($connection);

$true_false='true';
array_push($result,array('true_false'=>$true_false,'order_id'=>$order_id,'order_status'=>$order_status,'order_date'=>$order_date));
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
?>