<?php

$RELATE_PDF2TEXT_TYPE_INTERNAL="INTERNAL";
$RELATE_PDF2TEXT_TYPE_SYSTEM="SYSTEM";

function RELATE_pdf2text($pathPdf, $pathTxt){
	global $settings;
	global $RELATE_PDF2TEXT_TYPE_INTERNAL,$RELATE_PDF2TEXT_TYPE_SYSTEM;
	
	$pathTxt=changeFileExtension($pathTxt,"txt");
	$output="";
	$type=$settings->get("pdftotext.type","SYSTEM");
	if(strcasecmp($type,$RELATE_PDF2TEXT_TYPE_INTERNAL)==0){
		$pdf=new \PdfToText($pathPdf);
		file_put_contents($pathTxt,$pdf->Text);
	}else{
		ob_start();
		passthru("pdftotext -layout \"$pathPdf\" \"$pathTxt\"");
		$output .= ob_get_contents();
		ob_end_clean(); //Use this instead of ob_flush()
	}
	@chown($pathTxt,$settings->get("owner_user"));
	@chgrp($pathTxt,$settings->get("owner_group"));
	return $output;
}

?>