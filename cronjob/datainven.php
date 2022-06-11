<?php
require_once("../config.php");
$query = "SELECT * FROM inven_distrub_invoice_details";
$data = mysqli_query($hconnection, $query);
while($row = mysqli_fetch_array($data))
{ 
     $id=$row['id'];
     $order_id=$row['order_id'];
	 $product_mrp=$row['product_mrp'];
	 $discount=$row['discount'];
	 $product_qty=$row['product_qty'];
	 $pgst=$row['gst'];
	 $gst_type="1";
              $gst_amount_with=0;
              $final_gst_amt=0;
			   $new_sub=0;
             if($gst_type=="1")
              {
                  if($discount!=0)
                  {
		            $gst_amount_with2 =($product_mrp*100)/($pgst+100);
                    $final_gst_amt=number_format((float)$gst_amount_with2, 2, '.', '');      echo "<br>";
		            $dicount_in_rupee =($final_gst_amt*$discount)/100;
		            $final_discount=number_format((float)$dicount_in_rupee, 2, '.', '');         
		            $new_amt=$final_gst_amt-$final_discount; 
		            $prin_gst=($new_amt*$pgst)/100;
		            $gst_amount_with=number_format((float)$prin_gst, 2, '.', '');  
                    $final_g=$new_amt+$prin_gst; 
                    
		            $new_final=$final_g*$product_qty;       
                    $new_sub=$new_final;
                  }
                  else
                  {
                    $gst_amount_with =($product_mrp*100)/($pgst+100);
                    $final_gst_amt=number_format((float)$gst_amount_with, 2, '.', ''); 
		            $dicount_in_rupee =0;
		            $final_discount=0.00;         
		            $new_amt=$final_gst_amt-$final_discount; 
		            //$prin_gst=($new_amt*$pgst[$i])/100;
		            $f=$product_mrp-$new_amt;
                    $final_g=$f+$new_amt; 
                  
		            $new_final=$final_g*$product_qty;       
                    $new_sub=$new_final; 
                    
                  }
              }
    
        
	
	$dm=$product_mrp*$product_qty;
echo $up="UPDATE `inven_distrub_invoice_details` SET product_qty='$product_qty',discount='$discount',price='$dm',discount_rupess='$final_dis_rup',
    product_mrp='$product_mrp',gst='$pgst',gst_amount='$final_gst_amt',gst_type='$gst_type' where id='$id' and order_id='$order_id'";		  
mysqli_query($hconnection, $up);
	
}		  
			  
?>
