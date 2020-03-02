<?php

$AccessToken = 'wkDt6TmyCgAAAAAAAAACOM3JthdJTcrW5uIcczZ-0b--e-NH9MfzjNlJ_0hiEPHv';

function upload($path, $mode = 'overwrite')
{
	global $AccessToken;
	$fp   = fopen($path, 'rb');
	$size = filesize($path);
	
	$cheaders = array(
		'Authorization: Bearer ' . $AccessToken,
		'Content-Type: application/octet-stream',
		'Dropbox-API-Arg: {"path":"/' . $path . '", "mode":"' . $mode . '"}'
	);
	
	$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
	curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_INFILE, $fp);
	curl_setopt($ch, CURLOPT_INFILESIZE, $size);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	
	return json_decode($response);
	curl_close($ch);
	fclose($fp);
	
}

function sharing($path)
{
	global $AccessToken;
	$parameters = array(
		'path' => $path
	);
	
	$headers = array(
		'Authorization: Bearer ' . $AccessToken,
		'Content-Type: application/json'
	);
	
	$curlOptions = array(
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => json_encode($parameters),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_VERBOSE => true
	);
	
	$ch = curl_init('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
	curl_setopt_array($ch, $curlOptions);
	
	$response = curl_exec($ch);
	return json_decode($response);
	
	curl_close($ch);
}

function download($in_filepath, $out_filepath)
{
	global $AccessToken;
	$out_fp = fopen($out_filepath, 'w+');
	if ($out_fp === FALSE) {
		echo "fopen error; can't open $out_filepath\n";
		return (NULL);
	}
	
	$url = 'https://content.dropboxapi.com/2/files/download';
	
	$header_array = array(
		'Authorization: Bearer ' . $AccessToken,
		'Content-Type:',
		'Dropbox-API-Arg: {"path":"' . $in_filepath . '"}'
	);
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_array);
	curl_setopt($ch, CURLOPT_FILE, $out_fp);
	
	$metadata = null;
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $header) use (&$metadata)
	{
		$prefix = 'dropbox-api-result:';
		if (strtolower(substr($header, 0, strlen($prefix))) === $prefix) {
			$metadata = json_decode(substr($header, strlen($prefix)), true);
		}
		return strlen($header);
	});
	
	$output = curl_exec($ch);
	
	if ($output === FALSE) {
		echo "curl error: " . curl_error($ch);
	}
	
	curl_close($ch);
	fclose($out_fp);
	
	return ($metadata);
}