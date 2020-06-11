<title>Stories</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
	a { text-decoration: none; }
	input[type=submit], textarea {
		display: block;
		margin: 5px 0;
	}
</style>
<?php

include 'db.php';

$query = "SELECT * FROM story ORDER BY id DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// xoa id
if (isset($_GET['xoa_id'])) { 
	$query = "DELETE FROM story WHERE id = :id";
	$stmt = $db->prepare($query);
	$stmt->execute(array(':id' => $_GET['xoa_id']));
	header('Location: stories.php');
	exit;
}

include 'navbar.php';
echo '<p><a href="TTV/set.php">TTV set</a> | <a href="TF/set.php">TF set</a></p>';

foreach ($result as $row) {
	if ($row['flag'] == 'ttv') {
		$url = 'TTV/data.php?name='.$row['name'];
	} elseif ($row['flag'] == 'tf') {
		$url = 'TF/data.php?name='.$row['name'];
	}
	?>
	<pre>[<?php echo $row['flag'] ?>] <a href="<?php echo $url ?>"><?php echo $row['name'] ?></a> <span style="background-color: yellow"><?php echo count(json_decode($row['content'], true)) ?></span> <a href="?xoa_id=<?php echo $row['id'] ?>" onclick = "if (! confirm('Xoá?')) { return false; }"><span style="background-color: #c9f795">Xoá</span></a></pre>
	<?php
}
