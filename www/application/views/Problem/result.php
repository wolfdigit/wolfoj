<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>
<style>
pre {
	background-color: lightgray;
	min-height: 1em;
	margin: 0px;
	padding: 1em;
	border-radius: 0.5em;
}
pre.WA {
	background-color: mistyrose;
}

p + pre {
	margin-top: -15px;
}
</style>
</head>
<body>
<?php
include('header.inc.php');
?>
<p><a href="<?=site_url('Problem')?>">回題目列表</a></p>
<h2>題號: <?=$info->problem_id?></h2>
<p>作者: <?=$info->user_id?></p>
<p>result: <?=$info->result?></p>
<p>上傳時間: <?=$info->in_date?></p>

<p>code:</p>
<pre>
<?=htmlentities($code)?>
</pre>

<p>compiler message:</p>
<pre>
<?=htmlentities($ce)?>
</pre>

<?php
foreach ($results as $testId=>$res) {
?>
<p><?=$testId?>:</p>
<table>
<tr><th>your answer</th><th>correct answer</th></tr>
<?php
	$diffres = diff($res['out'], $res['ans']);
	if ($diffres!=0) $class='class="WA"';
	else             $class='';
?>
<tr>
<td><pre <?=$class?>><?=htmlentities($res['out'])?></pre></td>
<td><pre <?=$class?>><?=htmlentities($res['ans'])?></pre></td>
</tr>
</table>
<?php
}
?>
</body>
</html>
