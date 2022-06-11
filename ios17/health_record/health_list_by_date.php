<?php

require_once("../config.php");
$health_record_list = array();
$health_record_doc = array();
if (isset($_POST['patient_id'])) {
    $patient_id = $_POST['patient_id'];
    $folder_id = $_POST['folder_id'];
    if ($patient_id != '' && $folder_id != '') {
        $health_record_media = '';
        $sql2 = "SELECT id,media,date,created_at,type FROM health_record_media WHERE health_record_id='$patient_id' and folder_id='$folder_id' GROUP BY date order by date desc";
        $res2 = mysqli_query($connection, $sql2);
        $count_media = mysqli_num_rows($res2);
        if ($count_media > 0) {
            while ($row_media_ = mysqli_fetch_array($res2)) {
                $date = $row_media_['date'];
                $sql4 = "SELECT id,media,date,created_at,type,image_title,Image_caption FROM health_record_media WHERE date='$date' and health_record_id='$patient_id' and folder_id='$folder_id' order by id desc";
                $res4 = mysqli_query($connection, $sql4);
                $health_record_doc = '';
                while ($row_media = mysqli_fetch_array($res4)) {
                    $media_id = $row_media['id'];
                    $media = $row_media['media'];
                    $date = $row_media['date'];
                    $created_at = $row_media['created_at'];
                    $type_ = $row_media['type'];
                    $image_title = $row_media['image_title'];
                    $image_caption = $row_media['Image_caption'];
                    if ($type_ == 'pdf') {
                        $type_ = 'files';
                    } else {
                        $type_ = 'image';
                    }
                    $media_source = 'https://d2c8oti4is0ms3.cloudfront.net/images/health_record_media/' . $type_ . '/' . $media;

                    $health_record_doc[] = array(
                        'document_id' => $media_id,
                        'document_link' => $media_source,
                        'image_title' => $image_title,
                        'image_caption' => $image_caption
                    );
                }
                $health_record_media[] = array(
                    'document_date' => $date,
                    'document_array' => $health_record_doc,
                    'created_at' => $created_at
                );
            }
            $json = array(
                "status" => 200,
                "msg" => "success",
                "count" => sizeof($health_record_media),
                "data" => $health_record_media
            );
        } else {
            $json = array(
                "status" => 200,
                "msg" => "health list not found"
            );
        }
    } else {
        $json = array(
            "status" => 200,
            "msg" => "health list not found"
        );
    }
} else {
    $json = array(
        "status" => 200,
        "msg" => "health list not found"
    );
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>