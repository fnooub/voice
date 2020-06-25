<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Scan vietphrase</title>
<style>
.tooltip {
  position: relative;
  display: inline-block;
  background-color: yellow;
  padding: 10px;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  
  /* Position the tooltip */
  position: absolute;
  z-index: 1;
  top: -5px;
  left: 105%;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
}
</style>
<form method="post">
	<textarea name="text" style="width: 100%" placeholder="núi sông, tiên hiệp..."></textarea>
	<input type="submit" value="Scan Text" style="width: 100%; margin: 5px 0; padding: 5px 0">
</form>
<p><small>ghi chú: nhập cụm từ có nghĩa, 2 từ để tìm chuẩn xác nhất</small></p>
<?php

error_reporting(0);

if (isset($_POST['text'])) {
	if (!empty(trim($_POST['text']))) {
		$file = 'VietPhrase.txt';
		$find = clean_special($_POST['text']);

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

			$word = str_replace(" ", "", $word);

			echo "<div>Kết quả phân tích: ($count)</div>\n";
			echo "<!-- 1. Define some markup -->\n<div class=\"tooltip\" id=\"btn\" data-clipboard-text=\"$word\">$word <span class=\"tooltiptext\">Sao chép</span> = ".implode("", $hv)."</div>\n";
			//echo $word;
			echo "<hr>";
			echo "<div>Kết quả tìm thấy: (".count($matches[0]).")</div>\n";
			echo implode("<br>", array_slice($matches[0], 0, 20));

		} else {
			echo "<div>Không tìm thấy</div>";
		}
	} else {
		echo "<div>Rỗng</div>";
	}

}

function han_viet($find)
{
	$pattern = "/^\b$find\b=(.*)$/miu";
	if (preg_match($pattern, file_get_contents('ChinesePhienAmWords.txt'), $matches)) {
		return $matches[1];
	}
}

function clean_special($string)
{
	$string = preg_replace( "/(,|\"|\.|\?|:|!|;|\*| - )/", " ", $string );
	$string = preg_replace( "/\s+/", " ", $string );
	return trim($string);
}

function mb_str_split_x( $string ) {
	# Split at all position not after the start: ^
	# and not before the end: $
	return preg_split('/(?<!^)(?!$)/u', $string );
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
<!-- 2. Include library -->
<script src="clipboard.min.js"></script>

<!-- 3. Instantiate clipboard by passing a HTML element -->
<script>
var btn = document.getElementById('btn');
var clipboard = new ClipboardJS(btn);

clipboard.on('success', function(e) {
	console.log(e);
});

clipboard.on('error', function(e) {
	console.log(e);
});
</script>
