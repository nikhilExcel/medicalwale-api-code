<?php
require_once("config.php");


$sql = "SELECT * FROM `category` order by id asc";
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
$category_id=$row['id'];
$category=$row['category'];
$image='https://d2c8oti4is0ms3.cloudfront.net/images/images/'.$row['image'];
$store_image='https://d2c8oti4is0ms3.cloudfront.net/images/images/'.$row['store_image'];

array_push($result2,array('id'=>$category_id,'category'=>$category,'image'=>$image,'store_image'=>$store_image));

}
array_push($result1,array('true_false'=>$true_false));
$arry = array(array('true_false'=>$true_false),$result2);
echo json_encode($arry);
mysqli_close($connection); 
}
else
{
$error_msg='No Category List';
$true_false='false';
array_push($result1,array('true_false'=>$true_false,'error_msg'=>$error_msg));
echo json_encode($result1); 
mysqli_close($connection); 
}

?>