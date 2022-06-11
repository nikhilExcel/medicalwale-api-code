<?php
$result = array();
require_once("config.php");

$array_hospital=array(3115,3142,3170,27529,31554,36486,37662,39156,46281,46705,46773,46972,47363,47487,47943,48120,48335,48426,48524,48699,48812,50130,50239,50323,50797,50918,51855,51935,52766,52776,52942,53234,53653,54234,57769,58156,58289,60117);

foreach($array_hospital as $a){

$s1 ="DELETE FROM `user_ledger` WHERE `listing_id` = '.$a.' AND `vendor_category` = '13'";
$r1 = mysqli_query($hconnection, $s1);

}


?>


