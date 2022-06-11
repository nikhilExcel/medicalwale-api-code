<?php
include_once('../../config.php');
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
		$ask_saheli_commentQuery = mysqli_query($hconn,"SELECT * from sex_education_answer WHERE post_id='$post_id' order by id asc");
		$ask_saheli_comment_count = mysqli_num_rows($ask_saheli_commentQuery);
		if($ask_saheli_comment_count>0)
		{
		while($row = mysqli_fetch_array($ask_saheli_commentQuery))
		{
		extract($row);
		$id=$id;
		$answer=$answer;
		$username='Medicalwale Experts';			
		$answer_date=get_time_difference_php($date);
		$user_id=$user_id;
		$image='';
        $image='https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/doctor_male.png';
		
		
		$resultcharacter[] = array('id'=>$id,'username'=>$username,'image'=>$image,'answer'=>$answer,'answer_date'=>$answer_date); 
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
