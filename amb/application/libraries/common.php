<?php

/**
 * Format class
 *
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author  	Phil Sturgeon
 * @license		http://philsturgeon.co.uk/code/dbad-license
 */
class Common {

    public function android_push($token, $load, $key) {

        // $url = 'https://fcm.googleapis.com/fcm/send';
        // define( 'API_ACCESS_KEY', 'AIzaSyBdtoqZnDtDfLWElaGmRi9GrTy0t364SUs');
        // $fields = array(
        //     'registration_ids' => $token,
        //     'data' => $load
        // );
        // $headers = array(
        //     'Authorization: key=' . API_ACCESS_KEY,
        //     'Content-Type: application/json'
        // );
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // $result = curl_exec($ch);
        // print_r($result);
        // if ($result === FALSE) {
        //     die('Curl failed: ' . curl_error($ch));
        // }
        // curl_close($ch);
          //    $msg = array
          // (  
          //       'title' => 'You have a new Ride',
          //       'body'  => 'Your Ride Has been Cancelled',
          //       'icon'  => 'myicon',
          //       'sound' => 'mySound'
          // );
         define( 'API_ACCESS_KEY', 'AAAA3WXM0sY:APA91bHTtTr38NM_lFlgLbVZd0gex74YcLZToH0C4xgiiB0PBiQnDzZ7sYU4Spd-zc0B7cU_DdXqbzCDpMNF8GFFQgc2E3qtik9rIuIA5zeVgKD-X1y2wfBj--eLKxMWxii8Pwxdgym1');
            $fields = array
                    (
                    'registration_ids' => $token,
                        'notification'  => $load
                    );
        
        
        $headers = array
                (
                    'Authorization: key=' . API_ACCESS_KEY,
                    'Content-Type: application/json'
                );   
       // print_r($fields); 
      //  die;
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );

        curl_close( $ch );
    }

    public function Generate_hash($length) {

        $characters = '0123456789' . rand(0, 99999);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }

}

?>
