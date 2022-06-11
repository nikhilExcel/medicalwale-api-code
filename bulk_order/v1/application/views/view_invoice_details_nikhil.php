<?function get_number($num)
{ 
$ones = array( 
1 => "One", 
2 => "Two", 
3 => "Three", 
4 => "Four", 
5 => "Five", 
6 => "Six", 
7 => "Seven", 
8 => "Eight", 
9 => "Nine", 
10 => "Ten", 
11 => "Eleven", 
12 => "Twelve", 
13 => "Thirteen", 
14 => "Fourteen", 
15 => "Fifteen", 
16 => "Sixteen", 
17 => "Seventeen", 
18 => "Eighteen", 
19 => "Nineteen" 
); 
$tens = array( 
1 => "Ten",
2 => "Twenty", 
3 => "Thirty", 
4 => "Forty", 
5 => "Fifty", 
6 => "Sixty", 
7 => "Seventy", 
8 => "Eighty", 
9 => "Ninety" 
); 
$hundreds = array( 
"Hundred", 
"Thousand", 
"Million", 
"Billion", 
"Trillion", 
"Quadrillion" 
); //limit t quadrillion

$num = number_format($num,2,".",","); 
$num_arr = explode(".",$num); 
$wholenum = $num_arr[0]; 
$decnum = $num_arr[1]; 
$whole_arr = array_reverse(explode(",",$wholenum)); 

krsort($whole_arr); 
$rettxt = ""; 



foreach($whole_arr as $key => $i){ 
    
 
if($i < 20){ 
@$rettxt .= $ones[$i]; 
}elseif($i < 100){ 
    $i=ltrim($i, '0');  
 
@$rettxt .= $tens[substr($i,0,1)]; 
@$rettxt .= " ".$ones[substr($i,1,1)]; 
}else{ 
@$rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
@$rettxt .= " ".$tens[substr($i,1,1)]; 
@$rettxt .= " ".$ones[substr($i,2,1)]; 
} 
if($key > 0){ 
$rettxt .= " ".$hundreds[$key]." "; 
} 
} 
if($decnum > 0){ 
$rettxt .= ", "; 
if($decnum < 20){ 
@$rettxt .= $ones[$decnum]; 
}elseif($decnum < 100){ 
@$rettxt .= $tens[substr($decnum,0,1)]; 
@$rettxt .= " ".$ones[substr($decnum,1,1)]." paise"; 
} 
} 
return $rettxt; 
} 


 ?> 
<!DOCTYPE html>
<html>
<head>

	<style type="text/css">

table {
	border-spacing: 0;
	border-collapse: collapse
}

td,
th {
	padding: 0
}


button,
input,
select,
textarea {
	font-family: inherit;
	font-size: inherit;
	line-height: inherit
}

a {
	color: #337ab7;
	text-decoration: none
}

a:focus,
a:hover {
	color: #23527c;
	text-decoration: underline
}

a:focus {
	outline: 5px auto -webkit-focus-ring-color;
	outline-offset: -2px
}

img {
	vertical-align: middle
}

.img-rounded {
	border-radius: 6px
}

.img-thumbnail {
	display: inline-block;
	max-width: 100%;
	height: auto;
	padding: 4px;
	line-height: 1.42857143;
	background-color: #fff;
	border: 1px solid #ddd;
	border-radius: 4px;
	-webkit-transition: all .2s ease-in-out;
	-o-transition: all .2s ease-in-out;
	transition: all .2s ease-in-out
}

.img-circle {
	border-radius: 50%
}

hr {
	margin-top: 20px;
	margin-bottom: 20px;
	border: 0;
	border-top: 1px solid #eee
}

.h1,
.h2,
.h3,
.h4,
.h5,
.h6,
h1,
h2,
h3,
h4,
h5,
h6 {
	font-family: inherit;
	font-weight: 500;
	line-height: 1.1;
	color: inherit
}

.h1 .small,
.h1 small,
.h2 .small,
.h2 small,
.h3 .small,
.h3 small,
.h4 .small,
.h4 small,
.h5 .small,
.h5 small,
.h6 .small,
.h6 small,
h1 .small,
h1 small,
h2 .small,
h2 small,
h3 .small,
h3 small,
h4 .small,
h4 small,
h5 .small,
h5 small,
h6 .small,
h6 small {
	font-weight: 400;
	line-height: 1;
	color: #777
}

.h1,
.h2,
.h3,
h1,
h2,
h3 {
	margin-top: 20px;
	margin-bottom: 10px
}

.h1 .small,
.h1 small,
.h2 .small,
.h2 small,
.h3 .small,
.h3 small,
h1 .small,
h1 small,
h2 .small,
h2 small,
h3 .small,
h3 small {
	font-size: 65%
}

.h4,
.h5,
.h6,
h4,
h5,
h6 {
	margin-top: 10px;
	margin-bottom: 10px
}

.h4 .small,
.h4 small,
.h5 .small,
.h5 small,
.h6 .small,
.h6 small,
h4 .small,
h4 small,
h5 .small,
h5 small,
h6 .small,
h6 small {
	font-size: 75%
}

.h1,
h1 {
	font-size: 36px
}

.h2,
h2 {
	font-size: 30px
}

.h3,
h3 {
	font-size: 24px
}

.h4,
h4 {
	font-size: 18px
}

.h5,
h5 {
	font-size: 14px
}

.h6,
h6 {
	font-size: 12px
}

p {
	margin: 0 0 10px
}

.small,
small {
	font-size: 85%
}

.mark,
mark {
	padding: .2em;
	background-color: #fcf8e3
}

.text-left {
	text-align: left
}

.text-right {
	text-align: right
}

.text-center {
	text-align: center
}

.text-justify {
	text-align: justify
}

.text-nowrap {
	white-space: nowrap
}

.text-lowercase {
	text-transform: lowercase
}

.text-uppercase {
	text-transform: uppercase
}

.text-capitalize {
	text-transform: capitalize
}

.text-muted {
	color: #777
}

.text-primary {
	color: #337ab7
}

table {
	background-color: transparent
}

caption {
	padding-top: 8px;
	padding-bottom: 8px;
	color: #777;
	text-align: left
}

th {
	text-align: left
}

.table {
	width: 100%;
	max-width: 100%;
	margin-bottom: 20px
}

.table>tbody>tr>td,
.table>tbody>tr>th,
.table>tfoot>tr>td,
.table>tfoot>tr>th,
.table>thead>tr>td,
.table>thead>tr>th {
	padding: 8px;
	line-height: 1.42857143;
	vertical-align: top;
	border-top: 1px solid #ddd
}

.table>thead>tr>th {
	vertical-align: bottom;
	border-bottom: 2px solid #ddd
}

.table>caption+thead>tr:first-child>td,
.table>caption+thead>tr:first-child>th,
.table>colgroup+thead>tr:first-child>td,
.table>colgroup+thead>tr:first-child>th,
.table>thead:first-child>tr:first-child>td,
.table>thead:first-child>tr:first-child>th {
	border-top: 0
}

.table>tbody+tbody {
	border-top: 2px solid #ddd
}

.table .table {
	background-color: #fff
}

.table-condensed>tbody>tr>td,
.table-condensed>tbody>tr>th,
.table-condensed>tfoot>tr>td,
.table-condensed>tfoot>tr>th,
.table-condensed>thead>tr>td,
.table-condensed>thead>tr>th {
	padding: 5px
}

.table-bordered {
	border: 1px solid #ddd
}

.table-bordered>tbody>tr>td,
.table-bordered>tbody>tr>th,
.table-bordered>tfoot>tr>td,
.table-bordered>tfoot>tr>th,
.table-bordered>thead>tr>td,
.table-bordered>thead>tr>th {
	border: 1px solid #ddd
}

.table-bordered>thead>tr>td,
.table-bordered>thead>tr>th {
	border-bottom-width: 2px
}

.table-striped>tbody>tr:nth-of-type(odd) {
	background-color: #f9f9f9
}

.table-hover>tbody>tr:hover {
	background-color: #f5f5f5
}

table col[class*=col-] {
	position: static;
	display: table-column;
	float: none
}

table td[class*=col-],
table th[class*=col-] {
	position: static;
	display: table-cell;
	float: none
}

.table>tbody>tr.active>td,
.table>tbody>tr.active>th,
.table>tbody>tr>td.active,
.table>tbody>tr>th.active,
.table>tfoot>tr.active>td,
.table>tfoot>tr.active>th,
.table>tfoot>tr>td.active,
.table>tfoot>tr>th.active,
.table>thead>tr.active>td,
.table>thead>tr.active>th,
.table>thead>tr>td.active,
.table>thead>tr>th.active {
	background-color: #f5f5f5
}

.table-hover>tbody>tr.active:hover>td,
.table-hover>tbody>tr.active:hover>th,
.table-hover>tbody>tr:hover>.active,
.table-hover>tbody>tr>td.active:hover,
.table-hover>tbody>tr>th.active:hover {
	background-color: #e8e8e8
}

.table>tbody>tr.success>td,
.table>tbody>tr.success>th,
.table>tbody>tr>td.success,
.table>tbody>tr>th.success,
.table>tfoot>tr.success>td,
.table>tfoot>tr.success>th,
.table>tfoot>tr>td.success,
.table>tfoot>tr>th.success,
.table>thead>tr.success>td,
.table>thead>tr.success>th,
.table>thead>tr>td.success,
.table>thead>tr>th.success {
	background-color: #dff0d8
}

.table-hover>tbody>tr.success:hover>td,
.table-hover>tbody>tr.success:hover>th,
.table-hover>tbody>tr:hover>.success,
.table-hover>tbody>tr>td.success:hover,
.table-hover>tbody>tr>th.success:hover {
	background-color: #d0e9c6
}

.table>tbody>tr.info>td,
.table>tbody>tr.info>th,
.table>tbody>tr>td.info,
.table>tbody>tr>th.info,
.table>tfoot>tr.info>td,
.table>tfoot>tr.info>th,
.table>tfoot>tr>td.info,
.table>tfoot>tr>th.info,
.table>thead>tr.info>td,
.table>thead>tr.info>th,
.table>thead>tr>td.info,
.table>thead>tr>th.info {
	background-color: #d9edf7
}

.table-hover>tbody>tr.info:hover>td,
.table-hover>tbody>tr.info:hover>th,
.table-hover>tbody>tr:hover>.info,
.table-hover>tbody>tr>td.info:hover,
.table-hover>tbody>tr>th.info:hover {
	background-color: #c4e3f3
}

.table>tbody>tr.warning>td,
.table>tbody>tr.warning>th,
.table>tbody>tr>td.warning,
.table>tbody>tr>th.warning,
.table>tfoot>tr.warning>td,
.table>tfoot>tr.warning>th,
.table>tfoot>tr>td.warning,
.table>tfoot>tr>th.warning,
.table>thead>tr.warning>td,
.table>thead>tr.warning>th,
.table>thead>tr>td.warning,
.table>thead>tr>th.warning {
	background-color: #fcf8e3
}

.table-hover>tbody>tr.warning:hover>td,
.table-hover>tbody>tr.warning:hover>th,
.table-hover>tbody>tr:hover>.warning,
.table-hover>tbody>tr>td.warning:hover,
.table-hover>tbody>tr>th.warning:hover {
	background-color: #faf2cc
}

.table>tbody>tr.danger>td,
.table>tbody>tr.danger>th,
.table>tbody>tr>td.danger,
.table>tbody>tr>th.danger,
.table>tfoot>tr.danger>td,
.table>tfoot>tr.danger>th,
.table>tfoot>tr>td.danger,
.table>tfoot>tr>th.danger,
.table>thead>tr.danger>td,
.table>thead>tr.danger>th,
.table>thead>tr>td.danger,
.table>thead>tr>th.danger {
	background-color: #f2dede
}

.table-hover>tbody>tr.danger:hover>td,
.table-hover>tbody>tr.danger:hover>th,
.table-hover>tbody>tr:hover>.danger,
.table-hover>tbody>tr>td.danger:hover,
.table-hover>tbody>tr>th.danger:hover {
	background-color: #ebcccc
}


		body,div,table,thead,tbody,tfoot,tr,th,td,p { font-family:"Calibri"; font-size:10px }
		a.comment-indicator:hover + comment { background:#ffd; position:absolute; display:block; border:1px solid black; padding:0.5em;  } 
		a.comment-indicator { background:red; display:inline-block; border:1px solid black; width:0.5em; height:0.5em;  } 
		comment { display:none;  } 
		.no-border-top {
		    border-top:1px solid #fff!important;
		}
		.no-border-bottom {
		    border-bottom:1px solid #fff!important;
		}
		.no-border-left {
		    border-left:1px solid #fff!important;s
		}
		.no-border-right {
		    border-right:1px solid #fff!important;
		}
		.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		    padding:1px;
		}
		h3,h4,h5 {
		    margin-top:5px;
		    margin-bottom:5px;
		}
		body {
		    padding:1%;
		    width:100%;
            text-transform: capitalize;
		}
		.table {
    		   margin-bottom:3px; 
    		}
    		@page {
              size: A4;
              margin: 0;
            }
		@media print {
		    .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: #fff!important;
                page-break-after: always;
              }
		    .display-none{
		        display:none;
		    }
		    body {
		        font-size:x-small!important;
		    }
		    .no-border-top {
    		    border-top:0px solid #fff!important;
    		}
    		.no-border-bottom {
    		    border-bottom:0px solid #fff!important;
    		}
    		.no-border-left {
    		    border-left:0px solid #fff!important;
    		}
    		.no-border-right {
    		    border-right:0px solid #fff!important;
    		}
    		
		}
	</style>
	
</head>

<body>
    <h4><center><b>Tax Invoice</b></center></h4>
    <table class="table table-bordered">
        <tbody>
          <tr>
            <td style="text-align: center;padding: 3px 0;border-right:1px solid #fff!important;" rowspan="5" colspan="1"
            ><img style="width: 80px;" src="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/<?php echo $edit->profie_picture;?>">
            </td>  
            <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="4"><h4><b><?php echo $edit->name;?></b></h4>
            </td>
    		<td colspan="7" >Invoice No :<b style="font-size:18px;"><?php echo $order_detail['invoice_type'];?></b>
    		</td>
    		<td colspan="7">Dated :<span><b><?php $s = strtotime($order_detail['order_date']); echo $date = date('d/m/Y', $s);?></b></span>
    		</td>
          </tr>
         
          <tr>
            <td class="no-border-top no-border-bottom" style="border-top:1px solid #fff!important;border-bottom:1px solid #fff!important;" colspan="4">
                <?php echo $edit->address;?>,
               
                <?php echo $edit->landmark;?>,<?php echo $edit->city;?>
            </td>
    		<td colspan="7">Delivery Note
    		  
    		    <span><b><?php echo $order_detail['order_id'];?></b></span>
    		</td>
    		<td colspan="7">Mode/Terms of Payment</td>
          </tr>
          <tr>
            <td class="no-border-top no-border-bottom" style="border-top:1px solid #fff!important;border-bottom:1px solid #fff!important;" colspan="4">License No:<?php echo $edit->license_no;?>
            </td>
    		<td colspan="7">Supplier's Ref.
    		<br>
    		<!--<//?php echo $order_detail['invoice_type'];?>-->
    		</td>
    		<td colspan="7">Other Reference(s)</td>
          </tr>
          <tr>
            <td class="no-border-top no-border-bottom" style="border-top:1px solid #fff!important;border-bottom:1px solid #fff!important;" colspan="4">
                GSTIN/UIN: <b><?php echo $edit->gst_number;?></b>
                
                <br>
                State  :  <?php echo $edit->state;?>, Code : <?php echo $edit->party_code;?>
            </td>
    		<td colspan="7">Buyer's Order No.
    		    <br>
    		    <span><b>    		    <?php echo $order_detail['buyer'];?></b></span>

    		</td>
    		<td colspan="4">Dated</td>
          </tr>
          <tr>
            <td colspan="4">
                CIN: <?php echo $edit->cin_no;?>
                <br>
                E-Mail : <?php echo $edit->email;?>
            </td>
    		<td colspan="7">Despatch Document No.
    		    <br>
    		    
    		</td>
    		<td colspan="4">
    		    Delivery Note Date<br>
    		    <span><b><?php $s = strtotime($order_detail['order_date']); echo $date = date('d/m/Y', $s);?></b></span>
    		    
    		</td>
          </tr>
          <tr>
            <td colspan="5">
                Buyer<br>
                <strong><?php echo $order_detail['name'];?></strong><br>
	            <strong><?php echo $order_detail['address1'];?>&nbsp;<?php echo $order_detail['address2'];?>&nbsp;<?php echo $order_detail['city'];?>&nbsp;<?php echo $order_detail['state'];?></strong> 
                <strong><?php echo $order_detail['pincode'];?></strong><br>
                <strong>Mob:<?php echo $order_detail['mobile'];?></strong>
            </td>
    		<td colspan="5">Despatched through
    		    <br>
    		    
    		</td>
    		<td colspan="7">
    		    Destination<br>
    		    
    		</td>
          </tr>
          <tr>
            <td colspan="5">
                GSTIN/UIN:<br>
                <?echo $order_detail['gst_no']; ?> 
            </td>
    		<td colspan="8">Terms of Delivery
          </tr>
          
          <!--taxable details-->
          <tr style="text-align:center;">
              <td>Sr No.</td>
              <td style="width:33%;">
                  Description of
                  Goods and Services
              </td>
              <td>
                  HSN / SAC
              </td>
              <td>
                  Packing
              </td>
              <td>
                  Date of Exp
              </td>
              <td>
                  Batch No.
              </td>
              
              <td>
                  MRP (INR)
              </td>
              <td>
                  Rate / Nos
              </td>
              <td>
                  Quantity / Nos
              </td>
              <td>
                  Product Values (INR)
              </td>
              <td>
                  Discount (%)
              </td>
              <td>
                  Discount (INR)
              </td>
              <td>
                  Price after Discount
              </td>
               <td>
                 GST (%)
              </td>
              <td>
                 GST (INR)
              </td>
              <td>
                Net value (INR)
              </td>
          </tr>
        <?php 
            $gross = 0;
            $total_discount = 0;
            $tax_amount = 0;
            $i = 1;
            $total_quan = 0;
            $taxable_amount5 = 0;
            $taxable_amount12 = 0;
            $taxable_amount18 = 0;
            $taxable_amount28 = 0;
            $taxable_amount0 = 0;
	        $user_id=$user_id;
            $sub_total=0;
            $qty_t=0;
            $discount_total=0;
            $gst_amount=0;
            $total_gst_fin=0;
            $order=$order_detail['order_id'];
            $i=1;
            $final_gst1=0;
            $this->db->select('*');
            $this->db->from('inven_distrub_invoice_details');
            $this->db->where('order_id', $order);
            $cat = $this->db->get()->result_array();
            $dstock_product='dstock_'.$user_id;
            foreach($cat as $a){
            $pid=$a['product_id'];
            $this->db->select('*');
            $this->db->from($dstock_product);
            $this->db->where('product_id', $pid);
            $cat1 = $this->db->get()->row_array();
            // item rate
            $item_rate = round(($a['product_mrp']/(1+ (0.01 *$a['gst']))),2);
            // product value
            $product_value = round($a['product_qty']*($a['product_mrp']/(1+ (0.01 *$a['gst']))),2);
            // gross
            $gross += $product_value;
            // discount in rupees
            $discount_rupees = round($a['product_qty']*($a['product_mrp']/(1+ (0.01 *$a['gst']))),2)*round(($a['discount']/100),2);
            // total discount
            $total_discount += $discount_rupees;
            // price after discount
            $price_after_discount = $product_value - $discount_rupees;
            // gst in rupess
            $gst_rupees = round((($product_value-$discount_rupees)*$a['gst'])/100,2);
            // tax amount
            $tax_amount +=$gst_rupees;
            // net value
            $net_value = round($price_after_discount+$gst_rupees,2);
            // total quantity
            $total_quan += $a['product_qty'];
            
            if($a['gst'] == 5){
                $taxable_amount5 += $price_after_discount;
            } elseif($a['gst'] == 12){
                $taxable_amount12 += $price_after_discount;
            } elseif($a['gst'] == 18){
                $taxable_amount18 += $price_after_discount;
            } elseif($a['gst'] == 28){
                $taxable_amount28 += $price_after_discount;
            } else {
                $taxable_amount0 = 0;
            }
             
        ?>
          <tr style="text-align:center;">
              <td><?php echo $i;?></td>
              <td style="text-align:left;"><b><?php echo $cat1['product_name'];?></b></td>
              <td><?php echo $cat1['hsncode'];?></td>
              <td><?php echo $cat1['pack'];?>&nbsp;&nbsp;&nbsp;<?php echo $cat1['pack_unit'];?></td>
              <td><?php echo $cat1['expiry_date'];?></td>
              <td><?php echo $cat1['batch_no'];?></td>
              <td><?php echo $a['product_mrp'];?></td>
              <td><?php echo $item_rate;?></td>
              <td><?php echo $a['product_qty'];?></td>
              <td><?php echo $product_value;?></td>
              <td><?php echo $a['discount'];?></td>
              <td><?php echo $discount_rupees;?></td>
              <td><?php echo $price_after_discount;?></td>
              <td><?php echo $a['gst'];?></td>
              <td><?php echo $gst_rupees;?></td>
              <td><b><?php echo $net_value;?></b></td>
          </tr>
          
          <?php 
          $i++;
          } ?>
          
          <!---->
          
        </tbody>
    </table>
    
    <!--second table-->
          <table class="table table-bordered">
            <tbody>
                <?php 
              $grand_total = 0;
               $grand_total = round($gross-$total_discount+$tax_amount,2);?>
        
              <?php if($order_detail['state'] =='Maharashtra' || $order_detail['state'] =='maharashtra')  { ?>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">Total items </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $i-1;?> </td>
                  <td style="text-align:center;">GST </td>
                  <td style="text-align:center;">Taxable amount </td>
                  <td style="text-align:center;">SGST </td>
                  <td style="text-align:center;">SGST Amt</td>
                  <td style="text-align:center;">CGST </td>
                  <td style="text-align:center;">CGST Amt</td>
                  <td style="text-align:center;">IGST </td>
                  <td style="text-align:center;width:4%;">IGST Amt</td>
                  <td colspan="3">Gross : <?php echo $gross;?></td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">Total Qty. </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $total_quan; ?> </td>
                  <td style="text-align:center;">5%</td>
                  <td style="text-align:center;"><?php 
                  $main_taxable_amt5 = ($taxable_amount5*5/100);
                  echo round($taxable_amount5,2); ?> </td>
                  <td style="text-align:center;">2.50% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt5/2,2); ?></td>
                  <td style="text-align:center;">2.50% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt5/2,2); ?></td>
                  <td style="text-align:center;">5% </td>
                  <td style="text-align:center;">0</td>
                  <td colspan="3">Discount : <?php echo $total_discount; ?></td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">sale value </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $gross; ?> </td>
                  <td style="text-align:center;">12%</td>
                  <td style="text-align:center;"><?php 
                  $main_taxable_amt12 = ($taxable_amount12*12/100);
                  echo round($taxable_amount12,2); ?> </td>
                  <td style="text-align:center;">6% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt12/2,2); ?> </td>
                  <td style="text-align:center;">6% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt12/2,2); ?> </td>
                  <td style="text-align:center;">12% </td>
                  <td style="text-align:center;">0</td>
                  <td colspan="3">scheme discount : 0</td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">scheme disc </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : 0 </td>
                  <td style="text-align:center;">18%</td>
                  <td style="text-align:center;"><?php 
                  $main_taxable_amt18 = ($taxable_amount18*18/100);
                  echo round($taxable_amount18,2); ?></td>
                  <td style="text-align:center;">9% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt18/2,2); ?></td>
                  <td style="text-align:center;">9% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt18/2,2); ?></td>
                  <td style="text-align:center;">18% </td>
                  <td style="text-align:center;">0</td>
                  <td colspan="3">Tax amount : <?php echo $tax_amount; ?> </td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">Cash disc </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $total_discount; ?> </td>
                  <td style="text-align:center;">28%</td>
                  <td style="text-align:center;"><?php 
                  $main_taxable_amt28 = ($taxable_amount28*28/100);
                  echo round($taxable_amount28,2); ?></td>
                  <td style="text-align:center;">14% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt28/2,2); ?></td>
                  <td style="text-align:center;">14% </td>
                  <td style="text-align:center;"><?php echo round($main_taxable_amt28/2,2); ?></td>
                  <td style="text-align:center;">28% </td>
                  <td style="text-align:center;">0</td>
                  <td colspan="3">MW Discount : 0</td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">MW disc </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : 0 </td>
                  <td style="text-align:center;">0%</td>
                  <td style="text-align:center;">0 </td>
                  <td style="text-align:center;">0% </td>
                  <td style="text-align:center;">0</td>
                  <td style="text-align:center;">0% </td>
                  <td style="text-align:center;">0</td>
                  <td style="text-align:center;">0% </td>
                  <td style="text-align:center;">0</td>
                  <td colspan="3"><h5><b>grand total : <?php echo $grand_total;?></b></h5></td>
              </tr>
              <tr>
                  <td class="no-border-right" style="border-right:1px solid #fff!important;">Total GST </td>
                  <td class="no-border-left" style="border-left:1px solid #fff!important;"> : <?php echo $tax_amount; ?> </td>
                  <td class="no-border-left no-border-bottom no-border-right" style="border-left:1px solid #fff!important;border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;" colspan="11">  </td>
              </tr>
              <?php } else { ?>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">Total items </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $i-1;?> </td>
                  <td>GST </td>
                  <td>Taxable amount </td>
                  <td>SGST </td>
                  <td>SGST Amt</td>
                  <td>CGST </td>
                  <td>CGST Amt</td>
                  <td>IGST </td>
                  <td>IGST Amt</td>
                  <td colspan="3">Gross : <?php echo $gross;?></td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">Total Quantity </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $total_quan; ?> </td>
                  <td>5%</td>
                  <td><?php echo round($taxable_amount5,2); ?> </td>
                  <td>2.50% </td>
                  <td>0</td>
                  <td>2.50% </td>
                  <td>0</td>
                  <td>5% </td>
                  <td><?php echo round($taxable_amount5,2); ?> </td>
                  <td colspan="3">Discount : <?php echo $total_discount; ?></td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">sale value </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $gross; ?> </td>
                  <td>12%</td>
                  <td> <?php echo round($taxable_amount12,2); ?></td>
                  <td>6% </td>
                  <td>0</td>
                  <td>6% </td>
                  <td>0 </td>
                  <td>12% </td>
                  <td><?php echo round($taxable_amount12,2); ?></td>
                  <td colspan="3">scheme discount : 0</td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">scheme disc </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : 0 </td>
                  <td>18%</td>
                  <td><?php echo round($taxable_amount18,2); ?></td>
                  <td>9% </td>
                  <td>0</td>
                  <td>9% </td>
                  <td>0</td>
                  <td>18% </td>
                  <td><?php echo round($taxable_amount18,2); ?></td>
                  <td colspan="3">Tax amount : <?php echo $tax_amount; ?> </td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">Cash disc </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : <?php echo $total_discount; ?> </td>
                  <td>28%</td>
                  <td><?php echo round($taxable_amount28,2); ?></td>
                  <td>14% </td>
                  <td>0</td>
                  <td>14% </td>
                  <td>0</td>
                  <td>28% </td>
                  <td><?php echo round($taxable_amount28,2); ?></td>
                  <td colspan="3">MW Discount : 0</td>
              </tr>
              <tr>
                  <td class="no-border-right no-border-bottom" style="border-right:1px solid #fff!important;border-bottom:1px solid #fff!important;">MW disc </td>
                  <td class="no-border-left no-border-bottom" style="border-left:1px solid #fff!important;border-bottom:1px solid #fff!important;"> : 0 </td>
                  <td>0%</td>
                  <td>0 </td>
                  <td>0% </td>
                  <td>0</td>
                  <td>0% </td>
                  <td>0</td>
                  <td>0% </td>
                  <td>0</td>
                  <td colspan="3"><h4><b>grand total : <?php echo $grand_total;?></b></h4></td>
              </tr>
              <tr>
                  <td class="no-border-right" style="border-right:1px solid #fff!important;">Total GST </td>
                  <td class="no-border-left" style="border-left:1px solid #fff!important;"> : <?php echo $tax_amount; ?> </td>
                  <td class="no-border-left no-border-bottom no-border-right" colspan="11">  </td>
              </tr>      
              <?php } ?>
            </tbody>
          </table>
    
    <!--third table-->
    <table  class="table table-bordered">
        <tbody>
            <tr>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="5">Tax Amount (in words)  : <b><?echo ucfirst(get_number(@$tax_amount));?></b> </td>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="8"> </td>
          </tr>
          <tr>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="5">Company's PAN : <b><?php echo $edit->pan_no;?></b></td>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="8"> Company's Bank Details </td>
          </tr>
          <tr>
              <td class=""colspan="5"></td>
              <td class="no-border-bottom no-border-right" style="border-bottom:1px solid #fff!important;border-right:1px solid #fff!important;" colspan="2"> Bank Name </td>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="6"> <b> : <?php echo $edit->bank_name;?></b></b> </td>
          </tr>
          <tr>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="5">We declare that this invoice shows the actual price of the goods described </td>
              <td class="no-border-bottom no-border-right" style="border-bottom:1px solid #fff!important;border-right:1px solid #fff!important;" colspan="2"> A/c No.</td>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="6"> <b> : <?php echo $edit->account_no;?></b> </td>
          </tr>
          <tr>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="5">and that all particulars are true and correct.</td>
              <td class="no-border-right" style="border-right:1px solid #fff!important;" colspan="2"> Branch & IFS Code</td>
              <td class="" colspan="6"> <b> : <?php echo $edit->branch;?> & <?php echo $edit->ifsc_code;?></b> </td>
          </tr>
          <tr>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="5"></td>
              <td class="no-border-bottom" style="border-bottom:1px solid #fff!important;" colspan="8"> <b>for <?php echo $edit->name;?></b> </td>
          </tr>
          <tr>
              <td class=""colspan="5"></td>
              <td class="no-border-right" style="border-right:1px solid #fff!important;" colspan="4"> </td>
              <td class="text-center" colspan="4"> Authorised Signatory</td>
          </tr>
        </tbody>
    </table>
    <div><center>SUBJECT TO MUMBAI JURISDICTION</center></div>
    <div><center><i>This is a Computer Generated Invoice</i></center></div>
       
</body>

</html>
