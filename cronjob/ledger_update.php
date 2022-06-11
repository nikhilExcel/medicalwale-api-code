<?php
$result = array();
require_once("../config.php");
$query = "SELECT * FROM user_priviladge_card_new";
$data = mysqli_query($hconnection, $query);
while($row = mysqli_fetch_array($data))
{
    $user_id=$row['user_id'];
    $card_no=$row['card_no'];
    $pin_number=$row['pin_number'];
    $status=$row['status'];
    $card_type=$row['card_type'];
    $pay_type=$row['pay_type'];


    $sel="SELECT * FROM user_priviladge_card_new where card_no='$card_no'";
    $data1 = mysqli_query($hconnection, $sel);
    $my=mysqli_num_rows($data1);
    if($my > 0)
    {
      $ins="INSERT INTO `user_priviladge_card_new_test_1`(`user_id`, `card_no`, `pin_number`, `status`, `card_type`, `pay_type`) VALUES ('$user_id','$card_no','$pin_number','$status','$card_type','$pay_type')";
    $data12 = mysqli_query($hconnection, $up);
    }  
    else
    {
        $ins="INSERT INTO `user_priviladge_card_new_test`(`user_id`, `card_no`, `pin_number`, `status`, `card_type`, `pay_type`) VALUES ('$user_id','$card_no','$pin_number','$status','$card_type','$pay_type')";
    $data12 = mysqli_query($hconnection, $up);
    }
    
    

}

?>


