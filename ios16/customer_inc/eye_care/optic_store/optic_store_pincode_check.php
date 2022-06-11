<?php

include_once('../../../config.php');
if (isset($_POST['pincode'])) {

    $resultpincode = array();
    $pincode = $_POST['pincode'];
    $roots_herbs_pincodeQuery = mysqli_query($conn, "select id from optic_store_pincode WHERE pincode='$pincode' limit 1");
    $roots_herbs_pincode_count = mysqli_num_rows($roots_herbs_pincodeQuery);
    if ($roots_herbs_pincode_count > 0) {
        $json = array("status" => 1, "msg" => "success");
    } else {
        $json = array("status" => 0, "msg" => "pincode not found");
    }
} else {
    $json = array("status" => 0, "msg" => "pincode not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>
