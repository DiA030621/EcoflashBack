<?php
class Container extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("container_model");
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: Content-Type');
	}

	public function container_levels(): void
	{
		$id_container=$this->input->get('id_container');
		$grams=$this->input->get('grams');
		$centimeters=$this->input->get('centimeters');
		$r1=$this->container_model->validate_container($id_container);
		if($r1 != NULL){
			foreach ($r1 as $row){
				$id_container1=$row->id_container;
				$grams1=$row->grams;
				$centimeters1=$row->centimeters;

				$diff_grams = abs($grams - $grams1);
				$diff_centimeters = abs($centimeters - $centimeters1);

				if ($diff_grams <= 5 && $diff_centimeters <= 0.5) {
					$obj["mensaje"] = "Los niveles no han cambiado";
					echo json_encode($obj);
					return;
				}
			}
		}
		$data_fill=array(
			'id_container'=>$id_container,
			'centimeters'=>$centimeters
		);
		$data_weight=array(
			'id_container'=>$id_container,
			'grams'=>$grams
		);
		$r=$this->container_model->insert_levels($data_fill, $data_weight);
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se inserto niveles correctamente" :
			"No se insertaron los niveles";

		echo json_encode($obj);
	}

	public function create_container(): void
	{
		$lat=$this->input->post('lat');
		$long=$this->input->post('long');
		$max_fill_level=$this->input->post('max_fill_level');
		$max_weight=$this->input->post('max_weight');
		$data=array(
			'lat'=>$lat,
			'long'=>$long,
			'max_fill_level'=>$max_fill_level,
			'max_weight'=>$max_weight
		);
		$r=$this->container_model->create_container($data);
		$obj["resultado"] = TRUE != FALSE;
		$obj["mensaje"] = $obj["resultado"] ?
			"creacion de contenedor correcta" : "No se pudo crear el contenedor";

		echo json_encode($obj);
	}

	public function delete_container():void
	{
		$id=$this->input->post('id');
		$r=$this->container_model->delete_container($id);
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se elimino el contenedor correctamente" :
			"No se eliminaron los datos, intente mas tarde";

		echo json_encode($obj);
	}

//	public function update_user():void
//	{
//		$id=$this->input->post('id');
//		$name=$this->input->post('name');
//		$lastname=$this->input->post('lastname');
//		$type=$this->input->post('type');
//		$passwd=$this->input->post('passwd');
//		$email=$this->input->post('email');
//		$data=array(
//			'name'=>$name,
//			'lastname'=>$lastname,
//			'type'=>$type,
//			'passwd'=>$passwd,
//			'email'=>$email
//		);
//		$r=$this->user_model->update_user($data, $id);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se actualizo correctamente el usuario" :
//			"No se actualizaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//
//	public function get_user():void
//	{
//		$id=$this->input->post('id');
//		$r=$this->user_model->get_user($id);
//		$obj["resultado"] = TRUE != FALSE;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"recuperacion de datos correcto" : "No se encontraron datos";
//		$obj["user"] = $r;
//
//		echo json_encode($obj);
//	}



}
