<?php

$qa_debug=true;

if(!isset($_REQUEST['text']))die("Invalid call");
if(!isset($_REQUEST['model']))die("Invalid call");

$models=[
"qa_covid" => ["url"=>"http://localhost:9550/respond?question="],
];

$text=$_REQUEST['text'];
$model=$_REQUEST['model'];
if(!isset($models[$model]))die("Invalid call");


$url=$models[$model]["url"].urlencode($text);
if(!$qa_debug){
    $data=file_get_contents($url);
    echo $data;

    die();
}else{ 
$str=<<<END
{
    "response": [
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "R1",
            "confidence": 0.898547567957116,
            "date": "2022-04-12T08:53:00.0000000Z",
            "end_offset": 103,
            "name": "n1",               
            "snippet": "AAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 0,
            "url": "https://mesagerul.ro/cluj/pacientii-covid-pot-ramane-cu-sechele-pe-termen-lung-medic-pneumolog-atentie-sunt-foarte-multe-persoane-tinere-la-care-apare__7662/"
        },
        {
            "answer": "r2",
            "confidence": 0.5935818092948336,
            "date": "2022-04-05T21:40:00.0000000Z",
            "end_offset": 83,
            "name": "N2",
            "snippet": "AAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAA AAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAA AAAAAAAAAAAAAAAAAAAAAAAAAA",
            "start_offset": 7,
            "url": "https://www.digi24.ro/stiri/actualitate/medic-pacientii-vindecati-de-covid-pot-face-din-nou-boala-intr-o-forma-mai-grava-1713845"
        }
]
}
END;

echo $str;
}