<?php
function curr_date(){

        date_default_timezone_set('Asia/Calcutta');
        return $date = date('Y-m-d H:i:s');
}

function video_path(){

     return "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/video/";
}
function image_path(){

     return "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/image/";
}
function avatar_path(){

     return "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/";
}
function fitness_image_path(){

     return "https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/image/";
}

function SthreeImgUrl(){

	return "https://d2c8oti4is0ms3.cloudfront.net/images/";
}


function crul_api($ur,$data)
{
        $u="https://sandboxapi.medicalwale.com/v52/";
        $url=$u.$ur;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['User-ID:1','Authorizations:25iwFyq/LSO1U','Client-Service:frontend-client','Auth-Key:medicalwalerestapi']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
         return $result;
}






?>
