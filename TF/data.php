<?php

include '../db.php';

$query = "SELECT * FROM story WHERE name = :name";
$stmt = $db->prepare($query);
$stmt->execute(array(':name' => $_GET['name']));
$story = $stmt->fetch(PDO::FETCH_ASSOC);

$HOME = 'http://' . $_SERVER["SERVER_NAME"] . '/TF';

$list = array();
$count = 0;
foreach (json_decode($story['content']) as $url => $title) {
	$count++;
	$list[] = '<div><a href="/bookmark.php?title=[TF] '.$story['name'].' ➧ '.$title.'"><font color="black">['.$count.']</font></a> <a href="'.$HOME.'/getTF.php?link='.$url.'&slug='.$story['name'].'">'.$title.'</a></div>';
}

?>
<html>
	<title><?php echo $story['name'] ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style type="text/css">
		a {text-decoration: none;}
	</style>
	<h1><?php echo $story['name'] ?></h1>
	<?php echo implode("\n", $list) ?>
</html>