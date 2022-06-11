<!DOCTYPE html>
<html lang="en">
<head>
  <title>Invoice</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
  <body>
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
  
  $address = "";
?>

    <table  cellpadding="1" cellspacing="1" style="width:100%">    
    <!--<table style="width:50%;">-->
      <tr>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
        <td style="width:5%"></td>
      </tr>
      <tr style="border-bottom: 3px solid #3a9441;">
        <td style="text-align: center;border-left: 3px solid #3a9441;border-top: 3px solid #3a9441;border-bottom: 3px solid #3a9441;">
          <!--<span style="font-size: 70px;font-weight: 900;color: #049341;">&#43;</span>-->
          <img src="<?php echo base_url(); ?>assets/green-plus.jpg" style="width:50px">
        </td>
        <td style="text-align: center;vertical-align: baseline;border-top: 3px solid #3a9441;border-bottom: 3px solid #3a9441;font-weight: 900;" colspan="6">
          <span style="text-align: left!important;margin: 10px 0px 20px 0px;font-size: 13px;">INVOICE</span><br>
          <span style="font-size: 20px;padding: 0 10px;"><?php echo $detail->medical_name;?></span>
        </td>
        <td style="vertical-align: top;padding: 10px;border-right: 3px solid #3a9441;border-top: 3px solid #3a9441;border-bottom: 3px solid #3a9441;" colspan="3">
          <img src="<?php echo base_url(); ?>assets/green-convo.jpg" style="width: 15px;padding-bottom: 6px;">
          <span style="font-size: 15px;font-weight: 900;color: #3a9442;">+91 xxxx xxxx</span>
          <br>
          <img src="<?php echo base_url(); ?>assets/phone-receiver.png" style="width: 15px;">
          <span style="font-size: 15px;font-weight: 900;color: #3a9442;">022 xxxx xxxx</span>
          <br>
          
        </td>
      </tr>

      <tr>
        <td style="padding: 1px 10px;color: #333333;font-weight: 900;border-left: 3px solid #3a9441;"><?php if($bill->name != 'NA' && $bill->name != '' ){ echo 'Name'; } ?></td>
        <td style="padding: 1px 10px;" colspan="4"><?php if($bill->name != 'NA' ){ echo $bill->name; } ?></td>
        <td style="padding: 1px 10px;" colspan="2"><?php if(ctype_digit($bill->mobile)){ echo $bill->mobile; } ?></td>
        
        <td style="padding: 1px 10px;color: #333333;font-weight: 900;">Invoice</td>
        <td style="padding: 1px 10px;border-right: 3px solid #3a9441;" colspan="2"><?php echo $bill->invoice_no;?> </td>
      </tr>
        <?php 
            if($bill->address1 != ""){$address .= $bill->address1 ;}
            if($address != ""){ $address .= ', ';}
            if($bill->address2 != ""){$address .= $bill->address2 ;}
            if($address != ""){ $address .= ', ';}
            if($bill->city != ""){$address .= $bill->city ;}
            if($address != ""){ $address .= ', ';}
            if($bill->pincode > 0){$address .= $bill->pincode ;}
            
        ?>
        
       
      <tr>
        <td style="padding: 1px 10px;color: #333333;font-weight: 900;border-left: 3px solid #3a9441;"><?php if($address != ""){ ?>Add. <?php } ?></td>
        <td style="padding: 1px 10px;" colspan="6"><?php echo $address; ?></td>
        <td style="padding: 1px 10px;color: #333333;font-weight: 900;">Date</td>
        <td style="padding: 1px 10px;border-right: 3px solid #3a9441;" colspan="2"><?php echo date('dS M Y h:i A',strtotime($bill->order_date));?> </td>
      </tr>

      <tr>
        <td style="padding: 1px 10px;color: #333333;font-weight: 900;border-left: 3px solid #3a9441;"><?php if($bill->prescription_doctor != ""){ ?>Doctor<?php } ?></td>
        <td style="padding: 1px 10px;" colspan="6"><?php if($bill->prescription_doctor != ""){  echo $bill->prescription_doctor;  } ?></td>
        <td style="padding: 1px 10px;color: #333333;font-weight: 900;">Payment</td>
        <td style="padding: 1px 10px;border-right: 3px solid #3a9441;" colspan="2"><?php echo $bill->payment_method;?></td>
      </tr>

      <tr style="">
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-left: 3px solid #3a9441;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;"><b>QTY</b></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;"></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;" colspan="3"><b>ITEM</b></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;"><b>COMP</b></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;"><b>BATCH</b></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;"><b>EXP. DT.</b></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;"><b>PRICE</b></td>
        <td style="padding: 5px 10px;color: #333333;font-weight: 900;text-align: center;border-top:3px solid #3a9442;border-right: 3px solid #3a9441;border-bottom:3px solid #3a9442;"><b>AMOUNT</b></td>
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
      <tr style="border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;background:rgba(254, 31, 32, 0.2)">
        <td style="padding: 5px 10px;color: #555;text-align: center;border-left: 3px solid #3a9441;"><?php echo $dat->prescription_quantity;?></td>
        <td style="padding: 5px 10px;color: #555;text-align: left;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: left;text-transform: uppercase;" colspan="3"><?php echo $dat->prescription_name; ?></td>
        <td style="padding: 5px 10px;color: #555;text-align: center;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: center;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: center;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: right;"><?php echo number_format($selling_price,2);?></td>
        <td style="padding: 5px 10px;color: #555;text-align: right;border-right: 3px solid #3a9441;"><?php echo number_format($total,2); ?></td>
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
    
     <tr style="border-top:3px solid #3a9442;border-bottom:3px solid #3a9442;background:rgba(254, 31, 32, 0.2)">
        <td style="padding: 5px 10px;color: #555;text-align: center;border-left: 3px solid #3a9441;"><?php echo $dat->product_quantity;?></td>
        <td style="padding: 5px 10px;color: #555;text-align: left;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: left;text-transform: uppercase;" colspan="3"><?php echo $dat->product_name; ?></td>
        <td style="padding: 5px 10px;color: #555;text-align: center;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: center;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: center;">-</td>
        <td style="padding: 5px 10px;color: #555;text-align: right;"><?php echo number_format($selling_price1,2);?></td>
        <td style="padding: 5px 10px;color: #555;text-align: right;border-right: 3px solid #3a9441;"><?php echo number_format($total1,2); ?></td>
      </tr>
      
      <?php $i++;} ?>
      
      <tr style="">
        <td style="padding: 1px 10px;text-align: left;font-size: 13px;border-left: 3px solid #3a9441;border-top:3px solid #3a9442;" colspan="3">GST NO : 12345678901 </td>

        <td style="padding: 1px 10px;text-align: left;font-size: 13px;border-top:3px solid #3a9442;" colspan="4"><?php echo $detail->medical_name;?></td>

        <td style="padding: 1px 10px;text-align: left;font-size: 13px;border-top:3px solid #3a9442;" colspan="2">Total Amount </td>

        <td style="padding: 1px 10px;text-align: right;font-size: 13px;border-right: 3px solid #3a9441;border-top:3px solid #3a9442;" colspan="1"><?php echo $final_calculation['sub_total']; ?></td>

      </tr>

      <tr style="">
        <td style="padding: 1px 10px;text-align: left;font-size: 13px;border-left: 3px solid #3a9441;" colspan="3"></td>

        <td style="padding: 1px 10px;text-align: left;font-size: 13px;" colspan="4"></td>

        <td style="padding: 1px 10px;text-align: left;font-size: 13px;" colspan="2">Discount </td>
        <td style="padding: 1px 10px;text-align: right;font-size: 13px;border-right: 3px solid #3a9441;" colspan="1"><?php echo $final_calculation['discount']; ?> </td>
      </tr>

      <tr style="">
        <td style="padding: 1px 10px;text-align: left;font-size: 13px;border-left: 3px solid #3a9441;" colspan="3"></td>
        <td style="padding: 1px 10px;text-align: left;font-size: 13px;" colspan="4"></td>

        <td style="padding: 1px 10px;text-align: left;font-size: 13px;" colspan="2">Delivery charge </td>
        <td style="padding: 1px 10px;text-align: right;font-size: 13px;border-right: 3px solid #3a9441;" colspan="1"><?php echo $final_calculation['delivery_charges_by_customer']; ?></td>
      </tr>

      <tr style="border-bottom:3px solid #3a9442;">
        <td style="padding: 1px 10px;text-align: left;font-size: 13px;border-left: 3px solid #3a9441;" colspan="3"></td>
        <td style="padding: 1px 10px;text-align: left;font-size: 13px;" colspan="4">Authorised Signatory</td>

        <td style="padding: 1px 10px;text-align: left;font-size: 13px;font-weight: 900;" colspan="2">Grand Total </td>
        <td style="padding: 1px 10px;text-align: right;font-size: 13px;font-weight: 900;border-right: 3px solid #3a9441;" colspan="1"><?php echo $final_calculation['grand_total_customer']; ?> </td>
      </tr>
      <tr style="border-bottom:3px solid #3a9442;">
        <td style="padding:1px;text-align:right;border: 3px solid #3a9441;" colspan="10">
          Amount (in words) : <strong><?php echo ucwords(numtowords($final_calculation['grand_total_customer'])); ?> Only</strong>
        </td>
      </tr>
      <tr>
        <td style="font-size:9px;text-align:left;" colspan="5">*Consult doctor before using Medicine</td>
        <td style="font-size:9px;text-align:right;" colspan="5">Powered by Medicalwale.com</td>
      </tr>
    </table>
  </body>
</html>
<?php //die(); ?>