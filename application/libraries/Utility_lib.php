<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utility_lib{
	public function __construct(){
		$this->CI = &get_instance();
	}
	
	public function calculate_last_year_per_data($records){
		$data_per = array();
		foreach($records as $i => $item){
			if ($item->close){
				if ($item->last_year_per) break;
				else {
					$min = $max = $item->close;
					$one_year = date('Y-m-d', strtotime('-1 year', strtotime($item->date)));
					$i_run = $i;
					while($records[$i_run]->date >= $one_year){
						if ($records[$i_run]->close){
							if ($min > $records[$i_run]->close) $min = $records[$i_run]->close;
							elseif ($max < $records[$i_run]->close) $max = $records[$i_run]->close;	
						}
						$i_run++;
						if ($i_run >= count($records)) break;
					}
					
					if ($max == $min) $per = 0;
					else $per = ($item->close - $min) * 100 / ($max - $min);
					
					array_push($data_per, array("id" => $item->id, "last_year_per" => $per));
				}	
			}
		}
		return $data_per;
	}
	
	public function get_bvl($url, $datas, $is_post = false){
		if ($is_post){
			$datas = json_encode($datas);
			$header_data = array(
								"Content-Type: application/json",
								"Content-Length: ".strlen($datas)
							);
		}else $header_data = array("Content-Type: application/json");

		$ch = curl_init(); //curl 사용 전 초기화 필수(curl handle)

		curl_setopt($ch, CURLOPT_URL, $url); //URL 지정하기
		curl_setopt($ch, CURLOPT_POST, $is_post); //0이 default 값이며 POST 통신을 위해 1로 설정해야 함
		if ($is_post) curl_setopt ($ch, CURLOPT_POSTFIELDS, $datas); //POST로 보낼 데이터 지정하기
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_data); //header 지정하기
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); //이 옵션이 0으로 지정되면 curl_exec의 결과값을 브라우저에 바로 보여줌. 이 값을 1로 하면 결과값을 return하게 되어 변수에 저장 가능(테스트 시 기본값은 1인듯?)

		$res = curl_exec($ch);
		curl_close($ch);
		
		if ($res) return json_decode($res); else return null;
	}
	
	public function convert_today_to_record($today){
		if (property_exists($today, "sectorCode")) $today->is_national = true;
		else{
			$today->sectorCode = null;
			$today->sectorDescription = null;
			$today->is_national = false;
		}
		if (!property_exists($today, "opening")) $today->opening = $today->last = $today->maximun = $today->minimun = 0;
		if (!property_exists($today, "previous")) $today->previous = null;
		if (!property_exists($today, "previousDate")) $today->previousDate = null;
		if (!property_exists($today, "percentageChange")) $today->percentageChange = null;
		if (!property_exists($today, "lastDate")) $today->lastDate = null;
		if (!property_exists($today, "negotiatedQuantity"))
			$today->negotiatedQuantity = $today->negotiatedAmount = $today->negotiatedNationalAmount = null;
		if (!property_exists($today, "buy")) $today->buy = 0;
		if (!property_exists($today, "sell")) $today->sell = 0;
		
		if (!strcmp($today->currency, "US$")) $amountNegotiated = $today->negotiatedAmount;
		else{
			$amountNegotiated = $today->negotiatedNationalAmount;
			$today->currency = "S/.";
		}
		
		$record = new stdClass;
		$record->companyName = $today->companyName;
		$record->is_national = $today->is_national;
		$record->sectorCode = $today->sectorCode;
		$record->sectorDescription = $today->sectorDescription;
		$record->nemonico = $today->nemonico;
		$record->date = date('Y-m-d', strtotime($today->createdDate));
		$record->open = $today->opening;
		$record->close = $today->last;
		$record->high = $today->maximun;
		$record->low = $today->minimun;
		$record->average = ($today->opening + $today->last + $today->maximun + $today->minimun) / 4;
		$record->percentageChange = $today->percentageChange;
		$record->buy = $today->buy;
		$record->sell = $today->sell;
		$record->quantityNegotiated = $today->negotiatedQuantity;
		$record->amountNegotiated = $amountNegotiated;
		$record->yesterday = $today->previousDate;
		$record->yesterdayClose = $today->previous;
		$record->currencySymbol = $today->currency;
		
		return $record;
	}
	
	public function get_last_from_bvl($nemonico = "", $nemonicos = array(), $is_today = false){
		$records = array();
		$last_updated_at = strtotime(date("Y-m-d"));
		
		$url = 'https://dataondemand.bvl.com.pe/v1/stock-quote/market'; //접속할 url 입력
		$datas = array("sector" => "", "today" => $is_today, "companyCode" => "", "inputCompany" => $nemonico);
		$res = $this->get_bvl($url, $datas, true);
		if ($res){
			if ($nemonico) $nemonicos = array($nemonico);
			$result = $res->content;
			foreach($result as $item){
				$created_at = strtotime($item->createdDate);
				if ($last_updated_at < $created_at) $last_updated_at = $created_at;
				if ($nemonicos){
					if (in_array($item->nemonico, $nemonicos)) array_push($records, $this->convert_today_to_record($item));
				}else array_push($records, $this->convert_today_to_record($item));
			}
		}
		
		$result = new stdClass;
		$result->records = $records;
		$result->updated_at = date("Y-m-d h:i:s a", $last_updated_at);
		//$result->updated_at_time = $last_updated_at;
		
		return $result;
	}
	
	public function get_today_resume(){
		$resume = new stdClass;
		$resume->opNumber = 0;
		$resume->up = 0;
		$resume->down = 0;
		$resume->negociated = 0;
		
		$url = 'https://dataondemand.bvl.com.pe/v1/stock-quote/market'; //접속할 url 입력
		$datas = array("sector" => "", "today" => true, "companyCode" => "", "inputCompany" => "");
		$res = $this->get_bvl($url, $datas, true);
		if ($res){
			//$date_aux = null;
			$result = $res->content;
			if (!$result){
				$this->CI->load->model('record_today_model','record_today');
				$result = $this->CI->record_today->get_all();
			}
			
			foreach($result as $item){
				/*
				if ($date_aux){
					$converted = strtotime($item->createdDate);
					if ($converted > $date_aux) $date_aux = $converted;
				}else $date_aux = strtotime($item->createdDate);
				*/
				if (property_exists($item, 'operationsNumber')) $resume->opNumber += $item->operationsNumber;
				if (property_exists($item, 'negotiatedNationalAmount')) $resume->negociated += $item->negotiatedNationalAmount;
				if (property_exists($item, 'percentageChange')){
					if ($item->percentageChange > 0) $resume->up++;
					elseif ($item->percentageChange < 0) $resume->down++;	
				}
			}
			//$resume->date = date('Y-m-d', $date_aux);
			$resume->opNumber_text = number_format($resume->opNumber);
			$resume->negociated_text = "S/. ".$this->shortNumber($resume->negociated);
		}
		
		return $resume;
	}
	
	public function get_stocks_from_bvl($nemonico, $from = "", $to = ""){
		if (!$from) $from = "2000-01-01";
		if (!$to) $to = date('Y-m-d', strtotime('+1 year', time()));
		
		$nemonico = str_replace("/", "%2F", $nemonico);
		$from_history = date('Y-m-d', strtotime('-1 day', strtotime($from)));
		$to_history = date('Y-m-d', strtotime('+1 day', strtotime($to)));
		
		$datas = array();
		$url = "https://dataondemand.bvl.com.pe/v1/issuers/stock/".$nemonico."?startDate=".$from_history."&endDate=".$to_history;
		$res = $this->get_bvl($url, null, false);
		if ($res) foreach($res as $item){
			if ($item->quantityNegotiated or $item->close){
				if (!trim($item->currencySymbol)) $item->currencySymbol = "S/."; else $item->currencySymbol = "US$";
				array_push($datas, $item);
			}
		}
		
		usort($datas, function($a, $b){ return $a->date < $b->date; });
		
		return $datas;
	}
	
	public function shortNumber($num, $is_decimal = true){
		if (abs($num) > 1000){		
			$units = ['', 'K', 'M', 'B', 'T'];
			for ($i = 0; abs($num) >= 1000; $i++) $num /= 1000;
			$decimal = 2;
			return number_format(round($num, 2), $decimal).$units[$i];
		}else{
			if ($is_decimal){
				$decimal = strlen(substr(strrchr($num, "."), 1));
				if ($decimal < 2) $decimal = 2;
			}else $decimal = 0;
			return number_format($num, $decimal);
		}
	}
	
	function randomStr($length = 10){
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}