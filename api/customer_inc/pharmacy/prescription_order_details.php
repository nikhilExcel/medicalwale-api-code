<?php
require_once("config.php");
$result1 = array();
$result2 = array(); 

if(isset($_POST['user_id']) && ($_POST['order_id']) )
{
$user_id = $_POST['user_id'];
$order_id = $_POST['order_id'];

$sql = "SELECT prescription_order.id, prescription_order.user_id, prescription_order.medical_id, prescription_order.address_id, prescription_order.uni_id, prescription_order.date, prescription_order.status, prescription_order_details.order_id, prescription_order_details.prescription_image, prescription_order_details.prescription_image2, prescription_order_details.prescription_image3, prescription_order_details.prescription_image4, prescription_order_details.uni_id,medical_stores.id,medical_stores.medical_name,oc_address.address_id,oc_address.customer_id,oc_address.address_id,oc_address.firstname,oc_address.lastname,oc_address.telephone,oc_address.address_1,oc_address.address_2 ,oc_address.landmark FROM `prescription_order`
INNER JOIN `prescription_order_details` ON prescription_order.id = prescription_order_details.order_id 
INNER JOIN `medical_stores`
ON medical_stores.id=prescription_order.medical_id
INNER JOIN  `oc_address` 
ON oc_address.address_id=prescription_order.address_id
WHERE prescription_order.user_id='$user_id' AND prescription_order.id='$order_id'";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);

if($count>0)
{
$true_false='true';

$row = mysqli_fetch_array($res);
$order_no=$row['uni_id'];
$medical_name=$row['medical_name'];
$order_status=$row['status'];
$order_date=$row['date'];


//address details
$firstname=$row['firstname'];
$lastname=$row['lastname'];
$addr_patient_name=$firstname.' '.$lastname;
$addr_address1=$row['address_1'];
$addr_address2=$row['address_2'];
$addr_landmark=$row['landmark'];
$addr_mobile=$row['telephone'];

//prescription images 
if($row['prescription_image']!='')
{
$image_1=$row['prescription_image'];
$prescription_image=str_replace("images/","","$image_1");
$image1= 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/'.$prescription_image;
}
else
{
$image1= '';
}

//2
if($row['prescription_image2']!='')
{
$image_2=$row['prescription_image2'];
$prescription_image2=str_replace("images/","","$image_2");
$image2= 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/'.$prescription_image2;
}
else
{
$image2= '';
}

//3
if($row['prescription_image3']!='')
{
$image_3=$row['prescription_image3'];
$prescription_image3=str_replace("images/","","$image_3");
$image3= 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/'.$prescription_image3;
}
else
{
$image3= '';
}

//4
if($row['prescription_image4']!='')
{
$image_4=$row['prescription_image4'];
$prescription_image4=str_replace("images/","","$image_4");
$image4= 'https://d2c8oti4is0ms3.cloudfront.net/images/inc/images/'.$prescription_image4;
}
else
{
$image4= '';
}

array_push($result2,array('order_no'=>$order_no,'medical_name'=>$medical_name,'order_status'=>$order_status,'order_date'=>$order_date,'addr_patient_name'=>$addr_patient_name,'addr_address1'=>$addr_address1,'addr_address2'=>$addr_address2,'addr_landmark'=>$addr_landmark,'addr_mobile'=>$addr_mobile,'prescription_image'=>$image1,'prescription_image2'=>$image2,'prescription_image3'=>$image3,'prescription_image4'=>$image4));

array_push($result1,array('true_false'=>$true_false));
$arry = array(array('true_false'=>$true_false),$result2);

echo json_encode($arry);
mysqli_close($connection); 
}
else
{

$error_msg='No Order Details';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 

}
}
else
{
$error_msg='No Order Details';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}
?>