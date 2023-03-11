<?php

class record_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'record';
	}
  
    function get_by_id($id){
		$this->db->where('id', $id);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function get_general($fields = "", $where = null, $nemonicos = null, $orders = null){
		if ($fields) $this->db->select($fields);
		if ($where) $this->db->where($where);
		if ($nemonicos) $this->db->where_in('nemonico', $nemonicos);
		if ($orders) foreach($orders as $item) $this->db->order_by($item["order_by"], $item["order"]);
		
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
  
    function get_by_nemonico($nemonico, $fields = null, $order_by = "date", $order = "desc"){
		if ($fields) $this->db->select($fields);
		$this->db->where('nemonico', $nemonico);
		$this->db->order_by($order_by, $order);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
  
    function get_by_date($date){
		$this->db->where('date', $date);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
  
    function get_by_nemonico_date($nemonico, $date){
		$this->db->where('nemonico', $nemonico);
		$this->db->where('date', $date);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
  
    function get_dates($nemonico, $from, $to){
		$this->db->select("date");
		$this->db->where('date >=', $from);
		$this->db->where('date <=', $to);
		$this->db->where('nemonico', $nemonico);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_by_ids($ids, $f_where = null){
		$this->db->where_in('id', $ids);
		if ($f_where) $this->db->where($f_where);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_unique_nemonico(){
		$this->db->select("nemonico");
		$this->db->order_by('nemonico', 'asc');
		$this->db->group_by("nemonico");
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_last_single($nemonico, $is_valid = false){
		$this->db->where('nemonico', $nemonico);
		if ($is_valid) $this->db->where('open >', 0);
		$this->db->limit(1);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function get_by_limit($nemonico, $valid_close = false, $limit = null){
		$this->db->where('nemonico', $nemonico);
		if ($valid_close) $this->db->where('close >', 0);
		if ($limit) $this->db->limit($limit);
		$this->db->order_by('date', 'desc');
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_last($nemonicos = null, $order_by = "date", $order = "desc"){
		$this->db->select('nemonico');
		$this->db->select_max('date');
		$this->db->from($this->tablename);
		$this->db->where('close >', 0);
		if ($nemonicos) $this->db->where_in('nemonico', $nemonicos);
		$this->db->group_by("nemonico");
		$sub_query_1 = $this->db->get_compiled_select();
		
		$this->db->where_in("(nemonico, date)", $sub_query_1 ,false);
		$this->db->order_by($order_by, $order);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_checklist(){
		$today = date("Y-m-d");
		
		$where = array();
		$where["close >"] = 0;
		$where["date <"] = date('Y-m-d', strtotime('+1 day', strtotime($today)));
		$where["date >="] = date('Y-m-d', strtotime('-2 weeks', strtotime($today)));
		//$where_str = "(((0 < last_year_per) and (last_year_per <= 30)) or ((70 <= last_year_per) and (last_year_per < 100)))";
		$where_str = "((last_year_per <= 30) or (70 <= last_year_per))";
			
		$this->db->select('nemonico');
		$this->db->select_max('date');
		$this->db->where($where);
		$this->db->where($where_str);
		$this->db->from($this->tablename);
		$this->db->group_by("nemonico");
		$sub_query_1 = $this->db->get_compiled_select();
		
		$this->db->select("nemonico, last_year_per");
		$this->db->where_in("(nemonico, date)", $sub_query_1 ,false);
		$this->db->order_by("last_year_per", "desc");
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_last_date(){
		$this->db->select_max('date');
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function count_all(){
		$query = $this->db->get($this->tablename);
		return $query->num_rows();
	}
	
	function get_all($order_by = "date", $order = "desc"){
		$this->db->order_by("nemonico", "asc");
		$this->db->order_by($order_by, $order);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function insert($data){ 
		$this->db->insert($this->tablename, $data);
		return $this->db->insert_id();		
	}
	
	function insert_multi($data){
		return $this->db->insert_batch($this->tablename, $data);
	}
	
	function update($id, $data){ 
		$this->db->where('id', $id);	
		return $this->db->update($this->tablename, $data);
	}
	
	function update_multi($data){
		$this->db->update_batch($this->tablename, $data, 'id');
	}
	
	function update_multi_nemonico($data){
		$this->db->update_batch($this->tablename, $data, 'nemonico');
	}
	
	function delete($id){		
		$this->db->where('id', $id);	
		return $this->db->delete($this->tablename);		
	}
}
?>
