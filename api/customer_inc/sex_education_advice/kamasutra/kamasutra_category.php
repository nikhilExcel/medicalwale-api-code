<?php 
require_once("../../../config.php");
$category_array =array(); 
if(isset($_POST['id']))
{
$sql = "SELECT * FROM `kamasutra_category` order by id asc";
$res = mysqli_query($connection,$sql); 
$count = mysqli_num_rows($res);
if($count>0)
{
$true_false='true';
while($row = mysqli_fetch_array($res))
{
$true_false='true';
$category_id=$row['id'];
$category=$row['category'];

$sql2 = "SELECT * FROM `kamasutra_position_list` WHERE position_category='$category_id'";
$res2 = mysqli_query($connection,$sql2); 
$count2 = mysqli_num_rows($res2);

$category_array[]=array('category_id'=>$category_id,'category'=>$category,'total_items'=>$count2);
}
$json = array("status" => 1,"msg" => "success","count"=>sizeof($category_array),"data" => $category_array); 
}
else
{
$json = array("status" => 0, "msg" => "Category list not found");
}
}
else
{
$json = array("status" => 0, "msg" => "Category list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>