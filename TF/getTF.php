<?php

include '../db.php';
include '../functions.php';
include '../simple_html_dom.php';

$query = "SELECT * FROM site WHERE slug = :slug";
$stmt = $db->prepare($query);
$stmt->execute(array(':slug' => $_GET['slug']));
$site = $stmt->fetch(PDO::FETCH_ASSOC);

$html = file_get_html($_GET['link']);

$tieude = $html->find('a.chapter-title', 0)->plaintext;

$raw = $html->find('div#chapter-c', 0);
$noidung = remove($raw, $site['remove']);
$noidung = str_replace("<p>&nbsp;</p>", "", $noidung);

// nl2p
if ($site['nl2p'] == 'yes') {
	$noidung = strip_tags($noidung, '<br><p>');
	$noidung = preg_replace('/((<br\s*\/?>|<\/?p>)\s*)+/', "\n", $noidung);
	$noidung = nl2p($noidung);
}

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

function remove($nguon, $xoa) {
	foreach ($nguon->find($xoa) as $node) {
		$node->outertext = '';
	}
	return $nguon->innertext;
}
