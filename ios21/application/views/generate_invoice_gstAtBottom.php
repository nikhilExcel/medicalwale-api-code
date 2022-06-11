<?php 
function numtowords($number)
{
//$number = 190908100.25;
   $no = floor($number);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);
  $points = ($point) ?
    "." . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : '';
          $final = $result . "Rupees  ";
          if($points)
          {
              $final .= $points. " Paise";
          }
          
    $final = str_replace("."," and ",$final);
          
    return $final;
  }
?>

<table  cellpadding="5" style="width:100%">
    <tbody>
         <tr>
            <td colspan="11" style="text-align:center;font-size:11px"><b>INVOICE</b></td>
        </tr>
        <tr>
            <td  style="border:1px solid #ddd;border-right:none!important;vertical-align: top;width:40px;">
                
                <?php if($logo_url != ''){ ?>
                    <img  src="<?php echo $logo_url; ?>" width="100px">
                <?php  } ?>
            </td>
            <td colspan='2' style="border:1px solid #ddd;border-left:none!important;vertical-align: top;">
                <b><?php echo $detail->medical_name;?></b>
                <p style="margin:0;font-size:11px;"><?php echo $detail->address1;?></p>
                <p style="margin:0;font-size:11px;"><?php echo $detail->address2;?></p>
                <p style="margin:0;font-size:11px;"><?php echo $detail->city;?>,<?php echo $detail->state;?></p>
                <p style="margin:0;font-size:11px;"><?php echo $detail->pincode;?></p>
                    
            </td>
            
            <td colspan='4' style="border:1px solid #ddd;vertical-align: top;">
                <p style="margin:5px 0;font-size:11px">Bill to</p>
                <p style="margin: 0"><b><?php echo $bill->name; ?></b></p>
                <p style="margin:0;font-size:11px;"><?php echo $bill->address1; ?></p>
                <p style="margin:0;font-size:11px;"><?php echo $bill->address2; ?></p>
                <?php if($bill->landmark != '' ){ ?><p style="margin:0;font-size:11px;"><b>Landmark</b> : <?php echo $bill->landmark; ?></p> <?php } ?>
                <p style="margin:0;font-size:11px;"><?php echo $bill->city; ?> <?php if($bill->pincode > 0 ){ echo $bill->pincode; }?></p>
                <p style="margin:0;font-size:11px;"><?php echo $bill->state; ?>  </p>
                <?php if($bill->prescription_doctor != ''){ ?><p style="margin: 0"><b>Doctor : <?php echo $bill->prescription_doctor; ?></b></p><?php } ?>
                
            </td>
            <td colspan='2' style="border:1px solid #ddd;border-right:none!important;vertical-align: top;">
                <p style="margin:0;font-size:10px;"><b>Invoice No.</b> </p> 
                <p style="margin:0 0 10px 0;font-size:10px;"><?php echo $bill->invoice_no;?> </p>
                
                 <p style="margin:0;font-size:10px;"><b>Order No.</b> </p> 
                <p style="margin:0 0 10px 0;font-size:10px;"><?php echo $bill->order_id;?> </p>
           
                <p style="margin:0;font-size:10px;"><b>Order Date</b> </p> 
                <p style="margin:0 0 10px 0;font-size:10px;"><?php echo date('dS M Y h:i A',strtotime($bill->order_date));?>  </p>
                
                <p style="margin:0;font-size:10px;"><b>Payment Method</b> </p> 
                <p style="margin:0 0 10px 0;font-size:10px;"><?php echo $bill->payment_method;?></p>
                
            </td>
            <td colspan='2' style="border:1px solid #ddd;border-left:none!important;vertical-align: initial;">
            <?php if($barcode != ''){ ?>
                <?php echo $barcode; ?>
                <?php echo $bill->invoice_no;?>
            <?php } ?>
            </td>
            
        </tr>
        <tr>
            <th style="border:1px solid #ddd;font-size:11px;">Sr. No.</th>
            <th colspan='3' style="border:1px solid #ddd;font-size:11px;">Product</th>
            <th colspan='2' style="border:1px solid #ddd;font-size:11px;">HSN/SAC</th>
            <th style="border:1px solid #ddd;font-size:11px;">Price</th>
            <th style="border:1px solid #ddd;font-size:11px;">QTY</th>
            <th style="border:1px solid #ddd;font-size:11px;">Rate</th>
            <th style="border:1px solid #ddd;font-size:11px;">GST(%)</th>
            <th style="border:1px solid #ddd;font-size:11px;">Total</th>
        </tr>
      
        <?php 
        $i=1;
        $tot_sum= 0;
        $quantity_sum= 0;
        $amount_sum = 0;
        $sell_price_sum = 0;
        $disc_sum = 0; 
        $gst_sum = 0;
        $for_gst =array();
        foreach($bill_detail as $dat) { 
       // $ap= $this->PharmacyPartnerModel->get_pro_name($dat->product_id);
           $total = 0;
           $amount = $dat->prescription_price*$dat->prescription_quantity;
           $amount_sum += $amount;
           $quantity_sum += $dat->prescription_quantity;
           $selling_price = $dat->prescription_price*$dat->prescription_quantity;
           
           $sell_price_sum += $selling_price;
           $total = $selling_price;
           $tot_sum += $total;
           
           /*for gst*/
           $fg['product_name'] = $dat->prescription_name;
           $fg['gst'] = $dat->gst;
           $fg['qty'] = $dat->prescription_quantity;
           $fg['price'] = $dat->prescription_price;
           $for_gst[] = $fg;
           
        ?>
        <tr>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php  echo $i; ?></td>
            <td colspan='3' style="border:1px solid #ddd;text-align:left;font-size:11px;"><?php echo $dat->prescription_name; ?></td>
            <td colspan='2' style="border:1px solid #ddd;text-align:right;font-size:11px;"></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo number_format($dat->prescription_price,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo $dat->prescription_quantity;?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo number_format($selling_price,2);?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo $dat->gst; ?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo number_format($total,2); ?></td>
        </tr>
        <?php $i++;} ?>
        <?php
        foreach($bill_detail1 as $dat) { 
       // $ap= $this->PharmacyPartnerModel->get_pro_name($dat->product_id);
           $total1 = 0;
           $amount1 = $dat->product_price*$dat->product_quantity;
           $amount_sum += $amount1;
           $quantity_sum += $dat->product_quantity;
           $selling_price1 = $dat->product_price*$dat->product_quantity;
         
           $sell_price_sum += $selling_price1;
           
           $total1 = $selling_price1;
           $tot_sum += $total1;
           
                      /*for gst*/
           $fg['product_name'] = $dat->product_name;
           $fg['gst'] = $dat->gst;
           $fg['qty'] = $dat->product_quantity;
            $fg['price'] = $dat->product_price;
           $for_gst[] = $fg;
        ?>
        <tr>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php  echo $i; ?></td>
            <td colspan='3' style="border:1px solid #ddd;text-align:left;font-size:11px;"><?php echo $dat->product_name; ?></td>
            <td colspan='2' style="border:1px solid #ddd;text-align:right;font-size:11px;"></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo number_format($dat->product_price,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo $dat->product_quantity;?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo number_format($selling_price1,2);?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo $dat->gst; ?></td>
            <td style="border:1px solid #ddd;text-align:right;font-size:11px;"><?php echo number_format($total1,2); ?></td>
        </tr>
        <?php $i++;} ?>
        <!--for loop ends-->
        <!--total-->
        <tr>
            <td style="border:1px solid #ddd"></td>
            <th colspan='3' style="border:1px solid #ddd;font-weight:900;font-size:11px;"><b>Total</b></th>
            <td colspan='2' style="border:1px solid #ddd;font-size:11px;"></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right;font-size:11px;"><b><?php // echo number_format($amount_sum,2); ?></b></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right;font-size:11px;"><b><?php echo $quantity_sum; ?></b></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right;font-size:11px;"><b><?php echo number_format($sell_price_sum,2); ?></b></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right;font-size:11px;"><b></b></td>
            <th style="border:1px solid #ddd;font-weight:900;text-align:right;font-size:11px;"><b><?php echo number_format($tot_sum,2); ?></b></th>
        </tr>
        <!--total ends-->
        <tr >
            <?php
            
                foreach($for_gst as $calc_gst){
                    $gst_per = $calc_gst['gst'];
                    $product_name = $calc_gst['product_name'];
                    $product_quantity = $calc_gst['product_quantity'];
                    $product_price = $calc_gst['product_price'];
                    if($gst_per > 0){
                        $sgst = $cgst = $each_gst = $gst_per / 2;
                        $cgst_amount = $sgst_amount = $product_quantity * $product_price *($sgst/100);
                        
                    } else {
                        $cgst_amount = $sgst_amount = $sgst = $cgst = 0;
                    }
                    $fgc['product_name'] = $product_name;
                    $fgc['cgst_per'] = $cgst;
                    $fgc['cgst_rs'] = $cgst_amount;
                    $fgc['sgst_per'] = $cgst;
                    $fgc['sgst_rs'] = $sgst_amount;
                    $final_gst_Cals[] = $fgc;
                  } 
                  $total_rows = sizeof($final_gst_Cals);
                  ?>
            
            <td style="border:1px solid #ddd;text-align:center;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;text-align:center;"><b>Sr.No.</b></p>
                <?php for($p=0;$p<$total_rows;$p++){  $sr = $p + 1;?>
                    <p style="font-size:11px;text-align:center;"><?php echo $sr;  ?></p>   
                <?php } ?>
            </td>
            <td colspan='2' style="border:1px solid #ddd;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;"><b>Product</b></p>
                <?php for($p=0;$p<$total_rows;$p++){ ?>
                    <p style="font-size:11px;"><?php echo $final_gst_Cals[$p]['product_name'];  ?></p>   
                <?php } ?>
            </td>
            
            <td  style="border:1px solid #ddd;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;"><b>CGST(%)</b></p>
                <?php for($p=0;$p<$total_rows;$p++){ ?>
                    <p style="font-size:11px;"><?php echo $final_gst_Cals[$p]['cgst_per'];  ?></p>   
                <?php } ?>
            </td>
            
            <td  style="border:1px solid #ddd;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;"><b>SGST(%)</b></p>
                <?php for($p=0;$p<$total_rows;$p++){ ?>
                    <p style="font-size:11px;"><?php echo $final_gst_Cals[$p]['sgst_per'];  ?></p>   
                <?php } ?>
            </td>
            
            <td  style="border:1px solid #ddd;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;"><b>CGST</b></p>
                <?php for($p=0;$p<$total_rows;$p++){ ?>
                    <p style="font-size:11px;"><?php echo $final_gst_Cals[$p]['cgst_rs'];  ?></p>   
                <?php } ?>
            </td>
            
            <td  style="border:1px solid #ddd;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;"><b>SGST</b></p>
                <?php for($p=0;$p<$total_rows;$p++){ ?>
                    <p style="font-size:11px;"><?php echo $final_gst_Cals[$p]['sgst_rs'];  ?></p>   
                <?php } ?>
            </td>
            
            
            <td colspan='3' style="border:1px solid #ddd;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;">Total Amount </p>
                <p style="font-size:11px;">Discount</p>
                <?php $dicount_per = $final_calculation['gst_in_percent'] == "" ? 0 : $final_calculation['gst_in_percent']; ?>
                <p style="font-size:11px;">GST (<?php echo $dicount_per;?> %) </p>
                <p style="font-size:11px;">Delivery Charge</p>
                <p style="font-size:11px;"><b>Grand total</b></p>
            </td>
            <td colspan='1' style="border:1px solid #ddd;text-align:right;border-left:none!important;vertical-align: top;">
                <p style="font-size:11px;"><?php echo $final_calculation['sub_total'];?></p>
                <p style="font-size:11px;"><?php echo $final_calculation['discount'];?></p>
                <p style="font-size:11px;"><?php echo $final_calculation['gst_in_rupees'];?></p>
                <p style="font-size:11px;"><?php echo $final_calculation['delivery_charges_by_customer'];?></p>
                <p style="font-size:11px;"><b><?php echo $final_calculation['grand_total_customer'];?></b></p>
            </td>
        </tr>
        <tr>
            <td colspan="11" style="border:1px solid #ddd;text-align:right;font-size:11px;">Amount (in words) : <b><?php echo ucwords(numtowords($final_calculation['grand_total_customer'])); ?> Only</b></td>
        </tr>
        <tr >
            <td colspan='4' style="border:1px solid #ddd;font-size:11px;">
                <?php if($detail->license_registration != ''){ ?><p style="float:left;font-size:11px;">License : <?php echo $detail->license_registration;?></p><?php }  ?>
                
            </td>
            
            <td colspan='3' style="border:1px solid #ddd;vertical-align: initial;">
                <!--<p style="font-weight:700"><?php // echo $bill->invoice_no;?> </p>-->
            </td>
            <td colspan='4' style="border:1px solid #ddd;text-align:right;">
                <p style="float:left;font-size:11px;"><?php echo $detail->medical_name;?></p>
                <br><br>
                <p style="width:100%;text-align:right;horizontal-align: right;font-size:11px;">Authorised Signatory</p>
            </td>
        </tr>
        <tr>
            <td colspan='7'><p style="font-size:10px;">*Consult doctor before using Medicine</p></td>
            <td colspan='4' style="text-align:right"><p style="font-size:10px;">Powered by Medicalwale.com</p></td>
        </tr>
    </tbody> 
</table> 