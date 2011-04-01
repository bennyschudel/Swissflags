<?php

/**
 * This little php script create sprites and css of given images
 * 
 * usage: php create.php -s16,32,64 -c8 -nflag
 *        php create.php --sizes=16x16,32x32,64x64 --columns=8 --class-name=flag
 * 
 * author: Benjamin Schudel <benjamin.schudel at gmail>
 * date: 20011-03-31
 */

$sizes			= "16,32,64";
$columns		= null;
$class_name		= "flag";
$format			= "png";

$input_dir		= "images/";
$output_dir		= "build/";

$files		 	= array();
$lines 			= array();
$stripes 		= array();

/**
 * Gets command line options in short an long formats and maps given short format to long.
 * 
 * Example:
 * 
 *   -s16,32,64                 => array( sizes => 16,32, 64 )
 *   -s16,32,64 --sizes=16,32   => array( sizes => 16,32 )
 *   --sizes=16,32,64           => array( sizes => 16,32,64 )
 * 
 * @param string $sopt 
 * @param array $lopt 
 * 
 * @return array
 */
function getCliOptions($sopt, $lopt) {
	$opt = getopt($sopt, $lopt);
	foreach (explode(':', trim(str_replace('::', ':', $sopt), ':')) as $index => $value) {
		$param = str_replace(':', '', $lopt[$index]);
		if (isset($opt[$value]) && !isset($opt[$param])) {
			$opt[$param] = $opt[$value];
		}
		if (isset($opt[$value])) {
			unset($opt[$value]);
		}
	}
	
	return $opt;
}

/**
 * Checks if a given option value is valid. If not throws an error.
 *
 * @param array $opt
 * @param string $name 
 * @param string $reg 
 * @param value $default
 * 
 * @return value or exit
 */
function getOption($opt, $name, $reg, $default = null) {
	if (isset($opt[$name])) {
		if (preg_match($reg, @$opt[$name])) {
			
			return $opt[$name];
		}
		else {
			
			exit("ERROR: Invalid {$name} argument\n");
		}
	}
	
	return $default;
}


/*** Main ***/

	// cli arguments
$sopt = "s::c::n::f::h::";
$lopt  = array(
	"sizes::",
	"columns::",
	"class-name::",
	"format::",
	"help::"
);
$opt = getCliOptions($sopt, $lopt);
$sizes = explode(',', getOption($opt, 'sizes', '!^[\dx,]+$!', $sizes));
$columns = getOption($opt, 'columns', '!^[\d]+$!', $columns);
$class_name = getOption($opt, 'class-name', '!^[\w\-_]+$!', $class_name);
$format = strtolower(getOption($opt, 'format', '!^(jpg|png|gif)$!i', $format));
		// convert single sizes to ..x..
foreach ($sizes as &$size) {
	@list($size_x, $size_y) = explode('x', $size);
	if ($size_y === null) {
		$size = implode('x', array($size_x, $size_x));
	}
}

	// create output dir
$now = date('ymd-His');
$output_dir .= "{$now}/";
$output = shell_exec("mkdir -p $output_dir");

	// load input
foreach (glob("{$input_dir}*") as $file) {
	$files[] = pathinfo($file);
}
if ($columns === null) {
	$columns = count($files);
}

	// create image stripes
foreach ($sizes as $size) {
	list($size_x, $size_y) = explode('x', $size);
	$stripe = "{$class_name}-{$size}.{$format}";
	$output = shell_exec("montage {$input_dir}* -tile {$columns}x -geometry {$size} -quality 90 {$output_dir}{$stripe}");
	$stripes[$size] = $stripe;
}

	// create css
$lines[] = "/* Swissflags */";
$lines[] = ".{$class_name} {
	display: inline-block;
	background-repeat: no-repeat;
}";
$lines[] = "";
foreach ($stripes as $size => $stripe) {
	list($size_x, $size_y) = explode('x', $size);
	$lines[] = ".{$class_name}.{$class_name}-{$size} {
	background-image: url({$stripe});
	width: {$size_x}px;
	height: {$size_y}px;
}";
}
$lines[] = "";
foreach ($sizes as $size) {
	list($size_x, $size_y) = explode('x', $size);
	$row = $col = 0;
	foreach ($files as $index => $info) {
		if ($col > 0 && $col % $columns === 0) {
			$row += 1;
			$col = 0;
		}
		$pos_x = $col * (int)$size_x;
		$pos_y = $row * (int)$size_y; 
		if ($pos_x > 0) {
			$pos_x = "-{$pos_x}px";
		}
		if ($pos_y > 0) {
			$pos_y = "-{$pos_y}px";
		}
		$col += 1;
		$prefix = ($size_x !== $size_y) ? $size : $size_x;
		$lines[] = ".{$class_name}.{$class_name}-{$prefix}.{$class_name}-{$info['filename']}\t{ background-position: {$pos_x} {$pos_y}; }";
	}
	$lines[] = "";
}
array_pop($lines);
$lines[] = "/* /Swissflags */";
		// create css file
file_put_contents("{$output_dir}{$class_name}.css", implode("\n", $lines));

	// bye bye
print "built finished > {$output_dir}\n";

exit();
