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
		$roots_herbs_reviewQuery = mysqli_query($conn,"SELECT id,comment,user_id,date FROM find_sexologist_review_comment WHERE post_id='$post_id' order by id desc");
		$roots_herbs_review_count = mysqli_num_rows($roots_herbs_reviewQuery);
		if($roots_herbs_review_count>0)
		{
		while($row = mysqli_fetch_array($roots_herbs_reviewQuery))
		{
		extract($row);
		$id=$id;
		$comment=$comment;
		$user_id=$user_id;
		$user_reviewQuery = mysqli_query($conn,"SELECT name FROM timelines WHERE id='$user_id'");
		$username_list = mysqli_fetch_array($user_reviewQuery);
		$username=$username_list['name'];		
		
		$comment_date=get_time_difference_php($date);
		$count_query = mysqli_query($conn,"SELECT id FROM `find_sexologist_review_comment_like` where comment_id='$id'");
		$like_count = mysqli_num_rows($count_query);
		
		$like_count_query = mysqli_query($conn,"SELECT id FROM `find_sexologist_review_comment_like` where user_id='$user_id' and comment_id='$id'");
		$like_yes_no = mysqli_num_rows($like_count_query);
		
		$resultcharacter[] = array('id'=>$id,'username'=>$username,'like_count'=>$like_count,'like_yes_no'=>$like_yes_no,'comment'=>$comment,'comment_date'=>$comment_date); 
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
