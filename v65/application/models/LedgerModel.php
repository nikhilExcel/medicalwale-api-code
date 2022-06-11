<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LedgerModel extends CI_Model {
    
    public function pharmacy_ledger($user_id){
        $data = array();
        
        $ledger_balance = 10;
        $customer_name = 'Swapnali Waghunde';
        $invoive_no = 201909042134;
        $total_debit = 100;
        $total_credit = 200;
        $total_balance = $total_credit - $total_debit;
        $date = "10/05/2018 02:47 pm"; 
        $settled = 1;
        
        
        $bs['customer_name'] = $customer_name;
        $bs['invoice_no'] = $invoive_no;
        $bs['total_debit'] = $total_debit;
        $bs['total_credit'] = $total_credit;
        $bs['total_balance'] = $total_balance;
        $bs['date'] = $date;
        $bs['settled'] = $settled;
        
        $details['date'] = "10/05/2018 02:47 pm"; 
        $details['user_comment'] = "user_comment"; 
        $details['medi_comment'] = "medi_comment"; 
        $details['vendor_comment'] = "vendor_comment"; 
        $details['credit'] = 100; 
        $details['debit'] = 50; 
        $details['balance'] = 50; 
        $details['mode'] = 1; 
        $details['txn_no'] = "1"; 
       
        
        $bs['details'][] = $details;
        
           $details['date'] = "10/05/2018 02:47 pm"; 
        $details['user_comment'] = "user_comment"; 
        $details['medi_comment'] = "medi_comment"; 
        $details['vendor_comment'] = "vendor_comment"; 
        $details['credit'] = 100; 
        $details['debit'] = 50; 
        $details['balance'] = 50; 
        $details['mode'] = 1; 
        $details['txn_no'] = ""; 
       
        
        $bs['details'][] = $details;
        
        
        $balance_sheet[] = $bs;
        
        $data['balance_sheet'] = $balance_sheet;
        
         $customer_name = 'Swapnali Waghunde';
        $invoive_no = 201909042134;
        $total_debit = 100;
        $total_credit = 200;
        $total_balance = $total_credit - $total_debit;
      
        
        
        $bs =  $details = array();
       
         $settled = 0;
        
        $bs['customer_name'] = $customer_name;
        $bs['invoice_no'] = $invoive_no;
        $bs['total_debit'] = $total_debit;
        $bs['total_credit'] = $total_credit;
        $bs['total_balance'] = $total_balance;
        $bs['date'] = $date;
        $bs['settled'] = $settled;
      
        
        $details['date'] = "10/05/2018 02:47 pm"; 
        $details['user_comment'] = "user_comment"; 
        $details['medi_comment'] = "medi_comment"; 
        $details['vendor_comment'] = "vendor_comment"; 
        $details['credit'] = 100; 
        $details['debit'] = 50; 
        $details['balance'] = 50; 
        $details['mode'] = 1; 
        $details['txn_no'] = "48765"; 
       
        
        $bs['details'][] = $details;
        
           $details['date'] = "10/05/2018 02:47 pm"; 
        $details['user_comment'] = "user_comment"; 
        $details['medi_comment'] = "medi_comment"; 
        $details['vendor_comment'] = "vendor_comment"; 
        $details['credit'] = 100; 
        $details['debit'] = 50; 
        $details['balance'] = 50; 
        $details['mode'] = 1; 
       $details['txn_no'] = "549837"; 
        
        $bs['details'][] = $details;
        
        $balance_sheet[] = $bs;
        $data['ledger_balance'] = $ledger_balance;
        $data['balance_sheet'] = $balance_sheet;
     
        return $data;
    }
    
    
    public function get_ledger($user_id,$listing_id){
       $details = $data = array();
        $txn_count = $settled_count = $is_settled = $total_credit = $total_debit = $total_balance = 0;
        $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` WHERE `user_id` = '$user_id' order by id DESC")->row_array();
        if(sizeof($ledger_balance_row) > 0){
            $ledger_balance = $ledger_balance_row['ledger_balance'];
        } else {
            $ledger_balance = 0;
        } 
        
        function txn_date($a, $b)
        {
            $t1 = strtotime($a['txn_date']);
            $t2 = strtotime($b['txn_date']);
            return $t1 - $t2;
        } 
    //   have to check booking_date and appointment_date - pending
        $sql_query = "SELECT u.name as customer_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , uvl.ledger_id,  uvl.user_id,  uvl.ledger_owner_type,  uvl.invoice_no,  uvl.transaction_id,  uvl.listing_id,  uvl.listing_id_type,  uvl.credit,  uvl.debit,  uvl.balance,  uvl.payment_method,  uvl.user_comments,  uvl.mw_comments,  uvl.vendor_comments,  uvl.verified,  uvl.trans_status,  uvl.order_type,  uvl.created_at,  uvl.created_by,  uvl.modified_at,  uvl.modified_by,  uvl.settled FROM `user_vendor_ledger` as uvl left join user_order as uo on (uo.invoice_no = uvl.invoice_no AND uvl.order_type = 1) left join booking_master as bm on (uvl.invoice_no = bm.booking_id AND uvl.order_type = 2) left join doctor_booking_master as dbm on (uvl.invoice_no = dbm.booking_id AND uvl.order_type = 3) left join hospital_booking_master as hbm on (uvl.invoice_no = hbm.booking_id AND uvl.order_type = 4) left join pestcontrol_booking_master as pbm on (uvl.invoice_no = pbm.booking_id AND uvl.order_type = 5) left join users as u on (uvl.user_id = u.id)  WHERE (uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_id')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_id')
        UNION 
        SELECT u.name as customer_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , vvl.ledger_id,  vvl.user_id,  vvl.ledger_owner_type,  vvl.invoice_no,  vvl.transaction_id,  vvl.listing_id,  vvl.listing_id_type,  vvl.credit,  vvl.debit,  vvl.balance,  vvl.payment_method,  vvl.user_comments,  vvl.mw_comments,  vvl.vendor_comments,  vvl.verified,  vvl.trans_status,  vvl.order_type,  vvl.created_at,  vvl.created_by,  vvl.modified_at,  vvl.modified_by,  vvl.settled FROM `vendor_vendor_ledger` as vvl left join user_order as uo on (uo.invoice_no = vvl.invoice_no AND vvl.order_type = 1) left join booking_master as bm on (vvl.invoice_no = bm.booking_id AND vvl.order_type = 2) left join doctor_booking_master as dbm on (vvl.invoice_no = dbm.booking_id AND vvl.order_type = 3) left join hospital_booking_master as hbm on (vvl.invoice_no = hbm.booking_id AND vvl.order_type = 4) left join pestcontrol_booking_master as pbm on (vvl.invoice_no = pbm.booking_id AND vvl.order_type = 5) left join users as u on (vvl.user_id = u.id)  WHERE (vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_id')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_id')
        UNION 
        SELECT u.name as customer_name, uol.created_at as booking_date, null as doctor_booking_date, null as hospital_booking_date, null as pestcontrol_booking_date, uol.created_at as order_date , uol.ledger_id, uol.user_id, uol.ledger_owner_type, uol.invoice_no, uol.transaction_id, null as listing_id, null as listing_id_type, uol.credit, uol.debit, uol.balance, uol.payment_method, uol.user_comments, uol.mw_comments, uol.vendor_comments, uol.verified, uol.trans_status, null  as order_type, uol.created_at, uol.created_by, uol.modified_at, uol.modified_by, uol.settled FROM user_own_ledger as uol left join users as u on (uol.user_id = u.id)  WHERE uol.user_id = '$user_id' AND uol.ledger_owner_type = '$listing_id' 
        
        ORDER BY invoice_no  DESC ";
        // echo $sql_query; die();
      
        $user_vendor_ledger = $this->db->query($sql_query)->result_array();
        // print_r($user_vendor_ledger);
    //   die();
        $i = $old_invoive_no = 0;
        if(sizeof($user_vendor_ledger) > 0){
            // print_r($user_vendor_ledger); die();
            foreach($user_vendor_ledger as $vl){
               
                $invoive_no = $vl['invoice_no'];
                $customer_name = $vl['customer_name'];
                $row_debit =    $vl['debit'];
                $row_credit = $vl['credit'];
                $row_balance = $vl['balance'];
                $date = $vl['created_at'];
                $owner_user_id =    $vl['user_id'];
                $ledger_owner_type =  $vl['ledger_owner_type'];
                $listing_id =    $vl['listing_id'] ;
                $listing_id_type =    $vl['listing_id_type'];
                $payment_method =    $vl['payment_method'];
                $user_comments =    $vl['user_comments'];
                $mw_comments =    $vl['mw_comments'];
                $vendor_comments =    $vl['vendor_comments'];
                $verified =    $vl['verified'];
                $trans_status =    $vl['trans_status'];
                $order_type =    $vl['order_type'];
                $order_date =    $vl['order_date'];
                $booking_date = $vl['booking_date'];
                $doctor_booking_date = $vl['doctor_booking_date'];
                $hospital_booking_date = $vl['hospital_booking_date'];
                $pestcontrol_booking_date = $vl['pestcontrol_booking_date'];
                
                if($order_type == 1){
                    $order_date = $order_date; //order date
                } else if($order_type == 2){
                    $order_date = $booking_date; //booking date
                } else if($order_type == 3){
                    $order_date = $doctor_booking_date;  // check booking date
                } else if($order_type == 4){
                    $order_date = $hospital_booking_date; // check booking date
                } else if($order_type == 5){
                    $order_date = $pestcontrol_booking_date; // check booking date
                } 
                
                $settled =    $vl['settled'];
                
                if($owner_user_id == $user_id){
                    $debit = $row_debit;
                    $credit = $row_credit;
                    // $balance = $row_balance;
                } else {
                    $credit = $row_debit;
                    $debit = $row_credit;
                    // $balance = $row_balance;
                }
                $balance = $credit - $debit;
                if($settled == 1){
                    $settled_count++;
                }
        
                
                
                if($old_invoive_no == $invoive_no && $invoive_no != ""){
                    
                    
                    $detail['user_comment'] = $user_comments; 
                    $detail['mw_comment'] = $mw_comments; 
                    $detail['vendor_comment'] = $vendor_comments; 
                    $detail['credit'] = $credit; 
                    $detail['debit'] = $debit; 
                    $detail['balance'] = strval($balance); 
                    $detail['txn_no'] = $invoive_no;
                    $detail['txn_date'] = $date;
                    $txn_count++;
                     $details[] = $detail;
                } else {
                    
                    if(sizeof($details) > 0 ){
                        
                        if($txn_count == $settled_count){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                        $bs['total_debit'] = strval($total_debit); 
                        $bs['total_credit'] = strval($total_credit); 
                        
                         $total_balance = $total_credit - $total_debit;
                         $bs['total_balance'] = strval($total_balance); 
                        $bs['settled'] = $is_settled;
                     //   $details[] = $detail;
                        
                             
                        usort($details, 'txn_date');
                        
                        $bs['details'][] = $details;
                        // print_r($bs); die();
                        $balance_sheet[] = $bs;
                        $bs = $details = array();
                        $settled_count = $txn_count = $is_settled = $total_credit = $total_debit = $total_balance = 0;
                    }
                        
                        $txn_count++;
                        $bs['customer_name'] = $customer_name;
                        $bs['invoice_no'] = $invoive_no;
                        
                        $bs['order_date'] = $order_date;
                        
                        $detail['user_comment'] = $user_comments; 
                        $detail['mw_comment'] = $mw_comments; 
                        $detail['vendor_comment'] = $vendor_comments; 
                        $detail['credit'] = $credit; 
                        $detail['debit'] = $debit; 
                        $detail['balance'] = strval($balance); 
                        $detail['txn_no'] = $invoive_no;
                        $detail['txn_date'] = $date;
                        
                        $details[] = $detail;
                }
                
                $total_credit = $total_credit + $credit;
                $total_debit = $total_debit + $debit;
                $total_balance = $total_balance + $balance;
                
                $old_invoive_no = $invoive_no;
                $i++;
                
            }
          
            if(sizeof($details) > 0  ){
                        
                if($txn_count == $settled_count){
                    $is_settled = 1;
                } else {
                    $is_settled = 0;
                }
                
          
                $bs['total_debit'] = strval($total_debit); 
                $bs['total_credit'] = strval($total_credit); 
                $total_balance = $total_credit - $total_debit;
                $bs['total_balance'] = strval($total_balance); 
                $bs['settled'] = $is_settled;
                
                  
                usort($details, 'txn_date');
                
                
                $bs['details'][] = $details;
                $balance_sheet[] = $bs;
                
            }
            
            $data['ledger_balance'] = $ledger_balance;
            $data['balance_sheet'] = $balance_sheet;
            $final_data['status'] = 1;
            $final_data['data'] = $data;
            
        } else {
            $final_data['status'] = 0;
        }
    
        return $final_data;
    }
    
    
      public function get_all_ledger(){
       $details = $data = array();
        $txn_count = $settled_count = $is_settled = $total_credit = $total_debit = $total_balance = 0;
        $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` order by id DESC")->row_array();
        if(sizeof($ledger_balance_row) > 0){
            $ledger_balance = $ledger_balance_row['ledger_balance'];
        } else {
            $ledger_balance = 0;
        } 
        
        function txn_date($a, $b)
        {
            $t1 = strtotime($a['txn_date']);
            $t2 = strtotime($b['txn_date']);
            return $t1 - $t2;
        } 
    //   have to check booking_date and appointment_date - pending
        $sql_query = "SELECT u.name as customer_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , uvl.ledger_id,  uvl.user_id,  uvl.ledger_owner_type,  uvl.invoice_no,  uvl.transaction_id,  uvl.listing_id,  uvl.listing_id_type,  uvl.credit,  uvl.debit,  uvl.balance,  uvl.payment_method,  uvl.user_comments,  uvl.mw_comments,  uvl.vendor_comments,  uvl.verified,  uvl.trans_status,  uvl.order_type,  uvl.created_at,  uvl.created_by,  uvl.modified_at,  uvl.modified_by,  uvl.settled FROM `user_vendor_ledger` as uvl left join user_order as uo on (uo.invoice_no = uvl.invoice_no AND uvl.order_type = 1) left join booking_master as bm on (uvl.invoice_no = bm.booking_id AND uvl.order_type = 2) left join doctor_booking_master as dbm on (uvl.invoice_no = dbm.booking_id AND uvl.order_type = 3) left join hospital_booking_master as hbm on (uvl.invoice_no = hbm.booking_id AND uvl.order_type = 4) left join pestcontrol_booking_master as pbm on (uvl.invoice_no = pbm.booking_id AND uvl.order_type = 5) left join users as u on (uvl.user_id = u.id)  
        UNION 
        SELECT u.name as customer_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , vvl.ledger_id,  vvl.user_id,  vvl.ledger_owner_type,  vvl.invoice_no,  vvl.transaction_id,  vvl.listing_id,  vvl.listing_id_type,  vvl.credit,  vvl.debit,  vvl.balance,  vvl.payment_method,  vvl.user_comments,  vvl.mw_comments,  vvl.vendor_comments,  vvl.verified,  vvl.trans_status,  vvl.order_type,  vvl.created_at,  vvl.created_by,  vvl.modified_at,  vvl.modified_by,  vvl.settled FROM `vendor_vendor_ledger` as vvl left join user_order as uo on (uo.invoice_no = vvl.invoice_no AND vvl.order_type = 1) left join booking_master as bm on (vvl.invoice_no = bm.booking_id AND vvl.order_type = 2) left join doctor_booking_master as dbm on (vvl.invoice_no = dbm.booking_id AND vvl.order_type = 3) left join hospital_booking_master as hbm on (vvl.invoice_no = hbm.booking_id AND vvl.order_type = 4) left join pestcontrol_booking_master as pbm on (vvl.invoice_no = pbm.booking_id AND vvl.order_type = 5) left join users as u on (vvl.user_id = u.id) 
       ORDER BY invoice_no  DESC ";
      /*  UNION 
        SELECT u.name as customer_name, uol.created_at as booking_date, null as doctor_booking_date, null as hospital_booking_date, null as pestcontrol_booking_date, uol.created_at as order_date , uol.ledger_id, uol.user_id, uol.ledger_owner_type, uol.invoice_no, uol.transaction_id, null as listing_id, null as listing_id_type, uol.credit, uol.debit, uol.balance, uol.payment_method, uol.user_comments, uol.mw_comments, uol.vendor_comments, uol.verified, uol.trans_status, null  as order_type, uol.created_at, uol.created_by, uol.modified_at, uol.modified_by, uol.settled FROM user_own_ledger as uol left join users as u on (uol.user_id = u.id) 
      */  
        // echo $sql_query; die();
      
        $user_vendor_ledger = $this->db->query($sql_query)->result_array();
        // print_r($user_vendor_ledger);
    //   die();
        $i = $old_invoive_no = 0;
        if(sizeof($user_vendor_ledger) > 0){
            // print_r($user_vendor_ledger); die();
            foreach($user_vendor_ledger as $vl){
               
                $invoive_no = $vl['invoice_no'];
                $customer_name = $vl['customer_name'];
                $row_debit =    $vl['debit'];
                $row_credit = $vl['credit'];
                $row_balance = $vl['balance'];
                $date = $vl['created_at'];
                $owner_user_id =    $vl['user_id'];
                $ledger_owner_type =  $vl['ledger_owner_type'];
                $listing_id =    $vl['listing_id'] ;
                $listing_id_type =    $vl['listing_id_type'];
                $payment_method =    $vl['payment_method'];
                $user_comments =    $vl['user_comments'];
                $mw_comments =    $vl['mw_comments'];
                $vendor_comments =    $vl['vendor_comments'];
                $verified =    $vl['verified'];
                $trans_status =    $vl['trans_status'];
                $order_type =    $vl['order_type'];
                $order_date =    $vl['order_date'];
                $booking_date = $vl['booking_date'];
                $doctor_booking_date = $vl['doctor_booking_date'];
                $hospital_booking_date = $vl['hospital_booking_date'];
                $pestcontrol_booking_date = $vl['pestcontrol_booking_date'];
                
                if($order_type == 1){
                    $order_date = $order_date; //order date
                } else if($order_type == 2){
                    $order_date = $booking_date; //booking date
                } else if($order_type == 3){
                    $order_date = $doctor_booking_date;  // check booking date
                } else if($order_type == 4){
                    $order_date = $hospital_booking_date; // check booking date
                } else if($order_type == 5){
                    $order_date = $pestcontrol_booking_date; // check booking date
                } 
                
                $settled =    $vl['settled'];
                
             /*   if($owner_user_id == $user_id){
                    $debit = $row_debit;
                    $credit = $row_credit;
                    // $balance = $row_balance;
                } else {*/
                    $credit = $row_debit;
                    $debit = $row_credit;
                    // $balance = $row_balance;
               // }
                $balance = $credit - $debit;
                if($settled == 1){
                    $settled_count++;
                }
        
                
                
                if($old_invoive_no == $invoive_no && $invoive_no != ""){
                    
                    
                    $detail['user_comment'] = $user_comments; 
                    $detail['mw_comment'] = $mw_comments; 
                    $detail['vendor_comment'] = $vendor_comments; 
                    $detail['credit'] = $credit; 
                    $detail['debit'] = $debit; 
                    $detail['balance'] = strval($balance); 
                    $detail['txn_no'] = $invoive_no;
                    $detail['txn_date'] = $date;
                    $txn_count++;
                     $details[] = $detail;
                } else {
                    
                    if(sizeof($details) > 0 ){
                        
                        if($txn_count == $settled_count){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                        $bs['total_debit'] = strval($total_debit); 
                        $bs['total_credit'] = strval($total_credit); 
                        
                         $total_balance = $total_credit - $total_debit;
                         $bs['total_balance'] = strval($total_balance); 
                        $bs['settled'] = $is_settled;
                     //   $details[] = $detail;
                        
                             
                        usort($details, 'txn_date');
                        
                        $bs['details'] = $details;
                        // print_r($bs); die();
                        $balance_sheet[] = $bs;
                        $bs = $details = array();
                        $settled_count = $txn_count = $is_settled = $total_credit = $total_debit = $total_balance = 0;
                    }
                        
                        $txn_count++;
                        $bs['customer_name'] = $customer_name;
                        $bs['invoice_no'] = $invoive_no;
                        
                        $bs['order_date'] = $order_date;
                        
                        $detail['user_comment'] = $user_comments; 
                        $detail['mw_comment'] = $mw_comments; 
                        $detail['vendor_comment'] = $vendor_comments; 
                        $detail['credit'] = $credit; 
                        $detail['debit'] = $debit; 
                        $detail['balance'] = strval($balance); 
                        $detail['txn_no'] = $invoive_no;
                        $detail['txn_date'] = $date;
                        
                        $details[] = $detail;
                }
                
                $total_credit = $total_credit + $credit;
                $total_debit = $total_debit + $debit;
                $total_balance = $total_balance + $balance;
                
                $old_invoive_no = $invoive_no;
                $i++;
                
            }
          
            if(sizeof($details) > 0  ){
                        
                if($txn_count == $settled_count){
                    $is_settled = 1;
                } else {
                    $is_settled = 0;
                }
                
          
                $bs['total_debit'] = strval($total_debit); 
                $bs['total_credit'] = strval($total_credit); 
                $total_balance = $total_credit - $total_debit;
                $bs['total_balance'] = strval($total_balance); 
                $bs['settled'] = $is_settled;
                
                  
                usort($details, 'txn_date');
                
                
                $bs['details'] = $details;
                $balance_sheet[] = $bs;
                
            }
            
            $data['ledger_balance'] = $ledger_balance;
            $data['balance_sheet'] = $balance_sheet;
            $final_data['status'] = 1;
            $final_data['data'] = $data;
            
        } else {
            $final_data['status'] = 0;
        }
    
        return $final_data;
    }
    
    
    
// $user_id : user_id ;  $invoice_no :  invoice_no or booking id ; $ledger_owner_type : user id type type ;  $listing_id : listing id ;  $listing_id_type : listing id vendor type ;  $credit : credit amount ;  $debit : debit amount;  $payment_method : payment_method ; $user_comments : user comments ;  $mw_comments : mw comments ;  $vendor_comments : vendor comments  ; $order_type : where 1 : service ; 2 : appointment ; 3 : doctor booking master ; 4 : hospital booking master ; 5 : pestcontrol booking master ,  transaction_of where 1: package; 2: order amount; 3: delivery charges; 4: ledger balance; 5 : points
    public function create_ledger($user_id, $invoice_no, $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status)
    {
        $payment_method_details = array();
        $payment_method_name = "";
        // these data authentications are in both controller as well as in module because if we are calling this module from another module it should send error msg 
        if($user_id == "" || $invoice_no == "" ||   ($credit  == "" && $debit  == "" )  ||  $order_type == "" || $order_type > 5 || $order_type < 1 || $transaction_of == "")
        {
            $data['status'] = 4 ; //Something is missing
            $data['message'] = "Something is missing" ;
        } else {
            if($listing_id  == 0 && $listing_id_type == 0 ){
                $user_ledger = 0; // ledger against MW
            } else if($listing_id_type == 0 || $ledger_owner_type == 0 ){
                $user_ledger = 1;
            } else {
                $user_ledger = 0;
            }
            $created_by = $user_id;
            $modified_by = $user_id;
            $this->load->model('PartnermnoModel');
            $data = array();
            $inserted = $data['status'] = 0;
            $data['data'] = array();
            $balance = $credit - $debit;
            $order_info = "SELECT uo.order_id as id from user_order as uo where (uo.order_id = '$invoice_no' OR uo.invoice_no = '$invoice_no') AND $order_type = 1  UNION SELECT `id` FROM `booking_master` WHERE `booking_id` = '$invoice_no' AND $order_type = 2  UNION SELECT `id` FROM `doctor_booking_master` WHERE `booking_id` = '$invoice_no' AND $order_type = 3 UNION SELECT `id` FROM `hospital_booking_master` WHERE `booking_id` = '$invoice_no'  AND $order_type = 4 UNION SELECT `id` FROM `pestcontrol_booking_master` WHERE `booking_id` = '$invoice_no' AND $order_type = 5 "; 
            $get_order_info = $this->db->query($order_info)->row_array();
            if(sizeof($get_order_info) > 0){
                if($payment_method != ""){
                    $payment_method_details = $this->db->query("SELECT * FROM `payment_method` WHERE (`id` = '$payment_method' && `parent_id` != 0) || (`id` = '$payment_method' && `parent_id` = 0)")->row_array();
                    $payment_method_name = $this->PartnermnoModel->get_payment_method($payment_method);
                }
                if(($transaction_of == 2 && $payment_method_name != "" ) || $transaction_of != 2  ){
                    if($user_ledger == 1){
                        $this->db->query("INSERT INTO `user_vendor_ledger`( `user_id`, `invoice_no`, `ledger_owner_type`,  `listing_id`,`listing_id_type`, `credit`, `debit`, `balance`,  `payment_method`, `user_comments`, `mw_comments`, `vendor_comments`, `order_type`, `transaction_of`, `created_by`, `modified_by`, `transaction_id` ,  `trans_status`) VALUES ('$user_id', '$invoice_no', '$ledger_owner_type', '$listing_id', '$listing_id_type', '$credit', '$debit','$balance' , '$payment_method', '$user_comments', '$mw_comments', '$vendor_comments', '$order_type','$transaction_of' ,'$created_by' ,'$modified_by' , '$transaction_id' , '$trans_status')");      
                        $type = 'user';
                    } else {
                        $this->db->query("INSERT INTO `vendor_vendor_ledger`( `user_id`, `invoice_no`, `ledger_owner_type`,  `listing_id`,`listing_id_type`, `credit`, `debit`, `balance`,  `payment_method`, `user_comments`, `mw_comments`, `vendor_comments`, `order_type`, `transaction_of`, `created_by`, `modified_by`, `transaction_id` ,  `trans_status`) VALUES ('$user_id', '$invoice_no', '$ledger_owner_type', '$listing_id', '$listing_id_type', '$credit', '$debit','$balance' , '$payment_method', '$user_comments', '$mw_comments', '$vendor_comments', '$order_type','$transaction_of' ,'$created_by' ,'$modified_by' ,  '$transaction_id','$trans_status')");
                        $type = 'vendor';
                    }
                    $inserted = $this->db->insert_id();
                    $data['status'] = $inserted > 0 ? 1 : 0; 
                    $data['message'] = $inserted > 0 ? "success" : "failed" ;
                    $data['ledger_id'] = $inserted; 
                    $data['type'] = $type ;
                  
                } else {
                    $data['status'] = 3; // no payment method found in payment_method table
                    $data['message'] = "No payment method found " ;
                }
            } else {
                $data['status'] = 2; // no order found
                $data['message'] = "No order found" ;
            }
        }
        return $data;
    }
    
    // pharmacy_ledger_options
    
    public function pharmacy_ledger_options($user_id){
        
    }
    
    
    // create_user_ledger
    public function create_user_ledger($user_id, $ledger_owner_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments){
        $insert_id = 0;
        $data = array();
        $balance = $credit - $debit;
        $created_by = $modified_by = $user_id;
        
        $this->db->query("INSERT INTO `user_own_ledger`(`user_id` , `ledger_owner_type` , `credit` , `debit` , `balance` , `payment_method` , `user_comments` , `mw_comments` , `vendor_comments` , `created_by` , `modified_by`) VALUES ('$user_id' , '$ledger_owner_type' , '$credit' , '$debit' , '$balance' , '$payment_method' , '$user_comments' , '$mw_comments' , '$vendor_comments' , '$created_by' , '$modified_by')");
         $insert_id = $this->db->insert_id();
         $data['ledger_id'] = $insert_id;
         return $data;
    }
    
    // update_user_ledger
    public function update_user_ledger($user_id , $ledger_id, $invoice_no, $transaction_id , $trans_status){
        $status = 0;
        $get_ledger = $this->db->query("SELECT * from user_own_ledger where `ledger_id`= '$ledger_id'")->row_array();
        if(sizeof($get_ledger) > 0){
            $update_user_ledger = $this->db->query("UPDATE `user_own_ledger` SET `invoice_no`= '$invoice_no',`transaction_id`= '$transaction_id',`trans_status`= '$trans_status'  WHERE `ledger_id`= '$ledger_id'")   ;
            $status = 1;
        } else {
            $status = 2;
        }
       $data['status'] = $status;
       return $data;
    }
    
    // get_payment_options
    public function get_payment_methods($user_id, $vendor_id){
        $all_payment_methods = $apm = $sub_type_array = $all_sub_types = $data = array();
        $payment_methods  = $this->db->query("SELECT id, payment_method, parent_id, icon FROM `payment_method`  WHERE status = '1' ORDER BY `parent_id` ASC ")->result_array();
        if(sizeof($payment_methods) > 0){
            foreach($payment_methods as $pm){
                $id = $pm['id'];
                $payment_method = $pm['payment_method']; 
                $parent_id = $pm['parent_id']; 
                $icon = $pm['icon']; 
                
                $all_sub_types = array();
               
                if($parent_id > 0){
                    $sub_type_array = array_keys(array_column($payment_methods, 'parent_id'), $parent_id);
                    foreach($sub_type_array as $sta){
                        $all_sub_types[] = $payment_methods[$sta]; 
                    }
                    $parent_type_array_id = array_keys(array_column($all_payment_methods, 'id'), $parent_id);
                    if(sizeof($parent_type_array_id) > 0){
                        $parrent_id_array = $parent_type_array_id[0];
                        $all_payment_methods[$parrent_id_array]['payment_sub_types'] = $all_sub_types;
                    }
                }
                
                if($parent_id == 0 ){
                    $apm['id'] = $id;
                    $apm['payment_method'] = $payment_method;
                    $apm['parent_id'] = $parent_id;
                    $apm['icon'] = $icon;
                    $apm['payment_sub_types'] = $all_sub_types;
                    
                    $all_payment_methods[] = $apm;
                 
                }
                
                
            }
            
            
            $data['status'] = 1; // data found
            $data['data'] = $all_payment_methods;
            
        } else {
            $data['status'] = 2; // no data found
        }
        return $data;
    }
    
    // settle ledger
    
    public function settle_ledger($user_id, $listing_type,$invoice_no){
        $data = array();
        $status = 0;
        $message = "";
        $total_credit = $total_debit = 0;
        
        if ($user_id != "" || $listing_type >= 0 || $invoice_no != "") {
            $whereInUserVendor = "uvl.invoice_no = '$invoice_no' AND ((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_type')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_type')) AND ((uvl.trans_status = 1 AND uvl.transaction_of = 2) OR (uvl.transaction_of != 2))";
            
            $whereInVendorVendor = "vvl.invoice_no = '$invoice_no' AND ((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_type')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_type')) AND ((vvl.trans_status = 1 AND vvl.transaction_of = 2) OR (vvl.transaction_of != 2))";
            
            $sql = "SELECT uvl.ledger_id , uvl.user_id , uvl.ledger_owner_type , uvl.invoice_no , uvl.transaction_id , uvl.listing_id , uvl.listing_id_type , uvl.credit , uvl.debit , uvl.balance , uvl.payment_method , uvl.transaction_of , uvl.trans_status , uvl.order_type FROM user_vendor_ledger as uvl WHERE $whereInUserVendor
            UNION
            SELECT vvl.ledger_id , vvl.user_id , vvl.ledger_owner_type , vvl.invoice_no , vvl.transaction_id , vvl.listing_id , vvl.listing_id_type , vvl.credit , vvl.debit , vvl.balance , vvl.payment_method , vvl.transaction_of , vvl.trans_status , vvl.order_type FROM vendor_vendor_ledger as vvl WHERE $whereInVendorVendor";
            // echo $sql; die();
            $ledger_entries = $this->db->query($sql)->result_array();
            foreach($ledger_entries as $le){
                $ledger_owner_id = $le['user_id'];
                if($ledger_owner_id == $user_id){
                    $credit = $le['credit'];
                    $debit = $le['debit'];
                } else {
                    $credit = $le['debit'];
                    $debit = $le['credit'];
                }
                
                $total_credit = $total_credit + $credit;
                $total_debit = $total_debit + $debit;
            }
        //     echo $total_credit ." : total_credit<br>";
        //     echo $total_debit ." : total_debit<br>";
        //   print_r($ledger_entries);
        //   die();
            if($total_credit > 0 && $total_debit > 0 && $total_credit == $total_debit){
                // pending - points entry
                $whereUpdateInUserVendor = "uvl.invoice_no = '$invoice_no' AND ((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_type')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_type'))";
                
                $updateUserVendor = $this->db->query("UPDATE user_vendor_ledger AS uvl SET `settled` = 1 ,  `modified_by` = '$user_id' WHERE $whereUpdateInUserVendor ");
                
                $whereUpdateInVendorVendor = "vvl.invoice_no = '$invoice_no' AND ((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_type')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_type'))";
                
                $updateVendorVendor = "UPDATE vendor_vendor_ledger as vvl SET `settled` = 1 ,  `modified_by` = '$user_id' WHERE $whereUpdateInVendorVendor ";
               
               
             //  add points PENDING qwerty - have to discuss with vaibhav
                  
                $status = 1; // settled
                $message = "settled";
            } else {
                $status = 2; // not settled
                $message = "not settled";
                
            }
            
        } else {
            $status = 3; // please
            $message = "send user_id, listing_type, invoice_no";
        }
        $data['status'] = $status;
        $data['message'] = $message;
     
        return $data ; 
    }
    
    public function update_ledger($user_id , $ledger_id, $invoice_no, $transaction_id , $trans_status,$type){
        $status = 0;
        if($type == 'user'){
            $get_ledger = $this->db->query("SELECT * from user_vendor_ledger where `ledger_id`= '$ledger_id' AND `invoice_no` = '$invoice_no' ")->row_array();
            if(sizeof($get_ledger) > 0){
                $update_user_ledger = $this->db->query("UPDATE `user_vendor_ledger` SET `transaction_id`= '$transaction_id',`trans_status`= '$trans_status'  WHERE `ledger_id`= '$ledger_id'")   ;
                $status = 1; // success
            } else {
                $status = 2; // No ledger found
            }
        } else if($type == 'vendor'){ 
            $get_ledger = $this->db->query("SELECT * from vendor_vendor_ledger where `ledger_id`= '$ledger_id' AND `invoice_no` = '$invoice_no' ")->row_array();
            if(sizeof($get_ledger) > 0){
                $update_vendor_ledger = $this->db->query("UPDATE `vendor_vendor_ledger` SET `transaction_id`= '$transaction_id',`trans_status`= '$trans_status'  WHERE `ledger_id`= '$ledger_id'")   ;
                $status = 1; // success
            } else {
                $status = 2; // No ledger found
            }
        } else {
            $status = 3; // no type found
        }
        
        
       $data['status'] = $status;
       return $data;
    }
    
}

?>