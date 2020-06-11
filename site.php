<?php

include 'db.php';
//include 'functions.php';

if (isset($_POST['link'])) {
	if (preg_match('/truyencv\.com\/(.*?)\//i', $_POST['link'], $flags)) {
		$flag = 'tcv';
		$link = $flags[1];
	} elseif (preg_match('/truyen\.tangthuvien\.vn\/doc-truyen\/(.*)/', $_POST['link'], $flags)) {
		$flag = 'ttv';
		$link = $flags[1];
	} elseif (preg_match('/truyenfull\.vn\/(.*?)\//', $_POST['link'], $flags)) {
		$flag = 'tf';
		$link = $flags[1];
	}

	$stmt = $db->prepare('SELECT slug FROM site WHERE slug = :slug');
	$stmt->execute(array(':slug' => $link ));

	if (!empty($link) && $stmt->rowCount() == 0) {
		$query = "INSERT INTO site (name, slug, flag, start, end, nl2p, loc, remove, date_update) VALUES (:name, :slug, :flag, :start, :end, :nl2p, :loc, :remove, :date_update)";
		$stmt = $db->prepare($query) ;
		$stmt->execute(array(
			':name' => $link,
			':slug' => $link,
			':flag' => $flag,
			':start' => '1',
			':end' => '10',
			':nl2p' => 'no',
			':loc' => 'yes',
			':remove' => 'iframe, script, style, a, div, p',
			':date_update' => date('Y-m-d H:i:s')
		));

		if ($flag == 'ttv') {
			header('Location: TTV/set.php?link='.$link);
		} elseif ($flag == 'tf') {
			header('Location: TF/set.php?link='.$link);
		}
	}
}

$query = "SELECT * FROM site ORDER BY date_update DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<title>TCV REGEX</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
	a { text-decoration: none; }
</style>
<?php include 'navbar.php'; ?>
<hr>
<form action="" method="post">
	<input type="text" name="link">
	<input type="submit" value="New site">
</form>
<?php foreach ($result as $post): ?>
	<?php
		if ($post['flag'] == 'tcv') {
			$fl = 'https://truyencv.com/' . $post['slug'] . '/';
		} elseif ($post['flag'] == 'ttv') {
			$fl = 'https://truyen.tangthuvien.vn/doc-truyen/' . $post['slug'];
		} elseif ($post['flag'] == 'tf') {
			$fl = 'https://truyenfull.vn/' . $post['slug'] . '/';
		}
	?>
	<pre><a href="config_site.php?slug=<?php echo $post['slug'] ?>"><?php echo $post['name'] ?></a> <a href="regex.php?slug=<?php echo $post['slug'] ?>" style="background-color: yellow">Regex</a> <a href="<?php echo $fl ?>"><span style="background-color: #c9f795"><?php echo $post['flag'] ?></span></a></pre>
<?php endforeach ?>
