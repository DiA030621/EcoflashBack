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
			->select("*")
			->from("users")
			->where('passwd', $data['passwd'])
			->where('email', $data['email'])
			->get();
		return $rs->num_rows() > 0 ? $rs-> result() : null;
	}
//
//
//	public function get_tramo($clave_ruta)
//		/*obtiene clave de ruta,clave de paradas
//		y las coordenadas de las paradas
//		de una ruta seleccionada*/
//	{
//		$rs=$this->db
//			->select("t.*, latitud, longitud")
//			->from("paradas p")
//			->join('tramos t', 't.clave_parada=p.clave')
//			->where('t.clave_ruta', $clave_ruta)
//			->get();
//		//die($this->db->last_query());
//		return $rs-> num_rows() > 0 ? $rs->result() : null;
//	}
//
//

//
//
//	public function insert_tramo($data)
//		//inserta un nuevo tramo que debera tomar la ruta
//	{
//		$this->db->insert('tramos', $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//
//	public function update_ruta($data)
//		//actualiza el nombre de una ruta
//		//Ejemplo: ruta 7 cambia a ruta c21
//	{
//		$this->db
//			->where('clave', $data['clave'])
//			->update("ruta", $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//	/*
//	//comento enta funcion pues no se deberia de actualizar
//	//un tramo de una ruta, sino se debera de eliminar o agregar
//	// un nuevo tramo para cambiar la trayectoria de una ruta.
//
//		public function update_tramo($data)
//		{
//			$this->db
//			->where('clave', $data['clave'])
//			->update("tramos", $data);
//			$rs=$this->db->affected_rows();
//			return $rs > 0;
//		}
//	*/
//	public function delete_ruta($clave)
//		//elimina una ruta con su clave.
//	{
//		$this->db
//			->where('clave', $clave)
//			->delete("ruta");
//		//die($this->db->last_query());
//		$rs=$this->db->affected_rows();
//		return $rs >0;
//	}
//
//
//	public function delete_tramo($clave)
//		/*elimina un tramo usando
//		la clave de ruta y la clave de parada*/
//	{
//		$this->db
//			->where('clave', $clave)
//			->delete("tramos");
//		$rs=$this->db->affected_rows();
//		return $rs >0;
//	}
//
//
//	public function get_vehiculos_chofer()
//		//obtiene todos los registros de vehiculos
//	{
//		/* $rs=$this->db
//		 ->select("*")
//		 ->from("vehiculo")
//		 ->get();
//		 return $rs->num_rows() > 0 ? $rs-> result() : null;*/
//		$subquery = $this->db->select('clave_vehiculo')
//			->from('audita_chof')
//			->get_compiled_select();
//
//		$rs=$this->db->select('*')
//			->from('vehiculo')
//			->where("clave NOT IN ($subquery)", null, false)
//			->get();
//
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
//
//	public function get_vehiculos()
//	{
//		$rs=$this->db
//			->select("*")
//			->from("vehiculo")
//			->get();
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
//
//
//	public function get_vehiculo($clave)
//		//obtiene registro de vehiculos segun su clave
//	{
//		$rs=$this->db
//			->select("*")
//			->from("vehiculo")
//			->where("clave", $clave)
//			->get();
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
//
//
//	public function insert_vehiculo($data)
//		//inserta un nuevo vehiculo
//	{
//		$this->db->insert('vehiculo', $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//
//	public function update_vehiculo($data)
//		//actualiza los datos de un vehiculo
//	{
//		$this->db
//			->where('clave', $data['clave'])
//			->update("vehiculo", $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//
//	public function delete_vehiculo($clave)
//		//elimina el registro de un vehiculo usando su clave
//	{
//		$this->db
//			->where('clave', $clave)
//			->delete("vehiculo");
//		$rs=$this->db->affected_rows();
//		return $rs >0;
//	}
//
//
//	public function get_paradas()
//		//obtiene todos los registros de paradas
//	{
//		$rs=$this->db
//			->select("*")
//			->from("paradas")
//			->get();
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
//
//
//	public function insert_parada($data)
//		//ingresa un nueva parada
//	{
//		$this->db->insert('paradas', $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//
//	public function update_parada($clave,$data)
//		/*actualiza los datos de una parada,
//		 en este caso solo deberia ser su nombre */
//	{
//		$this->db
//			->where('clave', $clave)
//			->update("paradas", $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//
//	public function delete_parada($clave)
//		//elimina una parada usando su clave
//	{
//		$this->db
//			->where('clave', $clave)
//			->delete("paradas");
//		$rs=$this->db->affected_rows();
//		return $rs >0;
//	}
//
//
//	public function get_parada_tramo($clave)
//		//elimina una parada usando su clave
//	{
//		$subquery = $this->db->select('clave_parada')
//			->from('tramos')
//			->where('clave_ruta', $clave)
//			->get_compiled_select();
//
//		$rs=$this->db->select('*')
//			->from('paradas')
//			->where("clave NOT IN ($subquery)", null, false)
//			->get();
//
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
//
//	public function get_ruta_vehiculo($clave_vehiculo)
//		//modelo que obtiene la ruta de un vehiculo
//	{
//		$rs=$this->db
//			->select("ar.*, nombre")
//			->from("ruta r")
//			->join('audita_ruta ar', 'ar.clave_ruta=r.clave')
//			->where('ar.clave_vehiculo', $clave_vehiculo)
//			->get();
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
//
//	public function insert_ruta_vehiculo($data)
//		//ingresa una ruta a un vehiculo
//	{
//		$this->db->insert('audita_ruta', $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//	public function update_ruta_vehiculo($data)
//		//actualiza la ruta de un vehiculo
//	{
//		$this->db
//			->where('clave_vehiculo', $data['clave_vehiculo'])
//			->update("audita_ruta", $data);
//		$rs=$this->db->affected_rows();
//		return $rs > 0;
//	}
//
//	public function delete_ruta_vehiculo($clave)
//		//elimina una ruta de un vehiculo
//	{
//		$this->db
//			->where('clave_vehiculo', $clave)
//			->delete("audita_ruta");
//		$rs=$this->db->affected_rows();
//		return $rs >0;
//	}
//
//	public function grafica_pago()
//	{
//		$rs=$this->db->select('r.nombre AS nombre_ruta, COUNT(*) AS contador_pagos')
//			->from('pago p')
//			->join('vehiculo v', 'p.clave_vehiculo = v.clave')
//			->join('audita_ruta ar', 'v.clave = ar.clave_vehiculo')
//			->join('ruta r', 'ar.clave_ruta = r.clave')
//			->group_by('r.nombre')
//			->get();
//		//die($this->db->last_query());
//		return $rs->num_rows() > 0 ? $rs-> result() : null;
//	}
}
