<?php
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $current_date = date('Y-m-d H:i:s');
    $user_id = '1515';
    $comment = 'We are extremely sorry for the delay and the inconvenience caused to you. Doctors are still researching on your problem we will get back to you with the solution within another 48 hours. If it is an emergency, then we would suggest you to visit your nearest doctor.';
    define("POST_URL", "https://live.medicalwale.com/v62/healthwall/post_comment");
	$headers = array(
        'Client-Service:frontend-client',
        'Auth-Key:medicalwalerestapi',
        'Content-Type:application/json',
        'User-ID:1',
        'Authorizations:25iwFyq/LSO1U'
    );
	$sql = "SELECT id,post_id,user_id,description,created_at FROM comments where user_id='1515' GROUP BY post_id HAVING COUNT(*) < 2 ORDER BY id DESC";
    $res = mysqli_query($hconnection, $sql);
    while($list=mysqli_fetch_array($res)){
		$post_id=$list['post_id'];
		$system_date=$list['created_at'];
		$hourdiff = round((strtotime($current_date) - strtotime($system_date))/3600, 1);
		if($hourdiff>=48){
		$userql = "SELECT user_id FROM posts where id='$post_id' limit 1";
        $userres = mysqli_query($hconnection, $userql);
        $userlist=mysqli_fetch_array($userres);
        $user_id=$userlist['user_id'];
        $fields = array(
            'user_id' => '1515',
            'post_id' => $post_id,
            'comment' => $comment,
            'post_user_id' => $user_id
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, POST_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
		}
	}
?>