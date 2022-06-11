<?php
require_once("config.php");
$result1 = array();
$result2 = array(); 
if(isset($_POST['user_id']) && ($_POST['order_id']))
{
$user_id = $_POST['user_id'];
$order_id = $_POST['order_id'];

$sql = "SELECT cart_order.uni_id,cart_order.date,cart_order.status,medical_stores.medical_name,product.product_name,product.product_price,cart_order_products.product_status,cart_order_products.product_status_type,cart_order_products.product_status_value,cart_order_products.product_quantity,product.image,product.is_active,oc_address.address_id,oc_address.customer_id,oc_address.firstname,oc_address.lastname,oc_address.email,oc_address.telephone,oc_address.address_1,oc_address.address_2 FROM `cart_order` 
INNER JOIN `cart_order_products` 
ON cart_order.id=cart_order_products.order_id 
INNER JOIN product 
ON product.id=cart_order_products.product_id 
INNER JOIN medical_stores
ON medical_stores.id=cart_order_products.medical_id 
INNER JOIN `oc_address`
ON oc_address.address_id=cart_order.address_id
WHERE cart_order.user_id='$user_id' AND cart_order.id='$order_id'";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
if($count>0)
{
$true_false='true';

while($row = mysqli_fetch_array($res))
{

$true_false='true';
$order_no=$row['uni_id'];
$order_date=$row['date'];
$order_status=$row['status'];
$medical_name=$row['medical_name'];
$product_name=$row['product_name'];
$product_price=$row['product_price'];
$product_quantity=$row['product_quantity'];
//$image=$row['image'];
$firstname=$row['firstname'];
$lastname=$row['lastname'];
$addr_patient_name=$firstname.' '.$lastname;
$addr_address1=$row['address_1'];
$addr_address2=$row['address_2'];
$addr_landmark=$row['landmark'];
$addr_mobile=$row['telephone'];

$product_status=$row['product_status'];
$product_status_type=$row['product_status_type'];
$product_status_value=$row['product_status_value'];


$similar_product_id='';
$similar_product_name='';
$similar_product_price='';
$similar_product_pack='';
if($product_status_type=='Similar')
{
$sql2 = "SELECT * FROM `product` WHERE id='$product_status_value'";
$res2 = mysqli_query($connection,$sql2); 
$row2 = mysqli_fetch_array($res2);

$similar_product_id=$row2['id'];
$similar_product_name=$row2['product_name'];
$similar_product_price=$row2['product_price'];
$similar_product_pack=$row2['pack'];
}
else
{
$similar_product_id='0';
$similar_product_name='0';
$similar_product_price='0';
$similar_product_pack='0';
}


array_push($result2,array('order_no'=>$order_no,'order_date'=>$order_date,'order_status'=>$order_status,'medical_name'=>$medical_name,'product_name'=>$product_name,'product_price'=>$product_price,'product_quantity'=>$product_quantity,'product_status'=>$product_status,'product_status_type'=>$product_status_type,'product_status_value'=>$product_status_value,'similar_product_id'=>$similar_product_id,'similar_product_name'=>$similar_product_name,'similar_product_price'=>$similar_product_price,'similar_product_pack'=>$similar_product_pack,'addr_patient_name'=>$addr_patient_name,'addr_address1'=>$addr_address1,'addr_address2'=>$addr_address2,'addr_landmark'=>$addr_landmark,'addr_mobile'=>$addr_mobile));
}
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