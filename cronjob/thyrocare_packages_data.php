<?php
require_once("../config.php");
$curl_url='https://www.thyrocare.com/apis/master.svc/MvcLUEMPdBpopKzXZumcVTzBPG0RkYVeAomi@RBhXRrVcGyko7hIzQ==/all/products';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $curl_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$results= json_decode($result);
foreach($results->MASTERS->PROFILE as $values)
{
    $code=$values->code;
    $name=$values->name;
    $home_collection_charges=$values->hc;
    $image=$values->image_location;
    $price=$values->rate->pay_amt;
    $discounted_price=$values->rate->offer_rate;
    
    
    $margin=$values->margin;
    $user_id   = '4684';
    $margin_price=0;
    $res  = mysqli_query($hconnection, "SELECT id FROM lab_package_master WHERE code='$code' and user_id='$user_id' and discounted_price=0");
    $count_data = mysqli_num_rows($res);
    if ($count_data>0) {
        $test_list = mysqli_fetch_array($res);
        $test_id   = $test_list['id'];
        $margin_price = $margin/2;
        $discounted_price = $price-$margin_price;
        mysqli_query($connection, "UPDATE `lab_package_master` SET `price`='$price',`discounted_price`='$discounted_price' WHERE id='$test_id' and user_id='$user_id'");    
    }
}
?>