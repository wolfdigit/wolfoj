<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//define('SOLPATH', '/var/wolfoj/solutions/');
require_once('Const.inc.php');

class Judge extends CI_Controller {
	public function index() {
		$this->auth->needlogin();
		if ($this->auth->user()!=JUDGE) show_404();
		//
		$classProb = array();
		$results = $this->db->order_by('class_id, prob_order')->get('class_prob')->result();
		foreach ($results as $row) {
			$classProb[$row->class_id][$row->prob_order] = $row->problem_id;
		}

		$classUser = array();
		$results = $this->db->order_by('class_id, nick')->get('class_user')->result();
		foreach ($results as $row) {
			$classUser[$row->class_id][$row->user_id] = $row->nick;
		}
		//var_dump($classUser);
		$acProbs = array();
		#$results = $this->db->get_where('solution', "result IN ('AC', 'codeAC')")->result();
		$results = $this->db->get('solution')->result();
		$o = array('wait'=>0, 'CE'=>1, 'autoWA'=>2, 'WA'=>3, 'AC'=>4, 'codeAC'=>5);
		foreach ($results as $row) {
			if (!isset($acProbs[$row->user_id][$row->problem_id]) || $o[ $acProbs[$row->user_id][$row->problem_id]['result'] ] < $o[ $row->result ]) {
				$acProbs[$row->user_id][$row->problem_id] = array('result'=>$row->result, 'solution_id'=>$row->solution_id);
			}
		}
		//var_dump($acProbs);


		$this->load->view('Judge/index', array('classProb'=>$classProb, 'classUser'=>$classUser, 'acProbs'=>$acProbs));
	}

	private function get_file($fn) {
		if (!file_exists($fn)) return "";
		return file_get_contents($fn);
	}
	public function judging($prob=null, $class=null) {
		$this->auth->needlogin();
		if ($this->auth->user()!=JUDGE) show_404();
		#if ($prob==null) show_404();
		if ($class!=null) {
			$result = $this->db->join('class_user', 'class_user.user_id = solution.user_id')->where(array('solution.problem_id'=>$prob, 'class_user.class_id'=>$class, 'solution.result'=>'autoWA'))->order_by('in_date', 'desc')->limit(1)->get('solution')->result();
		}
		else {
			if ($prob!=null) {
				$result = $this->db->where(array('problem_id'=>$prob, 'result'=>'autoWA'))->order_by('in_date', 'desc')->limit(1)->get('solution')->result();
			}
			else {
				$result = $this->db->where(array('result'=>'autoWA'))->order_by('in_date', 'desc')->limit(1)->get('solution')->result();
			}
		}
		#var_dump($result);
		if (count($result)==0) {
			#include('header.inc.php');
			#echo "well done!";
			#die();
			$this->load->view('Judge/judging', array());
		}
		$info = $result[0];
		$sol_id = $info->solution_id;

		$code = $this->get_file(SOLPATH.$sol_id.'.cpp');
		$ce = $this->get_file(RESULTPATH.$sol_id.'/CE.txt');
		foreach (glob(DATAPATH . $info->problem_id . "/*.ans") as $fn) {
			$testId = basename($fn, '.ans');
			$fn_ans = $fn;
			$fn_out = RESULTPATH.$sol_id .'/' . $testId . '.out';
			$results[$testId] = array('out'=>$this->get_file($fn_out), 'ans'=>$this->get_file($fn_ans));
		}
		$this->load->view('Judge/judging', array('info'=>$info, 'code'=>$code, 'ce'=>$ce, 'results'=>$results));
	}

	public function judge_one($sol_id=null) {
		$this->auth->needlogin();
		if ($this->auth->user()!=JUDGE) show_404();
		if ($sol_id==null) show_404();

		$result = $this->db->get_where('solution', array('solution_id'=>$sol_id))->result();
		# var_dump($result);
		if (count($result)==0) show_404();
		$info = $result[0];

		$code = $this->get_file(SOLPATH.$sol_id.'.cpp');
		$ce = $this->get_file(RESULTPATH.$sol_id.'/CE.txt');
		foreach (glob(DATAPATH . $info->problem_id . "/*.ans") as $fn) {
			$testId = basename($fn, '.ans');
			$fn_ans = $fn;
			$fn_out = RESULTPATH.$sol_id .'/' . $testId . '.out';
			$results[$testId] = array('out'=>$this->get_file($fn_out), 'ans'=>$this->get_file($fn_ans));
		}
		$this->load->view('Judge/judging', array('info'=>$info, 'code'=>$code, 'ce'=>$ce, 'results'=>$results));
	}

	public function do_judge($sol_id=null) {
		if (!isset($sol_id)) show_404();
		$this->auth->needlogin();
		if ($this->auth->user()!=JUDGE) show_404();
		
		//var_dump($this->input->post());
		if ($this->input->post('result')=='WA') {
			$this->db->where('solution_id', $sol_id)->update('solution', array('result'=>'WA'));
		}
		if ($this->input->post('result')=='AC') {
			$this->db->where('solution_id', $sol_id)->update('solution', array('result'=>'AC'));
		}
		if ($this->input->post('result')=='codeAC') {
			$this->db->where('solution_id', $sol_id)->update('solution', array('result'=>'codeAC'));
		}
		//redirect('Judge/judging');
		echo '<script>window.history.go(-1);</script>';
	}

	public function user($user=null) {
		if ($user==null) show_404();
		$this->auth->needlogin();
		if ($this->auth->user()!=JUDGE) show_404();

		$classProb = array();
		$results = $this->db->join('class_user', 'class_user.class_id = class_prob.class_id')->order_by('class_prob.class_id, class_prob.prob_order')->get_where('class_prob', array('class_user.user_id'=>urldecode($user)))->result();
		# var_dump( $results );
		foreach ($results as $row) {
			$classProb[$row->class_id][$row->prob_order] = $row->problem_id;
		}
		# var_dump($classProb);

		$rows = $this->db->get_where('solution', array('user_id'=>urldecode($user)))->result();
		$probSol = array();
		foreach ($rows as $row) {
			$probSol[$row->problem_id][] = array('solution_id'=>$row->solution_id, 'in_date'=>$row->in_date, 'result'=>$row->result);
		}
		# var_dump($probSol);
		

		$this->load->view('Judge/user', array('classProb'=>$classProb, 'sol'=>$probSol));
	}
}
