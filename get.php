<?php

include 'functions.php';

$link = $_GET['link'];
$slug = $_GET['slug'];
$s = $_GET['s'];
$e = $_GET['e'];

$HOME = 'http://' . $_SERVER["SERVER_NAME"];

for ($i = $s; $i <= $e; $i++) { 
	$urls[] = $HOME . '/getTCV.php?link=' . $link . 'chuong-' . $i . '/&slug=' . $slug;
}

$content = multi_curl($urls);
$content = str_replace('⊙⊙', '…<br>…<br>…<br><br>', $content);

echo $content;
echo "s=$s&e=$e";