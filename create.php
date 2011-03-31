<?php

/**
 * This little php script creates a sprite of the 26 swiss canton flags and the related css
 * 
 * usage: php create.php 16,32,64 flag
 * 
 * author: Benjamin Schudel <benjamin.schudel at gmail>
 * date: 20011-03-31
 */

$sizes = "16,32,64";
$class_name = 'flag';
$input_dir = "images/";
$output_dir = "build/";

$files = array();
$lines = array();
$stripes = array();

// cli arguments
	// sizes: "16,32,64"
$sizes = explode(',', (isset($argv[1])) ? (string)$argv[1] : $sizes);
	// classname: "flag"
$class_name = (isset($argv[2])) ? (string)$argv[2] : $class_name;

// create output dir
$now = date('ymd-His');
$output_dir .= "{$now}/";
$output = shell_exec("mkdir -p $output_dir");

// load input
foreach (glob("{$input_dir}*") as $file) {
	$files[] = pathinfo($file);
}
$total = count($files);

// create image stripes
foreach ($sizes as $size) {
	$stripe = "{$class_name}-{$size}.png";
	$output = shell_exec("montage {$input_dir}* -tile {$total}x -geometry {$size}x{$size} {$output_dir}{$stripe}");
	$stripes[(string)$size] = $stripe;
}

// create css
$lines[] = ".{$class_name} {
	display: inline-block;
	background-repeat: no-repeat;
}";
$lines[] = "";
foreach ($stripes as $size => $stripe) {
	$lines[] = ".{$class_name}.{$class_name}-{$size} {
	background-image: url({$stripe});
	width: {$size}px;
	height: {$size}px;
}";
}
$lines[] = "";
foreach ($sizes as $size) {
	foreach ($files as $index => $info) {
		$pos_x = $index * (int)$size;
		if ($pos_x > 0) {
			$pos_x = "-{$pos_x}px";
		}
		$prefix = strtoupper($info['filename']);
		$lines[] = ".{$class_name}.{$class_name}-{$size}.{$class_name}-{$prefix}\t{ background-position: {$pos_x} 0; }";
	}
	$lines[] = "";
}
file_put_contents("{$output_dir}{$class_name}.css", implode("\n", $lines));

// bye bye
chdir($output_dir);
echo "done!\n";

