<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<?php
include('header.inc.php');
?>
<p><a href="<?=site_url('Problem')?>">回題目列表</a></p>
<h2>題號: <?=$prob?></h2>
<?php 
$this->load->helper('form');

echo form_open('problem/dosubmit/'.$prob);
echo form_textarea(array('name'=>'code', 'rows'=>'25', 'cols'=>'80'));
echo form_submit('submit', 'submit');
echo form_close();
