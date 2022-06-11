<?php
require_once("../config.php");

$res  = mysqli_query($hconnection, "SELECT * FROM test_filter_list where test_id>0 order by id asc");
$count_data = mysqli_num_rows($res);
if ($count_data>0) {
    while($test_list = mysqli_fetch_array($res)){
        $test_id            = $test_list['test_id'];
        $category           = $test_list['category'];
        $name               = $test_list['name'];
        $description        = $test_list['description'];
        $test_category      = $test_list['test_category'];
        $most_common_factor = $test_list['most_common_factor'];
        
        $insert=mysqli_query($connection, "INSERT INTO `lab_test_master`(`category_id`, `name`, `description`, `most_common_factor`, `test_category`) VALUES ('$category', '$name', '$description', '$most_common_factor', '$test_category')");
        $new_test_id = $connection->insert_id;
        if($new_test_id>0){
            $res2  = mysqli_query($hconnection, "SELECT * FROM lab_test_details1 WHERE test_id='$test_id'");
            $count_data2 = mysqli_num_rows($res2);
            if ($count_data2>0) {
                while($detail_list = mysqli_fetch_array($res2)){
                    $user_id   = $detail_list['user_id'];
                    $price   = $detail_list['price'];
                    $discounted_price   = $detail_list['discounted_price'];
                    $discount   = $detail_list['discount'];
                    $medicalwale_discount   = $detail_list['medicalwale_discount'];
                    $home_delivery   = $detail_list['home_delivery'];
                    mysqli_query($connection, "INSERT INTO `lab_test_master_details`(`test_id`, `user_id`,`type`, `price`, `discounted_price`, `discount`, `medicalwale_discount`, `home_delivery`) VALUES ('$new_test_id', '$user_id','TEST', '$price', '$discounted_price', '$discount', '$medicalwale_discount', '$home_delivery')"); 
                }
            }
        }
    }
}
?>
