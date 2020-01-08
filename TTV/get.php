<?php

include '../functions.php';

$s = $_GET['s'];
$e = $_GET['e']+1;

// lay tu 1 den 2
preg_match('#\['.$s.'\](.*?)\['.$e.'\]#is', file_get_contents('http://' . $_SERVER["SERVER_NAME"] . '/TTV/data.php?name='.$_GET['name']), $links);

// lay link tu 1 den 2
preg_match_all('#</a> <a href="(.*?)"#is', $links[1], $lists);

$noidung = multi_curl($lists[1]);

$noidung = str_replace('⊙⊙', '…<br>…<br>…<br><br>', $noidung);

echo $noidung;
echo "s=$s&e={$_GET['e']}";
