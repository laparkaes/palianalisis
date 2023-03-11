<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mercadopago_lib{
	public function __construct(){
		$this->CI = &get_instance();
		$this->api_url = "https://api.mercadopago.com/";
		
		$is_production = false;
		if ($is_production){
			$this->access_token = "APP_USR-7942690668821074-090215-30b88f7478ec9ba4e3c5412cf50fce13-1191109527";
			$this->weekly_preference_id = "1191109527-b739f63a-2ff6-4f2c-adbe-17827ec740a0";
		}else{
			$this->access_token = "TEST-2450507835733617-052600-9568c63858383fa324fa77483737ee85-1130632731";
			$this->weekly_preference_id = "1130632731-30e58feb-0811-46c2-914a-36214d33e7e1";
		}
	}
	/*
	public function make_preference($title, $price){
		$item = new stdClass;
		$item->currency_id = "PEN";
		$item->picture_url = "https://http2.mlstatic.com/D_NQ_NP_601528-MPE51486033192_092022-F.jpg";
		$item->title = $title;
		$item->quantity = 1;
		$item->unit_price = $price;
		
		MercadoPago\SDK::setAccessToken($this->access_token);
		$preference = new MercadoPago\Preference();
		$preference->back_urls = new stdClass;
		$preference->back_urls->success = "https://www.palianalisis.com";
		$preference->operation_type = "regular_payment";
		$preference->marketplace = "NONE";
		$preference->items = array($item);
		
		$preference->save();
		return $preference;
	}
	*/
	public function get_weekly_preference(){
		MercadoPago\SDK::setAccessToken($this->access_token);
		return MercadoPago\Preference::get($this->weekly_preference_id);
	}
	
	public function get_weekly_preference_id(){
		return $this->weekly_preference_id;
	}
	
	public function get_preference($id){
		MercadoPago\SDK::setAccessToken($this->access_token);
		return MercadoPago\Preference::get($id);
	}
	
	public function find_plans($filters = []){
		MercadoPago\SDK::setAccessToken($this->access_token);
		return MercadoPago\Preapproval_plan::search($filters);
	}
	
	public function get_plan($id){
		if ($id){
			MercadoPago\SDK::setAccessToken($this->access_token);
			return MercadoPago\Preapproval_plan::get($id);
		}else return null;
	}
	
	public function get_subscription_payment($id){
		if ($id){
			MercadoPago\SDK::setAccessToken($this->access_token);
			return MercadoPago\AuthorizedPayment::get($id);
		}else return null;
	}
	
	public function find_subscription_payments($filters = []){
		MercadoPago\SDK::setAccessToken($this->access_token);
		return MercadoPago\AuthorizedPayment::search($filters);
	}
	
	public function get_subscription($id){
		if ($id){
			MercadoPago\SDK::setAccessToken($this->access_token);
			return MercadoPago\Preapproval::get($id);
		}else return null;
	}
	
	public function update_subscription_status($id, $status){
		/*
		pending: Preapproval without a payment method
		authorized: Preapproval with a valid payment method
		paused: Preapproval with temporally discontinued collection of payments
		cancelled: Preapproval terminated. This is an irreversible state.
		*/
		MercadoPago\SDK::setAccessToken($this->access_token);
		$subs = MercadoPago\Preapproval::find_by_id($id);
		if ($subs){
			$subs->status = $status;
			@$subs->update();
			if ($subs->error) return $subs->error->message." [".$subs->error->status."]";
			else return null;
		}else return "No subscription";
	}
	
	public function find_subscriptions($filters = []){
		MercadoPago\SDK::setAccessToken($this->access_token);
		if (!array_key_exists("sort", $filters)) $filters['sort'] = "id:desc";
		return MercadoPago\Preapproval::search($filters);
	}
	
	public function post($path, $datas){
		$header_data = array(
			"Authorization: Bearer ".$this->access_token, 
			"Content-Type: application/json"
		);
		$url = $this->api_url.$path;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datas));
	
		$res = curl_exec($ch);
		curl_close($ch);
		
		if ($res) return json_decode($res); else return null;
	}
	
	public function get($path, $datas = null){
		$header_data = array("Authorization: Bearer ".$this->access_token);
		$url = $this->api_url.$path;
		if ($datas) $url = $url."?".http_build_query($datas);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$res = curl_exec($ch);
		curl_close($ch);
		
		if ($res) return json_decode($res); else return null;
	}
	
	public function put($path, $datas){
		$header_data = array(
			"Authorization: Bearer ".$this->access_token, 
			"Content-Type: application/json"
		);
		$url = $this->api_url.$path;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datas));
	
		$res = curl_exec($ch);
		curl_close($ch);
		
		if ($res) return json_decode($res); else return null;
	}
}