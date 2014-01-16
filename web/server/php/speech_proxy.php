<?php
/*
<!-- 
Licensed by AT&T under 'Software Development Kit Tools Agreement.' September 2011
TERMS AND CONDITIONS FOR USE, REPRODUCTION, AND DISTRIBUTION: http://developer.att.com/sdk_agreement/
Copyright 2011 AT&T Intellectual Property. All rights reserved. http://developer.att.com
For more information contact developer.support@att.com
-->
*/
header("Content-Type: text/html; charset=ISO-8859-1");
$api_key = "kdgjx3k1joucj5acmfokbcbtwwfn1miq";
$secret_key = "vnrpjlrjr57inj5inetscmsmgawtmo1f";
$FQDN = "https://api.att.com";
$endpoint = $FQDN."/rest/2/SpeechToText";
$scope = "SPEECH";
$oauth_file = "speechoauthtoken.php";
$refreshTokenExpiresIn = "";
$default_file = "speech.wav";
$speech_context_config = "Questions and Answers,Generic,TV,BusinessSearch,Websearch,SMS,Voicemail";
$x_arg = "ClientApp=NoteTaker, ClientVersion=1.0.1,DeviceType=iPhone4";  

include($oauth_file);
include("tokens.php");
error_reporting(0);
session_start();


$chkChunked = $_REQUEST["chkChunked"];
$_SESSION["chkChunked"] = $chkChunked;
$speechcontext = $_REQUEST["speechcontext"];
$_SESSION["speechcontext"] = $speechcontext;
$header_array = array();

$speech_context_array = preg_split('/,/', $speech_context_config, -1, PREG_SPLIT_NO_EMPTY);
$counter = count($speech_context_array);

$fullToken["accessToken"]=$accessToken;
$fullToken["refreshToken"]=$refreshToken;
$fullToken["refreshTime"]=$refreshTime;
$fullToken["updateTime"]=$updateTime;

$fullToken=check_token($FQDN,$api_key,$secret_key,$scope,$fullToken,$oauth_file);
$accessToken=$fullToken["accessToken"];

if($speechcontext == "") {
	 $speechcontext = "Generic";
}
if($chkChunked == true) {
	$transfer_encoding = 'Content-Transfer-Encoding: chunked';
	array_push($header_array, $transfer_encoding);
}
if($x_arg != null) {	
	$x_arg_header = "X-Arg:".urlencode($x_arg);
	array_push($header_array, $x_arg_header);
}

$filename = $_FILES['f1']['name'];

if($filename == null) {
	// Use QueryString parameter
	$serverFile = $_REQUEST["ServerFile"];
	if ($serverFile == "") $serverFile = $default_file;
	$filename = dirname(__FILE__).'/'.$serverFile;
	$file_binary = fread(fopen($filename, 'rb'), filesize($filename));
} else{
	$temp_file = $_FILES['f1']["tmp_name"];
	$dir = dirname($temp_file);
	$file_binary = fread(fopen($temp_file, "r"), filesize($temp_file));
}
$ext = end(explode('.', $filename));
$type = 'audio/'.$ext;

if($type == 'audio/wav' || $type == 'audio/amr' || $type =='audio/amr-wb' || $type =='audio/x-speex') {
	$speech_info_url = $FQDN."/rest/2/SpeechToText";
	$authorization = "Authorization: BEARER ".$accessToken;
	$accept = "Accept: application/json";
	$context = "X-Speech-Context:".$speechcontext;
	
	$content = "Content-Type:".$type;
	array_push($header_array, $authorization, $accept, $context, $content);

	$speech_info_request = curl_init();
	curl_setopt($speech_info_request, CURLOPT_URL, $speech_info_url);
	curl_setopt($speech_info_request, CURLOPT_HTTPGET, 1);
	curl_setopt($speech_info_request, CURLOPT_HEADER, 0);
	curl_setopt($speech_info_request, CURLINFO_HEADER_OUT, 1);
	curl_setopt($speech_info_request, CURLOPT_HTTPHEADER, $header_array);
	curl_setopt($speech_info_request, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($speech_info_request, CURLOPT_POSTFIELDS, $file_binary);
	curl_setopt($speech_info_request, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($speech_info_request, CURLOPT_SSL_VERIFYHOST, false);

	$speech_info_response = curl_exec($speech_info_request);
	$responseCode=curl_getinfo($speech_info_request,CURLINFO_HTTP_CODE);

	if($responseCode==200)
	{
		echo $speech_info_response;
	}else{
		// TO-DO - return an error JSON here
		$msghead="Error";
		$msgdata=curl_error($speech_info_request);
		$errormsg=$msgdata.$speech_info_response;
		echo $errormsg;
	}
	curl_close ($speech_info_request);
}else{
	// TO-DO - return an error JSON here
	echo "Invalid file specified. Valid file formats are .wav, .amr, .amr-wb and x-speex'";
}

?>

