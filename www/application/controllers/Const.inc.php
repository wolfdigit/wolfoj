<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('SOLPATH', '/var/wolfoj/solutions/');
define('DATAPATH', '/var/wolfoj/testdata/');
define('RESULTPATH', '/var/wolfoj/results/');
//define('JUDGE', 'hsinyiho@lssh.tp.edu.tw');

function diff($content1, $content2) {
	$arr1 = explode("\n", $content1);
	$arr2 = explode("\n", $content2);
	array_walk($arr1, function(&$item, $key) { $item = trim($item); } );
	array_walk($arr2, function(&$item, $key) { $item = trim($item); } );
	if (implode("\n", $arr1)==implode("\n", $arr2)) return 0;
	else                                            return 1;
}
