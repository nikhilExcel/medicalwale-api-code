 <?
 defined('BASEPATH') OR exit('No direct script access allowed');
 
 class NotificationcontrolModel extends CI_Model {
     
     
       var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }
    
     
     public function Notification_All_list($user_id,$page)
        {
            if($page==""){
             $page=1;   
            }
            $count1 = 0;
          //  $radius = $page*5;
            $limit = 10;
            $limitk = 20;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $startk = ($page - 1) * $limitk;
    
            //$query = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc LIMIT $start, $limit");
           $query =$this->db->query("SELECT * FROM `All_notification_Mobile` where (listing_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications' AND notification_type != 'appointment_notifications')  
                     UNION ALL
                     SELECT * FROM `myactivity_notification` where (listing_id = '$user_id' and   notification_type != 'order')   order by created_at desc LIMIT $start, $limit");
            $count = $query->num_rows();
            if ($count > 0) {
                
                $query1 =$this->db->query("SELECT * FROM `All_notification_Mobile` where (listing_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  
                UNION ALL
                SELECT * FROM `myactivity_notification` where (user_id = '$user_id' and   notification_type != 'order')   order by created_at desc LIMIT $start, $limit ");
              $count1 = $query1->num_rows();
    
                
            
                foreach($query->result_array() as $row_main)
                 {
                      $id  = $row_main['id'];
                      $user_id = $row_main['user_id'];
                      $listing_name = $row_main['listing_name'];
                      $listing_id = $row_main['listing_id'];
                      $post_id = $row_main['post_id'];
                      $order_id = $row_main['order_id'];
                      $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                      $order_status = $row_main['order_status'];
                      $booking_id = $row_main['booking_id'];
                      $invoice_no = $row_main['invoice_no'];
                      $notification_type= $row_main['notification_type'];
                      $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                      $package_id  = $row_main['package_id'];
                      $package_name  = $row_main['package_name'];
                      $title= $row_main['title'];
                      $list_type = $row_main['list_type'];
                      $message = $row_main['msg'];
                      $img_url = $row_main['img_url'];
                      $tag  = $row_main['tag'];
                      $healthmall_id  = $row_main['healthmall_id'];
                      $pdf_link  = $row_main['pdf_link'];
                      $booking_time  = $row_main['booking_time'];
                      $booking_from  = $row_main['booking_from'];
                      $booking_to  = $row_main['booking_to'];
                      $noti_type  = $row_main['trans_type'];
                      $is_read  = $row_main['is_read'];
                      $presription_id= $row_main['presription_id'];
                      $article_id = $row_main['article_id'];
                      $med_video_id = $row_main['med_video_id'];
                      $datetime = $row_main['created_at'];
                      if($is_read==0)
                      {
                          $read = "false";
                      }
                      else
                      { 
                          $read = "true";
                      } 
                      $resultpost[] = array(
                          'id'  => $id,
                          'user_id' => $user_id,
                          'listing_name' => $listing_name,
                          'listing_id' => $listing_id,
                          'post_id'   => $post_id,
                          'order_id'  => $order_id,
                          'order_date' => $order_date,
                          'order_status' => $order_status,
                          'booking_id'  => $booking_id,
                          'presription_id' => $presription_id,
                          'article_id' => $article_id,
                          'invoice_no' => $invoice_no,
                          'notification_type' => $notification_type,
                          'notification_date' => $datetime,
                          'title'  => $title,
                          'package_id'  => $package_id,
                          'package_name'  => $package_name,
                          'message' => $message,
                          'img_url'  => $img_url,
                          'tag'  => $tag,
                          'list_type' => $list_type,
                          'noti_type'=>$noti_type,
                          'healthmall_id' => $healthmall_id,
                          'pdf_link' => $pdf_link,
                          'booking_time' => $booking_time,
                          'booking_from' => $booking_from,
                          'booking_to' => $booking_to,
                          'med_video_id' => $med_video_id,
                          'is_read' => $read
                         
                          );
                      
                 }
            } else {
                $resultpost = array();
            }
          
            return array(
                        'status' => 200,
                        'message' => 'success',
                        'count' => $count1,
                        'data' => $resultpost
                        );
        }
    
     public function Notification_All_list_v1($user_id,$page,$type)
    {
        if($page==""){
         $page=1;   
        }
        $count=0;
        $count1 = 0;
      //$radius = $page*5;
        $limit = 10;
        $limitk = 20;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $startk = ($page - 1) * $limitk;


        if($type=="promo"){
        $query = $this->db->query("SELECT * FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and   notification_type!='article' and notification_type!='HealthDictionary' and  notification_type!='quoteweek' and (noti_type='andriod' || noti_type='android') order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
        
            foreach($query->result_array() as $row_main)
               {
                  $id  = $row_main['id'];
                   $query11 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                  $count = $query11->num_rows();
                    if($count>0){
                     $row = $query11->row_array() ;
                        $is_read  = $row['is_read'];
                      }else{
                           $is_read  = $row_main['is_read'];
                      }
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
            } else {
                $resultpost = array();
            }
        }
        
        if($type=="learn"){
        $query = $this->db->query("SELECT * FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and (notification_type='article' || notification_type='HealthDictionary' || notification_type='quoteweek') and (noti_type='andriod' || noti_type='android') order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
        
            foreach($query->result_array() as $row_main)
               {
                  $id  = $row_main['id'];
                  $query11 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                  $count = $query11->num_rows();
                 //   print_r("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                    if($count>0){
                     $row = $query11->row_array();
                        $is_read  = $row['is_read'];
                      }else{
                        $is_read  = $row_main['is_read'];
                      }
                  $user_id1 = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                 //$is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                    if($notification_type=='article'){ 
                      $share_url= 'https://medicalwale.com/blogs/'.$article_id;
                     }else{
                         $share_url='';
                     }
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id1, 
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                        'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'share_url' => $share_url,
                      'is_read' => $read
                     
                      );
                  
                         }
            } else {
                $resultpost = array();
            }
        }   
            
        if($type=="trans"){
                
                $query = $this->db->query("SELECT * FROM `All_notification_Mobile` where  (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order' AND notification_type != 'appointment_notifications') order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where  (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order' AND notification_type != 'appointment_notifications') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,                      
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
                    } else {
                        $resultpost = array();
                    }
            }
            
        if($type=="myactivity"){
              //  echo "SELECT * FROM `myactivity_notification` where (user_id = '$user_id'  AND notification_type != 'order') ||  (listing_id = '$user_id'  AND notification_type != 'order') order by created_at desc LIMIT $start, $limit";
                          $query = $this->db->query("SELECT * FROM `myactivity_notification` where (listing_id = '$user_id' and   notification_type != 'order' AND notification_type != 'appointment_notifications')  order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `myactivity_notification` where  (user_id = '$user_id' AND notification_type != 'order' AND notification_type != 'appointment_notifications') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
                    } else {
                        $resultpost = array();
                    }
            }
            
      
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'count' => $count,
                    'data' => $resultpost
                    );
    }
    
     public function Notification_unread_count($user_id)
        {
                
        $query = $this->db->query("SELECT * FROM `All_notification_Mobile` where  (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order' AND notification_type != 'appointment_notifications')   AND is_read = '0' 
               UNION ALL
           SELECT * FROM `myactivity_notification` where (listing_id = '$user_id' AND notification_type != 'order' AND notification_type != 'appointment_notifications') AND is_read = '0'  order by id desc");
        $count = $query->num_rows(); 
        
        $promo_count = "0";
        
        $learncount = "0";
        
        $trans = $this->db->query("SELECT * FROM `All_notification_Mobile` where  (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order' AND notification_type != 'appointment_notifications')  AND is_read = '0'   order by id desc ");
        $transcount = $trans->num_rows();
        
        
        $myactivity = $this->db->query("SELECT id FROM `myactivity_notification` where listing_id = '$user_id' AND notification_type != 'order' AND notification_type != 'appointment_notifications' AND is_read = '0'  order by created_at desc ");
        $myactivitycount = $myactivity->num_rows();
        
        return array(
                    'status' => 200,
                    'count' => $count,
                    'promo'=> $promo_count,
                    'learn'=> $learncount,
                    'trans'=> $transcount,
                    'myactivity'=> $myactivitycount
                );
        }
    
    public function Notification_read_update($user_id,$noti_id)
        {
         $updated_at = date('Y-m-d H:i:s');
         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE id = '$noti_id'");
         
        if($this->db->affected_rows()>0)
        {
                 return array(
                    'status' => 200,
                    'message' => 'Success'
                );
        }
        else
        {
       
                return array(
                    'status' => 201,
                    'message' => 'Already Updated'
                );
            
        }
    }
    
      public function Notification_Delete($user_id,$id,$type)
    {
        
            if($id == '')
            {
               
                $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' OR post_id = '$user_id'");
                return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
            else
            {
                $exp_noti = explode(',',$id);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                         
                        $ids=$exp_noti[$ip];
                        $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$ids'");
                    }
                }
                else
                {
                     
                  $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$id'");
                }
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
      
    }
    
    public function Notification_Delete_v1($user_id,$id,$type)
    {
        
            if($id == '')
            {
               
                $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' OR post_id = '$user_id'");
                return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
            else
            {
                $exp_noti = explode(',',$id);
                $type_noti = explode(',',$type);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                         
                        $ids=$exp_noti[$ip];
                     
                        if($type_noti[$ip]=="trans"){
                     $count_query1 = $this->db->query("DELETE FROM All_notification_Mobile WHERE id = '$ids'");
                      }
                        if($type_noti[$ip]=="myactivity"){
                     $count_query1 = $this->db->query("DELETE FROM myactivity_notification  WHERE  id = '$ids'");
                      } 
                    }
                }
                else
                {
                      
                        if($type=="trans"){
                     $count_query1 = $this->db->query("DELETE All_notification_Mobile WHERE id = '$id'");
                      }
                        if($type=="myactivity"){
                     $count_query1 = $this->db->query("DELETE myactivity_notification  WHERE  id = '$id'");
                      } 
                 // $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$id'");
                }
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
      
    }
    
        public function Notification_all_read($user_id,$id,$type)
    {
        
                $exp_noti = explode(',',$id);
                 $type_noti = explode(',',$type);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                        $ids=$exp_noti[$ip];
                       $updated_at = date('Y-m-d H:i:s');
                             if($type_noti[$ip]=="trans"){
                         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$ids'");
                          }
                             if($type_noti[$ip]=="myactivity"){
                         $query = $this->db->query("UPDATE myactivity_notification SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$ids'");
                          }
                    }
                }
                
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            
      
    }
    
  
    
    public function Notification_read_update_v1($user_id,$noti_id,$type)
    {
         $updated_at = date('Y-m-d H:i:s');
             $data=$this->Notification_unread_count($user_id);
          if($type=="promo"){
                
                $query = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$noti_id'");
                $count = $query->num_rows();
             
                     if($count>0){
                           return array(
                                        'status' => 201,
                                        'message' => 'Already Updated'
                                    );
                            }else{
                                
                                
                                  $insert_notfication = array(
                                    'user_id'  => $user_id,
                                    'noti_id' => $noti_id,
                                    'is_read'   => 1,
                                );
                                
                               $this->db->insert('other_notifications_status',$insert_notfication);
                            }
                      
          }
        if($type=="trans"){
         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$noti_id'");
          }
             if($type=="myactivity"){
         $query = $this->db->query("UPDATE myactivity_notification SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$noti_id'");
          }
            
            if($this->db->affected_rows()>0)
            {
                     return array(
                        'status' => 200,
                        'message' => 'Success',
                        'data'=>$data
                    );
            }
            else
            {
           
                    return array(
                        'status' => 201,
                        'message' => 'Already Updated'
                    );
                
            }
    }
    
    
    
 }    