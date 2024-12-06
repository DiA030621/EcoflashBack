<?php
class User_model extends CI_Model
{
	public function insert_user($data): bool
		//inserta una nueva ruta
	{
		$this->db->insert('users', $data);
		$rs=$this->db->affected_rows();
		return $rs > 0;
	}

	public function validate_user($data)
	{
		$rs=$this->db
			->select("id, type, score, name, lastname")
			->from("users")
			->where('passwd', $data['passwd'])
			->where('email', $data['email'])
			->get();
		return $rs->num_rows() > 0 ? $rs-> result() : null;
	}


	public function update_user($data, $id):bool
	{
		$this->db
			->where('id', $id)
			->update("users", $data);
			$rs=$this->db->affected_rows();
			return $rs > 0;
		//die($this->db->last_query());
	}

	public function get_user($id)
	{
		$rs=$this->db
			->select("*")
			->from("users")
			->where('id', $id)
			->get();
		return $rs->num_rows() > 0 ? $rs-> result() : null;
	}

	public function delete_user($id):bool
	{
		$this->db
			->where('id', $id)
			->delete("users");
		//die($this->db->last_query());
		$rs=$this->db->affected_rows();
		return $rs >0;
	}

	public function update_score($id, $data):bool
	{
		$this->db
			->where('id', $id)
			->update("users", $data);
		$rs=$this->db->affected_rows();
		return $rs > 0;
	}

}
