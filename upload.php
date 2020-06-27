<?php
if (isset($_GET['save_thuvien'])) {
	save_thuvien();
	exit;
}
if (isset($_GET['load_thuvien'])) {
	load_thuvien();
	exit;
}
if (isset($_GET['delete'])) {
	file_put_contents('thuvien.txt', '');
	header('Location: upload.php');
	exit;
}
function save_thuvien()
{
	$post_data = trim($_POST['data']);
	if (!empty($post_data)) {
		$path = getcwd() . '/thuvien.txt';
		$myfile = fopen($path, "a+") or die("Unable to open file!");
		fwrite($myfile, $post_data . "\n");
		fclose($myfile);
	}
}

function load_thuvien()
{
	$path = getcwd() . '/thuvien.txt';
	$data = trim(file_get_contents($path));
	$dulieu = explode("\n", $data);
	foreach (array_reverse($dulieu) as $key => $dl) {
		?>
		<p>
			<button class="btn" data-clipboard-action="copy" data-clipboard-target="#id<?= $key ?>">Copy</button>
			<span id="id<?= $key ?>"><?php echo $dl ?></span>
		</p>
		<?php
	}
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>upload</title>
<body onload="loadThuVien()">
<button id="upload_widget" class="cloudinary-button">Upload files</button>
<hr>
<div><a href="?delete">Xoa</a></div>
<!-- 1. Define some markup -->
<div id="cloudinary_result"></div>
<!-- 2. Include library -->
<script src="clipboard.min.js"></script>

<!-- 3. Instantiate clipboard by passing a string selector -->
<script>
var clipboard = new ClipboardJS('.btn');

clipboard.on('success', function(e) {
    console.log(e);
});

clipboard.on('error', function(e) {
    console.log(e);
});
</script>
<script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>  

<script type="text/javascript">  
var myWidget = cloudinary.createUploadWidget({
  cloudName: 'fivegins', 
  uploadPreset: 'luufile'}, (error, result) => { 
    if (!error && result && result.event === "success") { 
      console.log('Done! Here is the image info: ', result.info); 
      saveMedia('data=' + result.info.url);
      loadThuVien();
    }
  }
)

document.getElementById("upload_widget").addEventListener("click", function(){
    myWidget.open();
  }, false);
</script>

<script>
function saveMedia(data) {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      console.log(this.responseText);
    }
  };
  xhttp.open("POST", "?save_thuvien", true);
  xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhttp.send(data);
}
</script>

<script>
// load thu vien
function loadThuVien() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementById("cloudinary_result").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "?load_thuvien", true);
  xhttp.send();
}
</script>
</body>