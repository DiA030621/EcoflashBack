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
	public function singIn(): void
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

	public function logIn(): void
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
		$obj["user"] = $r;

		echo json_encode($obj);
	}

//
//	public function get_tramo()
//		/*controlador que obtiene el registro
//		de una ruta y sus paradas con coordenadas*/
//	{
//		$clave_ruta=$this->input->post('clave_ruta');
//		$r=$this->rutas_model->get_tramo($clave_ruta);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." tramos recuperados" : "No se encontraron tramos";
//		$obj["tramos"] = $r;
//
//		echo json_encode($obj);
//	}
//
//
//
//	public function insert_tramo()
//		//inserta las paradas que hace una ruta
//	{
//		$clave_ruta=$this->input->post('clave_ruta');
//		$clave_parada=$this->input->post('clave_parada');
//		$data=array(
//			'clave_ruta'=>$clave_ruta,
//			'clave_parada'=>$clave_parada
//		);
//		$r=$this->rutas_model->insert_tramo($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se insertaron datos correctamente" :
//			"No se insertaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function update_ruta()
//		//actiualiza el nombre de una routa
//		//ejemplo: ruta 7 cambia a ruta c21
//	{
//		$nombre=$this->input->post('nombre');
//		$clave=$this->input->post('clave');
//		$data=array(
//			'clave'=>$clave,
//			'nombre'=>$nombre
//		);
//		$r=$this->rutas_model->update_ruta($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se actualizaron los datos correctamente" :
//			"No se actualizaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function delete_ruta()
//		//elimina una ruta por su clave
//	{
//		$clave=$this->input->post('clave');
//		$r=$this->rutas_model->delete_ruta($clave);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se eliminaron datos correctamente" :
//			"No se actualizaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function delete_tramo()
//		//elimina parada de una ruta
//		//otras palabras, elimina un tramo de una ruta
//	{
//		$clave=$this->input->post('clave');
//		$r=$this->rutas_model->delete_tramo($clave);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se eliminaron datos correctamente" :
//			"No se eliminaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function get_vehiculos()
//		//obtiene el registro de todos los vehiculos
//	{
//		$r=$this->rutas_model->get_vehiculos();
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." vehiculos recuperados" : "No se encontraron vehiculos";
//		$obj["vehiculos"] = $r;
//
//		echo json_encode($obj);
//	}
//
//	public function get_vehiculos_chofer()
//		//obtiene el registro de todos los vehiculos que no tienen chofer
//	{
//		$r=$this->rutas_model->get_vehiculos_chofer();
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." vehiculos recuperados" : "No se encontraron vehiculos";
//		$obj["vehiculos"] = $r;
//
//		echo json_encode($obj);
//	}
//
//	public function get_vehiculo()
//		//obtiene el registro de un vehiculo segun su clave
//		//para realizar cambios
//	{
//		$clave=$this->input->post('clave');
//		$r=$this->rutas_model->get_vehiculo($clave);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." vehiculos recuperados" : "No se encontraron vehiculos";
//		$obj["vehiculos"] = $r;
//
//		echo json_encode($obj);
//	}
//
//
//	public function insert_vehiculo()
//		//ingresa los datos de un nuevo vehiculo
//		/*
//		!!!IMPORTANTE!!!  ESTADO SOLO PUEDE SER:
//		"activo" o "mantenimiento"
//		*/
//	{
//		$marca=$this->input->post('marca');
//		$modelo=$this->input->post('modelo');
//		$placa=$this->input->post('placa');
//		$estado=$this->input->post('estado');//activo o mantenimiento
//		$data=array(
//			'marca'=>$marca,
//			'modelo'=>$modelo,
//			'placa'=>$placa,
//			'estado'=>$estado
//		);
//		$r=$this->rutas_model->insert_vehiculo($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se insertaron datos correctamente" :
//			"No se insertaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function update_vehiculo()
//		//actualiza los dato de un nuevo vehiculo segun su clave
//		//la clave no se debe actualizar
//		/*
//		!!!IMPORTANTE!!!  ESTADO SOLO PUEDE SER:
//		-------"activo" o "mantenimiento"------
//		*/
//	{
//		$clave=$this->input->post('clave');
//		$marca=$this->input->post('marca');
//		$modelo=$this->input->post('modelo');
//		$placa=$this->input->post('placa');
//		$estado=$this->input->post('estado');//activo o mantenimiento
//		$data=array(
//			'clave'=>$clave,
//			'marca'=>$marca,
//			'modelo'=>$modelo,
//			'placa'=>$placa,
//			'estado'=>$estado
//		);
//		$r=$this->rutas_model->update_vehiculo($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se actualizaron datos correctamente" :
//			"No se actualizaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function delete_vehiculo()
//		//elimina un registro de cehiculo segun su clave
//	{
//		$clave=$this->input->post('clave');
//		$r=$this->rutas_model->delete_vehiculo($clave);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se eliminaron datos correctamente" :
//			"No se eliminaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function get_paradas()
//		//obtiene todos los registros de paradas(con coordenadas)
//	{
//		$r=$this->rutas_model->get_paradas();
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." paradas recuperados" : "No se encontraron paradas";
//		$obj["paradas"] = $r;
//
//		echo json_encode($obj);
//	}
//
//
//	public function insert_parada()
//		//ingresa un nuevo registro a una parada
//	{
//		$nombre=$this->input->post('nombre');
//		$latitud=$this->input->post('latitud');
//		$longitud=$this->input->post('longitud');
//		$data=array(
//			'nombre'=>$nombre,
//			'latitud'=>$latitud,
//			'longitud'=>$longitud
//		);
//		$r=$this->rutas_model->insert_parada($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se insertaron los datos correctamente" :
//			"No se insertaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function update_parada()
//		//esto solo actualiza el nombre de la parada
//		//no se deben de actualizar coordenadas
//	{
//		$clave=$this->input->post('clave');
//		$nombre=$this->input->post('nombre');
//		$data=array(
//			'nombre'=>$nombre
//		);
//		$r=$this->rutas_model->update_parada($clave,$data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se actualizarno datos correctamente" :
//			"No se actualizaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function delete_parada()
//		//elimina el registro de una parada
//	{
//		$clave=$this->input->post('clave');
//		$r=$this->rutas_model->delete_parada($clave);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se eliminaron datos correctamente" :
//			"No se eliminaron los datos";
//
//		echo json_encode($obj);
//	}
//
//
//	public function get_parada_tramo()
//		/*controlador que obtiene el registro
//		de una ruta y sus paradas con coordenadas*/
//	{
//		$clave=$this->input->post('clave_ruta');
//		$r=$this->rutas_model->get_parada_tramo($clave);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." paradas recuperadas" : "No se encontraron paradas";
//		$obj["paradas"] = $r;
//
//		echo json_encode($obj);
//	}
//
//	public function get_ruta_vehiculo()
//		//controlador que obtiene la ruta del vehiculo en caso de que tenga
//	{
//		$clave_vehiculo=$this->input->post('clave_vehiculo');
//		$r=$this->rutas_model->get_ruta_vehiculo($clave_vehiculo);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." ruta" : "No se encontro ruta del vehiculo ";
//		$obj["ruta_vehiculo"] = $r;
//
//		echo json_encode($obj);
//
//	}
//
//	public function insert_ruta_vehiculo()
//		//controlador que inserta una ruta en un vehiculo
//	{
//		$clave_vehiculo=$this->input->post('clave_vehiculo');
//		$clave_ruta=$this->input->post('clave_ruta');
//		$data=array(
//			'clave_vehiculo'=>$clave_vehiculo,
//			'clave_ruta'=>$clave_ruta
//		);
//		$r=$this->rutas_model->insert_ruta_vehiculo($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se inserto correctamente los datos" : "No se insertaron los datos";
//
//		echo json_encode($obj);
//	}
//
//	public function update_ruta_vehiculo()
//		//controlador que inserta una ruta en un vehiculo
//	{
//		$clave_vehiculo=$this->input->post('clave_vehiculo');
//		$clave_ruta=$this->input->post('clave_ruta');
//		$data=array(
//			'clave_vehiculo'=>$clave_vehiculo,
//			'clave_ruta'=>$clave_ruta
//		);
//		$r=$this->rutas_model->update_ruta_vehiculo($data);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se actualizaron correctamente los datos" : "No se actualizaron los datos";
//
//		echo json_encode($obj);
//	}
//
//	public function delete_ruta_vehiculo()
//		//controlador que elimina una ruta de un vehiculo usando clave de vehiculo
//	{
//		$clave_vehiculo=$this->input->post('clave_vehiculo');
//		$r=$this->rutas_model->delete_ruta_vehiculo($clave_vehiculo);
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			"Se eliminaron correctamente los datos" : "No se eliminaron los datos";
//
//		echo json_encode($obj);
//	}
//
//	public function grafica_pago()
//	{
//		$r=$this->rutas_model->grafica_pago();
//		$obj["resultado"] = $r != NULL;
//		$obj["mensaje"] = $obj["resultado"] ?
//			count($r)." rutas recuperados" : "No se encontraron rutas";
//		$obj["rutas"] = $r;
//
//		echo json_encode($obj);
//	}


}
