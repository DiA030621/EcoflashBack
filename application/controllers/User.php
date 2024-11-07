<?php
class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("user_model");
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: Content-Type');
	}


	public function index()
		//controlador por defecto
	{
		echo 'acceso restringido';
	}
	public function singin(): void
	{
		$name=$this->input->post('name');
		$lastname=$this->input->post('lastname');
		$type=$this->input->post('type');
		$passwd=$this->input->post('passwd');
		$email=$this->input->post('email');
		$data=array(
			'name'=>$name,
			'lastname'=>$lastname,
			'type'=>$type,
			'passwd'=>$passwd,
			'email'=>$email
		);
		$r=$this->user_model->insert_user($data);
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se inserto usuario correctamente" :
			"No se insertaron los datos";

		echo json_encode($obj);
	}

	public function login(): void
	{
		$passwd=$this->input->post('passwd');
		$email=$this->input->post('email');
		$data=array(
			'passwd'=>$passwd,
			'email'=>$email
		);
		$r=$this->user_model->validate_user($data);
		$obj["resultado"] = TRUE != FALSE;
		$obj["mensaje"] = $obj["resultado"] ?
			"inicio de sesion correcto" : "No se encontraron usuarios";
		$obj["id"] = $r;

		echo json_encode($obj);
	}

	public function update_user():void
	{
		$id=$this->input->post('id');
		$name=$this->input->post('name');
		$lastname=$this->input->post('lastname');
		$type=$this->input->post('type');
		$passwd=$this->input->post('passwd');
		$email=$this->input->post('email');
		$data=array(
			'name'=>$name,
			'lastname'=>$lastname,
			'type'=>$type,
			'passwd'=>$passwd,
			'email'=>$email
		);
		$r=$this->user_model->update_user($data, $id);
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se actualizo correctamente el usuario" :
			"No se actualizaron los datos";

		echo json_encode($obj);
	}



	public function get_user():void
	{
		$id=$this->input->post('id');
		$r=$this->user_model->get_user($id);
		$obj["resultado"] = TRUE != FALSE;
		$obj["mensaje"] = $obj["resultado"] ?
			"recuperacion de datos correcto" : "No se encontraron datos";
		$obj["user"] = $r;

		echo json_encode($obj);
	}


	public function delete_user():void
	{
		$id=$this->input->post('id');
		$r=$this->user_model->delete_user($id);
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se elimino el usuario correctamente" :
			"No se eliminaron los datos, intente mas tarde";

		echo json_encode($obj);
	}

}
