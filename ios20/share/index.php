<?php

echo $ref_text = $_GET['share_url'];
header("Location: https://play.google.com/store/apps/details?id=com.medicalwale.medicalwale&" . $ref_text);
?>