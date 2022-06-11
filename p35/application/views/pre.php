<?php
// echo"<pre>";
// print_r($dischargedata);
// exit();

    $str = $dischargedata['details']['booking_date'];
    $ss=explode(" ",$str);
    $date2 = $ss[0];
    $d2 = date("d/m/Y", strtotime($date2));
    $d2;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<title>Medicalwale </title>
	<!-- META TAGS -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- FAV ICON(BROWSER TAB ICON) -->
	<link rel="shortcut icon" href="images/fav.ico" type="image/x-icon">
	<!-- GOOGLE FONT -->
	<link href="https://fonts.googleapis.com/css?family=Poppins%7CQuicksand:500,700" rel="stylesheet">
	<!-- FONTAWESOME ICONS -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- ALL CSS FILES -->
	<link href="https://doctor.medicalwale.com/assets/css/materialize.css" rel="stylesheet">
	<link href="https://doctor.medicalwale.com/assets/css/style_invoice.css" rel="stylesheet">
	<link href="https://doctor.medicalwale.com/assets/css/bootstrap_invoice.css" rel="stylesheet" type="text/css" />
	<!-- RESPONSIVE.CSS ONLY FOR MOBILE AND TABLET VIEWS -->
	<link href="https://doctor.medicalwale.com/assets/css/responsive.css" rel="stylesheet">
	
	
	<style>
    .tz-db-table table tr th {
        font-size: 10px;
        color: #333;
        text-align: -webkit-center;
        padding: 0px 8px 0px 8px;
        border: 1px solid #2f2f2f;
    }
    .tz-db-table table .dosage tr td {
        font-size: 15px;
        color: #333;
        padding: 1px 14px 0px 34px;
        width: 14px!important;
        border: solid 1px #fff;
        /* width: 30%; */
    }
    h5, .h5 {
        font-size: 16px;
    }
    table.bordered > thead > tr, table.bordered > tbody > tr {
        border-bottom: 1px solid #ddd;
    }
    .tz-db-table table tr td {
        font-size: 9px;
        color: #333;
        padding: 14px 14px 14px 14px;
        width: 14px!important;
        border: solid 1px #ddd;
        /*width: 30%;*/
    }
    .invoice-1-add-right {
    
        background: #ffffff;}
    .bot{
    margin-bottom: 10px;
    }
    .bottom{
        margin-bottom: 3px;
    }
    .bottom1{
        margin-bottom: 50px;
    }
    #background1{
    position:absolute;
    z-index:0;
    background:white;
    display:block;
    min-height:50%; 
    min-width:50%;
    color:yellow;
}
#bg-text
{
    color:#d3d3d394;
    font-size:120px;
    margin-top: 388px;
    transform:rotate(300deg);
    -webkit-transform:rotate(300deg);
}

    
.tableRepeat{
 
    overflow-y: auto;
    overflow-x: auto;
}

@media print{
    .butt{
        display:none;
        
    }
    
    
    
    #background1{
    position:absolute;
    z-index:0;
    opacity: 0.2;
    background:white;
    display:block;
    min-height:50%; 
    min-width:50%;
    color:yellow;
}
#bg-text
{
    color:#fff !important;
    font-size:120px;
    margin-top: 388px;
    transform:rotate(300deg);
    -webkit-transform:rotate(300deg);
}
    
    
    .tz-db-table table tr th {
        font-size: 10px;
        color: #333;
        text-align: -webkit-center;
        padding: 0px 8px 0px 8px;
        border: 1px solid #2f2f2f;
    }
    .tz-db-table table .dosage tr td {
        font-size: 15px;
        color: #333;
        padding: 1px 14px 0px 34px;
        width: 14px!important;
        border: solid 1px #fff;
        /* width: 30%; */
    }
    h5, .h5 {
        font-size: 16px;
    }
    table.bordered > thead > tr, table.bordered > tbody > tr {
        border-bottom: 1px solid #ddd;
    }
    .tz-db-table table tr td {
        font-size: 9px;
        color: #333;
        padding: 14px 14px 14px 14px;
        width: 14px!important;
        border: solid 1px #ddd;
        /*width: 30%;*/
    }
    .invoice-1-add-right {
    
        background: #ffffff;}
    .bot{
    margin-bottom: 10px ;
    }
    .bottom{
        margin-bottom: 3px;
    }
    .bottom1{
        margin-bottom: 50px;
    }
    
    .invoice-1-add-left{
        
        float: left;
    }
    
    .tableRepeat{
 
    overflow-y: auto;
    overflow-x: auto;
}
.col-md-12, .col-lg-12 {
    position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;

}
.col-lg-12 {
    float: left;
}
.col-lg-6 {
    width: 50%;
}
.col-lg-12 {
    width: 100%;
}

img {
    vertical-align: middle;
}

img {
    margin-top: 8px;
    border: 0;
    /*width:100%;*/
    height: 100px;
}
.auth{
    border-top: 0px solid #636363;
    padding-top: 10px;
    margin-bottom: 0px;
    text-align: right;
    bottom: 0;
    /*position:absolute;*/
    /*margin-left: 38%;*/
    /*margin-right: 38%;*/
    bottom:-25pc;
}
.col-lg-2, .col-md-2{
    width:25%;
    float: left;
}
.col-lg-10, .col-md-10{
    width:75%;
    float: left;
    /*line-height: 8px;*/
}
/*b, strong {*/
/*    font-weight: bolder;*/
/*    color: #333333;*/
/*}*/
.tz-db-table table tr th {
    font-size: 10px;
    color: #333;
    text-align: -webkit-center;
    padding: 0px 8px 0px 8px;
    border: 1px solid #2f2f2f;
    /*line-height: 17px;*/
}
.tz-db-table table tr td {
    font-size: 9px;
    color: #333;
    padding: 5px 14px 5px 14px;
    width: 14px!important;
    border: solid 1px #333;
    /* width: 30%; */
    /*line-height: 17px;*/
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}
footer {
    position: fixed;
    bottom: -19mm;
    float:right;
    right:0;
    text-align:right;
    color: #9a9a9a;
    font-weight: 300;
    padding: 50px 0 50px;
  }
  .col-md-6{
      width: 50%;
      float: left;
      position: relative;
    min-height: 1px;
    padding-right: 15px;
    padding-left: 15px;
  }
  h5, .h5 {
        font-size: 11px;
    }
    p, a, li, span, label, tr, td, th, input {
        color: #636363;
        font-size: 10px!important;
        font-family: 'Poppins', sans-serif;
        font-weight: 300;
    }
}
/*b, strong {*/
/*    font-weight: bolder;*/
/*    color: #333333;*/
/*}*/
.auth{
    border-top: 0px solid #636363;
    padding-top: 10px;
    margin-bottom: 0px;
    text-align: right;
    bottom: 0;
    /*position:absolute;*/
    /*margin-left: 38%;*/
    /*margin-right: 38%;*/
    bottom:-0pc;
}
.tz-db-table table tr th {
    font-size: 10px;
    color: #333;
    text-align: -webkit-center;
    padding: 0px 8px 0px 8px;
    border: 1px solid #2f2f2f;
    /*line-height: 17px;*/
}
.tz-db-table table tr td {
    font-size: 9px;
    color: #333;
    padding: 0px 14px 0px 14px;
    width: 14px!important;
    border: solid 1px #333;
    /* width: 30%; */
    /*line-height: 17px;*/
}

.invoice-print {
    padding: 8px;
}
@page {
  /*size: A4;*/
  /*margin: 11mm 6mm 11mm 6mm;*/
  margin: 15mm 6mm 15mm 6mm;
}
/*.goog-logo-link{*/
/*    display:none;*/
/*}*/
/*.goog-te-gadget span{*/
/*    display:none;*/
/*}*/
h5, .h5 {
    font-size: 11px;
}
p, a, li, span, label, tr, td, th, input {
    color: #636363;
    font-size: 10px!important;
    font-family: 'Poppins', sans-serif;
    font-weight: 300;
}
</style>


<!-- added by nikhil-->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script type="text/javascript" src="pdf-libs/js_html2canvas_jquery.plugin.html2canvas.js"></script>
    
    
<!-- added by nikhil ends here-->



</head>
<script type="text/javascript">
function googleTranslateElementInit() {
new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
}

$(window).scroll(function() {
  if ($(this).scrollTop() > 0) {
    $('#google_translate_element').fadeOut();
  } else {
    $('#google_translate_element').fadeIn();
  }
});
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<body class="text-capitalize">
	<div id="preloader">
		<div id="status">&nbsp;</div>
	</div>
	<!--TOP SEARCH SECTION-->
	<section>
		<div class="tz tz-invo-full" id="targetz">
			<!--CENTER SECTION-->
			<div class="tz-2 tz-invo-full1" >
				<div class="tz-2-com tz-2-main" id="target">
					<div class="db-list-com tz-db-table">
				        <div class="row" style="position: fixed;z-index: 9999;/* background: white; */top: -4px;width: 100%;">
                    	    <div class="col-md-2">
                    	        <div id="google_translate_element" style="background: white;"></div>
                    	    </div>
                    	    
                    	</div>
						<div class="invoice">
						    	   <?php 
						    	 //  print_r($this->session->all_userdata());
    						    	 //  echo '<pre>';
    						    	 //  print_r($dischargedata); 
						    	   ?>
							<div class="invoice-1" id="<?echo $dischargedata['details_new']['booking_id'];?>">
							     <div id="background1">
  <!--<p id="bg-text">Medicalwale.com</p>-->
</div>	                    
                            <?php 
				    	    if($dischargedata['hospital_details']['is_header']=='0'){
				    	        $css = 'display:none;';
				    	        $css1 = 'display:block;';
				    	    }
				    	    else{
				    	        $css = 'display:block;';
				    	        $css1 = 'display:none;';
				    	    }
				    	   ?>
	                            
	                            <div class="col-md-12 col-lg-12" style="margin-bottom: 10px;float:left;width:100%;border-bottom: 1px solid #2f2f2f;<?php echo $css; ?>">
									<div class="col-lg-2 col-md-2" style="margin-bottom: 0;" >
									    <!--<p></p>-->
								
										<p><img class="imgh" src="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/<?echo $dischargedata['hospital_details']['image'];?>" style="width: 100%;height: 98px;object-fit: contain;"></p>
								    </div>
								    <div class="col-lg-10 col-md-10" style="margin-bottom: 0;" >
								        <p><b style="font-size:17px"><?echo $this->session->userdata('uname');?></b></p>
										<p><b>Phone :</b> <?echo $dischargedata['hospital_details']['phone'];?> </p>
										<p><b>Address :</b> <?echo $dischargedata['hospital_details']['address'];?></p>
										<!--<h5 class="bottom">Address : <?echo $dischargedata['hospital_details']['address'];?></h5>-->
									</div>
									
								</div>
				    	        <div class="col-md-12 col-lg-12" style="margin-bottom: 10px;float:left;width:100%;height: 166px;<?php echo $css1; ?>">
									
									
								</div>
<!--								<div class="invoice-1-logo col-md-8 col-lg-8" style="margin-bottom: 0; float:left; width:50%;<?php echo $css; ?>" >-->
<!--									<div class="invoice-1-add-left col-lg-12 col-md-12" style="margin-bottom: 0;" >-->
							
				    	        
<!--										<h3>	<img src="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/<?echo $dischargedata['hospital_details']['image'];?>" style="-->
<!--    width: 20%;-->
<!--">&nbsp<?echo $this->session->userdata('uname');?></h3>-->
										
<!--										<p><b>Address</b> : <?echo $dischargedata['hospital_details']['address'];?></p>-->
										
<!--									</div>-->
									
<!--								</div>-->
<!--								<div class="invoice-1-add col-md-4 col-lg-4" style="margin-bottom: 0; float:left; width:50%;<?php echo $css; ?>">-->
									
<!--									<div class="invoice-1-add-right">-->
    									
<!--										<h5>Contact</h5>-->
<!--										<p><b>Phone</b> : <?echo $dischargedata['hospital_details']['phone'];?> </p>-->
<!--									</div>-->
<!--								</div>-->
								<div class="invoice-1-add-left col-lg-12 col-md-12" style="/*border-top: 1px solid #2f2f2f;*/padding-top: 10px;margin-bottom: 0px;display:none">
								<p style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial">OPD Prescription</p>
								</div>
								<div class="invoice-1-add-left-no-margin col-lg-12 col-md-12" style="/*border-top: 1px solid #2f2f2f;*/padding-top: 0px;margin-bottom: -37px;">
										<div class="invoice-1-add-left col-lg-6 col-md-6" >
										<!--<p>Patient Details</h5>-->
										<p><b>Patient Name :</b> <span style="font-size: 13px;"> <?echo $dischargedata['details']['name'];?></span></p>
										<p><b>Age :</b> <span style="font-size: 13px;"><?echo $dischargedata['details']['age'];?></span></p>
										<p><b>Consulting Doctor :</b> <span style="font-size: 13px;"><?echo $dischargedata['details']['doctor_name'];?></span></p>
													
										</div>
										<div class="invoice-1-add-left col-lg-6 col-md-6" >
										
										<p><b>Sex :</b> <span style="font-size: 13px;"> <?echo $dischargedata['details']['sex'];?></span></p>
										<p><b>Booking Id :</b> <span style="font-size: 13px;"><?echo $dischargedata['details_new']['booking_id'];?></span></p>
										<p><b>Booking Date :</b> <span style="font-size: 13px;"><?echo $d2;?></span></p>
										<p><b>Booking time :</b> <span style="font-size: 13px;"><?echo $dischargedata['details']['booking_time'];?></span></p>
										</div>
								</div>
								
								
									<div class="invoice-1-add-left col-lg-12 col-md-12" style="padding-top: 10px;margin-bottom: 10px;display:none">
										
									<img src="https://medicalwale.com/master_assets/images/rx.png" alt="" style="height: 40px;margin-top: 15px;">		
									
								</div> 
								<!--nikhil_change-->
								<!--<div class="invoice-1-add-left col-lg-12 col-md-12" style="border-top: 1px solid #2f2f2f;padding-top: 10px;margin-bottom: 10px;">-->
								<!--<h5 style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial">Chief Complaint</h5>-->
								<!--</div>-->
								
								
								
								<?php if(!empty($dischargedata['details']['complaints'])) { ?>
										<div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat"  style="margin-bottom: 10px;">
									<table class="">
									<!--<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>-->
										<thead>
											<tr>
											 <!--   <th style="width:10%;border: 1px solid #2f2f2f;"><center><b>Sr No</center></b></th>-->
												<th style="width:20%;border: 1px solid #2f2f2f;">
												    <center><b>Chief Complaint</b></center>
												</th>
												<td style="width:80%;text-align:center;border: 1px solid #2f2f2f;" colspan="3">
												    <!--<center><b>Complaint</b></center> -->
												    <? echo $dischargedata['details']['complaints']; ?>
												</td>
												</tr>
										</thead>
										
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>
								<?php } ?>
								
								<div class="row">
								    <div class="col-md-12" style="margin-top: 5px;margin-bottom: -34px;">
                                       <div class="invoice-1-add-left tableRepeat">     
                                       <table >
                                            <tbody>
                                            <tr style="border: 1px solid #2f2f2f;text-align:center;"  >
        									    <!--<div class="invoice-1-add-left col-lg-12 col-md-12" style="border-top: 1px solid #2f2f2f;padding-top: 10px;margin-bottom: 10px;">-->
        									
        										<td style="padding: 5px;width:20%;border: 1px solid #2f2f2f;"><p><b>Medical History</b> </p></td>
        										
                                            <!--</div>-->
                                            </tr>
                                            <tr style="border: 1px solid #2f2f2f;text-align:center;" >
                                            	<!--<div class="invoice-1-add-left col-lg-12 col-md-12" style="border-top: 1px solid #2f2f2f;padding-top: 10px;">-->
                                <td style="padding: 5px;width:20%;border: 1px solid #2f2f2f;">
                                    
                                <?php if($dischargedata['details']['symptoms']!=''){?>
        					    <p class="bottom1" style="margin-bottom: 0px;padding: 1px 10px 0px 2px;"><b>Symptoms  :</b>
        					        <span style="font-size: 9px;position: relative;padding: 1px 10px 0px 0px;">
        					            <?php 
                                            $sy = explode(',',$dischargedata['details']['symptoms']);
                                            $s = '';
                                            foreach($symptoms as $symptoms){
                                                
                                                if(in_array($symptoms['id'],$sy)){ 
                                                    $s .=  $symptoms['category'].', '; 
                                                }
                                            
                                            } 
                                            echo $s;
                                        ?>
    					            </span>
					            </p>
					            <?php } ?>
                                    
                                    
        						<?php if($dischargedata['details_new']['prescription_note']!=''){?>
        						<p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Prescription Note :</b> 
            						<span style="font-size: 9px;position: absolute;padding: 1px 4px;">
            						    <?echo $dischargedata['details_new']['prescription_note'];?>
        						    </span>
    						    </p>
    						    <?php } ?>
                                
                                <?php if($dischargedata['details']['diagnosis']!=''){?>
								<p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Diagnosis :</b> 
								    <span style="font-size: 9px;position: absolute;padding: 1px 10px 0px 2px;"><?
							            // 			echo $dischargedata['details']['diagnosis'];
										// changes by ghanshyam parihar starts
                                        $d = explode(',',$dischargedata['details']['diagnosis']);
                                        $b = '';
                                        foreach($d as $dd){
                                            $d = $this->Doc_ipd_model->selectExistingDailyReport($dd);
                                           $b .= $d['description'].',';
                                        }
                                        echo rtrim($b,',');
                                        // changes by ghanshyam parihar ends
										?>
									</span>
								</p>
								<?php } ?>
								
								<?php if($dischargedata['details']['investigation']!=''){?>
							    <p class="bottom1"  style="margin-bottom: 0px;padding: 7px 0px;"><b>Investigation :</b> 
							        <span style="font-size: 9px;position: absolute;padding: 1px 10px 0px 2px;"><?
									 	echo $dischargedata['details']['investigation'];
										// changes by ghanshyam parihar starts
                                        $d1 = explode(',',$dischargedata['details']['investigation']);
                                        $b1 = '';
                                        foreach($d1 as $dd){
                                            $d1 = $this->Doc_ipd_model->selectExistingDailyReport($dd);
                                           $b1 .= $d1['description'].',';
                                        }
                                        echo rtrim($b1,',');
                                        // changes by ghanshyam parihar ends
        								?>
    								</span>
							    </p>
							    <?php } ?>
        								<!--	<h5 class="bottom1">Operation : <span style="font-size: 13px;"><?
        										// 		echo $dischargedata['details']['operation'];
        											// changes by ghanshyam parihar starts
                                                                $d2 = explode(',',$dischargedata['details']['operation']);
                                                                $b2 = '';
                                                                foreach($d2 as $dd2){
                                                                    $d2 = $this->Doc_ipd_model->selectExistingDailyReport($dd2);
                                                                   $b2 .= $d2['description'].',';
                                                                }
                                                                echo rtrim($b2,',');
                                                                // changes by ghanshyam parihar ends
        														?></span></h5>-->
        									
        						<?php if($dischargedata['details']['advised']!=''){?>				
								<p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Treatment Advised :</b> 
								    <span style="font-size: 9px;position: absolute;padding: 1px 10px 0px 2px;"><?
								        // 		echo $dischargedata['details']['advise_treatment'];
										// changes by ghanshyam parihar starts
                                        $d3 = explode(',',$dischargedata['details']['advised']);
                                        $b3 = '';
                                        foreach($d3 as $dd){
                                            $d3 = $this->Doc_ipd_model->selectExistingDailyReport($dd);
                                           $b3 .= $d3['description'].',';
                                        }
                                        echo rtrim($b3,',');
                                        // changes by ghanshyam parihar ends
										?>
									</span>
								</p>	
								<?php } ?>
								
								<?php if($dischargedata['details']['treatment_given']!=''){?>
        					    <p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Treatment Given :</b>
        					        <span style="font-size: 9px;position: absolute;padding: 1px 4px;">
        					            <?echo $dischargedata['details']['treatment_given'];?>
    					            </span>
					            </p>
					            <?php } ?>
        								
        								<!--</div>-->
        								</td>
        								    </tr>
        								    </tbody>
    								    </table>
							    </div>
							    </div>
							    </div>
								
								<? if(($dischargedata['details']['vital_bp'])>0 ||
								($dischargedata['details']['vital_temperature'])>0 || 
								($dischargedata['details']['vital_pulse'])>0 ||
								($dischargedata['details']['vital_respiratory'])>0 ||
								($dischargedata['details']['vital_weight'])>0 ||
								($dischargedata['details']['vital_height'])>0)
								{?>
								
								<div class="invoice-1-add-left col-lg-12 col-md-12" style="/*border-top: 1px solid #2f2f2f;*/padding-top: 8px;margin-bottom: -8px;text-align:center">
								    <b style="letter-spacing: 2px;color: #4a4747;font-weight: 100;text-align: center; font-size: 17px">Vital Parameters</b>
								</div>
								<div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat" style="margin-bottom: 10px;">
									<table class="">
									<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>
										<thead>
											<tr>
											    <th style="width:16%;border: 1px solid #2f2f2f;"><center><b>BP</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Temperature(&#8451;)</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Pulse</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Respiratory Rate(&#37;)</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Weight(Kgs)</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Height(cms)</b></center></th>
												
												</tr>
										</thead>
										<tbody>
										   											<tr>
											    <td style="width:10%;text-align:center;border: 1px solid #2f2f2f;"><?echo $dischargedata['details']['vital_bp'];?></td>
												<td style="width:20%;border: 1px solid #2f2f2f;text-align:center;padding:2px;"><?echo $dischargedata['details']['vital_temperature'];?></td>
												<td style="width:8%;border: 1px solid #2f2f2f;padding:2px;text-align:center;"><?echo $dischargedata['details']['vital_pulse'];?></td>
												<td style="width:8%;border: 1px solid #2f2f2f;padding:2px;text-align:center;"><?echo $dischargedata['details']['vital_respiratory'];?></td>
												<td style="width:20%;border: 1px solid #2f2f2f;padding:2px;text-align:center;"> <?echo $dischargedata['details']['vital_weight'];?></td>
												<td style="width:20%;border: 1px solid #2f2f2f;padding:2px;text-align:center;"><?echo $dischargedata['details']['vital_height'];?></td>
											
											</tr>
									
										</tbody>
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>
								
								<?php } ?>
								
								
								<!-- nikhil_change ends here-->
								<?php if(!empty($dischargedata['medicine'])) {?>
								<div class="invoice-1-add-left col-lg-12 col-md-12" style="/*border-top: 1px solid #2f2f2f;*/padding-top: 8px;margin-bottom: -7px;text-align:center">
									
										<b style="letter-spacing: 2px;color: #4a4747;font-weight: 100;text-align: center; font-size: 17px;">Medicine</b>
										
                                    </div>
								<div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat" style="margin-bottom: 10px;">
									<table class="">
									<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>
										<thead>
											<tr>
											    <th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Sr.</b></center></th>
												<th style="width:22%;border: 1px solid #2f2f2f;"><center><b>Name</b></center></th>
												<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Dosage</b></center></th>
												<th style="width:1%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Unit</b></center></th>
												<th style="width:15%;border: 1px solid #2f2f2f;"><center><b>Morning</b></center></th>
												<!--<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Frequency</b></center></th>-->
												<th style="width:13%;border: 1px solid #2f2f2f;"><center><b>Afternoon</b></center></th>
												<!--<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Frequency</b></center></th>-->
												<th style="width:13%;border: 1px solid #2f2f2f;"><center><b>Evening</b></center></th>
												<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Frequency</b></center></th>
												<th style="width:8%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">No. of Days</b></center></th>
												</tr>
										</thead>
										<tbody>
										   <?
										   $count=1;
										   foreach($dischargedata['medicine'] as $a){?>
											<tr>
											    <td style="width:4%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $count;?></center></td>
												<td style="width:20%;border: 1px solid #2f2f2f;/* padding:10px; */"><center><?echo $a['medicine_name'];?></center></td>
												<td style="width:4%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $a['dosage'];?></center></td>
												<td style="width:1%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $a['dosage_unit'];?></center></td>
												<td style="width:15%;border: 1px solid #2f2f2f;/* padding:10px; */"><center><?echo $a['frequency_first'];?></center> </td>
												<!--<td style="width:4%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $a['frequency'];?></center></td>-->
												<td style="width:13%;border: 1px solid #2f2f2f;/* padding:10px; */"><center><?echo $a['frequency_second'];?></center></td>
												<!--<td style="width:4%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $a['frequency'];?></center></td>-->
												<td style="width:13%;border: 1px solid #2f2f2f;/* padding:10px; */"><center><?echo $a['frequency_third'];?></center></td>
												<td style="width:4%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $a['frequency'];?></center></td>
												<td style="width:8%;border: 1px solid #2f2f2f;/* padding:10px; */"><center style="padding:2px;"><?echo $a['day'];?></center></td>
											</tr>
										<?
										$count++;
										}?>
									
										</tbody>
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>
								<?php } ?>
								
								<?php if(!empty($dischargedata['test'])) {?>
    								<!--<div class="invoice-1-add-left col-lg-12 col-md-12" style="/*border-top: 1px solid #2f2f2f;*/padding-top: 8px;margin-bottom: -7px;">-->
    								<!--    <h5 style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial">Test</h5>-->
    								<!--</div>-->
								    <div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat" style="margin-bottom: 10px;overflow: hidden;">
									<table class="">
									<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>
										<thead>
											<tr>
											    <!--<th style="width:1%;border: 1px solid #2f2f2f;"><center><b>Sr.</b></center></th>-->
												<th style="width: 12%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Test to be done</b></center></th>
												
												    <?
        										     $count=1;
        										     $tst = '';
        										  //   if(!empty($dischargedata['test'])){
                                                        
            										   foreach($dischargedata['test'] as $a){
            												if(!empty($a['test_name'])){ 
            												    $tst .= '<td style="width:20%;border: solid 1px #333;padding: 2px 0px 2px 0px;text-align: center;">'.$a['test_name'].'</td>'; 
        												    }
            												else{ 
            												    echo '<td style="width:20%;border: solid 1px #333;padding: 2px 0px 2px 0px;text-align: center;">test not fonund</td>';
            												}
                											$count++;
                										}
                										echo $tst;
    										          //  }
        										      //  else{  	
            										  //      echo 'No Record Found';
            									   // 	}
        										    ?>
												
												</tr>
										</thead>
										   
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>
								<?php } ?>
								
					            <?php if($dischargedata['eyes']['vision_r_eye']!="") {?>
				                    <div class="row ">
                     <!--<div class="col-md-12">-->
                        <div class="invoice-1-add-left col-lg-12 col-md-12" style="margin-bottom: -38px;padding: 0;">
                            <div class="col-md-6">
                                <div class="invoice-1-add-left  tableRepeat">     
                                    <table >
                                             <tr>
                                                  <th style="width:55%;border: 1px solid #2f2f2f;text-align:center;"></th>
                                                  <th style="width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>Right Eye</b></th>
                                                  <th style="width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>Left Eye</b></th>
                                             
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>Vision</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_r_eye'];?></td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_l_eye'];?></td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>Vision With Glass</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_glass_r_eye'];?></td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_glass_l_eye'];?></td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>Vision With P.H</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_ph_r_eye'];?></td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_ph_l_eye'];?></td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>I.O.P. Non Touch</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_iop_non_th_r_eye'];?></td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_iop_non_th_l_eye'];?></td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>I.O.P. Tono</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_iop_non_tono_r_eye'];?></td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['vision_with_iop_non_tono_l_eye'];?></td>
                                             </tr>
                                    </table>
                                </div>
                            <!--<span id="error_test"></span>-->
                            </div>
                            <div class="col-md-6">
                                    <div class="invoice-1-add-left tableRepeat">
                                        <table  class="">
                                         <tr style="border: 1px solid #2f2f2f;text-align:center;"  >
                                             <th style="width:20%;border: 1px solid #2f2f2f;text-align:center;" colspan='7'><b>Accept</b> </th>      
                                         </tr>
                                         <tr style="border: 1px solid #2f2f2f;text-align:center;"  >
                                          <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;padding: 8px 0px 8px 0px;"></th>
                                          <th style="width:15%;border: 1px solid #2f2f2f;text-align:center;" ><b>Right Eye</b></th>
                                          <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;" style="border-style:hidden;" ></th>
                                          <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;" style="border-right:solid 1px;"></th>
                                          <th style="width:15%;border: 1px solid #2f2f2f;text-align:center;"><b>Left Eye</b></th>
                                          <th style="padding: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;" style="border-style:hidden;"></th>
                                          <th style="padding: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;" style="border-style:hidden;"></th>
                                         </tr>
                                     
                                     <tr>
                                      <td style="width:4%;border: 1px solid #2f2f2f;text-align:center;padding: 8px 0px 8px 0px;"></td>
                                      <td style="width:15%;border: 1px solid #2f2f2f;text-align:center;"><b>SPH</b></td>
                                      <td style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>CYL</b></td>
                                      <td style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>AXIS</b></td>
                                      
                                      <td style="width:15%;border: 1px solid #2f2f2f;text-align:center;"><b>SPH</b></td>
                                      <td style="padding-left: 5px;padding-right: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>CYL</b></td>
                                      <td style="padding-left: 5px;padding-right: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>AXIS</b></td>
                                     </tr>
                                     
                                     <tr>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><b>Dist</b></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_dis_r_sph'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_dis_r_cyl'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_dis_r_axis'];?></td>
                                      
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_dis_l_sph'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_dis_l_cyl'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_dis_l_axis'];?></td>
                                      
                                      
                                     </tr>
                                      <tr>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><b>Near</b></td>
                                      
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_near_r_sph'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_near_r_cyl'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_near_r_axis'];?></td>
                                      
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_near_l_sph'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_near_l_cyl'];?></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><?echo $dischargedata['eyes']['accepts_near_l_axis'];?></td>
                                      
                                     
                                     </tr>
                                </table>
                                    </div>
                            </div>
                        </div>
                        
                    </div>
							    <?php	}?>
								
								<footer>
        <!--                         <div class="invoice-1-add-left col-lg-6 col-md-6 auth" style="/*border-top: 1px solid black*/;padding-top: 20px;margin-bottom: 20px;text-align: left;">-->
                                         
        								<!--<h5 class="bot">Print By : <span style="font-size: 13px;"><?echo $dischargedata['details']['printed_by'];?></span></h5>-->
        <!--                            </div>-->
                                            <!--<h5 class="bottom">Authorised Signatory</h5>-->
        							<div class="invoice-1-add-left col-lg-12 col-md-12 auth" style="/*border-top: 1px solid black*/;padding-top: 20px;margin-bottom: 20px;text-align: right;">
        							
        								<h5 class="bottom">Authorised Signatory</h5>
        								
                                    </div>
                                </footer>
							    	
<!-- 								<div class="invoice-1-add-left col-lg-12 col-md-12" style="border-top: 1px solid #2f2f2f;padding-top: 10px;">
											<h5 class="bottom1">Advised : <span style="font-size: 13px;"><?echo $dischargedata['details']['advised'];?></span></h5>
												
							</div> -->
							
							<div class="invoice-print butt" id="printd" >
							<a href="#!" style="font-size: 12px !important;padding: 1px 13px 10px 13px;;" class="waves-effect waves-light btn-large fon" onclick="printDiv1(<?echo $dischargedata['details_new']['booking_id'];?>);">Print</a> 
								<a href="#!" style="font-size: 12px !important;padding: 1px 13px 10px 13px;;" class="waves-effect waves-light btn-large fon" onclick="printDiv1(<?echo $dischargedata['details_new']['booking_id'];?>);">Download & Share Pdf</a>
							<!--<a href="#!" style="font-size: 12px !important;padding: 1px 13px 10px 13px;;" class="waves-effect waves-light btn-large fon" onclick="printDiv1(<?echo $dischargedata['details']['id'];?>);">Download Pdf</a> -->
							<!--<a style="font-size: 12px !important;padding: 1px 13px 10px 13px;" href="javascript:void(0)" class="waves-effect waves-light btn-large fon" onclick="window.location.href='<?php echo base_url(); ?>Doc_opd/view_prescription_pdf/<?php echo $this->uri->segment(3);?>'">Share</a>-->
						
						       	<!--<a href="javascript:void(0)" data-html2canvas-ignore onclick="capture()" class="waves-effect waves-light btn-large" >Download PDF(changes needed)</a>-->
							<a  style="font-size: 12px !important;padding: 1px 13px 10px 13px;;" href="javascript:void(0)" onclick="window.location.href='<?php echo base_url(); ?>Doc_opd/manage_prescriptiont/</a>'" class="waves-effect waves-light btn-large fon" style="float: right;">Back</a>
							</div>
					      
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</section>
	<!--END DASHBOARD-->


	
	<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        } );
    </script>
    
    
     <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha1/html2canvas.min.js"></script>
    
	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/functions.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/js/materialize.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/js/custom_invoice.js"></script>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/ui/1.12.0-beta.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.1.135/jspdf.min.js"></script>
    <script type="text/javascript" src="http://cdn.uriit.ru/jsPDF/libs/adler32cs.js/adler32cs.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2014-11-29/FileSaver.min.js
    "></script>
    <script type="text/javascript" src="libs/Blob.js/BlobBuilder.js"></script>
    <script type="text/javascript" src="http://cdn.immex1.com/js/jspdf/plugins/jspdf.plugin.addimage.js"></script>
    <script type="text/javascript" src="http://cdn.immex1.com/js/jspdf/plugins/jspdf.plugin.standard_fonts_metrics.js"></script>
    <script type="text/javascript" src="http://cdn.immex1.com/js/jspdf/plugins/jspdf.plugin.split_text_to_size.js"></script>
    <script type="text/javascript" src="http://cdn.immex1.com/js/jspdf/plugins/jspdf.plugin.from_html.js"></script>
    
      <script type="text/javascript" src="libs/Blob.js/BlobBuilder.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-alpha1/html2canvas.min.js"></script>



 <script>    

function capture() {
  var w = document.getElementById("target").offsetWidth;
  var h = document.getElementById("target").offsetHeight;
  var name="OPD_pdf_"+<?php echo $dischargedata['details']['id']; ?>;
  window.scrollTo(0,0); 
   html2canvas(document.getElementById("target"), {
     dpi: 300, // Set to 300 DPI 900
     scale: 45, // Adjusts your resolution 45 
     useCORS: true,
      
     onrendered: function(canvas) {
        
       var img = canvas.toDataURL("image/jpeg", 1);
  // alert(img);
       var doc = new jsPDF('L', 'px', [w, h]);
       doc.addImage(img, 'JPEG', 0, 0, w, h);
       doc.save(name+'.pdf');
    },
      logging:true
   });
 window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);



}
    </script>

   



    <script>
        function printDiv1(invoice) {
            // alert(invoice);
            var printContents = document.getElementById(invoice).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }    
    </script>
    
</body>

</html>
