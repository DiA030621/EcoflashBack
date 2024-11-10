<?php
class Container_model extends CI_Model
{
	public function validate_container($id_container)
	{

		$rs = $this->db->
		select('f.id_container, f.centimeters, w.grams')->
		from('fill_level f')->
		join('weight w', 'f.id_container = w.id_container')->
		where('f.id_container', $id_container)->
		order_by('f.id', 'DESC')->
		order_by('w.id', 'DESC')->
		limit(1)->
		get();

		return $rs->num_rows() > 0 ? $rs-> result() : null;
	}

	public function create_container($data)
	{
		$rs=$this->db
			->insert("container", $data);
		$rs=$this->db->affected_rows();
		return $rs > 0;
	}

	public function delete_container($id):bool
	{
		$this->db
			->where('id', $id)
			->delete("container");
		//die($this->db->last_query());
		$rs=$this->db->affected_rows();
		return $rs >0;
	}

	public function insert_levels($data_fill, $data_weight):bool
	{
		$this->db
			->insert('fill_level', $data_fill);
		$r1=$this->db->affected_rows();
		$this->db
			->insert('weight', $data_weight);
		$r2=$this->db->affected_rows();
		if ($r1 && $r2) {
			return true;
		} else {
			return false;
		}
		//die($this->db->last_query());
	}

//	public function get_user($id)
//	{
//		$rs=$this->db
//			->select("*")
//			->from("users")
//			->where('id', $id)
//			->get();
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
}
