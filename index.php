<?php
// config start
$root = '/foo/bar'; // root directory to display (without trailing slash and in UTF-8)
setlocale(LC_COLLATE, 'de_DE.utf8'); // locale used for file sorting
date_default_timezone_set('Europe/Berlin'); // timezone for last modified date
$dateformat = 'd.m.Y H:i'; // date format for last modified date
// config end / localization start
$l_dir_up = '⇐';
$l_last_mod = 'Last modified: ';
$l_files = ' files';
$l_sep = ' / ';
$l_folders = ' folders';
// localization end

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
}
else {
	$rel_dir_spl = explode('/', mb_substr($path, $root_len + 1));
	foreach ($rel_dir_spl as $key => $value) {$rel_dir_spl[$key] = rawurlencode($value);}
	
	echo '		<div class="element_main">
			<a href="../">
				<div class="element_dir">
					<span class="element_filename">' . $l_dir_up . '</span>
				</div>
			</a>
		</div>
';
}

$files = array();
$dirs = array();
if($dh = opendir($path)) {
	while(false !== ($file = readdir($dh))) {
		if(!in_array($file, array('.', '..'))) {
			if(is_file($path . '/' . $file)) {array_push($files, $file);}
			else {array_push($dirs, $file);}
		}
	}
	closedir($dh);
	
	usort($dirs, 'strcoll');
	usort($files, 'strcoll');
	$dir_cont = array_merge($dirs, $files);
}

foreach($dir_cont as $dir_cont_file)
{
	$filetype = filetype($path . '/' . $dir_cont_file);
	
	// create file link from splitted URL array
	$filelink = $rel_dir_spl;
	array_push($filelink, rawurlencode($dir_cont_file));
	$filelink = implode('/', $filelink);

	$top = $l_last_mod . date($dateformat, filemtime($path . '/' . $dir_cont_file));
	
	// set stuff according to file type
	if($filetype === 'file')
	{
		// get file size
		$filesize = filesize($path . '/' . $dir_cont_file);
		// calculate file size to display
		if ($filesize < 1024) {$bottom = $filesize . ' B';}
		else if ($filesize < 1048576) {$bottom = round($filesize / 1024, 1) . ' KB';}
		else if ($filesize < 1073741824) {$bottom = round($filesize / 1048576, 1) . ' MB';}
		else {$bottom = round($filesize / 1073741824, 1) . ' GB';}
	}
	else
	{
		$filelink .= '/';
		
		$fic = 0;
		$foc = 0;
		$fch = opendir($path . '/' . $dir_cont_file);
		while(false !== ($fchf = readdir($fch))) {
			if(!in_array($fchf, array('.', '..'))) {
				if(is_file($path . '/' . $fchf)) {$fic++;}
				else {$foc++;}
			}
		}
		closedir($fch);

		$bottom = $fic . $l_files . $l_sep . $foc . $l_folders;
	}
	
	echo '		<div class="element_main">
			<a href="/eac/' . $filelink . '">
				<div class="element_' . $filetype .'">
					<span class="element_filename">' . htmlspecialchars($dir_cont_file) . '</span>
					<div class="element_top">' . $top . '</div>
					<div class="element_bottom">' . $bottom . '</div>
				</div>
			</a>
		</div>
';
}
?>
	</div></body>
</html>