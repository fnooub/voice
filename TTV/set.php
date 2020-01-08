<?php

include '../functions.php';
include '../db.php';

if (isset($_POST['link'])) {
	$link = $_POST['link'];
	if (!empty($link)) {
		$single_curl = single_curl($link);

		// config
		preg_match('#<title>\s*(.*?)\s*</title>#is', $single_curl, $title);
		$seo_name = explode('/', $link);

		// get id
		preg_match('#value="(\d+)"#is', $single_curl, $id);

		// get danh sach chuong
		$list = single_curl('https://truyen.tangthuvien.vn/story/chapters?story_id=' . $id[1]);

		preg_match_all('#href="\s*(.*?)\s*".*?title="(.*?)"#is', $list, $lists);

		//print_r($lists);

		// list urls
		$list_urls = $lists[1];
		// list title
		$list_title = array();
		for ($i=0; $i < count($lists[2]); $i++) { 
			$list_title[] = html_entity_decode($lists[2][$i]);
		}

		$combine_files = array_combine($list_urls, $list_title);

		$stmt = $db->prepare('SELECT name FROM story WHERE name = :name');
		$stmt->execute(array(':name' => $seo_name[4] ));
		$row_post = $stmt->fetch(PDO::FETCH_ASSOC);

		if($stmt->rowCount() == 0) {
			$query = "INSERT INTO story (name, content, flag) VALUES (:name, :content, :flag)";
			$stmt = $db->prepare($query) ;
			$stmt->execute(array(
				':name' => $seo_name[4],
				':content' => json_encode($combine_files),
				':flag' => 'ttv'
			));
		}

	}
}

?>
<html>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style type="text/css">
		a {text-decoration: none;}
	</style>
	<form method="post">
		<input type="text" name="link" value="<?php if(isset($_GET['link'])) { echo 'https://truyen.tangthuvien.vn/doc-truyen/' . $_GET['link']; } ?>" style="width: 100%">
		<input type="submit" value="SET" style="width: 100%; padding: 5px 0; margin-top: 5px">
	</form>
</html>