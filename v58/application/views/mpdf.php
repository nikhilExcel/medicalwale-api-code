<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
//require_once('tcpdf_include.php');


// create new PDF documentPDF_HEADER_LOGO
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Medicalwale.com');
$pdf->SetTitle('Medicalwale.com');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, '','', array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print

//print_r($users);

/*$html = <<<EOD
 <table style="width:100%"> EOD;
 
  <tr>
    <td>Name :</td>
    <td></td>
  </tr>
  <tr>
    <td>Mobile :</td>
    <td>$users->mobile_num</td>
  </tr>
  
</table> 
EOD;*/

$html = "<table style='width:100%;border:1px solid #ccc'>";
//print_r($users);
foreach($users as $user){
    //echo $user;
    foreach($user as $key=>$us){
        //echo $key;
        if($key=='user_id'){
           $user_id = $us;
        }
        $html .="<tr>";
       
         if($key == 'Medical Condition'){
             $html .="<td align='center' style='width:150px'>".$key."</td>";
            $html .="<td align='center'>: ";
        
           
               
                $answers_query = $this->db->query('SELECT A.id,A.user_id,A.question_id, GROUP_CONCAT(A.answer) as final_ans,Q.question,Q.id as qid,Q.question_type FROM `userprofile_question_answer` as A LEFT JOIN userprofile_question as Q ON(A.`question_id` = Q.id ) WHERE Q.question_type=2 and A.`user_id`='.$user_id.' group by question_id');
                $asnwers_count = $answers_query->num_rows();
                $ans_list       = $answers_query->result_array();
                $None ="None";
                if ($asnwers_count >0) {
                    $None ="";
                    foreach($ans_list as $ans_q)
                    {
                        $ANS = $ans_q['final_ans'];// == '0'?'Self':$ans_q['answer']=='1'?'Hereditry':$ans_q['answer'];
                        
                        $string = str_replace(',', '-', $ANS);
                        $str2 = str_replace('0', 'Self', $string);
                        $str3 = str_replace('1', 'Hereditary', $str2);
                        $html .="<table><tr><td align='center'>".$ans_q['question']." - [".$str3."]</td></tr></table>";
                        
                    }
                }
                
            
            $html .=$None."</td>";
        }
        
        if($key == 'Allergies'){
             $html .="<td align='center' style='width:150px'>".$key."</td>";
            $html .="<td align='center'>: ";
        
           
               
                $answers_query = $this->db->query('SELECT * FROM `userprofile_question_answer` as A LEFT JOIN userprofile_question as Q ON(A.`question_id` = Q.id ) WHERE Q.question_type=81 and A.`user_id`='.$user_id);
                $asnwers_count = $answers_query->num_rows();
                $ans_list       = $answers_query->result_array();
                $None ="None";
                if ($asnwers_count >0) {
                    $None ="";
                    foreach($ans_list as $ans_q)
                    {
                        
                        $html .="<table><tr><td align='center'>";
                        $html .="".$ans_q['question']."</td></tr></table>";
                        
                    }
                }
                
            
            $html .=$None."</td>";
        }
        
        
        if($key == 'Addiction'){
             $html .="<td align='center' style='width:150px'>".$key."</td>";
            $html .="<td align='center'>: ";
        
           
              // echo 'SELECT * FROM `userprofile_question_answer` as A LEFT JOIN userprofile_question as Q ON(A.`question_id` = Q.id ) WHERE Q.question_type=1 and A.`user_id`='.$user_id;
               
                $answers_query = $this->db->query('SELECT * FROM `userprofile_question_answer` as A LEFT JOIN userprofile_question as Q ON(A.`question_id` = Q.id ) WHERE Q.question_type=5 and A.`user_id`='.$user_id);
                $asnwers_count = $answers_query->num_rows();
                $ans_list       = $answers_query->result_array();
                $None ="None";
                if ($asnwers_count >0) {
                    $None ="";
                    foreach($ans_list as $ans_q)
                    {
                           
                        $html .="<table><tr><td align='center'>Drug - ".$ans_q['question']."</td></tr><tr><td align='center'>Smoking - ".$ans_q['answer']."</td></tr></table>";
                        
                    }
                }
                
                
                $answers_query1 = $this->db->query('SELECT * FROM `userprofile_question_answer` as A LEFT JOIN userprofile_question as Q ON(A.`question_id` = Q.id ) WHERE Q.question_type=6 and A.`user_id`='.$user_id);
                $asnwers_count1 = $answers_query1->num_rows();
                $ans_list1       = $answers_query1->result_array();
                if ($asnwers_count1 >0) {
                    
                    foreach($ans_list1 as $ans_q1)
                    {
                           
                        $html .="<table><tr><td align='center'>Alcohol - ".$ans_q1['question']."</td></tr><tr><td align='center'>Smoking - ".$ans_q1['answer']."</td></tr></table>";
                        
                    }
                }
                
                $answers_query2 = $this->db->query('SELECT * FROM `userprofile_question_answer` as A LEFT JOIN userprofile_question as Q ON(A.`question_id` = Q.id ) WHERE Q.question_type=7 and A.`user_id`='.$user_id);
                $asnwers_count2 = $answers_query2->num_rows();
                $ans_list2       = $answers_query2->result_array();
                if ($asnwers_count2 >0) {
                    
                    foreach($ans_list2 as $ans_q2)
                    {
                           
                        $html .="<table><tr><td align='center'>Smoking - ".$ans_q2['question']."</td></tr><tr><td align='center'>Smoking - ".$ans_q2['answer']."</td></tr></table>";
                        
                    }
                }
                
            
            $html .=$None."</td>";
        }
        
        
        if($key == 'user_id'){
            $html .="<td align='center' style='width:150px'></td>";
            $html .="<td align='center' style='width:150px'></td>";
        }else{
            $html .="<td align='center' style='width:150px'>".$key."</td>";
            $html .="<td align='center' style='width:150px'>: ".$us."</td>";
        }
        
        
      $html .="</tr>";
    }
}
$html .= "</table>";
$pdf->SetProtection(array(), 'UserPassword', $pdfpass);
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------
date_default_timezone_set('Asia/Kolkata');
        $date = date('YmdHis');
        
//$filename = 'Site '.$date.'.pdf';
/*echo $pdf->Output($filename, 'S');
*/

    $filename= $pdf_file_name.".pdf"; 
    /*include('../s3_config.php'); 
    $actual_image_path = 'images/healthwall_avatar/' . $filename;
    $s3->putObjectFile($filename, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
*/
    //$filelocation = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar";

     $filelocation = "/home/medicalwale/public_html/sandbox.medicalwale.com/user_pdf";
     $fileNL = $filelocation."/".$filename;

    /*include('../s3_config.php'); 
    echo $actual_image_path = 'images/healthwall_avatar/' . $filename;
    $s3->putObjectFile($filelocation, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);*/


       $pdf->Output($fileNL,'F');
       $pdf->Output($filename, 'S');


// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('yourFileName', 'I');

//============================================================+
// END OF FILE
//============================================================+
