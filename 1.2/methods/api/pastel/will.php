<?php
	console("will.php");

	$uname = 'HUM012NETFTP';
	$pword = '88MNHe';
	$pin = '387467';

	/*class SOAPStatementRequest {
		var $Username;
		var $Password;
		var $PIN;
	}

	// For debugging use this initializer
	$soap = new SoapClient("https://www.netcash.co.za/netserv/ncUpload/service.asmx?wsdl",
	array("trace" => 1,'soap_version' => SOAP_1_1,'style' => SOAP_DOCUMENT,'encoding' => SOAP_LITERAL));
	unset($request);
	
	// Create and populate the request
	$request = new SOAPStatementRequest();
	$request->Username = "HUM012NETFTP";
	$request->Password = "88MNHe";
	$request->PIN = "387467";

	// Send the request
	$result = $soap->Request_UploadReport($request);
	// Debugging printouts
	echo "<b>Request Headers:</b>: <br />". $soap->__getLastRequestHeaders() ."<br />";
	echo "<b>Request Data:</b>: <br />". $soap->__getLastRequest() ."<br />";
	echo "<b>Response Headers:</b>: <br />". $soap->__getLastResponseHeaders() ."<br />";
	echo "<b>Response Data:</b>: <br />". $soap->__getLastResponse() ."<br />";
	
	// Dump result to screen
	print_r($result);*/



	$file = "test.txt";
	$f = fopen($file,"r",true);
	$theData = fread($f,filesize($file));
	fclose($f);
	
	class SOAPStatementRequest {
		var $Username;
		var $Password;
		var $PIN;
		var $tDate; 
	}

	// For debugging use this initializer
	$soap = new SoapClient("http://www.netcash.co.za/netserv/ncUpload/service.asmx?wsdl",
	array("trace" => 1,'soap_version' => SOAP_1_1,'style' => SOAP_DOCUMENT,'encoding' => SOAP_LITERAL));
	unset($request);

	// Create and populate the request
	$request = new SOAPStatementRequest();
	$request->Username = "myUsername";
	$request->Password = "myPassword";
	$request->PIN = "987654";
	$request->FileContents=$theData;

	var_dump($request);

/*	// Send the request
	$result = $soap->UploadBatchFile($request);
	// Debugging printouts
	echo "<b>Request Headers:</b>: <br />". $soap->__getLastRequestHeaders() ."<br />";
	echo "<b>Request Data:</b>: <br />". $soap->__getLastRequest() ."<br />";
	echo "<b>Response Headers:</b>: <br />". $soap->__getLastResponseHeaders() ."<br />";
	echo "<b>Response Data:</b>: <br />". $soap->__getLastResponse() ."<br />";
	
	// Dump result to screen
	print_r($result);   

	*/
	



?>