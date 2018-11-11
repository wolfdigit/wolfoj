<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>
<title>judging: #<?=$info->solution_id?></title>
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
if (!isset($info)) {
	echo "well done!";
	echo '<p><a href="'.site_url('Judge').'">回列表</a></p>';
	die();
}
#var_dump($info);
?>
<p><a href="<?=site_url('Judge')?>">回列表</a></p>
<h1>solution #<?=$info->solution_id?></h1>
<h2>prob <?=$info->problem_id?></h2>
<h2>by <?=$info->nick?>(<?=$info->user_id?>)</h2>
<p>result: <?=$info->result?></p>
<p>time: <?=$info->in_date?></p>

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

<div style="position:fixed; bottom:10px; right:10px; background-color:#EEE; border-radius:5px; padding:10px">
<form action="<?=site_url('Judge/do_judge/'.$info->solution_id)?>" method="POST">
<input type="submit" name="result" value="codeAC" style="height:120px; width:150px; border-radius:15px; font-size:2em; background-color:green">
<input type="submit" name="result" value="AC" style="height:120px; width:150px; border-radius:15px; font-size:5em; background-color:lightgreen">
<input type="submit" name="result" value="WA" style="height:120px; width:150px; border-radius:15px; font-size:5em; background-color:red">
</form>
</div>
</body>
</html>

