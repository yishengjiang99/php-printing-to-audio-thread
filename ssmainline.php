<?php
http_response_code(200);
header("Content-Type: application/javascript");
$presetIds=isset($_GET['presets']) ? $_GET['presets'] : "0";
$sffile =isset($r['sffile']) ? $r['sffile'] : "file.sf2";
$sff = fopen($sffile,"r");
$one = fopen("php://output","w");
$args=escapeshellarg(implode("\|",explode(",",$presetIds))."");
echo "./pdtaindex $sffile |grep $args|grep samp";
$proc=popen("./pdtaindex $sffile |grep $args|grep samp","r");
echo "const samples={};";
echo "const loopStarts={}";
while(!feof($proc)){
	$line=fgets($proc);
	$tokens=explode(",", $line);
	if(count($tokens)<7) continue;
	$strt = (int)$tokens[5];
	$end = (int)trim($tokens[6]);
	$sampleId= (int)$tokens[3];
	$loopStart= (int)$tokens[7]-$start;
	$loopEnd= (int)$tokens[8]-$end;

	fseek($sff,$start,SEEK_SET);
	fwrite($one, "\nsamples[$sampleId]=\"");
	fwrite($one, "\nloopStarts[$sampleId]=$loopStart;");
	fwrite($one, "\nloopEnd[$sampleId]=$loopEnd;");
	fwrite($one,base64_encode(fread($sff,$end-$strt)));
	fwrite($one,"\";\r\n");
}
echo file_get_contents("./sf-sample-proc.js");
?>