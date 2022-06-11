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
  return $final;
  }
?>

<table  cellpadding="5" style="width:100%">
    <tbody>
         <tr>
            <td colspan="100" style="text-align:center"><b>INVOICE</b></td>
        </tr>
        <tr>
            <td  style="border:1px solid #ddd;border-right:none!important;vertical-align: top;">
                <?php if($logo_url != ''){?><img  src="<?php echo $logo_url; ?><?php  } ?>" width="50">
            </td>
            <td colspan='3' style="border:1px solid #ddd;border-left:none!important;vertical-align: top;">
                <b><?php echo $detail->medical_name;?></b>
                <p style="margin:0;font-size:16px;"><?php echo $detail->address1;?></p>
                <p style="margin:0;font-size:16px;"><?php echo $detail->address2;?></p>
                <p style="margin:0;font-size:16px;"><?php echo $detail->city;?>,<?php echo $detail->state;?></p>
                <p style="margin:0;font-size:16px;"><?php echo $detail->pincode;?></p>
                    
            </td>
            <td colspan='3' style="border:1px solid #ddd;vertical-align: top;">
                <p style="margin:5px 0;font-size:14px">Bill to</p>
                <p style="margin: 0"><b><?php echo $bill->name; ?></b></p>
                <p style="margin:0;font-size:16px;"><?php echo $bill->address1; ?></p>
                <p style="margin:0;font-size:16px;"><?php echo $bill->address2; ?></p>
                <?php if($bill->landmark != '' ){ ?><p style="margin:0;font-size:16px;"><b>Landmark</b> : <?php echo $bill->landmark; ?></p> <?php } ?>
                <p style="margin:0;font-size:16px;"><?php echo $bill->city; ?> <?php if($bill->pincode > 0 ){ echo $bill->pincode; }?></p>
                <p style="margin:0;font-size:16px;"><?php echo $bill->state; ?>  </p>
                <?php if($bill->prescription_doctor != ''){ ?><p style="margin: 0"><b>Doctor : <?php echo $bill->prescription_doctor; ?></b></p><?php } ?>
                
            </td>
            <td colspan='2' style="border:1px solid #ddd;border-right:none!important;vertical-align: top;">
                <p style="margin:0"><b>Invoice No.</b> </p> 
                <p style="margin:0 0 10px 0"><?php echo $bill->invoice_no;?> </p>
                
                 <p style="margin:0"><b>Order No.</b> </p> 
                <p style="margin:0 0 10px 0"><?php echo $bill->order_id;?> </p>
           
                <p style="margin:0"><b>Order Date</b> </p> 
                <p style="margin:0 0 10px 0"><?php echo date('dS M Y h:i A',strtotime($bill->order_date));?>  </p>
                
                <p style="margin:0"><b>Payment Method</b> </p> 
                <p style="margin:0 0 10px 0"><?php echo $bill->payment_method;?></p>
                
            </td>
            <td colspan='2' style="border:1px solid #ddd;border-left:none!important;vertical-align: initial;">
            <?php if($barcode != ''){ ?>
                <?php echo $barcode; ?>
                <?php echo $bill->invoice_no;?>
            <?php } ?>
            </td>
            
        </tr>
        <tr>
            <th style="border:1px solid #ddd">SI. No.</th>
            <th style="border:1px solid #ddd">Item</th>
            <th style="border:1px solid #ddd">HSN/SAC</th>
            <th style="border:1px solid #ddd">QTY</th>
            <th style="border:1px solid #ddd">M.R.P.</th>
            <th style="border:1px solid #ddd">Discount</th>
            <th style="border:1px solid #ddd">Rate</th>
            <th style="border:1px solid #ddd">GST(%)</th>
            <th style="border:1px solid #ddd">GST Amount</th>
            <th style="border:1px solid #ddd">Total Value</th>
        </tr>
      
        <?php 
        $i=1;
        $tot_sum= 0;
        $quantity_sum= 0;
        $amount_sum = 0;
        $sell_price_sum = 0;
        $disc_sum = 0; 
        $gst_sum = 0;
        foreach($bill_detail as $dat) { 
       // $ap= $this->PharmacyPartnerModel->get_pro_name($dat->product_id);
           $total = 0;
           $amount = $dat->prescription_price*$dat->prescription_quantity;
           $amount_sum += $amount;
           $quantity_sum += $dat->prescription_quantity;
           $selling_price = $dat->prescription_price;
           if($dat->prescription_discount > 0)
           {
               $disc_rupee = ($amount*$dat->prescription_discount)/100 ;
               $selling_price = $dat->prescription_price - $disc_rupee;
               $disc_sum += $disc_rupee;
           }
           $gst_rupee=0;
           if($dat->gst > 0)
           {
               $gst_rupee = ($amount*$dat->gst)/100 ;
               $gst_sum += $gst_rupee;
           }
           $sell_price_sum += $selling_price;
           $total = $selling_price - $gst_rupee;
           $tot_sum += $total;
        ?>
        <tr>
            <td style="border:1px solid #ddd;text-align:right"><?php echo $i; ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo $dat->prescription_name; ?></td>
            <td style="border:1px solid #ddd;text-align:right"></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo $dat->prescription_quantity;?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($dat->prescription_price,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($disc_rupee,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($selling_price,2);?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($dat->gst,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($gst_rupee,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($total,2); ?></td>
        </tr>
        <?php $i++;} ?>
        <?php
        foreach($bill_detail1 as $dat) { 
       // $ap= $this->PharmacyPartnerModel->get_pro_name($dat->product_id);
           $total1 = 0;
           $amount1 = $dat->product_price*$dat->product_quantity;
           $amount_sum += $amount1;
           $quantity_sum += $dat->product_quantity;
           $selling_price1 = $dat->product_price;
           if($dat->product_discount > 0)
           {
               $disc_rupee1 = ($amount1*$dat->product_discount)/100 ;
               $selling_price1 = $dat->product_price - $disc_rupee1;
               $disc_sum += $disc_rupee1;
           }
           $gst_rupee1=0;
           if($dat->gst > 0)
           {
               $gst_rupee1 = ($amount1*$dat->gst)/100;
               $gst_sum += $gst_rupee1;
           }
           $sell_price_sum += $selling_price1;
           
           $total1 = $selling_price1 - $gst_rupee;
           $tot_sum += $total1;
        ?>
        <tr>
            <td style="border:1px solid #ddd;text-align:right"><?php echo $i; ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo $dat->product_name; ?></td>
            <td style="border:1px solid #ddd;text-align:right"></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo $dat->product_quantity;?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($dat->product_price,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($disc_rupee1,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($selling_price1,2);?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($dat->gst,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($gst_rupee1,2); ?></td>
            <td style="border:1px solid #ddd;text-align:right"><?php echo number_format($total1,2); ?></td>
        </tr>
        <?php $i++;} ?>
        <!--for loop ends-->
        <!--total-->
        <tr>
            <td style="border:1px solid #ddd"></td>
            <th style="border:1px solid #ddd;font-weight:900"><b>Total</b></th>
            <td style="border:1px solid #ddd"></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right"><b><?php echo $quantity_sum; ?></b></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right"><b><?php echo number_format($amount_sum,2); ?></b></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right"><b><?php echo number_format($disc_sum,2); ?></b></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right"><b><?php echo number_format($sell_price_sum,2); ?></b></td>
            <td style="border:1px solid #ddd"></td>
            <td style="border:1px solid #ddd;font-weight:900;text-align:right"><b><?php echo number_format($gst_sum,2); ?></b></td>
            <th style="border:1px solid #ddd;font-weight:900;text-align:right"><b><?php echo number_format($tot_sum,2); ?></b></th>
        </tr>
        <!--total ends-->
        <tr>
            <td colspan="100" style="border:1px solid #ddd;text-align:right">Amount (in words) : <b><?php echo ucwords(numtowords($tot_sum)); ?> Only</b></td>
        </tr>
        <tr >
            <td colspan='4' style="border:1px solid #ddd">
                <?php if($detail->license_registration != ''){ ?><p style="float:left">License : <?php echo $detail->license_registration;?></p><?php }  ?>
                
            </td>
            
            <td colspan='4' style="border:1px solid #ddd;vertical-align: initial;">
                <!--<p style="font-weight:700"><?php // echo $bill->invoice_no;?> </p>-->
            </td>
            <td colspan='3' style="border:1px solid #ddd;text-align:right;">
                <p style="float:left"><?php echo $detail->medical_name;?></p>
                <br><br><br>
                <p style="width:100%;text-align:right;horizontal-align: right">Authorised Signatory</p>
            </td>
        </tr>
        <tr>
            <td colspan='100'><p style="font-size:12px;">*Consult doctor before using Medicine</p></td>
        </tr>
    </tbody> 
</table> 