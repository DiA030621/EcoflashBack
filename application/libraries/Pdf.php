<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Incluir TCPDF manualmente si es necesario
require_once FCPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php';

class Pdf extends TCPDF {
	public function __construct() {
		parent::__construct();
	}
}
