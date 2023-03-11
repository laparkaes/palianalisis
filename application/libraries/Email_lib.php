<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_lib{
	protected $CI;
	
	public function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->library('email');
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'mail.palianalisis.com',
			'smtp_port' => '465',
			'smtp_timeout' => '10',
			'smtp_crypto' => 'ssl',
			'smtp_user' => 'notificacion@palianalisis.com',
			'smtp_pass' => 'Wjddn0315!',
			'charset' => 'utf-8',
			'newline' => "\r\n",
			'mailtype' => 'html',
			'validation' => TRUE
		);

        $this->CI->email->initialize($config);
		$this->is_test = false;
		$this->from_email = "notificacion@palianalisis.com";
		$this->from_name = "PALI Análisis";
		$this->img_logo = "https://www.palianalisis.com/images/logo_f.png";
		$this->icon_fb = "https://www.palianalisis.com/images/icon_fb.png";
		$this->icon_ig = "https://www.palianalisis.com/images/icon_ig.png";
		
		$this->CI->load->model('account_model','account');
		$this->CI->lang->load("email", "spanish");
		
		date_default_timezone_set("America/Lima");
		setlocale(LC_TIME, 'es_PE.UTF-8','esp');
	}
	
	private function send($to, $subject, $content){
		$this->CI->email->from($this->from_email, $this->from_name);
		$this->CI->email->to($to);
        $this->CI->email->subject($subject);
        $this->CI->email->message($content);  
		
		if ($this->CI->email->send()) return true;
		else return false;//$this->CI->email->print_debugger();
	}
	
	public function account_validation($account = null){
		if ($account){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"link" => base_url()."access/email_validation?aid=".$account->id."&code=".$account->validation_code,
				"main" => 'email/account_validation'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_account_validation'), $content);
		}
	}
	
	public function reset_pass($account = null){
		if ($account){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"link" => base_url()."access/reset_pass_confirm?aid=".$account->id."&code=".$account->validation_code,
				"main" => 'email/reset_pass'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_reset_pass'), $content);
		}
	}
	
	public function reset_pass_confirm($account, $pass){
		if ($account and $pass){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"pass" => $pass,
				"link" => base_url(),
				"main" => 'email/reset_pass_confirm'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_reset_pass_confirm'), $content);
		}
	}
	
	public function subscription_new($account, $subscription, $plan){
		if ($account and $subscription and $plan){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"subscription" => $subscription,
				"plan" => $plan,
				"link" => base_url(),
				"main" => 'email/subscription_new'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_subscription_new'), $content);
		}
	}
	
	public function subscription_new_gift($account, $subscription, $plan){
		if ($account and $subscription and $plan){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"subscription" => $subscription,
				"plan" => $plan,
				"link" => base_url(),
				"main" => 'email/subscription_new_gift'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_subscription_new_gift'), $content);
		}
	}
	
	public function subscription_new_week($account, $subscription, $plan){
		if ($account and $subscription and $plan){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"subscription" => $subscription,
				"plan" => $plan,
				"link" => base_url(),
				"main" => 'email/subscription_new_week'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_subscription_new_week'), $content);
		}
	}
	
	public function subscription_cancel($account, $subscription, $plan){
		if ($account and $subscription and $plan){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"subscription" => $subscription,
				"plan" => $plan,
				"link" => base_url(),
				"main" => 'email/subscription_cancel'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_subscription_cancel'), $content);
		}
	}
	
	public function subscription_payment($account, $payment){
		if ($account and $payment){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"payment" => $payment,
				"link" => base_url(),
				"main" => 'email/subscription_payment'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_subscription_payment'), $content);
		}
	}
	
	public function subscription_payment_fail($account, $link){
		if ($account and $link){
			$datas = array(
				"img_logo" => $this->img_logo,
				"icon_fb" => $this->icon_fb,
				"icon_ig" => $this->icon_ig,
				"account" => $account,
				"link" => $link,
				"main" => 'email/subscription_payment_fail'
			);
			$content = $this->CI->load->view('email/layout', $datas, true);
			$this->send($account->email, $this->CI->lang->line('subject_subscription_payment_fail'), $content);
		}
	}

	public function send_admin_generated($datas){
		if ($datas){
			$to = "laparkaes@gmail.com";
			$datas["link"] = base_url();
			$content = $this->CI->load->view('email/admin_generated', $datas, true);
			$this->send($to, $this->CI->lang->line('admin_generated_subject'), $content);
		}
	}
	
	public function testing($content){
		if ($content) $this->send("laparkaes@gmail.com", "Testing", $content);
	}
}