<?php 

function linkSSAMpariharDoctor(){
    $base_url = base_url();
    if($base_url == "https://live.medicalwale.com/Doctor/"){
       return $link = "https://doctor.medicalwale.com/"; 
    }
    else {
       return $link = "https://vendor.sandbox.medicalwale.com/doctor/"; 
    }
    
    if($base_url == "https://live.medicalwale.com/Fitnesscenter/"){
       return $link = "https://fitness.medicalwale.com/"; 
    }
    else {
       return $link = "https://vendor.sandbox.medicalwale.com/fitness/"; 
    }
}

?>