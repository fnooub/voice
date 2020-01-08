<?php

include 'db.php';

$sql = "SELECT table_name AS `Table`, round(((data_length + index_length) / 1024 / 1024), 2) `Size in MB` FROM information_schema.TABLES WHERE table_schema = \"yg1wwu54z7ur2jc3\" AND table_name = \"story\"";

$stmt = $db->query($sql);
$size = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<p>Table: ".$size['Table']." => ".$size['Size in MB']." MB</p>";
