<?php
class Container extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("container_model");
		$this->load->model("user_model");
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Headers: Content-Type');
	}

	public function container_levels(): void
	{
		$id_container=$this->input->get('id_container');
		$grams=$this->input->get('grams');
		$centimeters=$this->input->get('centimeters');
		$lat=$this->input->get('lat');
		$long=$this->input->get('long');
		if ($grams == null || $centimeters == null || $id_container == null || $lat == null || $long == null) {
			$obj["mensaje"] = "No se estan recibiendo los datos de manera correcta, revisa el formato de envio de datos";
			echo json_encode($obj);
			return;
		}
		$data_location = array(
			'lat' => $lat,
			'long' => $long
		);
		$r0=$this->container_model->update_location($id_container, $data_location);
		if($r0 == false){
			$obj["mensaje"] = "Error al actualizar coordenadas";
			echo json_encode($obj);
			return;
		}

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

	public function contribution():void
	{
		$id_container=$this->input->post('id_container');
		$email=$this->input->post('email');
		$passwd=$this->input->post('passwd');

		$r_levels_diff=$this->container_model->get_levels_diff($id_container);
		if ($r_levels_diff == false) {
			$obj["resultado"] = false;
			$obj["mensaje"]="Error, no se enconraron registros anteriores";
			echo json_encode($obj);
			return;
		}
		$grams_diff = (double) $r_levels_diff['grams'];

		$r_levels=$this->container_model->get_levels($id_container);
		if ($r_levels == false) {
			$obj["resultado"] = false;
			$obj["mensaje"]="Error, no se enconraron registros nuevos";
			echo json_encode($obj);
			return;
		}
		$grams_new = (double) $r_levels['grams'];
		$grams = $grams_new - $grams_diff;
		$score = $grams/20;

		$data_levels=array(
			'status'=> true
		);

		$r_status=$this->container_model->update_levels($id_container, $data_levels);
		if (!$r_status) {
			$obj["resultado"] = false;
			$obj["mensaje"]="Error al actualizar el estatus";
			echo json_encode($obj);
			return;
		}

		$data_user=array(
			'email'=>$email,
			'passwd'=>$passwd
		);

		$r_user=$this->user_model->validate_user($data_user);
		if (!$r_user) {
			$obj["resultado"] = false;
			$obj["mensaje"]="Error encontrando score de usuario";
			echo json_encode($obj);
			return;
		}
		$id_user = $r_user[0]->id;
		$score_init = (double) $r_user[0]->score;
		$score_final = $score_init + $score;

		$data_score = array(
			'score'=>$score_final
		);
		$r_user=$this->user_model->update_score($id_user, $data_score);
		if ($r_user) {
			$obj["resultado"] = true;
			$obj["mensaje"]="Se a actualizado el score de usuario correctamente";
			echo json_encode($obj);
		} else {
			$obj["resultado"] = false;
			$obj["mensaje"]="Error al actualizar el score de usuario";
			echo json_encode($obj);
		}
	}
	public function reset_container():void
	{
		$id_container=$this->input->post('id_container');
		$data_container=array(
			'status'=> true
		);
		$data_fill=array(
			'id_container'=> $id_container,
			'centimeters'=> 0,
			'status'=> true,
		);
		$data_weight=array(
			'id_container'=> $id_container,
			'grams'=> 0,
			'status'=> true,
		);
		$r=$this->container_model->reset_container($id_container, $data_container, $data_fill, $data_weight);
		$obj["resultado"] = $r != false;
		$obj["mensaje"] = $obj["resultado"] ?
			"Se reseteo el contenedor correctamente" :
			"No se resetearon los datos, intente mas tarde";

		echo json_encode($obj);
	}

	public function get_containers():void
	{
		$r=$this->container_model->get_containers();
		$obj["resultado"] = $r != NULL;
		$obj["mensaje"] = $obj["resultado"] ?
			"recuperacion de datos correcto" : "No se encontraron datos";
		$obj["container"] = $r;

		echo json_encode($obj);
	}

	public function get_container():void
	{
		$id_container=$this->input->post('id_container');
		$r=$this->container_model->get_container($id_container);
		$obj["resultado"] = $r != FALSE;
		$obj["mensaje"] = $obj["resultado"] ?
			"recuperacion de datos correcto" : "No se encontraron datos";
		$obj["container"] = $r;

		echo json_encode($obj);
	}

}
