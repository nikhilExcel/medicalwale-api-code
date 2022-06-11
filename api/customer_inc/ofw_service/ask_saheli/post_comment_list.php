<?php
include_once('../../../config.php');
if(isset($_POST['post_id']))
{
function get_time_difference_php($created_time)
{
        date_default_timezone_set('Asia/Calcutta');
        $str = strtotime($created_time);
        $today = strtotime(date('Y-m-d H:i:s'));
        $time_differnce = $today-$str;
        $years = 60*60*24*365;
        $months = 60*60*24*30;
        $days = 60*60*24;
        $hours = 60*60;
        $minutes = 60;
        if(intval($time_differnce/$years) > 1)
        {
            return intval($time_differnce/$years)." yrs ago";
        }else if(intval($time_differnce/$years) > 0)
        {
            return intval($time_differnce/$years)." yr ago";
        }else if(intval($time_differnce/$months) > 1)
        {
            return intval($time_differnce/$months)." months ago";
        }else if(intval(($time_differnce/$months)) > 0)
        {
            return intval(($time_differnce/$months))." month ago";
        }else if(intval(($time_differnce/$days)) > 1)
        {
            return intval(($time_differnce/$days))." days ago";
        }else if (intval(($time_differnce/$days)) > 0) 
        {
            return intval(($time_differnce/$days))." day ago";
        }else if (intval(($time_differnce/$hours)) > 1) 
        {
            return intval(($time_differnce/$hours))." hrs ago";
        }else if (intval(($time_differnce/$hours)) > 0) 
        {
            return intval(($time_differnce/$hours))." hr ago";
        }else if (intval(($time_differnce/$minutes)) > 1) 
        {
            return intval(($time_differnce/$minutes))." min ago";
        }else if (intval(($time_differnce/$minutes)) > 0) 
        {
            return intval(($time_differnce/$minutes))." min ago";
        }else if (intval(($time_differnce)) > 1) 
        {
            return intval(($time_differnce))." sec ago";
        }else
        {
            return "few seconds ago";
        }
}
		$resultcharacter =array(); 
		$post_id=$_POST['post_id'];
		$user_id=$_POST['user_id'];
		$ask_saheli_commentQuery = mysqli_query($conn,"SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_user.name,ask_saheli_character.image FROM ask_saheli_comment INNER JOIN ask_user on ask_user.user_id=ask_saheli_comment.user_id INNER JOIN ask_saheli_character on ask_saheli_character.id=ask_user.image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id asc");
		$ask_saheli_comment_count = mysqli_num_rows($ask_saheli_commentQuery);
		if($ask_saheli_comment_count>0)
		{
		while($row = mysqli_fetch_array($ask_saheli_commentQuery))
		{
		extract($row);
		$id=$id;
		$count_query = mysqli_query($conn,"SELECT * FROM `ask_saheli_comment_like` where comment_id='$id'");
		$like_count = mysqli_num_rows($count_query);
		
		$like_count_query = mysqli_query($conn,"SELECT * FROM `ask_saheli_comment_like` where user_id='$user_id' and comment_id='$id'");
		$like_yes_no = mysqli_num_rows($like_count_query);
		
		
		$comment=$comment;
		$username=$name;			
		$comment_date=get_time_difference_php($date);
		$image='https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/'.$image;
		$resultcharacter[] = array('id'=>$id,'username'=>$username,'image'=>$image,'like_count'=>$like_count,'like_yes_no'=>$like_yes_no,'comment'=>$comment,'comment_date'=>$comment_date); 
		}
		$json = array("status" => 1,"msg" => "success","count"=>sizeof($resultcharacter),"data" => $resultcharacter);
	}
	else
	{
	$json = array("status" => 0, "msg" => "comment list not found");
	}
}
	else
	{
	$json = array("status" => 0, "msg" => "comment list not found");
	}	

	@mysqli_close($conn);
	/* Output header */
	header('Content-type: application/json');
	echo json_encode($json);
?>
