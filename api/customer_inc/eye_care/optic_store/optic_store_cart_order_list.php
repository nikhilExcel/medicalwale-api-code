<?php
require_once ("../../../config.php");
$resultorderlist =array(); 
if(isset($_POST['user_id']))
{
$user_id = $_POST['user_id'];
$sql = "SELECT optic_store_cart_order.id AS orderid,optic_store_cart_order.user_id,optic_store_cart_order.address_id,optic_store_cart_order.uni_id,optic_store_cart_order.date,optic_store_cart_order.status,optic_store_cart_order_products.id,optic_store_cart_order_products.order_id,optic_store_cart_order_products.uni_id,optic_store_cart_order_products.product_id  FROM `optic_store_cart_order` 
INNER JOIN `optic_store_cart_order_products` 
ON optic_store_cart_order.id=optic_store_cart_order_products.order_id
WHERE optic_store_cart_order.user_id='$user_id' GROUP BY optic_store_cart_order.uni_id";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
if($count>0)
{
while($row = mysqli_fetch_array($res))
{
$order_id=$row['order_id'];
$order_no=$row['uni_id'];
$order_status=$row['status'];
$order_date=$row['date'];
$resultorderlist[] = array('order_id'=>$order_id,'order_no'=>$order_no,'order_status'=>$order_status,'order_date'=>$order_date);
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