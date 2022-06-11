<?php 
require_once ("config.php"); 
if(isset($_POST['order_id']) && isset($_POST['product_order_id']) && isset($_POST['product_order_status']))
{
$date=date('Y-m-d');
$order_id=$_POST['order_id'];
$product_order_id= explode(",",$_POST['product_order_id']);
$product_order_status= explode(",",$_POST['product_order_status']);

$cnt=count($product_order_id);
for($i=0;$i<$cnt;$i++)
{ 
$update.= mysqli_query($connection,"UPDATE `user_order_product` SET `order_status`='$product_order_status[$i]' where id='$product_order_id[$i]' and order_id='$order_id'");
}
if($update)
{
echo json_encode(array("status" => 201,"message" => "success"));
mysqli_close($connection);
}
else
{
echo json_encode(array("status" => 201,"message" => "failed"));
mysqli_close($connection);
}
}
else
{
echo json_encode(array("status" => 201,"message" => "required"));
mysqli_close($connection);
} 
?>