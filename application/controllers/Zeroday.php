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
		$email=$this->input->post('email');
		$this->load->library('email');

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
		$r=$this->zeroday_model->payment($order_id);
		if($r){
			$this->email->initialize($config);
			$this->email->from('diego.0d4y@gmail.com', 'Orden de compra 0D4Y');
			$this->email->to($email);
			$this->email->subject('Orde de compra 0D4Y');
			$this->email->message("Comprase Hardware VPN 0D4Y, para mas informacion consulta en la pagina oficial con el siguiente numero de orden: ".$order_id);

			if ($this->email->send()) {
//				echo json_encode(['status' => 'success', 'message' => 'Correo enviado orden de compra']);
			}
		}

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

	public function generate_invoice() {
		$order_id = $this->input->post('order_id');
		$customer_name = $this->input->post('customer_name');
		$customer_email = $this->input->post('customer_email');
		$customer_address = $this->input->post('customer_address');
		$customer_rfc = $this->input->post('customer_rfc');
		$amount = $this->input->post('amount');

		if (!$customer_email || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
			echo json_encode(['status' => 'error', 'message' => 'Correo inválido']);
			return;
		}

		$this->load->library('pdf');
		$pdf = new Pdf();
//		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 12);

		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 12);

// Agregar el logo y evitar superposición
		$pdf->Image(FCPATH . 'Logo.png', 10, 10, 40);
		$pdf->SetY(50); // Ajustar la posición después del logo

// Información de la empresa
		$html = "<h2 style='text-align:center;'>Factura Electrónica</h2>
<p><strong>Empresa:</strong> 0D4Y</p>
<p><strong>Propietario:</strong> Jose de Jesus Valencia Rojas</p>
<p><strong>Dirección:</strong> Av. Tecnológica #123, CDMX</p>
<p><strong>Teléfono:</strong> +52 55 1234 5678</p>
<hr>
<p><strong>Cliente:</strong> $customer_name</p>
<p><strong>Dirección:</strong> $customer_address</p>
<p><strong>RFC:</strong> $customer_rfc</p>
<hr>
<h3>Detalles de la Compra</h3>
<table border='1' cellspacing='0' cellpadding='5'>
    <tr>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio Unitario (con IVA)</th>
        <th>Subtotal</th>
    </tr>";

		$price_per_unit = 300;
		$total = $amount * $price_per_unit;
		$subtotal = $total / 1.16;
		$iva = $total - $subtotal;

		$html .= "<tr>
    <td>HARDWARE 0D4Y</td>
    <td>$amount</td>
    <td>$" . number_format($price_per_unit, 2) . "</td>
    <td>$" . number_format($subtotal, 2) . "</td>
</tr>
</table>
<p><strong>Subtotal (sin IVA):</strong> $" . number_format($subtotal, 2) . "</p>
<p><strong>IVA (16%):</strong> $" . number_format($iva, 2) . "</p>
<p><strong>Total (con IVA):</strong> $" . number_format($total, 2) . "</p>";

		$pdf->writeHTML($html, true, false, true, false, '');

// Agregar código QR en la esquina inferior derecha
//		$pdf->Image(FCPATH . 'QR_code.png', 160, 250, 30);

		$pdf_path = FCPATH . "facturas/factura_$order_id.pdf";
		$pdf->Output($pdf_path, 'F');


//die();
		// Configuración del correo
		$this->load->library('email');
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

		$this->email->initialize($config);
		$this->email->from('diego.0d4y@gmail.com', 'Empresa 0D4Y');
		$this->email->to($customer_email);
		$this->email->subject('Tu factura electrónica');
		$this->email->message('<p>Adjunto encontrarás tu factura electrónica.</p>');
		$this->email->attach($pdf_path);

		if ($this->email->send()) {
			echo json_encode(['status' => 'success', 'message' => 'Factura enviada correctamente']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Error al enviar la factura', 'debug' => $this->email->print_debugger()]);
		}
	}



}
