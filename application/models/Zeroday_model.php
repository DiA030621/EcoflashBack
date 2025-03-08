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


}
