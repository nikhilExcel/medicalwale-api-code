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

<table  cellpadding="1" cellspacing="0" style="width:35%">
    <tbody>
         <tr>
            <td colspan="14" style="text-align:center;font-size:12px"><b>INVOICE</b></td>
        </tr>
        <tr>
            <td style="width:5%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:8%;"></td>
            <td style="width:5%;"></td>
            <td style="width:5%;"></td>
            <td style="width:5%;"></td>
            
        </tr>
        <tr>
            <td colspan='2' style="border:1px solid #ddd;border-right:none!important;vertical-align:top;" >
                
                <?php if($logo_url != ''){ ?>
                    <img  src="<?php echo $logo_url; ?>" width="70px">
                <?php  } ?>
            </td>
            <td colspan='3' style="border:1px solid #ddd;border-left:none!important;vertical-align:top;" >
                <p style="font-size:9px;"><b><?php echo $detail->medical_name;?></b></p>
                <p style="font-size:8px;"><?php echo $detail->address1;?></p>
                <p style="font-size:8px;"><?php echo $detail->address2;?></p>
                <p style="font-size:8px;"><?php echo $detail->city;?>,<?php echo $detail->state;?></p>
                <p style="font-size:8px;"><?php echo $detail->pincode;?></p>
                
                    
            </td>
            <td colspan='3' style="border:1px solid #ddd;border-left:none!important;vertical-align:top;" >
                <p style="font-size:10px">Bill to</p>
                <p style="font-size:9px;"><b><?php echo $bill->name; ?></b></p>
                <p style="font-size:8px;"><?php echo $bill->mobile;?></p>
                <p style="font-size:8px;"><?php echo $bill->address1; ?></p>
                <p style="font-size:8px;"><?php echo $bill->address2; ?></p>
                <?php if($bill->landmark != '' ){ ?>
                    <p style="font-size:8px;"><b>Landmark</b> : <?php echo $bill->landmark; ?></p>
                <?php } ?>
                <p style="font-size:8px;"><?php echo $bill->city; ?> <?php  echo $bill->pincode; ?></p>
                <p style="margin:0;font-size:8px;"><?php echo $bill->state; ?>  </p>
                <?php if($bill->prescription_doctor != ''){ ?><p style="font-size:8px;"><b>Doctor : <?php echo $bill->prescription_doctor; ?></b></p><?php } ?>
                
            </td>
            <td colspan='3' style="border:1px solid #ddd;border-left:none!important;border-right:none!important;vertical-align:top;" >
                <p style="margin:0;font-size:9px;"><b>Invoice No.</b> </p> 
                <p style="margin:0;font-size:9px;"><?php echo $bill->invoice_no;?> </p>
                <p style="margin:0;font-size:9px;"><b>Order No.</b> </p> 
                <p style="margin:0;font-size:9px;"><?php echo $bill->order_id;?> </p>
                <p style="margin:0;font-size:9px;"><b>Order Date</b> </p> 
                <p style="margin:0;font-size:9px;"><?php echo date('dS M Y h:i A',strtotime($bill->order_date));?>  </p>
                <p style="margin:0;font-size:9px;"><b>Payment Method</b> </p> 
                <p style="margin:0;font-size:9px;"><?php echo $bill->payment_method;?></p>
            </td>
            <td colspan='3' style="border:1px solid #ddd;border-left:none!important;vertical-align:baseline;" >
                <?php if($barcode != ''){ ?>
                    <?php echo $barcode; ?>
                   <span style="margin:0;font-size:13px;"> <?php echo $bill->invoice_no;?> </span>
                <?php } ?>
            </td>    
        </tr>
        <tr style="margin-top:-100px!important;">
            <th rowspan="2" style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Sr. No.</th>
            <th rowspan="2" colspan='3' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Product</th>
            <th rowspan="2" colspan='2' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">HSN/SAC</th>
            <th rowspan="2" style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">MRP</th>
            <th rowspan="2" style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">QTY</th>
            <th rowspan="2" style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Price</th>
            <th colspan='2' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">CGST</th>
            <th colspan='2' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">SGST</th>
            <th rowspan="2" style="border:1px solid #ddd;border-top:none!important;font-size:9px;">Total</th>
        </tr>
        <tr>
            <th  style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Rate</th>
            <th  style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Amt</th>
            <th  style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Rate</th>
            <th  style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;">Amt</th>
        </tr>
        
        <?php 
        $i=1;
        $tot_sum= 0;
        $quantity_sum= 0;
        $amount_sum = 0;
        $sell_price_sum = 0;
        $disc_sum = 0; 
        $gst_sum = 0;
        $sgst_amount_total = 0;
        $cgst_amount_total = 0;
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
            $gst_per = $dat->gst;
            if($gst_per > 0){
                $sgst = $cgst = $gst_per / 2;
                 $gst_rup = $selling_price1 - ($selling_price1 / ( 1+ (0.01 * $gst_per))) ;
                $sgst_amount =  $cgst_amount = $gst_rup / 2;
                $sgst_amount_total = $sgst_amount_total + $sgst_amount; 
                $cgst_amount_total = $cgst_amount_total + $cgst_amount; 
            } else {
                $cgst_amount = $sgst_amount = $sgst = $cgst = 0;
            }
            
           
           
        ?>
        <tr>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;"><?php  echo $i; ?></td>
            <td colspan='3' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:left;font-size:8px;"><?php echo $dat->prescription_name; ?></td>
            <td colspan='2' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;"></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;"><?php echo number_format($dat->prescription_price,2); ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;"><?php echo $dat->prescription_quantity;?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;"><?php echo number_format($selling_price,2);?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;text-align:center;"><?php echo intval($cgst) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;text-align:center;"><?php echo number_format($cgst_amount,2) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;text-align:center;"><?php echo intval($sgst) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;text-align:center;"><?php echo number_format($sgst_amount,2) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;text-align:right;font-size:8px;"><?php echo number_format($total,2); ?></td>
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
            $gst_per = $dat->gst;
            if($gst_per > 0){
                $sgst = $cgst = $gst_per / 2;
                 $gst_rup = $selling_price1 - ($selling_price1 / ( 1+ (0.01 * $gst_per))) ;
                $sgst_amount =  $cgst_amount = $gst_rup / 2;
                $sgst_amount_total = $sgst_amount_total + $sgst_amount; 
                $cgst_amount_total = $cgst_amount_total + $cgst_amount; 
            } else {
                $cgst_amount = $sgst_amount = $sgst = $cgst = 0;
            }
            
           
        ?>
        <tr>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;"><?php  echo $i; ?></td>
            <td colspan='3' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:left;font-size:8px;"><?php echo $dat->product_name; ?></td>
            <td colspan='2' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;"></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;"><?php echo number_format($dat->product_price,2); ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;"><?php echo $dat->product_quantity;?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:right;font-size:8px;"><?php echo number_format($selling_price1,2);?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;text-align:center;"><?php echo intval($cgst) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;text-align:center;"><?php echo number_format($cgst_amount,2) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;text-align:center;"><?php echo intval($sgst) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:8px;text-align:center;"><?php echo number_format($sgst_amount,2) ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;text-align:right;font-size:8px;"><?php echo number_format($total1,2); ?></td>
        </tr>
        <?php $i++;} ?>
        <!--for loop ends-->
        <!--total-->
        <tr>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;"></td>
            <td colspan='3' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-weight:900;font-size:9px;text-align:center"><b>Total</b></td>
            <td colspan='2' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:9px;"></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-weight:900;text-align:right;font-size:9px;"><b><?php // echo number_format($amount_sum,2); ?></b></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-weight:900;text-align:center;font-size:9px;"><b><?php echo $quantity_sum; ?></b></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-weight:900;text-align:right;font-size:9px;"><b><?php echo number_format($sell_price_sum,2); ?></b></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:9px;"></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:9px;"><?php echo number_format($cgst_amount_total,2)  ; ?></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:9px;"></td>
            <td style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:center;font-size:9px;"><?php echo number_format($sgst_amount_total,2)  ; ?></td>
            <th style="border:1px solid #ddd;border-top:none!important;font-weight:900;text-align:right;font-size:9px;"><b><?php echo number_format($tot_sum,2); ?></b></th>
        </tr>
        <!--total ends-->
        <tr >
            <td colspan='4' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:11px;vertical-align:top">
                <?php if($detail->license_registration != ''){ ?><p style="float:left;font-size:11px;">License : <?php echo $detail->license_registration;?></p><?php }  ?>
                
            </td>
            <td colspan='4' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;font-size:11px;vertical-align:top">
                <p style="float:left;font-size:11px;"><?php echo $detail->medical_name;?></p>
                <br><br><br>
                <p style="width:100%;text-align:right;horizontal-align: right;font-size:11px;">Authorised Signatory</p>

            </td>
            <td colspan='4' style="border:1px solid #ddd;border-top:none!important;border-right:none!important;text-align:left;border-right:none!important;vertical-align: top;">
                <p style="font-size:11px;">Total Amount </p>
                <p style="font-size:11px;">Discount</p>
                <?php $dicount_per = $final_calculation['gst_in_percent'] == "" ? 0 : $final_calculation['gst_in_percent']; ?>
                
                <p style="font-size:11px;">Delivery Charge</p>
                <p style="font-size:11px;"><b>Grand total</b></p>
            </td>
            <td colspan='2' style="border:1px solid #ddd;border-top:none!important;text-align:right;border-left:none!important;vertical-align: top;">
                <p style="font-size:11px;"><?php echo $final_calculation['sub_total'];?></p>
                <p style="font-size:11px;"><?php echo $final_calculation['discount'];?></p>
                
                <p style="font-size:11px;"><?php echo $final_calculation['delivery_charges_by_customer'];?></p>
                <p style="font-size:11px;"><b><?php echo $final_calculation['grand_total_customer'];?></b></p>
            </td>
        </tr>
        <tr>
            <td colspan="14" style="border:1px solid #ddd;border-top:none!important;text-align:right;font-size:11px;">Amount (in words) : <b><?php echo ucwords(numtowords($final_calculation['grand_total_customer'])); ?> Only</b></td>
        </tr>
        
        <tr>
            <td colspan='7'>
                <p style="font-size:7px;">*Consult doctor before using Medicine</p>
                
            </td>
            <td colspan='7' style="text-align:right"><p style="font-size:7px;">Powered by Medicalwale.com</p></td>
        </tr>
    </tbody> 
</table> 
<?php   //   die(); ?>