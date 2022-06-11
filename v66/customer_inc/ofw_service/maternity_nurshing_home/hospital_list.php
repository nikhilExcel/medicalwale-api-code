<?php

require_once("../../../config.php");
$hospital_array = array();
if (isset($_POST['map_location'])) {
    $map_location = $_POST['map_location'];
    if ($map_location != '') {
        $sql = "SELECT * FROM `hospitals`";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $true_false = 'true';
            while ($row = mysqli_fetch_array($res)) {
                $true_false = 'true';
                $hospital_id = $row['id'];
                $name_of_hospital = $row['name_of_hospital'];
                $certificates_accred = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/acc-1.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/acc-2.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/acc-3.png';
                ;

                $speciality_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/neurology.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/orthopedic.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/gastroenterology.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/otology.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/urology.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/cardiology.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/pulmonology.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/plastic_surgery.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/oral_health.png';
                $speciality_text = 'Neurology,Orthopedic,Gastroenterology,Otology,Urology,Cardiology,Pulmonology,Plastic Surgery,Oral Health';



                $address = $row['address'];
                $pincode = $row['pincode'];
                $contact = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $email = $row['email'];
                $image = $row['image'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $rating = $row['rating'];

                $review_query = mysqli_query($conn, "SELECT id FROM `hospital_review` where hospital_id='$hospital_id'");
                $review = mysqli_num_rows($review_query);

                $distance = '500 meters away';

                $more_details = 'We developed a new vision, a new strategy and a new model for healthcare delivery. This new vision called for University Hospitals to better meet the healthcare needs of a significant portion of northeast Ohio through geographic expansion and an increase in the types of services we offer.A hospital is a health care institution providing patient treatment with specialized medical and nursing staff and medical equipment. The best-known type of hospital is the general hospital, which typically has an emergency department to treat urgent health problems ranging from fire and accident victims to a heart attack. ';

                $details = $row['details'] . ' ' . $more_details;


                $hospital_video = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/Hospital_Intro.mp4,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/Hospital_Intro.mp4';
                $hospital_video_thumnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/Hospital_Intro_thumbnail.png,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/Hospital_Intro_thumbnail.png';

                $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images1.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/Hospital_Intro.mp4,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images2.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images3.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images4.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images5.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images6.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/images7.jpg';
                $gallery_name = 'Sunshine Hospital,Hospital Introduction,Emergency Entry,City Hospital,Sir William Unit Care, GK Naidu Memorial Hospital,Hospital Inside,Emergency Patient';

                $hospital_array[] = array('hospital_id' => $hospital_id, 'name_of_hospital' => $name_of_hospital, 'distance' => $distance, 'certificates_accred' => $certificates_accred, 'speciality_image' => $speciality_image, 'speciality_text' => $speciality_text, 'address' => $address, 'pincode' => $pincode, 'contact' => $contact, 'city' => $city, 'state' => $state, 'email' => $email, 'gallery' => $gallery, 'gallery_name' => $gallery_name, 'details' => $details, 'rating' => $rating, 'review' => $review, 'lat' => $lat, 'lng' => $lng);
            }
            $json = array("status" => 1, "msg" => "success", "count" => sizeof($hospital_array), "details" => $more_details, "data" => $hospital_array);
        } else {
            $json = array("status" => 0, "msg" => "Hospital list not found");
        }
    } else {
        $json = array("status" => 0, "msg" => "Hospital list not found");
    }
} else {
    $json = array("status" => 0, "msg" => "Hospital list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>