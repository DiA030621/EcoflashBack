<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']    = 'smtp';
$config['smtp_host']   = 'smtp.gmail.com';  // Reemplázalo con tu servidor SMTP
$config['smtp_user']   = 'diego.0d4y@gmail.com'; // Tu correo
$config['smtp_pass']   = '2025Em1l14n0';         // Tu contraseña
$config['smtp_port']   = 587;                     // Puede ser 465 para SSL o 587 para TLS
$config['smtp_crypto'] = 'tls';                   // Usa 'ssl' si es necesario
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['wordwrap']    = TRUE;
