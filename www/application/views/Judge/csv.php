<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->helper('url');
?>
<?php 
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");
header("Pragma: no-cache");
header("Expires: 0");
echo "\xEF\xBB\xBF"; // UTF-8 BOM

krsort($classUser, SORT_STRING);
foreach ($classUser as $class=>$users) { ?>
<?=$class?>班
wait=等待中, CE=編譯錯誤, autoWA=待人工確認, WA=答案錯誤, AC=答案正確, codeAC=人工確認正確
<?php
	echo "User,";
	if (isset($classProb[$class])) foreach ($classProb[$class] as $prob) {
		echo "$prob,";
	}
	echo "\n";

	foreach ($users as $uid=>$user) {
		if (!$user) $user = $uid;
		echo "$user,";
		if (isset($classProb[$class])) foreach ($classProb[$class] as $prob) {
			if (isset($acProbs[$uid][$prob])) {
				$result = $acProbs[$uid][$prob]['result'];
				$sol_id = $acProbs[$uid][$prob]['solution_id'];
				echo $result.','; //.'">'.$sol_id.'</a></td>';
			}
			else {
				echo " ,";
			}
		}
		echo "\n";


	}
}
?>
