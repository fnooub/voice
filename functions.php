<?php

function single_curl($link)
{
	// Tạo mới một cURL
	$ch = curl_init();

	// Cấu hình cho cURL
	curl_setopt($ch, CURLOPT_URL, $link); // Chỉ định địa chỉ lấy dữ liệu
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36'); // Giả tên trình duyệt $_SERVER['HTTP_USER_AGENT']
	curl_setopt($ch, CURLOPT_HEADER, 0); // Không kèm header của HTTP Reponse trong nội
	curl_setopt($ch, CURLOPT_TIMEOUT, 600); // Định timeout khi curl
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Trả kết quả về ở hàm curl_exec
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Không xác nhận chứng chì ssl

	// Thực thi cURL
	$result = curl_exec($ch);

	// Ngắt cURL, giải phóng
	curl_close($ch);

	return $result;

}

function multi_curl($links){
	$mh = curl_multi_init();
	foreach($links as $k => $link) {
		$ch[$k] = curl_init();
		curl_setopt($ch[$k], CURLOPT_URL, $link);
		curl_setopt($ch[$k], CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
		curl_setopt($ch[$k], CURLOPT_HEADER, 0);
		curl_setopt($ch[$k], CURLOPT_TIMEOUT, 0);
		curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch[$k], CURLOPT_SSL_VERIFYPEER, 0);
		curl_multi_add_handle($mh, $ch[$k]);
	}
	$running = null;
	do {
		curl_multi_exec($mh, $running);
	} while($running > 0);
	foreach($links as $k => $link) {
		$result[$k] = curl_multi_getcontent($ch[$k]);
		curl_multi_remove_handle($mh, $ch[$k]);
	}
	curl_multi_close($mh);
	return join('', $result);

}

// lọc thẻ p vào nội dung văn bản
function nl2p($string, $nl2br = true)
{
	// Normalise new lines
	$string = str_replace(array("\r\n", "\r"), "\n", $string);

	// Extract paragraphs
	$parts = explode("\n", $string);

	// Put them back together again
	$string = '';

	foreach ($parts as $part) {
		$part = trim($part);
		if ($part) {
			if ($nl2br) {
				// Convert single new lines to <br />
				$part = nl2br($part);
			}
			$string .= "<p>$part</p>\n";
		}
	}

	return $string;
}

// luu anh ho tro ssl
function save_image($fullpath, $img) {
	// open file descriptor
	$fp = fopen ($fullpath, 'w+') or die('Unable to write a file'); 
	// file to download
	$ch = curl_init($img);
	// enable SSL if needed
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	// output to file descriptor
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	// set large timeout to allow curl to run for a longer time
	curl_setopt($ch, CURLOPT_TIMEOUT, 600);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
	// Enable debug output
	curl_setopt($ch, CURLOPT_VERBOSE, true);   
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}

//save_image('Images/cover.jpg', 'https://www.gutenberg.org/pics/pg-logo-002.png');

function get_title($title, $mb = false) {
	$title = strip_tags($title);
	$title = preg_replace('/\s+/', ' ', $title);
	$title = trim($title);
	if ($mb) {
		$title = mb_convert_case($title, MB_CASE_TITLE, "UTF-8");
	}
	return $title;
}

function slug($link)
{
	$a_str = array('ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'á', 'à', 'ả', 'ã', 'ạ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ');
	$d_str = array('đ', 'Đ');
	$e_str = array('é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ');
	$o_str = array('ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ');
	$i_str = array('í', 'ì', 'ỉ', 'ị', 'ĩ', 'Í', 'Ì', 'Ỉ', 'Ị', 'Ĩ');
	$u_str = array('ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ữ', 'ử', 'ự', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự');
	$y_str = array('ý', 'ỳ', 'ỷ', 'ỵ', 'ỹ', 'Ý', 'Ỳ', 'Ỷ', 'Ỵ', 'Ỹ');

	$link = str_replace($a_str, 'a', $link);
	$link = str_replace($d_str, 'd', $link);
	$link = str_replace($e_str, 'e', $link);
	$link = str_replace($o_str, 'o', $link);
	$link = str_replace($i_str, 'i', $link);
	$link = str_replace($u_str, 'u', $link);
	$link = str_replace($y_str, 'y', $link);

	$link = strtolower($link); //chuyển tất cả sang chữ thường
	$link = preg_replace('/[^a-z0-9]/', ' ', $link); //ngoài a-z0-9 thì chuyển sang khoảng trắng
	$link = preg_replace('/\s\s+/', ' ', $link); //2 khoảng trắng trở lên thì chỉ lấy 1
	$link = trim($link); //loại bỏ khoảng trắng đầu cuối
	$link = str_replace(' ', '-', $link); //chuyển khoảng trắng sang gạch ngang (-)
	return $link;
}

function filesize_formatted($path)
{
	$size = filesize($path);
	$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$power = $size > 0 ? floor(log($size, 1024)) : 0;
	return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

function getRemoteFilesize($file_url, $formatSize = true)
{
	$head = array_change_key_case(get_headers($file_url, 1));
	// content-length of download (in bytes), read from Content-Length: field

	$clen = isset($head['content-length']) ? $head['content-length'] : 0;

	// cannot retrieve file size, return "-1"
	if (!$clen) {
		return -1;
	}

	if (!$formatSize) {
		return $clen; 
		// return size in bytes
	}

	$size = $clen;
	switch ($clen) {
		case $clen < 1024:
			$size = $clen .' B'; break;
		case $clen < 1048576:
			$size = round($clen / 1024, 2) .' KB'; break;
		case $clen < 1073741824:
			$size = round($clen / 1048576, 2) . ' MB'; break;
		case $clen < 1099511627776:
			$size = round($clen / 1073741824, 2) . ' GB'; break;
	}

	return $size; 
	// return formatted size
}

function loc($word)
{
	$word = html_entity_decode($word);
	// loc chu
	$word = preg_replace(array('/\bria\b/iu', '/\bsum\b/iu', '/\bboa\b/iu', '/\bmu\b/iu', '/\bah\b/iu', '/\buh\b/iu', '/\bcm\b/iu', '/\bkm\b/iu', '/\bkg\b/iu', '/\bcmn\b/iu', '/\bgay go\b/iu'), array('dia', 'xum', 'bo', 'mư', 'a', 'ư', 'xen ti mét', 'ki lô mét', 'ki lô gam', 'con mẹ nó', 'khó khăn'), $word);
	// loc ki tu dac biet
	$word = preg_replace('/…/', '...', $word);
	$word = preg_replace('/\.(?:\s*\.)+/', '...', $word);
	$word = preg_replace('/,(?:\s*,)+/', ',', $word);
	$word = preg_replace('/-(?:\s*-)+/', '', $word);
	$word = preg_replace('/-*o\s*(0|O)\s*o-*/', '...', $word);
	$word = preg_replace('/~/', '-', $word);
	$word = preg_replace('/\*/', '', $word);
	$word = preg_replace('/ +(\.|\?|!|,)/', '$1', $word);
	// thay the
	$word = str_replace('"..."', '"Lặng!"', $word);
	return $word;
}

function loc_title($text)
{
	$text = preg_replace('/[^a-z0-9A-Z[:space:]àáãạảăắằẳẵặâấầẩẫậèéẹẻẽêềếểễệđìíĩỉịòóõọỏôốồổỗộơớờởỡợùúũụủưứừửữựỳỵỷỹýÀÁÃẠẢĂẮẰẲẴẶÂẤẦẨẪẬÈÉẸẺẼÊỀẾỂỄỆĐÌÍĨỈỊÒÓÕỌỎÔỐỒỔỖỘƠỚỜỞỠỢÙÚŨỤỦƯỨỪỬỮỰỲỴỶỸÝ]/u', '', $text);
	$text = preg_replace('/0+(\d)/', '$1', $text);
	return $text;
}

function upload_dropbox($path, $mode = 'add', $token, $show = false) {
	$fp = fopen($path, 'rb');
	$size = filesize($path);

	$cheaders = array('Authorization: Bearer ' . $token, 'Content-Type: application/octet-stream', 'Dropbox-API-Arg: {"path":"/test/' . $path . '", "mode":"' . $mode . '"}');

	$ch = curl_init('https://content.dropboxapi.com/2/files/upload');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
	curl_setopt($ch, CURLOPT_PUT, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_INFILE, $fp);
	curl_setopt($ch, CURLOPT_INFILESIZE, $size);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);

	if ($show) {
		echo $response;
	}
	curl_close($ch);
	fclose($fp);
}
//upload_dropbox('data.txt', 'overwrite', 'wkDt6TmyCgAAAAAAAAAB1Tp6TyGgcHivthPG7WD8Ka3aNkQmys95x-7dKSh51nCswu');
