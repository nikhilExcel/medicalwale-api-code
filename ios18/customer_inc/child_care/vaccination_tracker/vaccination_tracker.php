<?php

include_once('../../../config.php');
if (isset($_POST['id'])) {
    $resultdetails = array();
    $vaccinationQuery = mysqli_query($conn, "SELECT title,protects_against,details FROM `baby_vaccination_tracker` order by id asc");
    $vaccination_count = mysqli_num_rows($vaccinationQuery);
    if ($vaccination_count > 0) {
        while ($row = mysqli_fetch_array($vaccinationQuery)) {
            extract($row);
            $title = $title;
            $details = $details;
            $protects_against = $protects_against;
        }
        $words = "BCG (Bacillus Calmette-Guerin),OPV 0 (Oral Polio Vaccine),Hep B1 (Hepatitis B),DTP 1 (Diphtheria, Tetanus, Pertussis),IPV 1 (Inactivated Polio Vaccine),Hep-B 2 (Hepatitis B),Hib 1 (Haemophillus influenzae type b),RV 1 (Rotavirus),PCV 1 (Pneumococcal Conjugate Vaccine ),DTP 2 (Diphtheria, Tetanus, Pertussis),IPV 2 (Inactivated Polio Vaccine),Hib 2 (Haemophillus influenzae type b),RV 2 (Rotavirus),PCV 2 (Pneumococcal Conjugate Vaccine ),DTP 3 (Diphtheria, Tetanus, Pertussis),IPV 3 (Inactivated Polio Vaccine),Hib 3 (Haemophillus influenzae type b),RV 3 (Rotavirus),PCV 3 (Pneumococcal Conjugate Vaccine ),OPV 1 (Oral Polio Vaccine),Hep-B 2 (Hepatitis B),Flu 1 (Flu),OPV 2 (Oral Polio Vaccine),MMR 1 (Mumps, Measles, Rubella),Typhoid 1 (Typhoid),Hep-A1 (Hepatitis A),MMR 2 (Mumps, Measles, Rubella),VAR 1 (Varicella),PCV Booster (Pneumococcal Conjugate Vaccine),DTP B1 (Diphtheria, Tetanus, Pertussis),IPV B1 (Inactivated Polio Vaccine),Hib Booster 1 (Haemophillus influenzae type b),Hep -A 2 (Hepatitis A),Typhoid 2 (Typhoid),DTP B 2 (Diphtheria, Tetanus, Pertussis),OPV 3 (Oral Polio Vaccine),VAR 2 (Varicella),MMR 3 (Mumps, Measles, Rubella),Tdap (Diphtheria, Tetanus, Pertussis),HPV 1 (Human Papillomavirus),HPV 2 (Human Papillomavirus)";
        $wordsArray = explode('),', $words);
        foreach ($wordsArray as $words_Array) {
            $title = strtok($words_Array, '(');
            $ttitle .= $title . ',';

            $resultdetails[] = array("title" => $title, "protects_against" => $protects_against, "details" => $details);
        }
        $ttitle = rtrim($ttitle, ',');

        preg_match_all('/\(([A-Za-z0-9 ]+?)\)/', $words, $out);
        $long_title = $out[1];
        $json = array("status" => 1, "msg" => "success", "short_title" => $ttitle, "long_title" => $long_title, "data" => $resultdetails);
    } else {
        $json = array("status" => 0, "msg" => "vaccination tracker list not found");
    }
} else {
    $json = array("status" => 0, "msg" => "vaccination tracker list not found");
}
@mysqli_close($conn);
header('Content-type: application/json');
echo json_encode($json);
?>
