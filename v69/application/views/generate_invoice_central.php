<!DOCTYPE html>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice</title>
    <!--<LINK href="styles.css" rel="stylesheet" type="text/css">-->
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
</head>

<style>
    
    /*p {
        margin-block-start: 0em;
        margin-block-end: 0em;
    }*/

    /*th {
        BACKGROUND: #d8f3e4;
    }*/
    
    td {
        font-size: 12px;
    }

</style>

<body>
    <!--Swapnali Waghunde : 14th May 2020-->
<?php 
function numtowords($number)
{
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

<!--<div style="width:9%;float:left;">
    
</div>-->

<div style="width:5%;float:left;">
</div>
<div style="width:5%;float:left;">
    <table>
        <tbody>
            <tr>
                <td style="text-align:right;text-rotate:90;vertical-align:middle;">
                    <p>
                        
                        
                        <span style="font-size:9px;color:#000">Any overcharge due to oversight will be refunded. </span>
                        
                        <span style="font-size:9px;color:#3a9441">*Consult doctor before using Medicine</span>
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
    
</div>

<div style="width:60%;float:left;">
    <table style="border-spacing: 0px;">
        <thead>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
            <th style="width:10%!important;"></th>
        </thead>    
        <tbody>
            
            <tr>
                <!--logo, title, contact-->
                <td colspan='2' style="vertical-align:top;" >
                    <?php if($logo_url != ''){ ?>
                        <img  src="<?php echo $logo_url; ?>" width="70px">
                    <?php  } ?>
                </td>
                <td colspan='6' style="vertical-align:top;text-align:center" >
                    <p style="font-size:22px;text-align:center;text-transform: uppercase;">
                        <b><?php echo $detail->medical_name;?></b>
                    </p>
                    <p style="font-size:9px;text-align:center;">
                        <?php echo $detail->address1;?>
                        <?php echo $detail->address2;?>
                        <?php echo $detail->city;?> 
                        <?php if($detail->pincode > 0){ echo '-'.$detail->pincode; }?>
                        <?php echo $detail->state;?> 
                    </p>
                </td>
                <td colspan="2" style="vertical-align: top;padding: 10px;text-align:right">
                    <br>
                    <img src="<?php echo base_url(); ?>assets/phone-receiver.png" style="width: 12px;"> 
                    <span style="font-size:13px;font-weight: 900;"><?php echo $detail->contact_no; ?></span><!--Replace with pharmacy contact no-->
                    <br>
                    <?php if($detail->whatsapp_no != ""){ ?>
                        <img src="<?php echo base_url(); ?>assets/green-convo.jpg" style="width: 12px;"> 
                        <span style="font-size:13px;font-weight: 900;"><?php echo $detail->whatsapp_no; ?></span><!--Replace with pharmacy contact no-->
                        <br>
                    <?php } ?>
                    
                </td>
            </tr>
            <tr>
                <!--Customer name, INVOICE NO-->
                <td style="vertical-align:top;padding: 1px 10px;color: #333333;font-weight: 900;">
                    <?php if($bill->name != 'NA' && $bill->name != '' ){ echo 'Name'; } ?>
                </td>
                <td colspan="6" style="vertical-align:top;padding: 1px 10px;">
                    <?php if($bill->name != 'NA' ){ echo $bill->name; } ?> <?php if(ctype_digit($bill->mobile)){ echo $bill->mobile; } ?>
                </td>
                
                <td style="vertical-align:top;padding: 1px 10px;color: #333333;font-weight: 900;">Invoice</td>
                <td colspan="2" style="vertical-align:top;padding: 1px 10px;"><?php echo $bill->invoice_no;?> </td>
                
            </tr>
            <!--address-->
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
                <!--adress and date-->
                <td rowspan="2" style="padding: 1px 10px;color: #333333;font-weight: 900;vertical-align:top;"><?php if($address != ""){ ?>Add. <?php } ?></td>
                <td rowspan="2" style="padding: 1px 10px;vertical-align:top;font-size:10px;" colspan="6"><?php echo $address; ?></td>
                <td style="padding: 1px 10px;color: #333333;font-weight: 900;vertical-align:top;">Date</td>
                <td style="padding: 1px 10px;vertical-align:top;font-size:10px;" colspan="2"><?php echo date('dS M Y h:i A',strtotime($bill->order_date));?> </td>
            </tr>
            
            <tr>
                <!--dedlivery by-->
                <?php 
                $order_deliver_by = $bill->order_deliver_by;
                if($order_deliver_by == 'mno'){
                    $deliver_by = 'Night owl';
                } else if($order_deliver_by == 'pharmacy'){
                    $deliver_by = 'Pharmacy';
                } else if($order_deliver_by == 'walkin'){
                    $deliver_by = 'Walk-in customer';
                } else {
                    $deliver_by = $bill->order_deliver_by;
                }
                ?>
                <td style="padding: 1px 10px;color: #333333;font-weight: 900;vertical-align:top;">Delivery by</td>
                <td style="padding: 1px 10px;vertical-align:top;font-size:11px;" colspan="2"><?php echo $deliver_by;?> </td>
            </tr>
        
            <tr>
                <!--doctor, Payment-->
                <td style="vertical-align:top;padding: 1px 10px;color: #333333;font-weight: 900;">Doctor</td>
                <td style="vertical-align:top;padding: 1px 10px;font-size:11px;" colspan="6"><?php if($bill->prescription_doctor != ""){  echo $bill->prescription_doctor;  } else { echo '-';} ?></td>
                <td style="vertical-align:top;padding: 1px 10px;color: #333333;font-weight: 900;">Payment</td>
                <td style="vertical-align:top;padding: 1px 10px;font-size:11px;" colspan="2"><?php echo $bill->payment_method;?></td>
            </tr>
            
           
            <tr style="background:#d0e6d5">
                <!--head of medicines-->
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>HSN</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>QNTY.</b></td>
                <!--<td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>PACKING</b></td>-->
                <td colspan="2" style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>DESCRIPTION</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>MGF.</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>BATCH  NO.</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>EXP. DT.</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>GST%</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>MRP/RATE</b></td>
                <td style="background:#d0e6d5;font-size:10px;padding: 2px 5px;color: #333333;font-weight: 900;text-align: center;"><b>VALUE</b></td>
            
            </tr>
            <!--medicines name-->
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
                   $mrp = $dat->prescription_price;
                   $selling_price = $dat->prescription_price*$dat->prescription_quantity;
                   
                   $sell_price_sum += $selling_price;
                   $total = $selling_price;
                   $tot_sum += $total;
                   
                    /*for gst*/
                    $gst_per = $dat->gst;
                    if($gst_per > 0){
                        $sgst = $cgst = $gst_per / 2;
                         $gst_rup = $selling_price - ($selling_price / ( 1+ (0.01 * $gst_per))) ;
                        $sgst_amount =  $cgst_amount = $gst_rup / 2;
                        $sgst_amount_total = $sgst_amount_total + $sgst_amount; 
                        $cgst_amount_total = $cgst_amount_total + $cgst_amount; 
                    } else {
                        $cgst_amount = $sgst_amount = $sgst = $cgst = 0;
                    }
                    
                    // print_r($cgst_amount_total); die();
                 
                ?>   
                <!--HSN, QNTY, DISCRIPTION, MGF, BATCH, EXP, GST, MRP, VALUE-->
                <tr style="vertical-align:top;">
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->hsn;?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->prescription_quantity;?></td>
                    <td colspan="2" style="font-size:10px;padding: 2px 5px;color: #555;text-transform: uppercase;vertical-align:top;"><?php echo $dat->prescription_name; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->mgf_date;?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->batch_no;?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->expiry_date;?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $gst_per; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: right;vertical-align:top;"><?php echo number_format($mrp,2);?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: right;vertical-align:top;"><?php echo number_format($total,2); ?></td>
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
                    $mrp1 = $dat->product_price;
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
                <tr style="vertical-align:top;">
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->hsn; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->product_quantity;?></td>
                    <td colspan="2" style="font-size:10px;padding: 2px 5px;color: #555;text-align: left;text-transform: uppercase;vertical-align:top;"><?php echo $dat->product_name; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: left;vertical-align:top;"><?php echo $dat->mgf_date; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->batch_no; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $dat->expiry_date; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: center;vertical-align:top;"><?php echo $gst_per; ?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: right;vertical-align:top;"><?php echo number_format($mrp1,2);?></td>
                    <td style="font-size:10px;padding: 2px 5px;color: #555;text-align: right;vertical-align:top;"><?php echo number_format($total1,2); ?></td>
                </tr>
              
            <?php $i++;} ?>    
            
            <tr>
                <td>
                    <br>
                </td>
            </tr>
            <tr style="background:#d0e6d5">
                <td colspan="7"></td>
                <td style="padding: 1px 10px;text-align: left;font-size: 11px;" colspan="2">Total Amount </td>
        
                <td style="padding: 1px 10px;text-align: right;font-size: 11px;" colspan="1"><?php echo $final_calculation['sub_total']; ?></td>
        
            </tr>
        
            <tr style="background:#d0e6d5">
                <td colspan="7"></td>
        
                <td style="padding: 1px 10px;text-align: left;font-size: 11px;" colspan="2">Discount </td>
                <td style="padding: 1px 10px;text-align: right;font-size: 11px;" colspan="1"><?php echo $final_calculation['discount']; ?> </td>
            </tr>
        
            <tr style="background:#d0e6d5">
                <td colspan="7"></td>
        
                <td style="padding: 1px 10px;text-align: left;font-size: 11px;" colspan="2">Delivery charge </td>
                <td style="padding: 1px 10px;text-align: right;font-size: 11px;" colspan="1"><?php echo $final_calculation['delivery_charges_by_customer']; ?></td>
            </tr>
        
            <tr style="background:#d0e6d5">
                <td colspan="5">
                    <?php if($detail->license_registration != ""){ ?>
                        <p style="font-size:8px">
                            License No.: <?php echo $detail->license_registration; ?>
                        </p>
                    <?php } ?>
                    <p style="font-size:8px">
                        WITHOUT BILL GOODS WILL NOT BE ACCEPTED.
                    </p>
                </td>
                
                <td colspan="2" style="text-align: center;">R.P.SIGN</td>
                
                <td style="padding: 1px 10px;text-align: left;font-size: 11px;font-weight: 900;" colspan="2"><b>Grand Total </b></td>
                <td style="padding: 1px 10px;text-align: right;font-size: 11px;font-weight: 900;" colspan="1"><b><?php echo $final_calculation['grand_total_customer']; ?></b> </td>
            </tr>
            <tr>
                
                <?php $total_gst_rs = floatval($sgst_amount_total+$cgst_amount_total); ?>
                <td colspan="10" style="font-size:10px;">
                    Txbl <?php echo number_format($total_gst_rs,2); ?> , 
                     SGST <?php echo number_format($sgst_amount_total,2); ?> , 
                     CGST <?php echo  number_format($cgst_amount_total,2); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>    
    
<div style="width:10%;float:left;">
    <table>
        <tbody>
            <tr>
                <td style="text-align:right;text-rotate:-90;vertical-align:middle;"><p style="font-size:7px;">Conceptualised, Designed, Developed & Managed By Medicalwale.com | +91 2268443322 | +918695886958</p></td>
            </tr>
        </tbody>
    </table>
</div>    
    <!--Swapnali's code end-->
    
    </body>

</html>
