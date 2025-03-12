<?php
class Zeroday extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("zeroday_model");
		$this->load->library('email');
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
		$total_price=$this->input->post('total_price');
		$quantity=$this->input->post('quantity');
		$isDuplicated=$this->input->post('isDuplicated');
//		print_r($isDuplicated);
//		die();
		$data=array(
			'name'=>$name,
			'phone'=>$phone,
			'address'=>$address,
			'email'=>$email
		);
		if ($isDuplicated == 1){
			$r = $this->zeroday_model->customer_update($data);
			$customer_id = $r[0]->id;
			if(!$r){
				$obj["resultado"] = false;
				$obj["mensaje"] ="No se actualizaron los datos del comprador";
				$obj["id"] = $r;
				echo json_encode($obj);
				return;
			}
		}elseif($isDuplicated == 0){
			$r = $this->zeroday_model->customer_purchase($data);
			$customer_id=$r;
			if($r == NULL || $r < 0){
				$obj["resultado"] = false;
				$obj["mensaje"] ="No se insertaron los datos del comprador";
				$obj["id"] = $r;
				echo json_encode($obj);
				return;
			}
		}elseif ($isDuplicated == 2){
			$r = $this->zeroday_model->get_email($email);
			$customer_id = $r->result_object[0]->id??null;
			if($customer_id == null){
				$obj["resultant"] = false;
				$obj["mensaje"] ="Error en recuperacion de id";
				echo json_encode($obj);
				return;
			}
		}

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
			'amount'=> $quantity
		);
		$r=$this->zeroday_model->order($dataOrder, $dataOrderProduct, $dataPayment);
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se inserto la compra correctamente con ID: $r" :
			"No se insertaron los datos del comprador";
		$obj["orderId"] = $r;

		echo json_encode($obj);
	}

	public function payment(): void
	{
		$order_id=$this->input->post('order_id');

		$r=$this->zeroday_model->payment($order_id);

		$obj["resultado"] = $r;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se realizo el pago correctamente con ID: $order_id" :
			"No se pudo realizar el pago";

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

	public function send_verification_email() {
		$this->load->library('email');
		$email=$this->input->post('email');
		$r1 = $this->zeroday_model->get_email($email);
		$row = $r1->row();
		$verification_token = $row->verification_token;
		$is_verified = $row->is_verified;
		if($verification_token != null && $is_verified ==1){
			echo 'hola';
			return;
		}

		$config = array(
			'protocol'    => 'smtp',
			'smtp_host'   => 'smtp.gmail.com',
			'smtp_user'   => 'diego.0d4y@gmail.com',
			'smtp_pass'   => 'qmjo ftds nfds vuzl',
			'smtp_port'   => 587,
			'smtp_crypto' => 'tls',
			'mailtype'    => 'html',
			'charset'     => 'utf-8',
			'newline'     => "\r\n",
			'wordwrap'    => TRUE
		);

		if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo json_encode(['status' => 'error', 'message' => 'Correo inválido']);
			return;
		}
		$token = bin2hex(random_bytes(32));

		if ($this->zeroday_model->set_verification_token($email, $token)) {
			$verification_link = base_url("verify?token=" . $token);

			// Configurar y enviar el correo
			$this->email->initialize($config);
			$this->email->from('diego.0d4y@gmail.com', 'Verificación de Cuenta');
			$this->email->to($email);
			$this->email->subject('Verifica tu correo');
			$this->email->message("Haz clic en el siguiente enlace para verificar tu correo: <a href='$verification_link'>$verification_link</a>");

			if ($this->email->send()) {
				echo json_encode(['status' => 'success', 'message' => 'Correo de verificacion enviado']);
			} else {
				echo json_encode(['status' => 'error', 'message' => 'Error al enviar el correo']);
			}
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No se pudo generar el token']);
		}
	}

	public function verify()
	{
		$token = $this->input->get('token');

		if (!$token) {
			show_error("Token inválido", 400);
			return;
		}

		// Validar token en la BD
		$email = $this->zeroday_model->verify_token($token);
		if ($email) {
			// Marcar el correo como verificado
			$this->zeroday_model->set_verified($email);
			echo "Correo verificado correctamente.";
		} else {
			show_error("Token inválido o expirado", 400);
		}

	}

	public function get_email()
	{
		$email = $this->input->get('email');
		$rEmail = $this->zeroday_model->get_email($email);

		$obj["resultado"] = $rEmail != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"recuperacion de email" : "No se encontraro email";
		$obj["order"] = $rEmail;
		echo json_encode($obj);
	}

}
