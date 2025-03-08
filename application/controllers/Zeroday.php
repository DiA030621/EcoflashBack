<?php
class Zeroday extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("zeroday_model");
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
	}


	public function index()
		//controlador por defecto
	{
		echo 'zeroday';
	}
	public function customer_purchase(): void
	{
		$name=$this->input->post('name');
		$email=$this->input->post('email');
		$phone=$this->input->post('phone');
		$address=$this->input->post('address');
		$data=array(
			'name'=>$name,
			'phone'=>$phone,
			'address'=>$address,
			'email'=>$email
		);
		$r = $this->zeroday_model->customer_purchase($data);

		$obj["resultado"] = $r != NULL && $r > 0;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se inserto comprador correctamente con ID: $r" :
			"No se insertaron los datos del comprador";
		$obj["id"] = $r; // Agregar el ID en la respuesta


		echo json_encode($obj);
	}

	public function purchase(): void
	{
		$customer_id=$this->input->post('customer_id');
		$total_price=$this->input->post('total_price');
		$quantity=$this->input->post('quantity');
		$amount=$this->input->post('amount');

		$dataOrder=array(
			'customer_id'=>$customer_id,
			'total_price'=>$total_price,
			'order_status'=>"pending"
		);
		$dataOrderProduct=array(
			'product_id'=>1,
			'quantity'=> $quantity
		);
		$dataPayment=array(
			'payment_status'=>"pending",
			'amount'=> $amount
		);
		$r=$this->zeroday_model->order($dataOrder, $dataOrderProduct, $dataPayment);


		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se inserto la compra correctamente con ID: $r" :
			"No se insertaron los datos del comprador";
		$obj["id"] = $r;


		echo json_encode($obj);

	}

	public function get_order(): void
	{
		$order_id=$this->input->get('order_id');
		$r = $this->zeroday_model->get_order($order_id);

		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"recuperacion de datos correcto" : "No se encontraron datos";
		$obj["order"] = $r;


		echo json_encode($obj);
	}


}
