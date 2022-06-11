<?php

$resulttrimester = array();
$week = 'week';
$days = '273';
$trimester = '1-st trimester';
$length_array = '-,-,-,-,0.3 cm from crown to rump,0.5 cm from crown to rump,1.2 cm from crown to rump,1.6 cm from crown to rump,2.3 cm from crown to rump,3.1 cm from crown to rump,4.1 cm from crown to rump,5.4 cm from crown to rump,7.4 cm from crown to rump,8.7 cm from crown to rump,10.1 cm from crown,11.6 cm from crown to rump,13 cm from crown to rump,14.2 cm from crown to rump,15.3 cm from crown to rump,25.6 cm from crown,26.7 cm from crown to rump,27.8  cm from crown to rump,28.8 cm from crown to rump,30 cm from crown to rump,34.6 cm from crown,35.6 cm from crown to rump,36.6 cm from crown to rump,37.5 cm from crown to rump,38.6 cm from crown to rump,39.9 cm from crown,41.1 cm from crown to rump,42.4 cm from crown to rump,43.8 cm from crown to rump,45 cm from crown to rump,46.3 cm from crown,47.4 cm from crown to rump,48.5 cm from crown to rump,49.8  cm from crown to rump,50.6 cm from crown to rump,51.2 cm from crown,51.7 cm from crown to rump,52.6 cm from crown to rump';
$weight_array = '-,-,-,-,-,-,0.5 - 1 g,1 - 1.3 g ,2 g,4 g ,7 - 8 g ,14 g,23 - 24 g,42 - 44 g ,70 g,100 g ,138 - 142 g,190 g,249 g,300 g,360 g ,430 g,500 g,600 g ,660 - 670 g,760 g,870 - 880 g,1 - 1.1 kg,1.15 kg,1.3 - 1.32 kg,1.5 kg ,1.7 kg,1.9 - 2 kg,2.15 kg,2.38 - 2.4 kg,2.6 kg,2.85 - 2.9 kg,3 - 3.1 kg,3.2 - 3.3 kg,3.4 - 3.5 kg,3.6 kg ,3.69 - 3.7 kg';
$size_array = '-,-,-,poppy seed,sesame seed,pomegranate seed,blueberry,rasperry,cherry,green olive,fig,lime,peach,nectarine,apple,avocado,pear,bellpepper,pomegranate,ear of corn,grapefruit,zucchini,large mango,papaya,pomelo,eggplant,cauliflower,kabocha squash,butternut squash,bunch of bananas,coconut,large cabbage,pineapple,cantaloupe,honeydew melon,bunch of grapes,swiss chard,leek,small pumpkin,watemelon,big watermelon,big watermelon';
$j = '0';
for ($i = 1; $i <= 42; $i++) {
    if ($i > 39) {
        $days = 'few';
    }
    if ($i > 13) {
        $trimester = '2nd trimester';
    }
    if ($i > 27) {
        $trimester = '3-rd trimester';
    }
    $length = explode(',', trim($length_array));
    $weight = explode(',', trim($weight_array));
    $size = explode(',', trim($size_array));
    $week_day = $i . ' ' . $week;
    $images = 'trimester' . $i . '.jpg';
    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ofw_images/trimester/' . $images;
    $resulttrimester[] = array("weeks" => $week_day,
        "trimester" => $trimester,
        'days' => $days . ' days left',
        'length' => $length[$j],
        'weight' => $weight[$j],
        'size' => $size[$j],
        'image' => $image,
    );
    $week = 'weeks';
    $days = $days - 7;
    $j++;
}
$json = array("status" => 1, "msg" => "success", "count" => "42", "data" => $resulttrimester);
header('Content-type: application/json');
echo json_encode($json);
?>