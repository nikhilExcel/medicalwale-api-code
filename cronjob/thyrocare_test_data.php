<?php
require_once("../config.php");

$curl_url='https://www.thyrocare.com/apis/master.svc/MvcLUEMPdBpopKzXZumcVTzBPG0RkYVeAomi@RBhXRrVcGyko7hIzQ==/all/products';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $curl_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
$results= json_decode($result);

foreach($results->MASTERS->TESTS as $values)
{
    $code=$values->code;
    $name=$values->name;
    $aliasname=$values->aliasName;
    $fasting=$values->fasting;
    $home_collection_charges=$values->hc;
    $price=$values->rate->pay_amt;
    $discounted_price=$values->rate->offer_rate;
    $disease_group=$values->disease_group;
    $type=$values->type;
    
    $margin=$values->margin;
    $user_id   = '4684';
    $margin_price=0;
    $res  = mysqli_query($hconnection, "SELECT id FROM lab_test_master_details WHERE code='$code' and user_id='$user_id'");
    $count_data = mysqli_num_rows($res);
    if ($count_data>0) {
        $test_list = mysqli_fetch_array($res);
        $test_id   = $test_list['id'];
        $margin_price = $margin/2;
        $discounted_price = $price-$margin_price;
        mysqli_query($connection, "UPDATE `lab_test_master_details` SET `price`='$price',`discounted_price`='$discounted_price' WHERE id='$test_id' and user_id='$user_id'");    
    }
}
?>