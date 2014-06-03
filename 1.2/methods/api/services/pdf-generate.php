<?php
/*

Caldoza Engine ------------------------

File	:	api/services/pdf-convert.php
Created	: 	2014-02-04

*/
//$content = file_get_contents('http://humble.co.za/service/invoice/nope');
//$file = fopen('tmp.html', 'w+');
//fwrite($file, $content);
//fclose($file);
$template = $params['type'];
if(!empty($_GET['template'])){
	$template = $_GET['template'];
}
header('Content-Description: File Transfer');
header('Content-type: application/pdf');
//header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$template.'.pdf"'); //<<< Note the " " surrounding the file name
header('Content-Transfer-Encoding: binary');
header('Connection: Keep-Alive');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
//header('Content-Length: ' . filesize($file));
//passthru("xvfb-run wkhtmltopdf tmp.html -");
//unlink('tmp.html');
passthru("xvfb-run wkhtmltopdf -q --margin-bottom 10 --margin-left 5 --margin-top 5 --margin-right 5 --ignore-load-errors http://api.humble.co.za/1.1/".$params['token']."/pdf-report/".$params['type']."?".urlencode( http_build_query( $_GET ) )." -");
//passthru("xvfb-run wkhtmltopdf -q --margin-bottom 0 --margin-left 0 --margin-top 0 --margin-right 0 --ignore-load-errors https://sprresponsive.com/dev/sprinvoice/paid.html -");

?>