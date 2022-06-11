<?php

include_once('../../../config.php');

if (isset($_POST['id'])) {

    $resulttrimester = array();

    $trimesterQuery = mysqli_query($conn, "SELECT title, details, image FROM `pregnancy_child` order by id asc");
    $trimester_count = mysqli_num_rows($trimesterQuery);
    if ($trimester_count > 0) {
        while ($row = mysqli_fetch_array($trimesterQuery)) {
            extract($row);
            $title = $title;
            $details = $details;
            $image = $image;
            $details = str_replace("\n", "", $details);
            $details = rtrim($details, ',');
            $title = rtrim($title, ',');
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ofw_images/child/' . $image;
            $resulttrimester[] = array("title" => $title,
                'details' => $details,
                'image' => $image,
            );
        }



        $json = array("status" => 1, "msg" => "success", "count" => sizeof($resulttrimester), "data" => $resulttrimester);
    } else {
        $json = array("status" => 0, "msg" => "mom list not found");
    }
} else {
    $json = array("status" => 0, "msg" => "mom list not found");
}


@mysqli_close($conn);

/* Output header */
header('Content-type: application/json');
echo json_encode($json);
?>
