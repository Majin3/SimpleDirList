<?php
// config start
$root = '/home/foo'; // root directory to display (without trailing slash and in UTF-8)
date_default_timezone_set('Europe/Berlin'); // timezone used for last changed date
setlocale(LC_COLLATE, 'de_DE.utf8'); // locale used for file sorting
$l_files = 'files'; // localization
// config end

function cmp($a, $b) {
	global $path;
	$a = $path . '/' . $a;
	$b = $path . '/' . $b;
	if((is_dir($a) && is_dir($b)) || (!is_dir($a) && !is_dir($b))) {return strcoll($a, $b);}
	else if(is_dir($a)) {return -1;}
	else {return 1;}
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
mb_internal_encoding('UTF-8');
$root_len = mb_strlen($root);

// set real request path unless it doesn't exist or is not allowed to be accessed
if(isset($_GET['p'])) {
	$path = realpath($root . '/' . $_GET['p']);
	if(!file_exists($path) || !mb_substr($path, 0, $root_len) === $root) {$path = $root;}
}
else {$path = $root;}

if(is_file($path))
{
	// set downloadable file header
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment');
	header('Content-Length: ' . filesize($path));
	// disable buffering, otherwise large files will fail
	ob_end_flush();
	readfile($path);
	exit();
}
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>SimpleDirList</title>
		<link rel="stylesheet" type="text/css" href="/style.css" />
	</head>
	<body><div id="content">
<?php
// create a dir array with url-encoded elements for link creation. If root is requested, it's an empty array
if($path === $root) {
	$rel_dir_spl = array();
	$i = 2;
}
else {
	$rel_dir_spl = explode('/', mb_substr($path, $root_len + 1));
	foreach ($rel_dir_spl as $key => $value) {$rel_dir_spl[$key] = rawurlencode($value);}
	$i = 1;
}

$dir_cont = scandir($path);
usort($dir_cont, 'cmp');

$dir_cont_c = count($dir_cont);
for($i = $i; $i < $dir_cont_c; $i++)
{
	$filetype = filetype($path . '/' . $dir_cont[$i]);
	
	// create a copy of the dir array
	$filelink = $rel_dir_spl;
	// if going up, remove the last element of the dir array, otherwise put the target in it
	if ($dir_cont[$i] === '..') {unset($filelink[count($filelink) - 1]);}
	else {array_push($filelink, rawurlencode($dir_cont[$i]));}
	// merge the final link array
	$filelink = implode('/', $filelink);

	// get properties according to content
	if($dir_cont[$i] === '..')
	{
		$up = '';
		$down = '';
		$dir_cont[$i] = '⇐';
	}
	else if($filetype === 'file')
	{
		// get file size
		$filesize = filesize($path . '/' . $dir_cont[$i]);
		// calculate file size to display
		if ($filesize < 1024) {$filesize = $filesize . ' B';}
		else if ($filesize < 1048576) {$filesize = round($filesize / 1024, 1) . ' KB';}
		else if ($filesize < 1073741824) {$filesize = round($filesize / 1048576, 1) . ' MB';}
		else {$filesize = round($filesize / 1073741824, 1) . ' GB';}	

		$up = '
			<div class="element_top">' . date('d.m.Y H:i', filemtime($path . '/' . $dir_cont[$i])) . '</div>';
		$down = '
			<div class="element_bottom">' . $filesize . '</div>';
	}
	else
	{
		$up = '<div class="element_top">' . date('d.m.Y H:i', filemtime($path . '/' . $dir_cont[$i])) . '</div>';
		$down = '<div class="element_bottom">' . (count(scandir($path . '/' . $dir_cont[$i])) - 2) . ' ' .  $l_files . '</div>';
	}
	
	echo '		<div class="element_main">
			<a href="/eac/' . $filelink . '">
				<div class="element_' . $filetype .'">
					<span class="element_filename">' . htmlspecialchars($dir_cont[$i]) . '</span>' . 
					$up .  
					$down 
				 . '</div>
			</a>
		</div>
';
}
?>
	</div></div></body>
</html>