<?php

    if(!isset($_REQUEST['word']))die();
    $word=$_REQUEST['word'];
    if(!isset($_REQUEST['sid']))die();
    $sid=$_REQUEST['sid'];
    
    $data=ROWN_call($word,$sid);
    
     echo "<!-- \n";var_dump($data);echo "\n -->\n";
     $data=json_decode($data,true);
     echo "<!-- \n";var_dump($data);echo "\n -->\n";
?>

<!DOCTYPE html>
<html>

<head>
    <title>RoWordNet View</title>
    <style>
.synset {
    border:1px solid blue;
    margin:10px;
}

.synset .s_id {
    font-weight:bold;
}

.synset .s_data {
    font-style:italic;
}    
    </style>
</head>

<body>
    <h2 align="center">RoWordNet</h2>
    <?php if(empty($data)){ ?>
    Word not found !
    <?php }else{ ?>

		<p>
				<a href="index.php?path=rown/rownttl&word=<?php echo $word;?>&sid=<?php echo $sid;?>">View as RDF</a>
				<a href="index.php?path=rown/rownjson&word=<?php echo $word;?>&sid=<?php echo $sid;?>">View as JSON</a>
		</p>

    <?php foreach($data['senses'] as $s){?>
    <div class="synset">
        <div class="s_id">Synset: <?php echo $s['id'];?> <?php echo $s['literal'];?></div>
        <div class="s_data">(<?php echo $s['pos'];?>) <?php echo $s['definition'];?></div>
        <div class="s_rel">
            <ul>
                
                <?php foreach($s['relations'] as $rel){ ?>
                    <li><?php echo $rel['rel'];?> <a href="index.php?path=rown/rown&word=&sid=<?php echo $rel['tid'];?>"><?php echo $rel['tid'];?></a> <?php echo $rel['tliteral'];?></li>
                <?php } ?>
                
            </ul>
        </div>
    </div>
    <?php } ?>
    <?php } ?>
    <br/><br/>
</body>


</html>
