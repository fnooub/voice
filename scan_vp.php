<?php

error_reporting(0);

function search($find)
{
	$file = 'VietPhrase.txt';
	$find = clean_special($find);

	$content = file_get_contents($file);
	$pattern = "/^.*\b$find\b.*$/miu";

	if (preg_match_all($pattern, $content, $matches)) {
		foreach ($matches[0] as $key => $value) {
			$cn_word[$key] = explode("=", $value)[0];
		}
		$noidung = implode("", $cn_word);

		$noidung = clean_special($noidung);
		$noidung = mb_str_split( $noidung );

		$length_word = length_word($find);

		$stats = build_stats($noidung, $length_word);

		$word = $count = NULL;
		foreach ($stats as $word => $count) {
			break;
		}

		// hanviet
		$hanviet = explode(" ", $word);
		foreach ($hanviet as $term) {
			$hv[] = han_viet(trim($term));
		}

		//echo "$word = $count\n";

		if ($count > 1) {
			$word = str_replace(" ", "", $word);
		} else {
			$word = '*';
		}

		return array('find' => $find, 'word' => $word, 'count' => $count, 'hv' => implode(" ", $hv));

	} else {
		return array('find' => $find, 'word' => '*', 'count' => 0, 'hv' => 0);
	}
}

function han_viet($find)
{
	$pattern = "/^\b$find\b=(.*)$/miu";
	if (preg_match($pattern, file_get_contents('ChinesePhienAmWords.txt'), $matches)) {
		return trim($matches[1]);
	}
}

function clean_special($string)
{
	$string = preg_replace( "/(,|\"|\.|\?|:|!|;|\*| - )/", " ", $string );
	$string = preg_replace( "/\s+/", " ", $string );
	return trim($string);
}

function length_word($string)
{
	$string = preg_replace('/\s+/', ' ', $string);
	$string = trim($string);
	return substr_count($string, ' ')+1;
}

/**
 * Parses text and builds array of phrase statistics
 *
 * @param string $input source text
 * @param int $num number of words in phrase to look for
 * @rerturn array array of phrases and counts
 */
function build_stats($input,$num) {

	//init array
	$results = array();
	
	//loop through words
	foreach ($input as $key=>$word) {
		$phrase = '';
		
		//look for every n-word pattern and tally counts in array
		for ($i=0;$i<$num;$i++) {
			if ($i!=0) $phrase .= ' ';
			$phrase .= mb_strtolower( $input[$key+$i], 'UTF-8' );
		}
		if ( !isset( $results[$phrase] ) )
			$results[$phrase] = 1;
		else
			$results[$phrase]++;
	}
	if ($num == 1) {
		//clean boring words
		$a = explode(" ","the of and to a in that it is was i for on you he be with as by at have are this not but had his they from she which or we an there her were one do been all their has would will what if can when so my");
		foreach ($a as $banned) unset($results[$banned]);
	}
	
	//sort, clean, return
	array_multisort($results, SORT_DESC);
	unset($results[""]);
	return $results;
}

?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>scan vietphrase</title>
<form method="post">
	<textarea name="word" style="width: 100%;"><?php if(isset($_POST['word'])){ echo $_POST['word']; } ?></textarea>
	<input type="checkbox" name="hv" <?php if (isset($_POST['hv'])) { echo "checked"; } ?>> Hán-Việt<br>
	<input type="submit" value="Scan Text" style="width: 100%; padding: 5px; margin-top: 10px">
</form>
<code><small>ps: khi <font color="red">word</font> = <font color="red">*</font> thì cần chia nhỏ hơn. phân tách 2 chữ là tốt nhất để scan (vd: nhất niệm, vĩnh hằng). Hán-Việt sẽ tự động phân tách từng chữ bởi dấu cách (khoảng trắng)</small></code>
<?php

if (isset($_POST['word'])) {

	if (!empty(trim($_POST['word']))) {
		$arr = array();

		if (isset($_POST['hv'])) {
			$arr = explode(" ", $_POST['word']);
		} else {
			$arr = explode(",", $_POST['word']);
		}

		$i=0;
		
		foreach ($arr as $value) {
			$i++;
			echo "<h5>$i</h5>";
			echo "<ul>";
			echo "<li>find: ".search($value)['find']."</li>";
			echo "<li>word: ".search($value)['word']."</li>";
			echo "<li>count: ".search($value)['count']."</li>";
			echo "<li>hanviet: ".search($value)['hv']."</li>";
			;
			echo "</ul>\n";
			$words[] = search($value)['word'];
		}
		
		//echo implode("", $words);
		echo "<!-- 1. Define some markup -->\n<button class=\"btn\">Copy</button>\n<div class=\"copy\">".implode("", $words)."</div>\n";
	} else {
		echo "<p>Rỗng</p>\n";
	}
}

?>
<!-- 2. Include library -->
<script src="clipboard.min.js"></script>

<!-- 3. Instantiate clipboard -->
<script>
var clipboard = new ClipboardJS('.btn', {
    target: function() {
        return document.querySelector('div.copy');
    }
});

clipboard.on('success', function(e) {
    console.log(e);
});

clipboard.on('error', function(e) {
    console.log(e);
});
</script>
