<?php

include 'db.php';

$query = "SELECT * FROM site WHERE slug = :slug";
$stmt = $db->prepare($query);
$stmt->execute(array(':slug' => $_GET['slug']));
$site = $stmt->fetch(PDO::FETCH_ASSOC);

// ghi du lieu
if (isset($_POST['submit'])) {
	extract($_POST);
	$flag = isset($flag) ? $flag : 'g';
	if (!empty($s)) {
		$query = "INSERT INTO regex (s, r, flag, site_id) values (:s, :r, :flag, :site_id)";
		$stmt = $db->prepare($query);
		$stmt->execute(array(
			':s' => $s,
			':r' => $r,
			':flag' => $flag,
			':site_id' => $site_id
		));
	}
}


$query = "SELECT * FROM regex WHERE site_id = :site_id ORDER BY id ASC";
$stmt = $db->prepare($query);
$stmt->execute(array(':site_id' => $site['id']));
$regexs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// xoa id
if (isset($_GET['xoa_id'])) { 
	$query = "DELETE FROM regex WHERE id = :id";
	$stmt = $db->prepare($query);
	$stmt->execute(array(':id' => $_GET['xoa_id']));
	header('Location: regex.php?slug=' . $_GET['slug']);
	exit;
}

// xóa trang
if(isset($_GET['xoa_site'])){ 
	$query = "DELETE FROM site WHERE id = :id";
	$stmt = $db->prepare($query) ;
	$stmt->execute(array(':id' => $_GET['xoa_site']));

	$query = "DELETE FROM regex WHERE site_id = :site_id";
	$stmt = $db->prepare($query) ;
	$stmt->execute(array(':site_id' => $_GET['xoa_site']));

	header('Location: index.php');
	exit;
} 

?>
<title>Regex</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
	a { text-decoration: none; }
	input[type=submit], textarea {
		display: block;
		margin: 5px 0;
	}
</style>
<form action="?slug=<?php echo $site['slug'] ?>" method="post">
	<input type="hidden" name="site_id" value="<?php echo $site['id'] ?>">
	<textarea name="s" style="width: 100%;"></textarea>
	<textarea name="r" style="width: 100%;"></textarea>
	<input type="radio" name="flag" value="u"> <b>/u</b>
	<input type="radio" name="flag" value="i"> <b>/i</b>
	<input type="radio" name="flag" value="is"> <b>#is</b>
	<input type="radio" name="flag" value="iu"> <b>/iu</b>
	<input type="radio" name="flag" value="td"> <b>/tđ</b>
	<input type="submit" name="submit" value="Replace">
</form>
<p><a href="?xoa_site=<?php echo $site['id'] ?>" onclick = "if (! confirm('Xoá site?')) { return false; }">Xoá site</a> | <a href="config_site.php?slug=<?php echo $site['slug'] ?>">Config site</a><?php if ($site['slug'] == 'userscript'): ?> | <a href="userscript.php?slug=<?php echo $site['slug'] ?>">Userscript</a><?php endif ?></p>
<hr>
<div style="white-space: nowrap;overflow: auto;">
<?php
foreach ($regexs as $key => $value) {
	//echo "[$key] " . $value['search'] . " => " . $value['replace'] . "<hr>";
	$s = str_replace(' ', '▂', $value['s']);
	$r = str_replace(' ', '▂', $value['r']);
	$id = $value['id'];

	if ($value['flag'] == 'u') {
		$flag = '/u';
	} elseif ($value['flag'] == 'i') {
		$flag = '/i';
	} elseif ($value['flag'] == 'is') {
		$flag = '#is';
	} elseif ($value['flag'] == 'iu') {
		$flag = '/iu';
	} elseif ($value['flag'] == 'td') {
		$flag = '/td';
	} else {
		$flag = '/g';
	}
	?>
	<pre>[<b><?php echo sprintf("%02d", $id) ?></b>] <span style="background-color: yellow"><?php echo htmlspecialchars($s) ?> <font color="red"><?php echo $flag ?></font> <?php echo (($r != null) ? htmlspecialchars($r) : '<font color="gray"><i>null</i></font>') ?></span> [<a href="?slug=<?php echo $site['slug'] ?>&xoa_id=<?php echo $id ?>" onclick = "if (! confirm('Xoá?')) { return false; }">xóa</a> | <a href="edit.php?id=<?php echo $id ?>">sửa</a>]</pre>
	<?php
}
?>
</div>
