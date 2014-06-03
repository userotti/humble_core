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
header('Content-type: application/pdf');
//passthru("xvfb-run wkhtmltopdf tmp.html -");
//unlink('tmp.html');
passthru("xvfb-run wkhtmltopdf -q --margin-bottom 0 --margin-left 0 --margin-top 0 --margin-right 0 --ignore-load-errors http://api.humble.co.za/1.1/service/order/".$params['saleguid']." -");
//passthru("xvfb-run wkhtmltopdf -q --margin-bottom 0 --margin-left 0 --margin-top 0 --margin-right 0 --ignore-load-errors https://sprresponsive.com/dev/sprinvoice/paid.html -");

?>