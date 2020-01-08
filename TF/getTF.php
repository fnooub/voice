<?php

include '../db.php';
include '../functions.php';

$single_curl = single_curl($_GET['link']);

$query = "SELECT * FROM site WHERE slug = :slug";
$stmt = $db->prepare($query);
$stmt->execute(array(':slug' => $_GET['slug']));
$site = $stmt->fetch(PDO::FETCH_ASSOC);

preg_match('#<h2>(.*?)</h2>#is', $single_curl, $title);

preg_match('#<div id="chapter-c" class="chapter-c">(.*?)<hr class="chapter-end">#is', $single_curl, $content);

$tieude = strip_tags($title[1]);
$tieude = preg_replace('/chương 0+(\d+)/iu', 'Chương $1', $tieude);
$noidung = $content[1];

$noidung = str_replace("<em>*Chương này có nội dung ảnh, nếu bạn không thấy nội dung chương, vui lòng bật chế độ hiện hình ảnh của trình duyệt để đọc.</em>", "", $noidung);

$noidung = preg_replace('/<img[^>]*>/', '', $noidung);
$noidung = preg_replace('#<a(.*?)</a>#is', '', $noidung);
$noidung = preg_replace('#<script(.*?)</script>#is', '', $noidung);
$noidung = preg_replace('#<style(.*?)</style>#is', '', $noidung);
// nl2p
if ($site['nl2p'] == 'yes') {
	$noidung = preg_replace('/\s+/', ' ', $noidung);
	$noidung = strip_tags($noidung, '<br><p>');
	$noidung = preg_replace('/((<br\s*\/?>|<\/?p>)\s*)+/', "\n", $noidung);
	$noidung = nl2p($noidung);
	$noidung = preg_replace('/>Chương\s*\d+.*?</iu', '><', $noidung);
}

// fix
$noidung = preg_replace(array('/Bạn đang đọc truyện được copy tại/iu', '/Bạn đang đọc truyện được lấy tại/iu', '/Bạn đang đọc truyện tại/iu', '/Text được lấy tại/iu', '/nguồn truyện\s*:/iu', '/nguồn\s*:/iu', '/chấm cơm\.?/iu', '/www\s*\./i', '/https?\s*:\s*\/?\/?/i', '/\.\s*vn/i', '/\.\s*com/iu', '/Truyen\s*FULL/iu', '/truyện\s*full/iu', '/Đọc Truyện Online Tại/iu', '/Đọc Truyện Kiếm Hiệp Hay Nhất\:?/iu', '/Truyện.{1,10}Hiệp/iu', '/truyenyy/iu', '/Truyện\s*YY/iu', '/\(adsbygoogle \= window\.adsbygoogle \|\| \[\]\)\.push\(\{\}\);/'), '', $noidung);

// loc
if ($site['loc'] == 'yes') {
	$noidung = loc($noidung);
}

$query = "SELECT s, r, flag FROM regex WHERE site_id = :site_id";
$stmt = $db->prepare($query);
$stmt->execute(array(':site_id' => $site['id']));
$regexs = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($regexs as $regex) {
	if ($regex['flag'] == 'g') {
		$noidung = preg_replace('/' . $regex['s'] . '/', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'u') {
		$noidung = preg_replace('/' . $regex['s'] . '/u', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'i') {
		$noidung = preg_replace('/' . $regex['s'] . '/i', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'is') {
		$noidung = preg_replace('#' . $regex['s'] . '#is', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'iu') {
		$noidung = preg_replace('/' . $regex['s'] . '/iu', $regex['r'], $noidung);
	} elseif ($regex['flag'] == 'td') {
		$tieude = preg_replace('/' . $regex['s'] . '/iu', $regex['r'], $tieude);
	}
}

echo "$tieude<br>➥<br>➥<br><br>$noidung<br>⊙⊙";
