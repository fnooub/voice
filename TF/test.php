<?php

include '../db.php';
include '../functions.php';

$single_curl = single_curl('https://truyenfull.net/pham-nhan-tu-tien-chi-tien-gioi-thien-pham-nhan-tu-tien-2/chuong-1/');


preg_match('#<h2>(.*?)</h2>#is', $single_curl, $title);

preg_match('#<hr class="chapter-end">(.*?)<hr class="chapter-end">#is', $single_curl, $content);

print_r($content);