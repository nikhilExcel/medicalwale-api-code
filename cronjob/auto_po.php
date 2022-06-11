<?php
    require_once("../config.php");
    date_default_timezone_set('Asia/Calcutta');
    $current_date = date('Y-m-d H:i:s');
    $date= date('Y-m-d');
   /* function send_gcm_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type) {
       echo $reg_id;
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "tag" => $tag,
                'sound' => 'default',
                "notification_image" => $img_url,
                "notification_type" => $type,
                "notification_date" => $date
               
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        print_r($result); die;
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }*/
    
    $dictionary_sql = "SELECT user_id,pbioms FROM medical_stores WHERE pbioms = '1' AND is_active='1'";
    $dictionary_res = mysqli_query($hconnection, $dictionary_sql);
    $count_data = mysqli_num_rows($dictionary_res);
   
    if($count_data>0)
    {
        while ($list = mysqli_fetch_array($dictionary_res)) {
            $user_id = $list['user_id']; 
         
            $table_name = "stock_".$user_id;
            
            $val = mysqli_query($hconnection,"select 1 from $table_name LIMIT 1");
            if($val !== FALSE)
            {
                $dictionary_s = "SELECT *,sum(quantity) as tot_qunatity FROM $table_name group by product_id, expiry_date, user_id HAVING tot_qunatity < 5 ";
             
                $dictionary = mysqli_query($hconnection, $dictionary_s);
                $count_ = mysqli_num_rows($dictionary);
                
                if($count_ > 0)
                {
                    $i=1;
                    while ($list1 = mysqli_fetch_array($dictionary)) {
                       
                        $users_id       = $list1['user_id']; 
                        $product_id     = $list1['product_id']; 
                        $expiry_date    = $list1['expiry_date']; 
                        $tot_qunatity   = $list1['tot_qunatity']; 
                        $warehouse_id   = $list1['warehouse_id']; 
                        $fixed_min_level   = $list1['fixed_min_level'];
                        $fixed_max_level   = $list1['fixed_max_level'];
                        $batch_no   = $list1['batch_no'];
                        $mrp            = $list1['mrp']; 
                        $distributor_id = $list1['distributor_id']; 
                        $dictionarys    = "SELECT * FROM inventory_new_po WHERE user_id='$users_id' AND product_id='$product_id' AND expiry_date='$expiry_date' AND po_status='Created'";
              
                        $dictionaryss = mysqli_query($hconnection, $dictionarys);
                        $count_s = mysqli_num_rows($dictionaryss);
                       
                        if($count_s == 0)
                        {
                            
                            $s = "INSERT INTO inventory_new_po (user_id, vendor_id, warehouse_id, distributor_id, po_date, po_status, expiry_date, generated_by,batch_no,product_id, fixed_min_level,fixed_max_level,pre_quantity,quantity, price_per_quantity, created_at)
                                                           VALUES ('$users_id', '13', '$warehouse_id', '$distributor_id', '$date', 'Created', '$expiry_date', 'Auto', '$batch_no', '$product_id', '$fixed_min_level','$fixed_max_level', '$tot_qunatity', '$fixed_max_level', '$mrp', '$current_date')";
                          
                            $r = mysqli_query($hconnection, $s);
                           
                           // $last_id = mysqli_insert_id($hconnection); 
                           /* 
                            $s1 = "INSERT INTO inventory_po_details (po_id, )
                                                    VALUES ('$last_id', '$product_id', '$expiry_date', '100', '$mrp')";
                            $r1 = mysqli_query($hconnection, $s1);*/
                            
                        }
                        
                        $i++;
                    }
                }
            }
          
           /* $s = "INSERT INTO diet_plan_notifications (user_id, package_id, booking_id, title, msg, notification_type, created_at)
                   VALUES ('$user_id', '$packageid', '$packageid', '$title', '$msg', '$type', '$current_date')";
                   $r = mysqli_query($hconnection, $s);*/
         
      
    }
    
    }
   
   
?>

