<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//define('SOLPATH', '/var/wolfoj/solutions/');
require_once('Const.inc.php');

class Problem extends CI_Controller {
	public function index() {
		$this->auth->needlogin();
		$classProb = array();
		$results = $this->db->join('class_user', 'class_user.class_id = class_prob.class_id')->order_by('class_prob.class_id, class_prob.prob_order')->get_where('class_prob', array('class_user.user_id'=>$this->auth->user()))->result();
		# var_dump( $results );
		foreach ($results as $row) {
			$classProb[$row->class_id][$row->prob_order] = $row->problem_id;
		}
		# var_dump($classProb);

		$rows = $this->db->get_where('solution', array('user_id'=>$this->auth->user()))->result();
		$probSol = array();
		foreach ($rows as $row) {
			$probSol[$row->problem_id][] = array('solution_id'=>$row->solution_id, 'in_date'=>$row->in_date, 'result'=>$row->result);
		}
		# var_dump($probSol);
		

		$this->load->view('Problem/index', array('classProb'=>$classProb, 'sol'=>$probSol));
	}

	public function submit($prob=null) {
		$this->auth->needlogin();
		if ($prob==null) show_404();
		$this->load->view('Problem/submit', array('prob'=>$prob));
	}

	public function dosubmit($prob=null) {
		$this->auth->needlogin();
		if ($prob==null) show_404();
		var_dump($this->input->post());
		$row = array('problem_id'=>$prob, 'user_id'=>$this->auth->user(), 'in_date'=>date("Y-m-d H:i:s"), 'ip'=>$this->input->ip_address());
		$this->db->insert('solution', $row);
		$sol_id = $this->db->insert_id();
		file_put_contents(SOLPATH.$sol_id.'.cpp', $this->input->post('code'));
		redirect('Problem/index');
	}

	private function get_file($fn) {
		if (!file_exists($fn)) return "";
		return file_get_contents($fn);
	}
	public function result($sol_id=null) {
		$this->auth->needlogin();
		if ($sol_id==null) show_404();
		$result = $this->db->get_where('solution', array('user_id'=>$this->auth->user(), 'solution_id'=>$sol_id))->result();
		# var_dump($result);
		if (count($result)==0) show_404();
		$info = $result[0];

		$code = $this->get_file(SOLPATH.$sol_id.'.cpp');
		$ce = $this->get_file(RESULTPATH.$sol_id.'/CE.txt');
		$results = array();
		foreach (glob(DATAPATH . $info->problem_id . "/*.ans") as $fn) {
			$testId = basename($fn, '.ans');
			$fn_ans = $fn;
			$fn_out = RESULTPATH.$sol_id .'/' . $testId . '.out';
			$results[$testId] = array('out'=>$this->get_file($fn_out), 'ans'=>$this->get_file($fn_ans));
		}
		$this->load->view('Problem/result', array('info'=>$info, 'code'=>$code, 'ce'=>$ce, 'results'=>$results));
	}
}
