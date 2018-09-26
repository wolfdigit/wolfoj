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

krsort($classUser, SORT_STRING);
foreach ($classUser as $class=>$users) { ?>
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
<?php
	echo "<tr><td>User</td>";
	if (isset($classProb[$class])) foreach ($classProb[$class] as $prob) {
		echo "<th><a href=\"".site_url("Judge/judging/$prob/$class")."\">$prob</a></th>";
	}
	echo "</tr>";

	foreach ($users as $uid=>$user) {
		if (!$user) $user = $uid;
		echo "<tr><th><a href=\"".site_url('Judge/user/'.urlencode($uid))."\">$user</a></th>";
		if (isset($classProb[$class])) foreach ($classProb[$class] as $prob) {
			if (isset($acProbs[$uid][$prob])) {
				$result = $acProbs[$uid][$prob]['result'];
				$sol_id = $acProbs[$uid][$prob]['solution_id'];
				echo '<td class="'.$result.'"><a href="'.site_url('Judge/judge_one/'.$sol_id).'">'.$sol_id.'</a></td>';
			}
			else {
				echo "<td>"."</td>";
			}
		}
		echo "</tr>";


	}
?>
</table>
<?php
}
?>
</body>
</html>
