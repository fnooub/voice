<?php

include '../db.php';
include '../functions.php';

$single_curl = single_curl($_GET['link']);

$query = "SELECT * FROM site WHERE slug = :slug";
$stmt = $db->prepare($query);
$stmt->execute(array(':slug' => $_GET['slug']));
$site = $stmt->fetch(PDO::FETCH_ASSOC);

preg_match('#<h2>(.*?)</h2>#is', $single_curl, $title);

preg_match('#<div class="box-chap box-chap-[0-9]+">(.*?)</div>#is', $single_curl, $content);

$tieude = $title[1];
$noidung = $content[1];
$noidung = preg_replace('/\t/', '', $noidung);
$noidung = nl2p($noidung);

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
