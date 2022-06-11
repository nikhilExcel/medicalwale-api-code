<?php
require_once ("../../config.php");
$articlelist =array();
if(isset($_POST['tag']) || isset($_POST['author']) || isset($_POST['story_id']) || isset($_POST['user_id']))
{
$tag = $_POST['tag'];
$author = $_POST['author'];
$story_id = $_POST['story_id'];
$user_id = $_POST['user_id'];
if($tag!='' || $author!='' || $story_id!='' || $user_id='')
{
$sql = "SELECT * FROM `survival_stories` where tag like '%$tag%' AND author like '%$author%' AND id<>$story_id order by id desc";
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

$id=$row['id'];
$story_link= "https://medicalwale.com/survivor_stories/survivor_stories_details.php?id=".$row['id'];
$title=$row['title'];
$description=$row['description'];
$author=$row['author'];
$tag=$row['tag'];
$date=$row['date'];
$image=$row['image'];
$image=str_replace(' ','%20',$image);
$image='https://d2c8oti4is0ms3.cloudfront.net/images/article_images/'.$image;



$res2 = mysqli_query($connection,"SELECT id FROM `survival_stories_bookmark` WHERE user_id='$user_id' and survival_stories_id='$story_id'");
$count2= mysqli_num_rows($res2);
if($count2>0)
{
$is_bookmark=1;
}
else
{
$is_bookmark=0;
}

$res3 = mysqli_query($connection,"SELECT id FROM `survival_stories_likes` WHERE user_id='$user_id' and survival_stories_id='$story_id'");
$count3=mysqli_num_rows($res3);
if($count3>0)
{
$is_like=1;
}
else
{
$is_like=0;
}

$res4 = mysqli_query($connection,"SELECT id FROM `survival_stories_views` WHERE survival_stories_id='$story_id'");
$views=mysqli_num_rows($res4);

$res5 = mysqli_query($connection,"SELECT id FROM `survival_stories_likes` WHERE survival_stories_id='$story_id'");
$total_like=mysqli_num_rows($res5);




$articlelist[] = array('story_id'=>$id,'story_link'=>$story_link,'tag'=>$tag,'title'=>$title,'author'=>$author,'total_like'=>$total_like,'is_bookmark'=>$is_bookmark,'is_like'=>$is_like,'views'=>$views,'description'=>$description,'image'=>$image,'date'=>$date);
}
$json = array("status" => 1,"msg" => "success","count"=>sizeof($articlelist),"data" => $articlelist);
}
else
{
$json = array("status" => 0, "msg" => "story list not found");
}
}
else
{
$json = array("status" => 0, "msg" => "story list not found");
}
}
else
{
$json = array("status" => 0, "msg" => "story list not found");
}
@mysqli_close($connection);
header('Content-type: application/json');
echo json_encode($json);
?>
