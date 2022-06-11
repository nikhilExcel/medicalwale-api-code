<?php

include_once('../../config.php');
if (isset($_POST['user_id']) && isset($_POST['post_id'])) {

    function get_time_difference_php($created_time) {
        date_default_timezone_set('Asia/Calcutta');
        $str = strtotime($created_time);
        $today = strtotime(date('Y-m-d H:i:s'));
        $time_differnce = $today - $str;
        $years = 60 * 60 * 24 * 365;
        $months = 60 * 60 * 24 * 30;
        $days = 60 * 60 * 24;
        $hours = 60 * 60;
        $minutes = 60;
        if (intval($time_differnce / $years) > 1) {
            return intval($time_differnce / $years) . " yrs ago";
        } else if (intval($time_differnce / $years) > 0) {
            return intval($time_differnce / $years) . " yr ago";
        } else if (intval($time_differnce / $months) > 1) {
            return intval($time_differnce / $months) . " months ago";
        } else if (intval(($time_differnce / $months)) > 0) {
            return intval(($time_differnce / $months)) . " month ago";
        } else if (intval(($time_differnce / $days)) > 1) {
            return intval(($time_differnce / $days)) . " days ago";
        } else if (intval(($time_differnce / $days)) > 0) {
            return intval(($time_differnce / $days)) . " day ago";
        } else if (intval(($time_differnce / $hours)) > 1) {
            return intval(($time_differnce / $hours)) . " hrs ago";
        } else if (intval(($time_differnce / $hours)) > 0) {
            return intval(($time_differnce / $hours)) . " hr ago";
        } else if (intval(($time_differnce / $minutes)) > 1) {
            return intval(($time_differnce / $minutes)) . " min ago";
        } else if (intval(($time_differnce / $minutes)) > 0) {
            return intval(($time_differnce / $minutes)) . " min ago";
        } else if (intval(($time_differnce)) > 1) {
            return intval(($time_differnce)) . " sec ago";
        } else {
            return "few seconds ago";
        }
    }

    $resultcharacter = array();
    $product_id = $_POST['post_id'];
    $user_ids = $_POST['user_id'];
    $survival_stories_reviewQuery = mysqli_query($conn, "SELECT survival_stories_review.id,survival_stories_review.user_id,survival_stories_review.survival_stories_id,survival_stories_review.review, survival_stories_review.service,survival_stories_review.date as review_date,users.id as user_id,users.name as firstname FROM `survival_stories_review` INNER JOIN `users` ON survival_stories_review.user_id=users.id WHERE survival_stories_review.survival_stories_id='$product_id' order by survival_stories_review.id desc");
    $survival_stories_review_count = mysqli_num_rows($survival_stories_reviewQuery);
    if ($survival_stories_review_count > 0) {
        while ($row = mysqli_fetch_array($survival_stories_reviewQuery)) {
            extract($row);
            $id = $id;
            $username = $firstname;
            $userimage = '';
            $review = $review;
            $service = $service;
            $review_date = get_time_difference_php($review_date);

            $count_query = mysqli_query($conn, "SELECT id FROM `survival_stories_review_likes` where post_id='$id'");
            $like_count = mysqli_num_rows($count_query);

            //	$comment_query = mysqli_query($conn,"SELECT id FROM `survival_stories_review` where survival_stories_id='$id'");
            //	$post_count = mysqli_num_rows($comment_query);

            $user_ids = $_POST['user_id'];
            $like_count_query = mysqli_query($conn, "SELECT id FROM `survival_stories_review_likes` where user_id='$user_ids' and post_id = '$id'");
            $like_yes_no = mysqli_num_rows($like_count_query);

            $resultcharacter[] = array('id' => $id, 'username' => $username, 'userimage' => $userimage, 'comment' => $review, 'service' => $service, 'comment_date' => $review_date, 'like_count' => $like_count, 'like_yes_no' => $like_yes_no);
        }
        $json = array("status" => 200, "msg" => "success", "count" => sizeof($resultcharacter), "data" => $resultcharacter);
    } else {
        $json = array("status" => 400, "msg" => "review list not found");
    }
} else {
    $json = array("status" => 400, "msg" => "review list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>