<?php
$date='July 2019,June 2019';
$date = explode(',',$date);
$c_date=date('F Y', strtotime('2019-09-04'));

if (in_array($c_date, $date))
{
  echo "Match found";
}
else
{
  echo "Match not found";
}
?>