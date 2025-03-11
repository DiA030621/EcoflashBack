<?php
class Zeroday_model extends CI_Model
{
	public function customer_purchase($data): int
	{
		$this->db->insert('customer', $data);
		$insert_id = $this->db->insert_id();
//		print_r($insert_id);
//		die();
		return $insert_id;
	}


	public function order($dataOrder, $dataOrderProducts, $dataPayment): bool|string
	{
		$this->db->trans_start();

		$this->db->insert('orders', $dataOrder);
		$rOrders = $this->db->insert_id();
		if ($rOrders <= 0) {
			$this->db->trans_rollback();
			return 'Error agregando a orden de venta';
		}

		if (!empty($dataOrderProducts) && is_array($dataOrderProducts)) {
			$dataOrderProducts['order_id'] = $rOrders;
			$this->db->insert('order_product', $dataOrderProducts);
			if ($this->db->affected_rows() <= 0) {
				$this->db->trans_rollback();
				return 'Error agregando a relación orden y producto';
			}
//			foreach ($dataOrderProducts as $product) {
//				$product['order_id'] = $rOrders;
//				$this->db->insert('order_product', $product);
//				if ($this->db->affected_rows() <= 0) {
//					$this->db->trans_rollback();
//					return 'Error agregando a relación orden y producto';
//				}
//			}
		} else {
			$this->db->trans_rollback();
			return 'No hay productos en la orden';
		}

		$dataPayment['order_id'] = $rOrders;
		$this->db->insert('payment', $dataPayment);
		if ($this->db->affected_rows() <= 0) {
			$this->db->trans_rollback();
			return 'Error agregando el pago';
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return "Error en la transacción";
		}

		return $rOrders;
	}

	public function get_order($order_id)
	{
		$this->db->select('o.order_status, o.customer_id, p.payment_status, p.amount');
		$this->db->select("(SELECT c.name FROM customer c WHERE c.id = o.customer_id) AS customer_name", false);
		$this->db->select("(SELECT c.email FROM customer c WHERE c.id = o.customer_id) AS customer_email", false);
		$this->db->select("(SELECT c.phone FROM customer c WHERE c.id = o.customer_id) AS customer_phone", false);
		$this->db->from('orders o');
		$this->db->join('payment p', 'p.order_id = o.id', 'inner');
		$this->db->where('o.id', $order_id);

		$rs = $this->db->get();
		return $rs->num_rows() > 0 ? $rs-> result() : null;

	}

	public function payment ($order_id): bool
	{
		$data=array(
			'order_status' => "paid"
		);
		$dataPayment=array(
			'payment_status' => "completed"
		);
		$this->db->select('p.amount');
		$this->db
			->where('id', $order_id)
			->update('orders', $data);
		$rs1=$this->db->affected_rows();
		if ($rs1) {
			$this->db
				->where('order_id', $order_id)
				->update('payment', $dataPayment);
			$r1=$this->db->affected_rows();
			if($r1){
				return true;
			}
			else{
				return false;
			}
		}
		return false;
	}


	public function set_verification_token($email, $token) {
		$this->db->where('email', $email);
		return $this->db->update('customer', ['verification_token' => $token]);
	}
	public function verify_token($token) {
		$this->db->where('verification_token', $token);
		$query = $this->db->get('customer');

		if ($query->num_rows() == 1) {
			return $query->row()->email;
		}
		return false;
	}

	public function set_verified($email) {
		$this->db->where('email', $email);
		return $this->db->update('customer', ['is_verified' => 1, 'verification_token' => NULL]);
	}

	public function get_email($email) {
		$rs=$this->db
			->select("id, email, is_verified")
			->from("customer")
			->where('email', $email)
			->get();
		return $rs->num_rows() > 0 ? $rs-> result() : null;
	}
	public function customer_update($data)
	{
		$this->db->where('email', $data['email']);
		$this->db->update('customer', $data);
		$r = $this->db->affected_rows();
		if ($r < 0) {
			return false;
		}
		$this->db->select('id');
		$this->db->from('customer');
		$this->db->where('email', $data['email']);
		$customer_id=$this->db->get();
		return $customer_id->num_rows() > 0 ? $customer_id-> result() : false;
	}
}
