<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//define('SOLPATH', '/var/wolfoj/solutions/');
require_once('Const.inc.php');

class Api extends CI_Controller {
	// state: waiting -> CE -> RE / TLE -> WA -> man AC / PE -> auto AC -> code OK
	// 0:wait  1:CE  2:RE  3:TLE  4:autoWA  5:WA  6:AC  7:codeAC
	public function __construct() {
		parent::__construct();
		if ($this->input->ip_address()!='127.0.0.1') show_404();
	}

	public function index() {
		show_404();
	}

	public function job() {
		// FILE \n RID \n PROB
		$result = $this->db->get_where('solution', array('result'=>'wait'), 1, 0)->result();
		//var_dump($result);
		if (count($result)>0) {
			$result = $result[0];
			echo $result->solution_id . ".cpp" . "\n";
			echo $result->solution_id . "\n";
			echo $result->problem_id . "\n";
		}
	}
	
	public function code($file=null) {
		if ($file==null) show_404();
		if (strpos($file, '/')!==false) show_404();

		echo file_get_contents(SOLPATH . $file);
	}

	public function data($prob=null, $file=null) {
		if ($prob==null) show_404();
		if (strpos($file, '/')!==false) show_404();
		if ($file==null) $file = 'index';

		if ($file=='index') {
			foreach (glob(DATAPATH . "$prob/*.in") as $fn) {
				echo basename($fn) . "\n";
			}
		}
		else {
			echo file_get_contents(DATAPATH . $prob . "/" . $file);
		}
	}

	public function upload($sol_id=null) {
		if ($sol_id==null) show_404();
		var_dump($this->input->post());
		$solDir = RESULTPATH . $sol_id;
		if (!file_exists($solDir)) mkdir($solDir, 0777, true);

		$input_id = basename($this->input->post('in'), '.in');
		rename($_FILES['out']['tmp_name'], $solDir.'/'.$input_id.'.out');
		rename($_FILES['err']['tmp_name'], $solDir.'/'.$input_id.'.err');
		
		//var_dump($_FILES);
/*
		$uploadConf = array(
			'upload_path'=>'/tmp/uploading',
			'allowed_types'=>'txt|application/x-empty|empty|*',
			'max_size'=>'0'
		);

		$this->load->library('upload', $uploadConf);
		if (!$this->upload->do_upload('out')) {
			echo $this->upload->display_errors();
		}
		else {
			echo var_dump($this->upload->data());
		}
*/
	}

	public function result($prob=null, $sol_id=null) {
		if ($sol_id==null) show_404();
		if ($prob==null) show_404();
		if (!file_exists(RESULTPATH.$sol_id)) mkdir(RESULTPATH.$sol_id, 0777, true);
		rename($_FILES['ce']['tmp_name'], RESULTPATH.$sol_id.'/CE.txt');
		
		$hardAC = 1;
		foreach (glob(DATAPATH . "$prob/*.ans") as $fn) {
			$testId = basename($fn, '.ans');
			$fn_ans = $fn;
			$fn_out = RESULTPATH.$sol_id .'/' . $testId . '.out';
			$fn_diff = RESULTPATH.$sol_id .'/' . $testId . '.diff';
			//system("diff -y -W 130 \"$fn_out\" \"$fn_ans\" > \"$fn_diff\"", $res);
			$res = diff(file_get_contents($fn_out), file_get_contents($fn_ans));
			if ($res!=0) $hardAC = 0;
		}
		$data = array('result'=>'wait');
		if ($this->input->post('res')==1) $data['result']='CE';
		else if ($hardAC)                 $data['result']='AC';
		else                              $data['result']='autoWA';
		$this->db->where('solution_id', $sol_id);
		$this->db->update('solution', $data); 
	}
}
