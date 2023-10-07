<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// non active for develop
$config['active'] = false;
$config['useragent'] = 'CodeIgniter';
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'ssl://smtp.gmail.com';
$config['smtp_port'] = 465;
$config['smtp_timeout'] = 60;

$ci = get_instance();
$ci->load->config('secret');
$config['smtp_user'] = $ci->config->item('smtp_user');
$config['smtp_pass'] = $ci->config->item('smtp_pass');

// simple code
// $config['smtp_user'] = '{your_mail@mail.com}';
// $config['smtp_pass'] = '{your pass mail app}';

$config['charset'] = 'iso-8859-1';
$config['mailtype'] = 'html';
$config['newline'] = "\r\n";
$config['wordwrap'] = TRUE;
$config['validate'] = TRUE;
$config['wrapchars'] = 76;

// $config['priority'] = 3;
// $config['crlf'] = "\r\n";
// $config['bcc_batch_mode'] = FALSE;
// $config['bcc_batch_size'] = 200;

?>
