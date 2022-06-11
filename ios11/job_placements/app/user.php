<?php

/*
  Author         : Sumit Shinde
  Author Email   : sumit@hostingduty.com
  Author Website : Hostingduty.com
  Licence        : GPL V2
 */

//define('pic_image',"http://recharge.hostingduty.com/medical/uploads/resume");
//define('pic_root',"/home/recharge/public_html/medical/uploads/resume");

define('pic_image', "https://s3.amazonaws.com/medicalwale/images/medical_college_resume");
define('pic_root', "https://s3.amazonaws.com/medicalwale/images/medical_college_resume");

// Get all users


$app->get('/q', function($request, $response) {

    $data = $request->getQueryParams();

    // extract($data);

    echo "welcome to Medical";
    exit;
});

//1)========================Get api for job Cate=========================
$app->get('/get_cat', function($request, $response, $args) {

    require 'db_connect.php';
    $stmt = $pdo->query("SELECT * FROM job_cat");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //echo $result;
    if ($stmt->rowCount() > 0) {

        $categories = array(
            "status" => "true",
            "statuspic_root_code" => "200",
            "data" => $result
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($categories));
    } else {
        $result = array(
            "status" => "false",
            "message" => "Result not found"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});
//2)============Post Api for Creat Job============================================================

$app->post('/post_job', function($request, $response) {
    $post = $request->getParsedBody();
    extract($post);
    require 'db_connect.php';
    // $is_active = 0;
    $query_to_insert = "INSERT INTO `job_list`(`gender`, `job_title`, `job_description`, `category_id`, `job_type`, `min_salary`, `max_salary`, `company_name`, `job_location`, `mobile`, `email`,`user_id`) VALUES ('" . $gender . "','" . $job_title . "','" . $job_desc . "','" . $job_category . "'," . $job_type . ",'" . $job_min_salary . "','" . $job_max_salary . "','" . $job_companey_name . "','" . $job_city . "','" . $job_phone_no . "','" . $job_email . "','" . $user_id . "')";
    // echo $query_to_insert;

    $user = $pdo->query($query_to_insert);
    if ($user) {
        $result = array(
            "status" => "True",
            "status_code" => "200",
            "message" => "Job Post Successfully"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "201",
            "message" => "Job not post"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});





//3)=======================api for get find job========================

$app->get('/get_find', function($request, $response, $args) {

    require 'db_connect.php';

    $stmt = $pdo->query("SELECT category_id FROM job_list");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //echo $result;
    if ($stmt->rowCount() > 0) {

        $categories = array(
            "status" => "true",
            "statuspic_root_code" => "200",
            "data" => $result
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($categories));
    } else {
        $result = array(
            "status" => "false",
            "message" => "Result not found"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//4)=======================api for get selected job========================

$app->get('/job_by_type', function($request, $response, $args) {

    require 'db_connect.php';
    $data = $request->getQueryParams();

    $query = "SELECT * FROM `job_list` jp inner join job_category jtc on jp.sub_category_industry_type=jtc.id INNER JOIN job_cat on jp.company_industry_type = job_cat.cat_id where jp.company_industry_type=" . $data['job_type'] . " and jp.is_active=1";

    $stmt = $pdo->query($query);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if ($stmt->rowCount() > 0) {

        $categories = array(
            "status" => "true",
            "statuspic_root_code" => "200",
            "data" => $result
        );
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json')->write(json_encode($categories));
    } else {
        $result = array(
            "status" => "false",
            "message" => "Result not found"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//5)=========================job_title_category================================

$app->get('/job_title', function($request, $response) {

    // $data = $request->getQueryParams();
    $data = $request->getQueryParams();
    require 'db_connect.php';

    $stmt = $pdo->query("SELECT * FROM job_category");
    //$stmt2 = $pdo->query("SELECT * FROM cities");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //$result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    //$result = array_merge($result1,$result2);
    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_pic_rootcode" => "200",
            "data" => $result
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "20resume_upload1",
            "message" => "Job not post"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//6)====================Approve job by title cat============================

$app->post('/job_approve_by_title', function($request, $response) {

    $post = $request->getParsedBody();
    extract($post);
    require 'db_connect.php';

    $stmt = $pdo->query("SELECT * FROM job_list where category_id='" . $job_post . "' and is_active=1");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_pic_rootcode" => "201",
            "message" => $result
        );
        return $response->withStatus(201)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "20resume_upload1",
            "message" => "Job not post"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//7)====================Baner Api============================

$app->post('/baner_api', function($request, $response) {

    $post = $request->getParsedBody();
    extract($post);
    require 'db_connect.php';

    $stmt = $pdo->query("SELECT * FROM job_img where img_active=1");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_pic_rootcode" => "201",
            "data" => $result
        );
        return $response->withStatus(201)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "20resume_upload1",
            "message" => "Job not post"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//8)===============================Api For Upload Resume==========================

$app->post('/upload', function($request, $response, $args) {

    $post = $request->getParsedBody();
    extract($post);
    require 'db_connect.php';
    $dd = date("d/m/Y", strtotime($user_dob));
    // print_r($post);die;
//temp commented by jakir for user_id insert in table
   // $query_to_insert = "INSERT INTO `job_user_profile`(`name`, `phone`, `email`, `dob`, `gender`, `job_role`, `min_salary`, `max_salary`, `year_exp`, `month_exp`, `city`, `resume`) VALUES ('" . $user_name . "','" . $user_mobile . "','" . $user_email . "'," . date("d/m/Y", strtotime($user_dob)) . ",'" . $user_gender . "','" . $user_job_title . "'," . $user_min_salary . "," . $user_max_salary . "," . $user_exp_year . ",'" . $user_exp_month . "','" . $user_city . "','" . $path . "')";
      $query_to_insert = "INSERT INTO `job_user_profile`(`user_id`,`name`, `phone`, `email`, `dob`, `gender`, `job_role`, `min_salary`, `max_salary`, `year_exp`, `month_exp`, `city`, `resume`) VALUES ('".$user_id."','".$user_name."','".$user_mobile."','".$user_email."','".$dd."','".$user_gender."','".$user_job_title."',".$user_min_salary.",".$user_max_salary.",".$user_exp_year.",'".$user_exp_month."','".$user_city."','".$path."')";


    $user = $pdo->query($query_to_insert);

    if ($user) {
        $result = array(
            "status" => "True",
            "status_code" => "200",
            "message" => "Resume Upload Successfully"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "201",
            "message" => "Resume Not Upload"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//9)======api for upload resume only===========================
$app->post('/upload_resume', function($request, $response, $args) {

    $post = $request->getParsedBody();
    extract($post);
    require 'db_connect.php';
    // print_r($post);die;

    $uploadedFiles = $request->getUploadedFiles();

    /*$directory = "/home/recharge/public_html/medical/uploads/resume";

    $upload_to_database = "http://recharge.hostingduty.com/medical/uploads/resume/";*/
    
    $directory = "https://medicalwale.s3.amazonaws.com/images/job_image/";

    $upload_to_database = "https://medicalwale.s3.amazonaws.com/images/job_image/";

    $uploadedFile = $uploadedFiles['resume'];
   //  print_r($uploadedFile);
    //die;

    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $filename = moveUploadedFile($directory, $uploadedFile);

        $path = $upload_to_database .'/'. $filename;

        $result = array(
            "status" => "true",
            "status_code" => "200",
            "data" => $path
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

function moveUploadedFile($directory, $uploadedFile) {
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
    return $filename;
}

//9)===========get job by job cat=============================================

$app->get('/job_by_cat/q', function($request, $response) {

    require 'db_connect.php';

    $data = $request->getQueryParams();

    $query = "SELECT * FROM `job_list` jp inner join job_category jtc on jp.category_id=jtc.id INNER JOIN job_cat 
    on jp.job_type = job_cat.cat_id where category_id=" . $data['category_id'] . " and is_active=1";

    // $query="SELECT * FROM job_post where job_category=".$data['job_cat_id']." and job_status=2";
    // echo $query;
    $stmt = $pdo->query($query);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // print_r($result);die;
    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_pic_rootcode" => "200",
            "data" => $result
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "201",
            "message" => "Job not post"
        );
        return $response->withStatus(201)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//10)===========API For hire Can=============================================

$app->get('/job_hire', function($request, $response) {

    require 'db_connect.php';

    $data = $request->getQueryParams();


    $query = "SELECT jup.*,c.city_name as city,jc.category as job_role FROM job_user_profile as jup LEFT JOIN job_category as jc ON(jc.id=jup.job_role) LEFT JOIN cities as c ON(c.city_id=jup.city) where jup.is_active=1";
    // echo $query;
    $stmt = $pdo->query($query);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //$f = $stmt->fetch();
    //$result1 = $f['dob'];
    //$dateOfBirth = $stmt->fetchColumn();
    /* $result = array(
      "id"=> $f['id'],
      "id"=> $f['id'],
      "name"=> $f['name'],
      "phone"=> $f['phone'],
      "email"=> $f['email'],
      "dob"=> $f['dob'],
      "gender"=> $f['gender'],
      "job_role"=> $f['job_role'],
      "min_salary"=> $f['min_salary'],
      "max_salary"=> $f['max_salary'],
      "year_exp"=> $f['year_exp'],
      "month_exp"=> $f['month_exp'],
      "lang_known"=> $f['lang_known'],
      "add_skills"=> $f['add_skills'],
      "comp_skill"=> $f['comp_skill'],
      "passport"=> $f['passport'],
      "preferred_locn"=> $f['preferred_locn'],
      "qualifications"=> $f['qualifications'],
      "present_job_type"=> $f['present_job_type'],
      "preferred_job_type"=> $f['preferred_job_type'],
      "commited_job_timing"=> $f['preferred_job_type'],
      "looking_commited_job_timing"=> $f['looking_commited_job_timing'],
      "adhar_card"=> $f['adhar_card'],
      "pan_card"=> $f['pan_card'],
      "driving_lic"=> $f['pan_card'],
      "city"=> $f['dob'],
      "resume_name"=> $f['resume_name'],
      "resume"=> $f['resume_name'],
      "posted_on"=> $f['posted_on'],
      "m_verified"=> $f['m_verified'],
      "is_active"=> $f['is_active']
      ); */
    //$today = date("d/m/y");
    /* $from = new DateTime($result1);
      $to   = new DateTime('today');
      echo $from->diff($to)->y, "\n"; */

    // print_r($result);die;
    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_pic_rootcode" => "200",
            "data" => $result
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "201",
            "message" => "Job not post"
        );
        return $response->withStatus(201)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//11)===========API For hire Candidate details ============================================= by jakir on 22 may 

$app->post('/get_Hire_Candidate_Details', function($request, $response) {

    require 'db_connect.php';

    //  $post = $request->getParsedBody();
    //  extract($post);


    $data = $request->getParsedBody();
    extract($data);

    $query = "SELECT jup.*,c.city_name as city,jc.category as job_role FROM job_user_profile as jup LEFT JOIN job_category as jc ON(jc.id=jup.job_role) LEFT JOIN cities as c ON(c.city_id=jup.city) where jup.id=" . $data['id'] . "";
    // echo $query;
    $stmt = $pdo->query($query);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_pic_rootcode" => "200",
            "data" => $result
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "201",
            "message" => "candidate details not available"
        );
        return $response->withStatus(201)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});

//=====================ALl City=======================================

$app->get('/get_city', function($request, $response) {
    // $data = $request->getQueryParams();
    $data = $request->getQueryParams();
    require 'db_connect.php';


    $stmt = $pdo->query("SELECT * FROM cities");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // $data1=array();
    // array_push($data1, $result);
    // print_r($data1); die;
    if ($stmt->rowCount() > 0) {
        $data = array(
            "status" => "True",
            "status_code" => "200",
            "data" => $result
                // "data" => $data1
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($data));
    } else {

        $result = array(
            "status" => "false",
            "status_code" => "201",
            "message" => "Job not post"
        );
        return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($result, JSON_FORCE_OBJECT));
    }
});
