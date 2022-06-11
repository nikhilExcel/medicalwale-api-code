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
    
    
    public function get_ledger_old($user_id,$listing_id,$search,$page_no,$per_page,$invoice_no){
        
        $details = $data = array();
        $txn_count = $settled_count = $is_settled = $total_credit = $total_debit = $total_balance = 0;
        $total_balance_amt = $total_debit_amt = $total_credit_amt = 0;
        if($user_id > 0){
            $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` WHERE `user_id` = '$user_id' order by id DESC")->row_array();    
        } else {
            $ledger_balance_row = array();
        }
        
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
     /*     UNION 
        SELECT u.name as customer_name, uol.created_at as booking_date, null as doctor_booking_date, null as hospital_booking_date, null as pestcontrol_booking_date, uol.created_at as order_date , uol.ledger_id, uol.user_id, uol.ledger_owner_type, uol.invoice_no, uol.transaction_id, null as listing_id, null as listing_id_type, uol.credit, uol.debit, uol.balance, uol.payment_method, uol.user_comments, uol.mw_comments, uol.vendor_comments, uol.verified, uol.trans_status, null  as order_type, uol.created_at, uol.created_by, uol.modified_at, uol.modified_by, uol.settled FROM user_own_ledger as uol left join users as u on (uol.user_id = u.id)  WHERE uol.user_id = '$user_id' AND uol.ledger_owner_type = '$listing_id' 
     */
    //   have to check booking_date and appointment_date - pending
        $whereConditionUser = $whereConditionVendor = 0;
        if($user_id > 0 && $invoice_no > 0){
            $whereConditionUser = "(((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_id')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_id'))  AND uvl.invoice_no = '$invoice_no')"   ; 
            $whereConditionVendor = "(((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_id')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_id'))  AND vvl.invoice_no = '$invoice_no')";
            
        } else  if($user_id > 0 && $invoice_no == ""){
            $whereConditionUser = "((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_id')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_id'))"   ; 
            $whereConditionVendor = " ((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_id')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_id'))";
            
        } else if($user_id == 0 && ($invoice_no > 0 || $invoice_no!= "")){
            $whereConditionUser = "uvl.invoice_no = '$invoice_no'"   ; 
            $whereConditionVendor = "vvl.invoice_no = '$invoice_no'";
            
        }  
        
    
        if($search != ""){
            $searchWhereUser = "AND (uvl.user_comments like '%$search%' OR uvl.mw_comments Like '%$search%'OR  uvl.vendor_comments like '%$search%' OR  uo.name like '%$search%'  OR uo.listing_name like '%$search%' OR uvl.invoice_no  like '%$search%' OR uvl.transaction_id like '%$search%') ";
            $searchWhereVendor = "AND (vvl.user_comments like '%$search%' OR vvl.mw_comments Like '%$search%'OR  vvl.vendor_comments like '%$search%' OR  uo.name like '%$search%' OR uo.listing_name like '%$search%' OR vvl.invoice_no  like '%$search%' OR vvl.transaction_id like '%$search%') ";
        } else {
            $searchWhereUser = $searchWhereVendor = "";
        }
        $sql_query = "SELECT duph.created_at as diet_plan_created_date , u.name as user_name ,jup.date as bhc_issued_date , uvl.transaction_date,uvl.transaction_of,uo.name as customer_name,uo.listing_name as vendor_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , uvl.ledger_id,  uvl.user_id,  uvl.ledger_owner_type,  uvl.invoice_no,  uvl.transaction_id,  uvl.listing_id,  uvl.listing_id_type,  uvl.credit,  uvl.debit,  uvl.balance,  uvl.payment_method,  uvl.user_comments,  uvl.mw_comments,  uvl.vendor_comments,  uvl.verified,  uvl.trans_status,  uvl.order_type,  uvl.created_at,  uvl.created_by,  uvl.modified_at,  uvl.modified_by,  uvl.settled FROM `user_vendor_ledger` as uvl left join user_order as uo on ((uo.invoice_no = uvl.invoice_no OR uo.order_id = uvl.invoice_no) AND uvl.order_type = 1) left join booking_master as bm on (uvl.invoice_no = bm.booking_id AND uvl.order_type = 2) left join doctor_booking_master as dbm on (uvl.invoice_no = dbm.booking_id AND uvl.order_type = 3) left join hospital_booking_master as hbm on (uvl.invoice_no = hbm.booking_id AND uvl.order_type = 4) left join pestcontrol_booking_master as pbm on (uvl.invoice_no = pbm.booking_id AND uvl.order_type = 5) left join users as u on (uvl.user_id = u.id)  left join user_privilage_card as jup on (jup.id = uvl.invoice_no) left join diet_user_package_history as duph on (duph.booking_id = uvl.invoice_no AND uvl.order_type = 8) WHERE $whereConditionUser $searchWhereUser

        UNION 
        SELECT duph.created_at as diet_plan_created_date , u.name as user_name ,jup.date as bhc_issued_date, vvl.transaction_date,vvl.transaction_of,uo.name as customer_name,uo.listing_name as vendor_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , vvl.ledger_id,  vvl.user_id,  vvl.ledger_owner_type,  vvl.invoice_no,  vvl.transaction_id,  vvl.listing_id,  vvl.listing_id_type,  vvl.credit,  vvl.debit,  vvl.balance,  vvl.payment_method,  vvl.user_comments,  vvl.mw_comments,  vvl.vendor_comments,  vvl.verified,  vvl.trans_status,  vvl.order_type,  vvl.created_at,  vvl.created_by,  vvl.modified_at,  vvl.modified_by,  vvl.settled FROM `vendor_vendor_ledger` as vvl left join user_order as uo on ( (uo.invoice_no = vvl.invoice_no OR uo.order_id = vvl.invoice_no)  AND vvl.order_type = 1) left join booking_master as bm on (vvl.invoice_no = bm.booking_id AND vvl.order_type = 2) left join doctor_booking_master as dbm on (vvl.invoice_no = dbm.booking_id AND vvl.order_type = 3) left join hospital_booking_master as hbm on (vvl.invoice_no = hbm.booking_id AND vvl.order_type = 4) left join pestcontrol_booking_master as pbm on (vvl.invoice_no = pbm.booking_id AND vvl.order_type = 5) left join users as u on (vvl.user_id = u.id)  left join user_privilage_card as jup on (jup.id = vvl.invoice_no) left join diet_user_package_history as duph on (duph.booking_id = vvl.invoice_no AND vvl.order_type = 8) WHERE $whereConditionVendor $searchWhereVendor
        
        ORDER BY invoice_no  DESC ";
        // echo $sql_query; die();
      
        $user_vendor_ledger = $this->db->query($sql_query)->result_array();
        // print_r($user_vendor_ledger);
    //   die();
        $i = $old_invoive_no = 0;
        if(sizeof($user_vendor_ledger) > 0 && $page_no < 2){
            // print_r($user_vendor_ledger); die();
            foreach($user_vendor_ledger as $vl){
                $user_comments = $mw_comments = $vendor_comments = '';
                // print_r($vl); die();
                if($user_id == 0){
                    $user_id = $vl['user_id'];
                }
                
               $bhc_date = $vl['bhc_issued_date'];
               $transaction_of = $vl['transaction_of'];
                $invoive_no = $vl['invoice_no'];
                $customer_name = $vl['customer_name'];
                $vendor_name = $vl['vendor_name'];
                $row_debit =    $vl['debit'];
                $row_credit = $vl['credit'];
                $row_balance = $vl['balance'];
                $date = $vl['created_at'];
                $transaction_date = $vl['transaction_date'];
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
                $diet_plan_created_date = $vl['diet_plan_created_date'];
                
                if($trans_status == 3){
                    $mw_comments = "Payment cancelled";
                }
                
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
                } else if($order_type == 6){
                    $order_date = $bhc_date; // check BHC
                    $customer_name = $vl['user_name'];
                    $vendor_name = 'Medicalwale';
                }  else if($order_type == 8){
                    $order_date = $diet_plan_created_date; // record created date
                    $customer_name = $vl['user_name'];
                    $vendor_name = 'Missbelly';
                } 
                
                $settled =    $vl['settled'];
                
                if($owner_user_id == $user_id){
                    $debit = $row_debit;
                    $credit = $row_credit;
                    $listing_id = $listing_id;
                    $listing_id_type = $listing_id_type;
                    // $balance = $row_balance;
                } else {
                    $credit = $row_debit;
                    $debit = $row_credit;
                    $listing_id = $owner_user_id;
                    $listing_id_type = $ledger_owner_type;
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
                    $detail['txn_date'] = $transaction_date;
                    $detail['listing_id'] = $listing_id;
                    $detail['listing_id_type'] = $listing_id_type;
                    

                    $txn_count++;
                    $details[] = $detail;
                } else {
                    
                    if(sizeof($details) > 0 ){
                        
                        /*if($total_debit == $total_credit){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }*/
                        if($total_debit_amt == $total_credit_amt){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                       /* $bs['total_debit'] = strval($total_debit); 
                        $bs['total_credit'] = strval($total_credit); */
                        $bs['total_debit'] = strval($total_debit_amt); 
                        $bs['total_credit'] = strval($total_credit_amt); 
                        

                        //  $total_balance = $total_credit - $total_debit;
                          $total_balance = $total_credit_amt - $total_debit_amt;
                         $bs['total_balance'] = strval($total_balance); 
                        $bs['settled'] = $is_settled;
                     //   $details[] = $detail;
                        
                             
                        usort($details, 'txn_date');
                        
                        $bs['details'] = $details;
                        // print_r($bs); die();
                        $balance_sheet[] = $bs;
                        $bs = $details = array();
                        $settled_count = $txn_count = $is_settled = $total_debit_amt = $total_credit_amt = $total_credit = $total_debit = $total_balance = 0;
                    }
                        
                        $txn_count++;
                        $bs['customer_name'] = $customer_name;
                        $bs['vendor_name'] = $vendor_name;
                        $bs['invoice_no'] = $invoive_no;
                        
                        $bs['order_date'] = $order_date;
                        
                        $detail['user_comment'] = $user_comments; 
                        $detail['mw_comment'] = $mw_comments; 
                        $detail['vendor_comment'] = $vendor_comments; 
                        $detail['credit'] = $credit; 
                        $detail['debit'] = $debit; 
                        $detail['balance'] = strval($balance); 
                        $detail['txn_no'] = $invoive_no;
                        $detail['txn_date'] = $transaction_date;
                        $detail['listing_id'] = $listing_id;
                        $detail['listing_id_type'] = $listing_id_type;
                        
                        $details[] = $detail;
                }
                if($trans_status != 3){
                    $total_credit = $total_credit + $credit;
                    $total_debit = $total_debit + $debit;
                    $total_balance = $total_balance + $balance;
                    
                    if($transaction_of == 1 || $transaction_of == 2 || $transaction_of == 3){ 
                        // print_r($vl); die();
                        $total_debit_amt = $total_debit_amt +  $debit;
                        $total_credit_amt = $total_credit_amt +  $credit;
                        $total_balance_amt = $total_balance_amt + $balance;
                    }
                }
               
                
                $old_invoive_no = $invoive_no;
                $i++;
                
            }
          
            if(sizeof($details) > 0  ){
                        
                /*if($txn_count == $settled_count){
                    $is_settled = 1;
                } else {
                    $is_settled = 0;
                }*/
                
                 /*if($total_debit == $total_credit){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                
          
                $bs['total_debit'] = strval($total_debit); 
                $bs['total_credit'] = strval($total_credit); 
                $total_balance = $total_credit - $total_debit;
                $bs['total_balance'] = strval($total_balance); 
                $bs['settled'] = $is_settled;*/
                
                if($total_debit_amt == $total_credit_amt){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                    
                        $bs['total_debit'] = strval($total_debit_amt); 
                        $bs['total_credit'] = strval($total_credit_amt); 
                        

                          $total_balance = $total_credit_amt - $total_debit_amt;
                         $bs['total_balance'] = strval($total_balance); 
                        $bs['settled'] = $is_settled;
                
                  
                usort($details, 'txn_date');
                
                
                $bs['details'] = $details;
                $balance_sheet[] = $bs;
                
            }
            // print_r($balance_sheet); die();
            $data['ledger_balance'] = $ledger_balance;
            $data['balance_sheet'] = $balance_sheet;
            $final_data['status'] = 1;
            $final_data['data'] = $data;
            
        } else {
            $final_data['status'] = 0;
        }
    
        return $final_data;
    }
    
    public function get_ledger($user_id,$listing_id,$search,$page_no,$per_page,$invoice_no){
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        $sql_query_count = 0;
        if($search != "" ||  $invoice_no > 0){
            $getOwnLedger = 0;
        } else {
            $getOwnLedger = 1;
        }
        $details = $data = array();
        $txn_count = $settled_count = $is_settled = $total_credit = $total_debit = $total_balance = 0;
        $total_balance_amt = $total_debit_amt = $total_credit_amt = 0;
        if($user_id > 0){
            $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` WHERE `user_id` = '$user_id' order by id DESC")->row_array();    
        } else {
            $ledger_balance_row = array();
        }
        
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
     /*     UNION 
        SELECT u.name as customer_name, uol.created_at as booking_date, null as doctor_booking_date, null as hospital_booking_date, null as pestcontrol_booking_date, uol.created_at as order_date , uol.ledger_id, uol.user_id, uol.ledger_owner_type, uol.invoice_no, uol.transaction_id, null as listing_id, null as listing_id_type, uol.credit, uol.debit, uol.balance, uol.payment_method, uol.user_comments, uol.mw_comments, uol.vendor_comments, uol.verified, uol.trans_status, null  as order_type, uol.created_at, uol.created_by, uol.modified_at, uol.modified_by, uol.settled FROM user_own_ledger as uol left join users as u on (uol.user_id = u.id)  WHERE uol.user_id = '$user_id' AND uol.ledger_owner_type = '$listing_id' 
     */
     
     /*
     SELECT null as diet_plan_created_date, null as user_name, null as bhc_issued_date, null as transaction_date, null as transaction_of, u.name as customer_name, null as vendor_name, null as booking_date, null as doctor_booking_date, null as hospital_booking_date, null as pestcontrol_booking_date, uol.created_at as order_date , uol.ledger_id, uol.user_id,uol.ledger_owner_type, uol.invoice_no, uol.transaction_id, null as listing_id, null as listing_id_type, uol.credit, uol.debit, uol.balance, uol.payment_method, uol.user_comments, uol.mw_comments, uol.vendor_comments, uol.verified, uol.trans_status, null  as order_type, uol.created_at, uol.created_by, uol.modified_at, uol.modified_by, uol.settled  FROM user_own_ledger as uol left join users as u on (uol.user_id = u.id)  WHERE uol.user_id = '$user_id' AND uol.ledger_owner_type = '$listing_id' 
     */
    //   have to check booking_date and appointment_date - pending
        $whereConditionUser = $whereConditionVendor = 0;
        if($user_id > 0 && $invoice_no > 0){
            $whereConditionUser = "(((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_id')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_id'))  AND uvl.invoice_no = '$invoice_no')"   ; 
            $whereConditionVendor = "(((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_id')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_id'))  AND vvl.invoice_no = '$invoice_no')";
            
        } else  if($user_id > 0 && $invoice_no == ""){
            $whereConditionUser = "((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_id')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_id'))"   ; 
            $whereConditionVendor = " ((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_id')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_id'))";
            
        } else if($user_id == 0 && ($invoice_no > 0 || $invoice_no!= "")){
            $whereConditionUser = "uvl.invoice_no = '$invoice_no'"   ; 
            $whereConditionVendor = "vvl.invoice_no = '$invoice_no'";
            
        }  
        
    
        if($search != ""){
            $searchWhereUser = "AND (uvl.user_comments like '%$search%' OR uvl.mw_comments Like '%$search%'OR  uvl.vendor_comments like '%$search%' OR  uo.name like '%$search%'  OR uo.listing_name like '%$search%' OR uvl.invoice_no  like '%$search%' OR uvl.transaction_id like '%$search%') ";
            $searchWhereVendor = "AND (vvl.user_comments like '%$search%' OR vvl.mw_comments Like '%$search%'OR  vvl.vendor_comments like '%$search%' OR  uo.name like '%$search%' OR uo.listing_name like '%$search%' OR vvl.invoice_no  like '%$search%' OR vvl.transaction_id like '%$search%') ";
        } else {
            $searchWhereUser = $searchWhereVendor = "";
        }
        
        $group_by_in_count = "  GROUP BY invoice_no ";
        $order_by_in_query = " ORDER BY ledger_id  DESC ";
        
        $uvl_table_joins = " left join user_order as uo on ((uo.invoice_no = uvl.invoice_no OR uo.order_id = uvl.invoice_no) AND uvl.order_type = 1) left join booking_master as bm on (uvl.invoice_no = bm.booking_id AND uvl.order_type = 2) left join doctor_booking_master as dbm on (uvl.invoice_no = dbm.booking_id AND uvl.order_type = 3) left join hospital_booking_master as hbm on (uvl.invoice_no = hbm.booking_id AND uvl.order_type = 4) left join pestcontrol_booking_master as pbm on (uvl.invoice_no = pbm.booking_id AND uvl.order_type = 5) left join users as u on (uvl.user_id = u.id)  left join user_privilage_card as jup on (jup.id = uvl.invoice_no) left join diet_user_package_history as duph on (duph.booking_id = uvl.invoice_no AND uvl.order_type = 8) left join inven_distrub_invoice as idi on (idi.invoice_no = uvl.invoice_no AND uvl.order_type = 10) ";
        $vvl_table_joins = " left join user_order as uo on ( (uo.invoice_no = vvl.invoice_no OR uo.order_id = vvl.invoice_no)  AND vvl.order_type = 1) left join booking_master as bm on (vvl.invoice_no = bm.booking_id AND vvl.order_type = 2) left join doctor_booking_master as dbm on (vvl.invoice_no = dbm.booking_id AND vvl.order_type = 3) left join hospital_booking_master as hbm on (vvl.invoice_no = hbm.booking_id AND vvl.order_type = 4) left join pestcontrol_booking_master as pbm on (vvl.invoice_no = pbm.booking_id AND vvl.order_type = 5) left join users as u on (vvl.user_id = u.id)  left join user_privilage_card as jup on (jup.id = vvl.invoice_no) left join diet_user_package_history as duph on (duph.booking_id = vvl.invoice_no AND vvl.order_type = 8) left join inven_distrub_invoice as idi on (idi.invoice_no = vvl.invoice_no AND vvl.order_type = 10) ";
        
        
        $invoice_query = "SELECT uvl.ledger_id,uvl.invoice_no FROM `user_vendor_ledger` as uvl $uvl_table_joins  WHERE $whereConditionUser $searchWhereUser  group by uvl.invoice_no

        UNION 
        SELECT vvl.ledger_id,vvl.invoice_no FROM `vendor_vendor_ledger` as vvl  $vvl_table_joins  WHERE $whereConditionVendor $searchWhereVendor group by vvl.invoice_no 
          ";
        
        $sql_query_for_invoices = $invoice_query.$order_by_in_query.$limit;

        $sql_query_count = $this->db->query($invoice_query)->num_rows();
        $input = $all_invoices = $this->db->query($sql_query_for_invoices)->result_array();
       $output_invs = "";
        $output_invs = implode(', ', array_map(
            function ($v, $k) {
                if(is_array($v)){
                    unset($v['ledger_id']);
                    return "'".implode('&'.$k.'=', $v)."'";
                }else{
                    return $k.'='.$v;
                }
            }, 
            $input, 
            array_keys($input)
        ));
        //   echo $output_invs; die();
            $whereInvsUser = ' AND uvl.invoice_no IN ('.$output_invs.') ';
            $whereInvsVendor = ' AND vvl.invoice_no IN ('.$output_invs.') ';
            
            if($getOwnLedger == 1){
                $userOwnLedger = "UNION
            SELECT null as diet_plan_created_date, null as user_name, null as bhc_issued_date, null as transaction_date, null as transaction_of, u.name as customer_name, null as vendor_name, null as booking_date, null as doctor_booking_date, null as hospital_booking_date, null as pestcontrol_booking_date, uol.created_at as order_date , uol.ledger_id, uol.user_id,uol.ledger_owner_type, uol.invoice_no, uol.transaction_id, null as listing_id, null as listing_id_type, uol.credit, uol.debit, uol.balance, uol.payment_method, uol.user_comments, uol.mw_comments, uol.vendor_comments, uol.verified, uol.trans_status, null  as order_type, uol.created_at, uol.created_by, uol.modified_at, uol.modified_by, uol.settled  FROM user_own_ledger as uol left join users as u on (uol.user_id = u.id)  WHERE uol.user_id = '$user_id' AND uol.ledger_owner_type = '$listing_id' ";   
            } else {
                $userOwnLedger = "";
            }
       
            
                    
        if($output_invs != "" ){
            $sql_query = "SELECT  uv.name as vendor_user_name, vt.vendor_name as vendor_type, duph.created_at as diet_plan_created_date , u.name as user_name ,jup.date as bhc_issued_date , uvl.transaction_date,uvl.transaction_of,uo.name as customer_name,uo.listing_name as vendor_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , uvl.ledger_id,  uvl.user_id,  uvl.ledger_owner_type,  uvl.invoice_no,  uvl.transaction_id,  uvl.listing_id,  uvl.listing_id_type,  uvl.credit,  uvl.debit,  uvl.balance,  uvl.payment_method,  uvl.user_comments,  uvl.mw_comments,  uvl.vendor_comments,  uvl.verified,  uvl.trans_status,  uvl.order_type,  uvl.created_at,  uvl.created_by,  uvl.modified_at,  uvl.modified_by,  uvl.settled, idi.name as ditributor_customer_name, idi.listing_name as ditributor_vendor_name, idi.order_date as ditributor_order_date FROM `user_vendor_ledger` as uvl $uvl_table_joins left join vendor_type as vt on (uvl.vendor_id = vt.id) left join users as uv on (uvl.listing_id = uv.id) WHERE $whereConditionUser $searchWhereUser $whereInvsUser  

            UNION 
            SELECT  uv.name as vendor_user_name,vt.vendor_name as vendor_type, duph.created_at as diet_plan_created_date , u.name as user_name ,jup.date as bhc_issued_date, vvl.transaction_date,vvl.transaction_of,uo.name as customer_name,uo.listing_name as vendor_name,bm.booking_date, dbm.booking_date as doctor_booking_date, hbm.booking_date as hospital_booking_date, pbm.appointment_date as pestcontrol_booking_date, uo.order_date , vvl.ledger_id,  vvl.user_id,  vvl.ledger_owner_type,  vvl.invoice_no,  vvl.transaction_id,  vvl.listing_id,  vvl.listing_id_type,  vvl.credit,  vvl.debit,  vvl.balance,  vvl.payment_method,  vvl.user_comments,  vvl.mw_comments,  vvl.vendor_comments,  vvl.verified,  vvl.trans_status,  vvl.order_type,  vvl.created_at,  vvl.created_by,  vvl.modified_at,  vvl.modified_by,  vvl.settled, idi.name as ditributor_customer_name,idi.listing_name as ditributor_vendor_name, idi.order_date as ditributor_order_date FROM `vendor_vendor_ledger` as vvl $vvl_table_joins left join vendor_type as vt on (vvl.vendor_id = vt.id)  left join users as uv on (vvl.listing_id = uv.id)  WHERE $whereConditionVendor $searchWhereVendor $whereInvsVendor 
            
            
            ORDER BY invoice_no  DESC ";
            
           // echo $sql_query; die();
            $user_vendor_ledger = $this->db->query($sql_query)->result_array();
        } else {
            $user_vendor_ledger = array();
        }
        
        
        // print_r($user_vendor_ledger); die();
        $i = $old_invoive_no = 0;
        if(sizeof($user_vendor_ledger) > 0 ){
            // print_r($user_vendor_ledger); die();
            foreach($user_vendor_ledger as $vl){
                $user_comments = $mw_comments = $vendor_comments = '';
                // print_r($vl); die();
                if($user_id == 0){
                    $user_id = $vl['user_id'];
                }
                
               $bhc_date = $vl['bhc_issued_date'];
               $transaction_of = $vl['transaction_of'];
                $invoive_no = $vl['invoice_no'];
                $customer_name = $vl['customer_name'];
                $vendor_name = $vl['vendor_name'];
                $row_debit =    $vl['debit'];
                $row_credit = $vl['credit'];
                $row_balance = $vl['balance'];
                $date = $vl['created_at'];
                $transaction_date = $vl['transaction_date'];
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
                $diet_plan_created_date = $vl['diet_plan_created_date'];
                $ditributor_customer_name = $vl['ditributor_customer_name'];
                $ditributor_vendor_name = $vl['ditributor_vendor_name'];
                $ditributor_order_date = $vl['ditributor_order_date'];
                
                if($trans_status == 3){
                    $mw_comments = "Payment cancelled";
                }
                
                if($order_type == 1){ 
                    $order_date = $order_date; //order date
                    $customer_name = $vl['user_name'];
                    
                    $vendor_name = $vendor_name != null && $vendor_name != "" ? $vendor_name : $vl['vendor_user_name'] ; 
                    
                } else if($order_type == 2){
                    $order_date = $booking_date; //booking date
                    $customer_name = $vl['user_name'];
                    $vendor_name = $vl['vendor_user_name'];
                } else if($order_type == 3){
                    $order_date = $doctor_booking_date;  // check booking date
                    $customer_name = $vl['user_name'];
                    $vendor_name = $vl['vendor_user_name'];
                } else if($order_type == 4){
                    $order_date = $hospital_booking_date; // check booking date
                    $customer_name = $vl['user_name'];
                    $vendor_name = $vl['vendor_user_name'];
                } else if($order_type == 5){
                    $order_date = $pestcontrol_booking_date; // check booking date
                    $customer_name = $vl['user_name'];
                    $vendor_name = $vl['vendor_user_name'];
                } else if($order_type == 6){
                    $order_date = $bhc_date; // check BHC
                    $customer_name = $vl['user_name'];
                    $vendor_name = 'Medicalwale';
                }  else if($order_type == 8){
                    $order_date = $diet_plan_created_date; // record created date
                    $customer_name = $vl['user_name'];
                    $vendor_name = 'Missbelly';
                } else if($order_type == 9){ // points conversion 
                    $order_date = $transaction_date; 
                    $customer_name = $vl['user_name'];
                    $vendor_name = 'Medicalwale';
                } else if($order_type == 10){
                    $order_date = $ditributor_order_date; 
                    $customer_name = $ditributor_customer_name;
                    $vendor_name = $ditributor_vendor_name;
                }
                
                $settled =    $vl['settled'];
                
                if($listing_id == 0){
                    $vendor_name = 'Medicalwale';
                }
                
                if($owner_user_id == $user_id){
                    $debit = $row_debit;
                    $credit = $row_credit;
                    $listing_id = $listing_id;
                    $listing_id_type = $listing_id_type;
                    // $balance = $row_balance;
                } else {
                    $credit = $row_debit;
                    $debit = $row_credit;
                    $listing_id = $owner_user_id;
                    $listing_id_type = $ledger_owner_type;
                    // $balance = $row_balance;
                }
                $balance = $credit - $debit;
                if($settled == 1){
                    $settled_count++;
                }
        
                
                
                if($old_invoive_no == $invoive_no && $invoive_no != ""){
                    
                    $bs['customer_name'] = $customer_name;
                        $bs['vendor_name'] = $vendor_name;
                        $bs['invoice_no'] = $invoive_no;
                        $bs['order_date'] = $order_date;
                    
                    $detail['user_comment'] = $user_comments; 
                    $detail['mw_comment'] = $mw_comments; 
                    $detail['vendor_comment'] = $vendor_comments; 
                    $detail['credit'] = $credit; 
                    $detail['debit'] = $debit; 
                    $detail['balance'] = strval($balance); 
                    $detail['txn_no'] = $invoive_no;
                    $detail['txn_date'] = $transaction_date;
                    $detail['listing_id'] = $listing_id;
                    $detail['listing_id_type'] = $listing_id_type;
                    

                    $txn_count++;
                    $details[] = $detail;
                } else {
                    
                    if(sizeof($details) > 0 ){
                        
                        /*if($total_debit == $total_credit){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }*/
                        if(($total_debit_amt == $total_credit_amt) || ($transaction_of == 4)){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                       /* $bs['total_debit'] = strval($total_debit); 
                        $bs['total_credit'] = strval($total_credit); */
                        $bs['total_debit'] = strval($total_debit_amt); 
                        $bs['total_credit'] = strval($total_credit_amt); 
                        

                        //  $total_balance = $total_credit - $total_debit;
                          $total_balance = $total_credit_amt - $total_debit_amt;
                          if(floor( $total_balance ) != $total_balance){
                              $total_balance  = number_format((float)$total_balance, 2, '.', '');
                          } else {
                              $total_balance = $total_balance;
                          }
                         $bs['total_balance'] = strval($total_balance); 
                        $bs['settled'] = $is_settled;
                     //   $details[] = $detail;
                        
                             
                        usort($details, 'txn_date');
                        
                        $bs['details'] = $details;
                        // print_r($bs); die();
                        $balance_sheet[] = $bs;
                        $bs = $details = array();
                        $settled_count = $txn_count = $is_settled = $total_debit_amt = $total_credit_amt = $total_credit = $total_debit = $total_balance = 0;
                    }
                        
                        $txn_count++;
                        $bs['customer_name'] = $customer_name;
                        $bs['vendor_name'] = $vendor_name;
                        $bs['invoice_no'] = $invoive_no;
                        
                        $bs['order_date'] = $order_date;
                        
                        $detail['user_comment'] = $user_comments; 
                        $detail['mw_comment'] = $mw_comments; 
                        $detail['vendor_comment'] = $vendor_comments; 
                        $detail['credit'] = $credit; 
                        $detail['debit'] = $debit; 
                        $detail['balance'] = strval($balance); 
                        $detail['txn_no'] = $invoive_no;
                        $detail['txn_date'] = $transaction_date;
                        $detail['listing_id'] = $listing_id;
                        $detail['listing_id_type'] = $listing_id_type;
                        
                        $details[] = $detail;
                }
                if($trans_status != 3){
                    $total_credit = $total_credit + $credit;
                    $total_debit = $total_debit + $debit;
                    $total_balance = $total_balance + $balance;
                    if($listing_id == 44){
                        if( $transaction_of == 2 || $transaction_of == 3 || $transaction_of == 4){ 
                            // print_r($vl); die();
                            $total_debit_amt = $total_debit_amt +  $debit;
                            $total_credit_amt = $total_credit_amt +  $credit;
                            $total_balance_amt = $total_balance_amt + $balance;
                        }
                    } else {
                        if($transaction_of == 1 || $transaction_of == 2 || $transaction_of == 3 || $transaction_of == 4){ 
                            // print_r($vl); die();
                            $total_debit_amt = $total_debit_amt +  $debit;
                            $total_credit_amt = $total_credit_amt +  $credit;
                            $total_balance_amt = $total_balance_amt + $balance;
                        }
                    }
                    
                }
               
                
                $old_invoive_no = $invoive_no;
                $i++;
                
            }
          
            if(sizeof($details) > 0  ){
                        
                /*if($txn_count == $settled_count){
                    $is_settled = 1;
                } else {
                    $is_settled = 0;
                }*/
                
                 /*if($total_debit == $total_credit){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                
          
                $bs['total_debit'] = strval($total_debit); 
                $bs['total_credit'] = strval($total_credit); 
                $total_balance = $total_credit - $total_debit;
                $bs['total_balance'] = strval($total_balance); 
                $bs['settled'] = $is_settled;*/
                
                if(($total_debit_amt == $total_credit_amt) || ($transaction_of == 4)){
                            $is_settled = 1;
                        } else {
                            $is_settled = 0;
                        }
                        $bs['customer_name'] = $customer_name;
                        $bs['vendor_name'] = $vendor_name;
                        $bs['invoice_no'] = $invoive_no;
                        $bs['order_date'] = $order_date;
                        
                        
                        $bs['total_debit'] = strval($total_debit_amt); 
                        $bs['total_credit'] = strval($total_credit_amt); 
                        

                          $total_balance = $total_credit_amt - $total_debit_amt;
                          if(floor( $total_balance ) != $total_balance){
                              $total_balance  = number_format((float)$total_balance, 2, '.', '');
                          } else {
                              $total_balance = $total_balance;
                          }
                         $bs['total_balance'] = strval($total_balance); 
                        $bs['settled'] = $is_settled;
                
                  
                usort($details, 'txn_date');
                
                
                $bs['details'] = $details;
                $balance_sheet[] = $bs;
                
            }
            
            // 
            // print_r($balance_sheet); die();
            if(sizeof($balance_sheet) > 0){
                $balance_sheet = $this->sortInvoiceArray($balance_sheet,$all_invoices);
                
            } 
            // print_r($balance_sheet); die();
            
            /*pagination*/
            /*$page_no
            $per_page
            $sql_query_count*/
            if($sql_query_count > 0){
                $last_page = ceil($sql_query_count/$per_page);
                $data_count = intval($sql_query_count);
            } else {
                $data_count = $per_page = $page_no = $last_page = 0;
            }
            
            $data['data_count'] = $data_count;;
            $data['per_page'] = intval($per_page);
            $data['current_page'] = intval($page_no);
            $data['first_page'] = 1;
            $data['last_page'] = intval($last_page);
            
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

     /*1 : service(user_order) ;
        2 : appointment(booking_master) ;
        3 : doctor booking master(doctor_booking_master) ;
        4 : hospital booking master(hospital_booking_master) ;
        5 : pestcontrol booking master(pestcontrol_booking_master);
        6: BHC ;
        8 :missbelly(diet_user_package_history);
        9: points to ledger convert;
        10 => inven_distrub_order*/
        
    public function create_ledger($user_id, $invoice_no, $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data)
    {
        if(array_key_exists('accepted_by',$array_data)){ $accepted_by = $array_data['accepted_by']; } else {$accepted_by = "";}
        if(array_key_exists('submitted_by',$array_data)){ $submitted_by = $array_data['submitted_by']; } else {$submitted_by = "";}
        
        
          date_default_timezone_set('Asia/Kolkata');
          if($transaction_date == ""){
              $transaction_date = date('Y-m-d H:i:s');
          }
          
          if($vendor_id == ""){
              $vendor_id = $listing_id_type;
          }
          
        $data = array(
                            'ledger_id' =>"",
                            'type' => ""
                        );
        $payment_method_details = array();
        $payment_method_name = "";
        // these data authentications are in both controller as well as in module because if we are calling this module from another module it should send error msg 
        
       
        if($user_id == "" || $invoice_no == "" ||   ($credit  == "" && $debit  == "" ) || ($credit  == 0 && $debit  == 0 )  ||  $order_type == "" || $order_type > 10 || $order_type < 1 || $transaction_of == "")
        {
            $data['status'] = 4 ; //Something is missing
            $data['message'] = "Something is missing" ;
        } else {
            if($ledger_owner_type == 0 && $listing_id  == 0 && $listing_id_type == 0 ){
                $user_ledger = 1; // ledger against MW of user
            } else if($ledger_owner_type > 0 && $listing_id  == 0 && $listing_id_type == 0 ){
                $user_ledger = 0; // ledger against MW of vendor
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
            
            $credit = $credit > 0 ? floatval($credit) : 0;
            $debit = $debit > 0 ? floatval($debit) : 0;
            
            $balance = $credit - $debit;
            // $order_info = "SELECT uo.order_id as id from user_order as uo where (uo.order_id = '$invoice_no' OR uo.invoice_no = '$invoice_no') AND $order_type = 1  UNION SELECT `id` FROM `booking_master` WHERE `booking_id` = '$invoice_no' AND $order_type = 2  UNION SELECT `id` FROM `doctor_booking_master` WHERE `booking_id` = '$invoice_no' AND $order_type = 3 UNION SELECT `id` FROM `hospital_booking_master` WHERE `booking_id` = '$invoice_no'  AND $order_type = 4 UNION SELECT `id` FROM `pestcontrol_booking_master` WHERE `booking_id` = '$invoice_no' AND $order_type = 5 UNION SELECT `id` FROM `user_privilage_card` WHERE `id` = '$invoice_no' AND $order_type = 6"; 
            // $get_order_info = $this->db->query($order_info)->row_array();
            /*if(sizeof($get_order_info) > 0){*/
                if($payment_method != ""){
                    $payment_method_details = $this->db->query("SELECT * FROM `payment_method` WHERE (`id` = '$payment_method' && `parent_id` != 0) || (`id` = '$payment_method' && `parent_id` = 0)")->row_array();
                    $payment_method_name = $this->PartnermnoModel->get_payment_method($payment_method);
                }
                if(($transaction_of == 2 && $payment_method_name != "" ) || $transaction_of != 2  ){
                    if($user_ledger == 1){
                        $this->db->query("INSERT INTO `user_vendor_ledger`( `user_id`, `invoice_no`, `ledger_owner_type`,  `listing_id`,`listing_id_type`, `credit`, `debit`, `balance`,  `payment_method`, `user_comments`, `mw_comments`, `vendor_comments`, `order_type`, `transaction_of`, `created_by`, `modified_by`, `transaction_id` ,  `trans_status`,`transaction_date`,`vendor_id`,`accepted_by`,`submitted_by`) VALUES ('$user_id', '$invoice_no', '$ledger_owner_type', '$listing_id', '$listing_id_type', '$credit', '$debit','$balance' , '$payment_method', '$user_comments', '$mw_comments', '$vendor_comments', '$order_type','$transaction_of' ,'$created_by' ,'$modified_by' , '$transaction_id' , '$trans_status','$transaction_date','$vendor_id','$accepted_by','$submitted_by')");      
                        $type = 'user';
                    } else {
                        $this->db->query("INSERT INTO `vendor_vendor_ledger`( `user_id`, `invoice_no`, `ledger_owner_type`,  `listing_id`,`listing_id_type`, `credit`, `debit`, `balance`,  `payment_method`, `user_comments`, `mw_comments`, `vendor_comments`, `order_type`, `transaction_of`, `created_by`, `modified_by`, `transaction_id` ,  `trans_status`,`transaction_date`,`vendor_id`,`accepted_by`,`submitted_by`) VALUES ('$user_id', '$invoice_no', '$ledger_owner_type', '$listing_id', '$listing_id_type', '$credit', '$debit','$balance' , '$payment_method', '$user_comments', '$mw_comments', '$vendor_comments', '$order_type','$transaction_of' ,'$created_by' ,'$modified_by' ,  '$transaction_id','$trans_status','$transaction_date','$vendor_id','$accepted_by','$submitted_by')");
                        $type = 'vendor';
                       
                    }
                    $inserted = $this->db->insert_id();
                    if($inserted > 0){
                        // settle user ledger
                         $res = $this->LedgerModel->settle_ledger($user_id,$ledger_owner_type, $invoice_no);
                        //  settle mno
                         $res = $this->LedgerModel->settle_ledger($listing_id,$listing_id_type, $invoice_no);
                         
                         
                        //  points entry 
                        if($debit > 0 && $transaction_of == 2 && $trans_status == 1){
                            // qweqweqwe -update pendnig
                            $user_id_points = $user_id;
                            $invoice_no_points = $invoice_no;
                            $transaction_date_points = $transaction_date;
                            $transaction_id_points = "";
                            $points_points = round($debit);
                            $comments_points = $mw_comments;
                            $status_points = "active";
                            $listing_type_points = $vendor_id;
                            $expire_at_points =  date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 365 day"));
                            $this->LedgerModel->add_points($user_id_points,  $invoice_no_points, $transaction_date_points, $transaction_id_points,  $points_points,  $comments_points,  $status_points,  $listing_type_points,  $expire_at_points);
                    
                        }
                        
                    }
                    
                    $data['status'] = $inserted > 0 ? 1 : 0; 
                    $data['message'] = $inserted > 0 ? "success" : "failed" ;
                    $data['ledger_id'] = $inserted; 
                    $data['type'] = $type ;
                  
                } else {
                    $data['status'] = 3; // no payment method found in payment_method table
                    $data['message'] = "No payment method found " ;
                }
            /*} else {
                $data['status'] = 2; // no order found
                $data['message'] = "No order found" ;
            }*/
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
         
          if($debit > 0){
            // qweqweqwe -update pendnig
            $transaction_date = date("Y-m-d H:i:s");
            $user_id_points = $user_id;
            $invoice_no_points = "";
            $transaction_date_points = $transaction_date;
            $transaction_id_points = "";
            $points_points = round($debit);
            $comments_points = $mw_comments;
            $status_points = "active";
            $listing_type_points = "";
            $expire_at_points =  date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 365 day"));
            $this->LedgerModel->add_points($user_id_points,  $invoice_no_points, $transaction_date_points, $transaction_id_points,  $points_points,  $comments_points,  $status_points,  $listing_type_points,  $expire_at_points);
        }
        
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
        //  points entry 
        if(sizeof($get_ledger) > 0 && $status == 1){
            $trans_status = $get_ledger['trans_status'];
            $debit = $get_ledger['debit'];
            if($debit > 0 && $trans_status == 1){
                $transaction_date = date('Y-m-d H:i:s');
                $user_id_points = $user_id;
                $invoice_no_points = $invoice_no;
                $transaction_date_points = $transaction_date;
                $transaction_id_points = "";
                $points_points = round($debit);
                $comments_points = '';
                $status_points = "active";
                $listing_type_points = 0;
                $expire_at_points =  date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 365 day"));
                $this->LedgerModel->add_points($user_id_points,  $invoice_no_points, $transaction_date_points, $transaction_id_points,  $points_points,  $comments_points,  $status_points,  $listing_type_points,  $expire_at_points);
            }
        }
        
        
       $data['status'] = $status;
       return $data;
    }
    
    // get_payment_options
    public function get_payment_methods($user_id, $vendor_id){
        $this->load->model('PaymentModel');
        $all_payment_methods = $apm = $sub_type_array = $all_sub_types = $data = array();
        $payment_methods  = $this->db->query("SELECT id as payment_id, payment_method, parent_id, icon,offer_text FROM `payment_method`  WHERE status = '1' AND vendor_type IN($vendor_id)  ORDER BY `sequence_no` ASC ")->result_array();
        if(sizeof($payment_methods) > 0){
            foreach($payment_methods as $pm){
                
                $id = $pm['payment_id'];
                $payment_method = $pm['payment_method']; 
                $parent_id = $pm['parent_id']; 
                $icon = $pm['icon']; 
                
                // vendor_id 35 meand no CAP for BHC
                if($vendor_id != '35' || ($vendor_id == '35' && $id != '3' && $id != '14')){    
                    $all_sub_types = array();
                   
                    if($parent_id > 0){
                        $sub_type_array = array_keys(array_column($payment_methods, 'parent_id'), $parent_id);
                        foreach($sub_type_array as $sta){
                            $all_sub_types[] = $payment_methods[$sta]; 
                        }
                        $parent_type_array_id = array_keys(array_column($all_payment_methods, 'payment_id'), $parent_id);
                        if(sizeof($parent_type_array_id) > 0){
                            $parrent_id_array = $parent_type_array_id[0];
                            $all_payment_methods[$parrent_id_array]['payment_sub_types'] = $all_sub_types;
                        }
                    }
                    
                    if($parent_id == 0 ){
                        $apm['payment_id'] = $id;
                        $apm['payment_method'] = $payment_method;
                        $apm['parent_id'] = $parent_id;
                        $apm['icon'] = $icon;
                        $apm['payment_sub_types'] = $all_sub_types;
                        
                        $all_payment_methods[] = $apm;
                     
                    }
                }
            }
            
            $ledger_details  = $this->PaymentModel->get_ledgerBal_Points($user_id);
           
            if($ledger_details['status'] == 200){
                 $user_data['ledger_details'] = $ledger_details['data'];
            } else {
                 $user_data['ledger_details'] = (object)[];
            }
           
            $user_data['payment_methods'] = $all_payment_methods;
            
            $data['status'] = 1; // data found
            $data['data'] = $user_data;
            
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
            $whereInUserVendor = "uvl.invoice_no = '$invoice_no' AND ((uvl.`user_id` = '$user_id' AND uvl.ledger_owner_type = '$listing_type')  OR (uvl.listing_id = '$user_id'  AND uvl.listing_id_type =  '$listing_type')) AND ((uvl.trans_status = 1 AND uvl.transaction_of = 2) OR (uvl.transaction_of != 2) AND uvl.trans_status != 3)";
            
            $whereInVendorVendor = "vvl.invoice_no = '$invoice_no' AND ((vvl.`user_id` = '$user_id' AND vvl.ledger_owner_type = '$listing_type')  OR (vvl.listing_id = '$user_id'  AND vvl.listing_id_type =  '$listing_type')) AND ((vvl.trans_status = 1 AND vvl.transaction_of = 2) OR (vvl.transaction_of != 2) AND vvl.trans_status != 3)";
            
            $sql = "SELECT uvl.ledger_id , uvl.user_id , uvl.ledger_owner_type , uvl.invoice_no , uvl.transaction_id , uvl.listing_id , uvl.listing_id_type , uvl.credit , uvl.debit , uvl.balance , uvl.payment_method , uvl.transaction_of , uvl.trans_status , uvl.order_type FROM user_vendor_ledger as uvl WHERE $whereInUserVendor
            UNION
            SELECT vvl.ledger_id , vvl.user_id , vvl.ledger_owner_type , vvl.invoice_no , vvl.transaction_id , vvl.listing_id , vvl.listing_id_type , vvl.credit , vvl.debit , vvl.balance , vvl.payment_method , vvl.transaction_of , vvl.trans_status , vvl.order_type FROM vendor_vendor_ledger as vvl WHERE $whereInVendorVendor";
         //  echo $sql; die();
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
   //  print_r($data); die();
        return $data ; 
    }
    
    public function update_ledger($user_id , $ledger_id, $invoice_no, $transaction_id , $trans_status,$type,$accepted_by = null, $submitted_by = null){
        $accepted_by_set = $submitted_by_set = "";
        if($accepted_by != "" && $accepted_by != null){
            $accepted_by_set = " , accepted_by = '$accepted_by' ";
        }
        if($submitted_by != "" && $submitted_by != null){
            $submitted_by_set = " , submitted_by = '$submitted_by' ";
        }
        $status = 0;
        if($type == 'user'){
            $get_ledger = $this->db->query("SELECT * from user_vendor_ledger where `ledger_id`= '$ledger_id' AND `invoice_no` = '$invoice_no' ")->row_array();
            if(sizeof($get_ledger) > 0){
                $update_user_ledger = $this->db->query("UPDATE `user_vendor_ledger` SET `transaction_id`= '$transaction_id',`trans_status`= '$trans_status' $accepted_by_set $submitted_by_set WHERE `ledger_id`= '$ledger_id'")   ;
                $status = 1; // success
            } else {
                $status = 2; // No ledger found
            }
        } else if($type == 'vendor'){ 
            $get_ledger = $this->db->query("SELECT * from vendor_vendor_ledger where `ledger_id`= '$ledger_id' AND `invoice_no` = '$invoice_no' ")->row_array();
            if(sizeof($get_ledger) > 0){
                $update_vendor_ledger = $this->db->query("UPDATE `vendor_vendor_ledger` SET `transaction_id`= '$transaction_id',`trans_status`= '$trans_status' $accepted_by_set $submitted_by_set  WHERE `ledger_id`= '$ledger_id'")   ;
                $status = 1; // success
            } else {
                $status = 2; // No ledger found
            }
        } else {
            $status = 3; // no type found
        }
        //  points entry 
        if(sizeof($get_ledger) > 0 && $status == 1){
            $transaction_of = $get_ledger['transaction_of'];
            $trans_status = $get_ledger['trans_status'];
            $debit = floatval($get_ledger['debit']);
            $vendor_id = $get_ledger['vendor_id'];
            if($debit > 0 && $transaction_of == 2 && $trans_status == 1){
                $transaction_date = date('Y-m-d H:i:s');
                $user_id_points = $user_id;
                $invoice_no_points = $invoice_no;
                $transaction_date_points = $transaction_date;
                $transaction_id_points = "";
                $points_points = round($debit);
                $comments_points = '';
                $status_points = "active";
                $listing_type_points = $vendor_id;
                $expire_at_points =  date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s", time()) . " + 365 day"));
                $this->LedgerModel->add_points($user_id_points,  $invoice_no_points, $transaction_date_points, $transaction_id_points,  $points_points,  $comments_points,  $status_points,  $listing_type_points,  $expire_at_points);
            }
        }
        
       $data['status'] = $status;
       return $data;
    }
    
    
     public function ledger_select($user_id,$listing_id) 
   {
        
        
        $sql = "SELECT * FROM ledger_select order by type ASC ";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0) {
            foreach ($query->result_array() as $row) {
                $vendor_type = explode(",",$row['vendor_type']);
                foreach($vendor_type as $vt){
                    if($vt == $listing_id){
                        $id=$row['id'];    
                        $image=$row['image'];
                        $name=$row['name'];
                        $adddis=$row['info'];
                        $dis_type=$row['dis_type'];
                        $type=$row['type'];
                      /*  if($type=='2'){    
                        $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` WHERE `user_id` = '$user_id' order by id DESC")->row_array();       
                        if($ledger_balance_row->num_rows()>0)
                                {
                                    $resultpost=array();
                                
                                foreach ($query->result_array() as $row) {
                                        
                                    $discount=$row['discount'];
                                    $reward_amount=$row['reward_amount'];
                                    $status=$row['status'];
                                    $used_amount=$row['used_amount'];
                                    
                                        if($status!="1"){
                                             $adddis=$discount+$used_amount;
                                        }else{
                                         $adddis+=$discount;
                                        }
                                  
                             }
                                
                        }
                }*/
                        
                        $resultpost[] = array('id'=> $id,
                                         'image' => $image,
                                         'name' => $name,
                                         'type'=>$type,
                                         'Discount'=>$adddis,
                                         'dis_type'=>$dis_type,
                                         'user_id'=>$user_id
                             ); 
                        }
                    }
                }   
            }  else  {
            $resultpost=array(); 
        }
        return $resultpost;
       
   } 
    
    // add_points
    public function add_points($user_id,  $invoice_no, $transaction_date, $transaction_id,  $points,  $comments,  $status,  $listing_type,  $expire_at){
        $data = array();
        $message = "";
        $add_points_id = 0;
        date_default_timezone_set('Asia/Kolkata');
      if($transaction_date == ""){
          $transaction_date = date('Y-m-d H:i:s');
      }
        if($user_id == "" ||   $points < 1 ||  ($listing_type == "" && $listing_type < 0) ||  $expire_at == "" )  {
            $message = 'Please enter user_id, invoice_no, points should be greater than 0, comments, listing_type means vendor type id from where user got pointss, expire_at and transaction_date. transaction_date is default current date';
        } else {
            /*
            added logic on 24th Oct 2020 by Swapnali Waghunde
            max points should be 25000
            */
            $points = $points <= 25000 ? $points : 25000;
            $add_points = $this->db->query("INSERT INTO `user_points` (`user_id`, `order_id`, `trans_id`, `transaction_date`, `points`, `listing_type`, `comments`, `expire_at`, `status`) VALUES ('$user_id',  '$invoice_no' , '$transaction_id' , '$transaction_date', '$points', '$listing_type','$comments','$expire_at','$status')");
            $add_points_id = $this->db->insert_id();
            $message = 'success';
        }
        $data['insert_id'] = $add_points_id;
        $data['message'] = $message;
        return $data;
    }
    
    public function ledger_page_options($user_id, $vendor_type){
        $options = $data = array();
        date_default_timezone_set('Asia/Calcutta');
        $currentDate = date('Y-m-d H:i:s');
        
        // selection_type = > 1 : ledger; 2 : Bachat ; 3 : Points ; 4 : coupon wall 	: define in DB
        $get_select_options = $this->db->query("SELECT lpo.* FROM `ledger_page_options` as lpo WHERE  lpo.active = 1 ORDER BY lpo.options_order ASC ")->result_array();
        foreach($get_select_options as $op){
            $added_vendor_type = explode(",",$op['vendor_type']);
            foreach($added_vendor_type as $vt){
                if($vt == $vendor_type){
                    $amount = '0';
                    if($op['selection_type'] == 1){
                        $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` WHERE `user_id` = '$user_id' order by id DESC")->row_array();   
                        $amount = $ledger_balance_row['ledger_balance'];
                    } 
                    
                    if($op['selection_type'] == 3){
                        $sum_of_points = $this->db->query("SELECT SUM(points) as amount FROM `user_points` WHERE `user_id` = '$user_id' AND `expire_at` > '$currentDate' AND (`status` LIKE 'Active' or `status` LIKE 'active')  ")->row_array();
                        if(sizeof($sum_of_points) > 0){
                            $amount = $sum_of_points['amount'];
                        }
                    }
                    
                    $opts['name']  =  $op['name'];
                    $opts['display_type']  =  $op['display_type'];
                    $opts['image']  =  $op['image'];
                    $opts['type']  =  $op['selection_type'];
                    $opts['amount']  =  $amount == null ? "0" : $amount;
        
                    $options[] = $opts;
                }
            }
        }
        
        return $options;
    }
    
    public function get_points($user_id, $vendor_type, $per_page, $page_no){
        date_default_timezone_set('Asia/Calcutta');
        $currentDate = date('Y-m-d H:i:s');
        
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
   
        $points = $data = array();
        $total_points = '0';
        $status_id = 0;
        $get_all_points = $this->db->query("SELECT up.comments, up.expire_at, up.status, up.user_id, up.order_id, up.trans_id, up.transaction_date, up.points FROM `user_points` as up WHERE `user_id` = '$user_id' ORDER BY up.transaction_date DESC $limit")->result_array();
        foreach($get_all_points as $p){
            $current_points = $p['points'];
            $expire_at = $p['expire_at'];
            $status = $p['status'];
            $points_status = "";
            if($currentDate > $expire_at  ){
                 $points_status = 'Expired';
                $status_id = 3;
                
            }  else {
               if($status == 'converted'){
                    $points_status = 'Credited in ledger';
                    $status_id = 2;
                } else if($status == 'active' || $status == 'Active'){
                    $points_status = 'Active';
                    $status_id = 1;
                    $total_points = $total_points + $current_points;
                }
            }
            
            $fp['user_id'] = $p['user_id'];
            $fp['order_id'] = $p['order_id'];
            $fp['trans_id'] = $p['trans_id'];
            $fp['transaction_date'] = $p['transaction_date'];
            $fp['expiry_date'] = $expire_at;
            $fp['comments'] = $p['comments'];
            $fp['points'] = $current_points;
            $fp['status'] = $points_status;
            $fp['status_id'] = $status_id;
            
            $points[] = $fp;
            
        }
        $data['total_points'] = $total_points;
        $data['points'] = $points;
        return $data;
    }
    
    public function mw_team_extra_points($user_id, $invoice_no, $transaction_id, $points, $comments, $listing_type   ){
        $status = $insert_id = 0;
        $dt = date('Y-m-d');
        $transaction_date = date('Y-m-d H:s:i');
        // $expire_at = date('Y-m-d H:s:i', strtotime('+50 years')); // no expiry
        $expire_at = '2037-12-31 00:00:00'; // no expiry
        
        $start_of_month = date("Y-m-01 00:00:00", strtotime($dt));
        
        $already_added_for_invoice = $this->db->query("SELECT *  FROM `user_points` WHERE `user_id` = '$user_id' and mw_team_extra_points = 1 AND order_id = '$invoice_no' ")->row_array();
        if(sizeof($already_added_for_invoice) > 0){
             $status = 4; // already added for given order
        } else {
            $total_added_points_row = $this->db->query("SELECT sum(points) as total_added_points  FROM `user_points` WHERE `user_id` = '$user_id' and mw_team_extra_points = 1 AND created_at > '$start_of_month' ")->row_array();
            $conversion_rate = $this->db->query("SELECT rate  FROM `points_rate` WHERE `vendor_type` = '0'")->row_array();
            if(sizeof($conversion_rate) > 0){
                $rate = $conversion_rate['rate']; 
                // max 500 rs
                $max_points_can_add = $rate * 500;
                $total_added_points = $total_added_points_row['total_added_points'] == null ? 0 : $total_added_points_row['total_added_points'];
                $remaining_points_to_add = $max_points_can_add - $total_added_points;
                
                if($remaining_points_to_add >= $points){
                    $points_to_add = $points;
                } else {
                    $points_to_add = $remaining_points_to_add;
                }
                if($points_to_add > 0){
                    $points_to_add = $points_to_add <= 25000 ? $points_to_add : 25000;
                    $this->db->query("INSERT INTO `user_points`( `user_id`, `order_id`, `trans_id`, `transaction_date`, `points`, `listing_type`, `comments`, `mw_team_extra_points`, `created_at`, `expire_at`, `status`) VALUES ('$user_id','$invoice_no','$transaction_id','$transaction_date','$points_to_add','$listing_type','$comments','1','$transaction_date','$expire_at','active')");
                    $insert_id = $this->db->insert_id();
                    if($insert_id > 0){
                        $status = 1; // added
                    } else {
                        $status = 2; // something went wrong
                    }
                } else {
                    $status = 3; // can not add points
                }
            } else {
                 $status = 4; // no conversion rate available
            }
        }
        
        $data['status'] = $status;
        return $data;
    }
    
    public function payment_method_by_vendor_type($user_id,$vendor_type){
        $all_payment_methods = $apm = $sub_type_array = $all_sub_types = $data = array();
        $payment_methods  = $this->db->query( "SELECT p.id as payment_id, p.payment_method, p.parent_id, p.icon,p.offer_text FROM payment_method as p WHERE p.status = '1' AND FIND_IN_SET('$vendor_type', p.vendor_type) AND NOT EXISTS (SELECT * FROM payment_method WHERE parent_id = p.id) order by sequence_no asc ")->result_array();
        return $payment_methods;
    }
    
    
    /*Added by swapnali for sorting it in given format of array on 20th Jul 2020*/
    
    public function sortInvoiceArray($given_array, $sortorder){
        $result_array = $given_array;
        $invoices = array_column($sortorder, 'invoice_no');
       
        
        foreach($given_array as $ga){
            $key = array_search($ga['invoice_no'], $invoices);
            $result_array[$key] = $ga;
            
        }
        return $result_array;
    }

}
?>
