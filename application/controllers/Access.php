<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Lima');
		$this->load->model('account_model','account');
		$this->load->model('subscription_model','subscription');
		$this->load->model('plan_model','plan');
		$this->lang->load("system", "spanish");
	}
	
	public function login(){
		$email = $this->input->post("email_login");
		$password = $this->input->post("password_login");
		$msgs = array();
		
		/* start validations */
		$msg = ""; $account = null;
		if ($email){
			$account = $this->account->get_by_email($email);
			if (!$account) $msg = $this->lang->line('error_email');
		}else $msg = $this->lang->line('error_email_empty');
		if ($msg) array_push($msgs, array("dom_id" => "email_login_msg", "type" => "error", "msg" => $msg));
		
		$msg = "";
		if ($password){
			if ($account) if (!password_verify($password, $account->password)) $msg = $this->lang->line('error_password');
		}else $msg = $this->lang->line('error_password_blank');
		if ($msg) array_push($msgs, array("dom_id" => "password_login_msg", "type" => "error", "msg" => $msg));
		/* end validations */
		
		if ($msgs) $status = false;
		else{
			$subscription = $this->subscription->get_valid($account->id, date('Y-m-d'));
			if ($subscription){
				$plan = $this->plan->get_by_id($subscription->plan_id);
				if ($subscription->mp_preapproval_id){
					$mp = $this->mercadopago_lib->get_subscription($subscription->mp_preapproval_id);
					$this->subscription->update($subscription->id, array("valid_to" => $mp->next_payment_date));
				}
			}
			else $plan = $this->plan->get_free();
			
			$session_data = array(
				"aid" => $account->id,
				"email" => $account->email,
				"name" => $account->name,
				"is_validated" => $account->is_validated,
				"plan" => $plan,
				"logged_in" => true
			);
			$this->session->set_userdata($session_data);
			
			$login_data = array(
				"last_agent" => $this->agent->agent_string(),
				"last_ip" => $this->input->ip_address(),
				"last_logged_at" => mdate("%Y-%m-%d %H:%i:%s", time())
			);
			$this->account->update($account->id, $login_data);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs));
	}
	
	public function register(){
		$email = $this->input->post("email_register");
		$password = $this->input->post("password_register");
		$name = $this->input->post("name_register");
		$chk_terms = $this->input->post("chk_terms");
		$status = false;
		$msgs = array();
		
		/* start validations */
		$msg = "";
		if ($email){
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $msg = $this->lang->line('error_email_format');
			elseif ($this->account->get_by_email($email, false)) $msg = $this->lang->line('error_email_duplicate');
		}else $msg = $this->lang->line('error_email_empty');
		if ($msg) array_push($msgs, array("dom_id" => "email_register_msg", "type" => "error", "msg" => $msg));
		
		$msg = "";
		if ($password){
			if (strlen($password) < 6) $msg = $this->lang->line('error_password_length');
		}else $msg = $this->lang->line('error_password_blank');
		if ($msg) array_push($msgs, array("dom_id" => "password_register_msg", "type" => "error", "msg" => $msg));
		
		if (!$name) array_push($msgs, array("dom_id" => "name_register_msg", "type" => "error", "msg" => $this->lang->line('error_name')));
		
		if (!$chk_terms) array_push($msgs, array("dom_id" => "terms_register_msg", "type" => "error", "msg" => $this->lang->line('error_chk_terms')));
		/* end validations */
		
		if (!$msgs){
			$data = array(
				"email" => $email,
				"password" => password_hash($password, PASSWORD_BCRYPT),
				"name" => $name,
				"is_validated" => 0,
				"validation_code" => $this->utility_lib->randomStr(6),
				"registed_at" => mdate("%Y-%m-%d %H:%i:%s", time())
			);
			
			$account_id = $this->account->insert($data);
			if ($account_id){
				$status = true;
				array_push($msgs, array("dom_id" => "", "type" => "success", "msg" => $this->lang->line('success_registration')));
				$this->email_lib->account_validation($this->account->get_by_id($account_id));
			}else array_push($msgs, array("dom_id" => "result_register_msg", "type" => "error", "msg" => $this->lang->line('error_internal')));
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs));
	}
	
	public function send_validation_email(){
		$account = $this->account->get_by_id($this->session->userdata('aid'));
		if ($account){
			$account->validation_code = $this->utility_lib->randomStr(6);
			$this->account->update($account->id, array("validation_code" => $account->validation_code));
			$this->email_lib->account_validation($account);
			
			$response = array("status" => true, "msg" => $this->lang->line('success_val_code'));
		}else $response = array("status" => false, "msg" => $this->lang->line('error_session'));
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function email_validation(){
		/* need to be checked */
		$is_error = true;
		$account = $this->account->get_by_id($this->input->get("aid"));
		if ($account){
			if (!strcmp($this->input->get("code"), $account->validation_code)){
				$this->account->update($account->id, array("is_validated" => 1));
				$this->session->set_userdata('is_validated', 1);
				$is_error = false;
			}
		}
		
		if ($is_error) echo $this->lang->line('error_email_validation');
		else redirect("/dashboard");
	}
	
	public function reset_pass(){
		$email = $this->input->post("email_reset_pass");
		$status = false;
		$msgs = array();
		
		/* start validations */
		$msg = ""; $account = null;
		if ($email){
			$account = $this->account->get_by_email($email, false);
			if (!$account) $msg = $this->lang->line('error_email');
		}else $msg = $this->lang->line('error_email_empty');
		if ($msg) array_push($msgs, array("dom_id" => "email_reset_pass_msg", "type" => "error", "msg" => $msg));
		/* end validations */
		
		if (!$msgs){
			$account->validation_code = $this->utility_lib->randomStr(6);
			$this->account->update($account->id, array("validation_code" => $account->validation_code));
			$this->email_lib->reset_pass($account);//send password reset confirm email
			array_push($msgs, array("dom_id" => "", "type" => "success", "msg" => $this->lang->line('success_password_reset_confirm')));
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs));
	}
	
	public function reset_pass_confirm(){
		$is_error = true;
		$account = $this->account->get_by_id($this->input->get("aid"));
		if ($account){
			if (!strcmp($this->input->get("code"), $account->validation_code)){
				$pass_new = $this->utility_lib->randomStr(6);
				$data = array(
					"password" => password_hash($pass_new, PASSWORD_BCRYPT),
					"validation_code" => $this->utility_lib->randomStr(6)
				);
				$this->account->update($account->id, $data);
				$this->email_lib->reset_pass_confirm($account, $pass_new);//send password reset confirm email
				$is_error = false;
			}
		}
		
		if ($is_error) echo $this->lang->line('error_internal');
		else echo $this->lang->line('success_password_reset');
	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect('/', 'refresh');
	}
	
	/* mercadopago json structure
	stdClass Object ( 
		[id] => 2c93808481f933db018203aeaad505ec 
		[payer_id] => 1130640940 
		[payer_email] => 
		[back_url] => https://www.google.com/ 
		[collector_id] => 1130632731 
		[application_id] => 2450507835733617 
		[status] => authorized 
		[reason] => Avanzado 
		[external_reference] => 
		[date_created] => 2022-07-15T17:05:54.898-04:00 
		[last_modified] => 2022-07-16T17:58:49.951-04:00 
		[init_point] => https://www.mercadopago.com.pe/subscriptions/checkout?preapproval_id=2c93808481f933db018203aeaad505ec 
		[preapproval_plan_id] => 2c9380848100905c0181170e45300958 
		[auto_recurring] => stdClass Object ( 
			[frequency] => 1 
			[frequency_type] => months 
			[transaction_amount] => 59 
			[currency_id] => PEN 
			[start_date] => 2022-07-15T17:05:54.899-04:00 
			[billing_day_proportional] => 
			[has_billing_day] => 
			[free_trial] => 
		) 
		[summarized] => stdClass Object ( 
			[quotas] => 
			[charged_quantity] => 1 
			[pending_charge_quantity] => 
			[charged_amount] => 59 
			[pending_charge_amount] => 
			[semaphore] => green 
			[last_charged_date] => 2022-07-15T17:06:31.571-04:00 
			[last_charged_amount] => 59 
		) 
		[next_payment_date] => 2022-08-15T17:05:56.000-04:00 
		[payment_method_id] => master 
		[card_id] => 9117097960 
		[first_invoice_offset] => 
	) 
	*/
}
