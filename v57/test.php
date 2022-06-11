<?php
$foo = 1;
$bar = ($foo == 1) ? "brand" : (($foo == 2)  ? "product" : "category");
echo $bar;
?>