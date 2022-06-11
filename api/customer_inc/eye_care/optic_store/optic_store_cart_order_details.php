<?php
require_once ("../../../config.php");
$resultorderlist =array();
if(isset($_POST['user_id']) && ($_POST['order_id']))
{
$user_id = $_POST['user_id'];
$order_id = $_POST['order_id'];
$sql = "SELECT optic_store_cart_order.uni_id,optic_store_cart_order.date,optic_store_cart_order.status,optic_store_subcategory.name,optic_store_subcategory.price,optic_store_cart_order_products.product_quantity,optic_store_subcategory.image1,oc_address.address_id,oc_address.customer_id,oc_address.firstname,oc_address.lastname,oc_address.email,oc_address.telephone,oc_address.address_1,oc_address.address_2,oc_address.landmark FROM `optic_store_cart_order` 
INNER JOIN `optic_store_cart_order_products` 
ON optic_store_cart_order.id=optic_store_cart_order_products.order_id 
INNER JOIN optic_store_subcategory 
ON optic_store_subcategory.id=optic_store_cart_order_products.product_id 
INNER JOIN `oc_address`
ON oc_address.address_id=optic_store_cart_order.address_id
WHERE optic_store_cart_order.user_id='$user_id' AND optic_store_cart_order.id='$order_id'";
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
$product_name=$row['name'];
$product_price=$row['price'];
$product_quantity=$row['product_quantity'];
//$image=$row['image'];
$firstname=$row['firstname'];
$lastname=$row['lastname'];
$addr_patient_name=$firstname.' '.$lastname;
$addr_address1=$row['address_1'];
$addr_address2=$row['address_2'];
$addr_landmark=$row['landmark'];
$addr_mobile=$row['telephone'];


$resultorderlist[] = array('order_no'=>$order_no,'order_date'=>$order_date,'order_status'=>$order_status,'product_name'=>$product_name,'product_price'=>$product_price,'product_quantity'=>$product_quantity,'addr_patient_name'=>$addr_patient_name,'addr_address1'=>$addr_address1,'addr_address2'=>$addr_address2,'addr_landmark'=>$addr_landmark,'addr_mobile'=>$addr_mobile);
}
$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultorderlist),"data" => $resultorderlist); 
}
else
{
$json = array("status" => 0, "msg" => "order list not found");
}
}
else
{
$json = array("status" => 0, "msg" => "order list not found"); 
}
@mysqli_close($connection);
header('Content-type: application/json');
echo json_encode($json);
?>