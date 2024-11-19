<?php
class Container_model extends CI_Model
{
	public function validate_container($id_container)
	{

		$rs = $this->db
			->select('f.id_container, f.centimeters, w.grams')
			->from('fill_level f')
			->join('weight w', 'f.id_container = w.id_container')
			->where('f.id_container', $id_container)
			->order_by('f.id', 'DESC')
			->order_by('w.id', 'DESC')
			->limit(1)
			->get();

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

	public function get_levels($id_container)
	{
		$rs1=$this->db
			->select_sum("centimeters", "centimeters")
			->from("fill_level")
			->where('status', false)
			->where('id_container', $id_container)
			->get();
		$rs2=$this->db
			->select_sum("grams", "grams")
			->from("weight")
			->where('status', false)
			->where('id_container', $id_container)
			->get();
		print_r($rs1->result()[0]->centimeters);
		if ($rs1->result()[0]->centimeters > 0 and $rs2->result()[0]->grams > 0) {
			$response["centimeters"]=$rs1->result()[0]->centimeters;
			$response["grams"]=$rs2->result()[0]->grams;
			return $response;
		}
		return false;
	}

	public function get_levels_diff($id_container)
	{
		$rs1=$this->db
			->select("centimeters", "centimeters")
			->from("fill_level")
			->where('status', true)
			->where('id_container', $id_container)
			->order_by('id', 'DESC')
			->get();
		$rs2=$this->db
			->select("grams", "grams")
			->from("weight")
			->where('status', true)
			->where('id_container', $id_container)
			->order_by('id', 'DESC')
			->get();
		if ($rs1->result()[0]->centimeters > 0 and $rs2->result()[0]->grams > 0) {
			$response["centimeters"]=$rs1->result()[0]->centimeters;
			$response["grams"]=$rs2->result()[0]->grams;
			return $response;
		}
		return false;
	}

	public function update_levels($id_container, $data_levels):bool
	{
		$this->db
			->where('id_container', $id_container)
			->update('fill_level', $data_levels);
		$rs1=$this->db->affected_rows();
		$this->db
			->where('id_container', $id_container)
			->update('weight', $data_levels);
		$rs2=$this->db->affected_rows();
		if ($rs1 && $rs2) {
			return true;
		}
		return false;
	}

	public function reset_container($id_container, $data_container, $data_fill, $data_weight):bool
	{
		$this->db
			->where('id', $id_container)
			->update('container', $data_container);
		$rs1=$this->db->affected_rows();

		$rs2=$this->db
			->insert("fill_level", $data_fill);
		$rs2=$this->db->affected_rows();

		$rs3=$this->db
			->insert("weight", $data_weight);
		$rs3=$this->db->affected_rows();

		if ($rs1 && $rs2 && $rs3) {
			return true;
		}
		return false;
	}

	public function get_containers() {
		$sql = "
            WITH LastWeight AS (
                SELECT id_container, grams
                FROM weight w
                WHERE id IN (
                    SELECT MAX(id)
                    FROM weight
                    GROUP BY id_container
                )
            ),
            LastFillLevel AS (
                SELECT id_container, centimeters
                FROM fill_level fl
                WHERE id IN (
                    SELECT MAX(id)
                    FROM fill_level
                    GROUP BY id_container
                )
            )
            SELECT c.*, lw.grams, lfl.centimeters
            FROM container c
            LEFT JOIN LastWeight lw ON c.id = lw.id_container
            LEFT JOIN LastFillLevel lfl ON c.id = lfl.id_container;
        ";

		$rs = $this->db->query($sql);
		return $rs->num_rows() > 0 ? $rs-> result() : null;
	}

	public function update_location($id_container, $data_location):bool
	{
		$this->db
			->where('id', $id_container)
			->update('container', $data_location);
		$rs1=$this->db->affected_rows();
		if ($rs1) {
			return true;
		}
		return false;
	}
}
