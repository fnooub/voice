<?php

include 'db.php';

if (isset($_POST['submit'])) {
	extract($_POST);
	// s khong rong
	if (!empty($s)) {
		$flag = isset($flag) ? $flag : 'g';
		$query = "UPDATE regex SET s = :s, r = :r, flag = :flag WHERE id = :id";
		$stmt = $db->prepare($query);
		$stmt->execute(array(
			':id' => $id,
			':s' => $s,
			':r' => $r,
			':flag' => $flag
		));
	}
}

$query = "SELECT * FROM regex WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->execute(array(':id' => $_GET['id']));
$regex = $stmt->fetch(PDO::FETCH_ASSOC);

// check
if ($regex['flag'] == 'u') {
	$u = 'checked=checked';
} elseif ($regex['flag'] == 'i') {
	$i = 'checked=checked';
} elseif ($regex['flag'] == 'is') {
	$is = 'checked=checked';
} elseif ($regex['flag'] == 'iu') {
	$iu = 'checked=checked';
} elseif ($regex['flag'] == 'td') {
	$td = 'checked=checked';
} else {
	$g = 'checked=checked';
}

?>
<title>Sửa</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
	a { text-decoration: none; }
	input[type=submit],
	textarea {
		display: block;
		margin: 10px 0;
		width: 100%;
	}
	input[type=radio] {
		display: inline-block;
		padding-right: 10px;
	}
</style>
<form action="?id=<?php echo $regex['id'] ?>" method="post">
	<input type="hidden" name="id" value="<?php echo $regex['id'] ?>">
	<textarea name="s"><?php echo $regex['s'] ?></textarea>
	<textarea name="r"><?php echo $regex['r'] ?></textarea>
	<input type="radio" name="flag" value="g" <?php if(isset($g)) echo $g ?>> <b>/g</b>
	<input type="radio" name="flag" value="u" <?php if(isset($u)) echo $u ?>> <b>/u</b>
	<input type="radio" name="flag" value="i" <?php if(isset($i)) echo $i ?>> <b>/i</b>
	<input type="radio" name="flag" value="is" <?php if(isset($is)) echo $is ?>> <b>#is</b>
	<input type="radio" name="flag" value="iu" <?php if(isset($iu)) echo $iu ?>> <b>/iu</b>
	<input type="radio" name="flag" value="td" <?php if(isset($td)) echo $td ?>> <b>/tđ</b>
	<input type="submit" name="submit" value="Replace">
</form>
