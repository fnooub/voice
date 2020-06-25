<?php

include 'db.php';

if (isset($_POST['content'])) {
	if (!empty($_POST['content'])) {
		$query = "INSERT INTO note (content) VALUES (:content)";
		$stmt = $db->prepare($query) ;
		$stmt->execute(array(':content' => $_POST['content']));
	}
}

if(isset($_GET['xoa_id'])){ 
	$query = "DELETE FROM note WHERE id = :id";
	$stmt = $db->prepare($query) ;
	$stmt->execute(array(':id' => $_GET['xoa_id']));

	header('Location: note.php');
	exit;
} 

$notes = $db->query("SELECT * FROM note ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<title>Ghi chú</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style type="text/css">
  a {text-decoration: none;}
  input[type=submit],
  textarea {
  	display: block;
  	margin: 10px 0;
  	width: 100%;
  }
</style>
<?php include 'navbar.php'; ?>
<form method="post">
	<textarea name="content"></textarea>
	<input type="submit" value="Note">
</form>
<?php

foreach ($notes as $note) {
	$note['content'] = preg_replace('/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]\.[^\s]{2,})/', '<a href="$1">$1</a>', $note['content']);
	?>
	<p><font color="red"><strong><a href="?xoa_id=<?php echo $note['id'] ?>" onclick = "if (! confirm('Xoá?')) { return false; }"><?php echo $note['id'] ?></a>.</strong></font> <?php echo nl2br($note['content']) ?></p><hr>
	<?php
	//htmlspecialchars
}

?>
