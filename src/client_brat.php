<?php

$data=null;
if(isset($_REQUEST['data'])){
    $data=json_decode($_REQUEST['data'],true);
}

if(!is_array($data) || empty($data)){

    $data=[
        "text" => "Ed O'Kelley was the man who shot the man who shot Jesse James.",
        "entities" => [
            ['T1', 'PER', [[0, 11]]],
            ['T2', 'PER', [[20, 23]]],
            ['T3', 'PER', [[37, 40]]],
            ['T4', 'PER', [[50, 61]]],
        ],
    ];

}

?>
<!DOCTYPE html>
<html>
<head>
    <title>BRAT Client</title>

    <link rel="stylesheet" type="text/css" href="brat/style-vis.css"/>
    <script type="text/javascript" src="brat/client/lib/head.load.min.js"></script>

</head>

<body>
    <div id="brat"></div>
    
    <script>
var bratLocation = 'brat';
head.js(
    // External libraries
    bratLocation + '/client/lib/jquery.min.js',
    bratLocation + '/client/lib/jquery.svg.min.js',
    bratLocation + '/client/lib/jquery.svgdom.min.js',

    // brat helper modules
    bratLocation + '/client/src/configuration.js',
    bratLocation + '/client/src/util.js',
    bratLocation + '/client/src/annotation_log.js',
    bratLocation + '/client/lib/webfont.js',

    // brat modules
    bratLocation + '/client/src/dispatcher.js',
    bratLocation + '/client/src/url_monitor.js',
    bratLocation + '/client/src/visualizer.js'
);

var webFontURLs = [
    bratLocation + '/static/fonts/Astloch-Bold.ttf',
    bratLocation + '/static/fonts/PT_Sans-Caption-Web-Regular.ttf',
    bratLocation + '/static/fonts/Liberation_Sans-Regular.ttf'
];

var collData = {
    entity_types: [ 
        {
            type   : 'PER',
            labels : ['PER'],
            bgColor: 'lightblue',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'ORG',
            labels : ['ORG'],
            bgColor: 'orange',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'LOC',
            labels : ['LOC'],
            bgColor: 'lightgreen',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'TIME',
            labels : ['TIME'],
            bgColor: 'yellow',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'DISO',
            labels : ['DISO'],
            bgColor: '#ff00ff',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'CHEM',
            labels : ['CHEM'],
            bgColor: '#66ffff',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'ANAT',
            labels : ['ANAT'],
            bgColor: '#ff9980',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        }, 
        {
            type   : 'PROC',
            labels : ['PROC'],
            bgColor: '#009900',
            // Use a slightly darker version of the bgColor for the border
            borderColor: 'darken'
        } 

    ]
};

var docData=<?php echo json_encode($data);?>;

head.ready(function(){
              Util.embed(
                      // id of the div element where brat should embed the visualisations
                      'brat',
                      // object containing collection data
                      collData,
                      // object containing document data
                      docData,
                      // Array containing locations of the visualisation fonts
                      webFontURLs
              );          
});


</script>
    
</body>
</html>