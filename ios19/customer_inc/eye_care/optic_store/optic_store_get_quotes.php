<?php

include_once('../../../config.php');
if (isset($_POST['pincode'])) {
    $resultcity = array();
    $pincode = $_POST['pincode'];
    $pincodeQuery = mysqli_query($conn, "SELECT id,pincode FROM optic_store_pincode where pincode='$pincode' order by pincode asc");
    $pincode_count = mysqli_num_rows($pincodeQuery);
    if ($pincode_count > 0) {
        $resultpincode = '100';
        $json = array("status" => 1, "msg" => "success", "delivery" => $resultpincode);
    } else {
        $json = array("status" => 0, "msg" => "city list not found");
    }
} else {
    $json = array("status" => 0, "msg" => "city list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>
