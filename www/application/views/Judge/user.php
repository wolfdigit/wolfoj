<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url');
?><!DOCTYPE html>
<html>
<head>
<style>
a {
	color: inherit;
}
.CE {
	color: red;
	margin-left: 1em;
}
.CE:before {
	content: 'C';
	background-color: red;
	color: white;
}

.autoWA {
	color: black;
	margin-left: 1em;
}

.WA {
	color: red;
	margin-left: 1em;
}
.WA:before {
	content: 'W';
	background-color: red;
	color: white;
}

.AC {
	color: green;
	background-color: lightgreen;
	margin-left: 1em;
}
.codeAC {
	color: white;
	background-color: green;
	margin-left: 1em;
}

.wait {
	color: gray;
	margin-left: 1em;
}
</style>
</head>
<body>
<?php 
include('header.inc.php');

foreach ($classProb as $class=>$probs) { ?>
<h2><?=$class?>班</h2>
<p style="border:solid 3px #EEE; padding:0.3em; border-radius:0.3em">
<span class="wait">等待中</span>
<span class="CE">編譯錯誤</span>
<span class="autoWA">待人工確認</span>
<span class="WA">答案錯誤</span>
<span class="AC">答案正確</span>
<span class="codeAC">人工確認正確</span>
</p>
<table>
<tr><th>題號</th><th>上傳</th><th>解題時間</th></tr>
<?php	foreach ($probs as $prob) { ?>
	<tr>
		<th><?=$prob?></th><th><a href="<?=site_url('problem/submit/'.$prob)?>"> submit </a></th><td>
<?php
		if (isset($sol[$prob])) {
		foreach ($sol[$prob] as $solution) {
			echo '<span class="'.$solution['result'].'" id="sol-'.$solution['solution_id'].'">';
			echo '<a href="'. site_url('Judge/judge_one/'.$solution['solution_id']) .'">';
			echo date('m/d H:i', strtotime($solution['in_date']));
			echo '</a>';
			echo '</span>' . "\n";
		}
		}
?>
	</td></tr>
<?php	} ?>
</table>
<?php
}
?>
</body>
</html>
