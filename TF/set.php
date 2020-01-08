<?php

include '../db.php';
include '../functions.php';

if (isset($_POST['link'])) {

	$link = $_POST['link'];

	if (!preg_match('/truyenfull\.net/', $link)) {
		$status = 'Error!';
	} else {
		$single_curl = single_curl($link);

		// name
		$seo_name = explode('/', $link);

		//tong
		if(!preg_match('#<ul class="pagination pagination-sm">#is', $single_curl)){
			$last = 1;
		}else{
			if(preg_match('#Trang ([0-9]{1,3})">Cuối#is', $single_curl)){
				preg_match('#Trang ([0-9]{1,3})">Cuối#is', $single_curl, $tong);
				$last = $tong[1];
			}else{
				preg_match('#(.*)>([0-9]{1,3})</a></li><li>#is', $single_curl, $tong);
				$last = $tong[2];
			}
		}


		// get multi pages
		$urls = array();
		for ($i=1; $i <= $last; $i++) { 
			$urls[] = $link . 'trang-' . $i . '/';
		}

		$multi_curl = multi_curl($urls);
		preg_match_all('#<ul class="list-chapter">(.*?)</ul>#is', $multi_curl, $list_chapter);
		preg_match_all('#<a href="(.*?)".*?>(.*?)</a>#is', print_r($list_chapter[1], true), $lists);

		// list urls
		$list_urls = $lists[1];
		// list title
		$list_title = array();
		for ($i=0; $i < count($lists[2]); $i++) { 
			$list_title[] = strip_tags($lists[2][$i]);
		}

		$combine_files = array_combine($list_urls, $list_title);

		$stmt = $db->prepare('SELECT name FROM story WHERE name = :name');
		$stmt->execute(array(':name' => $seo_name[3] ));
		$row_post = $stmt->fetch(PDO::FETCH_ASSOC);

		if($stmt->rowCount() == 0) {
			$query = "INSERT INTO story (name, content, flag) VALUES (:name, :content, :flag)";
			$stmt = $db->prepare($query) ;
			$stmt->execute(array(
				':name' => $seo_name[3],
				':content' => json_encode($combine_files),
				':flag' => 'tf'
			));
		}

		$status = 'Ok!';

	}


}


?>
<html>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style type="text/css">
		a {text-decoration: none;}
	</style>
	<?php if (isset($status)): ?>
		<p><?php echo $status ?></p>
	<?php endif ?>
	<form method="post">
		<input type="text" name="link" value="<?php if(isset($_GET['link'])) { echo 'https://truyenfull.net/' . $_GET['link'] . '/'; } ?>" style="width: 100%">
		<input type="submit" value="SET" style="width: 100%; padding: 5px 0; margin-top: 5px">
	</form>
</html>