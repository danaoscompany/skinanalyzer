<?php

class Test extends CI_Controller {
	
	public function email() {
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'danaos.apps@gmail.com',
		    'smtp_pass' => 'PublicVoid123',
		    'mailtype'  => 'html', 
    		'charset'   => 'iso-8859-1'
		);
		$this->load->library('email', $config);
		$this->email->set_mailtype("html");
		$this->email->from('danaos.apps@gmail.com', 'danaos.apps@gmail.com');
		$this->email->to('danaoscompany@gmail.com');
		$this->email->subject('Test email from CI and Gmail');
		$message = $this->load->view("email_template.php", "", true);
		$message = str_replace("[CODE]", "123456", $message);
		$this->email->message($message);
		$this->email->send();
	}
	
	public function test2() {
		echo file_get_contents("http://www.google.com");
	}
}
