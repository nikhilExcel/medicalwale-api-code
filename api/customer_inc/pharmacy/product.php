<?php
require_once("config.php");
$result = array();
if(isset($_POST['keyword']))
{
$keyword = $_POST['keyword'];
$sql = "SELECT * FROM product WHERE product_name LIKE '%$keyword%' limit 15";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
if($count>0)
{
$true_false='true';
while($row = mysqli_fetch_array($res))
{
$true_false='true';
$id=$row['id'];
$product=$row['product_name'];
$company=$row['company'];
$pack=$row['pack'];
$product_price=$row['product_price'];
$content=$row['content'];
$image='https://d2c8oti4is0ms3.cloudfront.net/images/product/medicalwale_medicine_icon.png';

$status='1';
$msg='success';
array_push($result,array('id'=>$id,'product'=>$product,'product_price'=>$product_price,'company'=>$company,'pack'=>$pack,'content'=>$content,'image'=>$image));
$arry = array('status'=>$status,'msg'=>$msg,'data'=>$result);
}
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$status='0';
$msg='No Product Found';
$result='';
$arry = array('status'=>$status,'msg'=>$msg,'data'=>$result);
echo json_encode($arry);
mysqli_close($connection); 
}
}
else
{
$status='0';
$msg='No Product Found';
$result='';
$arry = array('status'=>$status,'msg'=>$msg,'data'=>$result);
echo json_encode($arry);
mysqli_close($connection);
}
?>