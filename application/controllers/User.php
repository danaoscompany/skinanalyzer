<?php

class User extends CI_Controller {

	public function sync_devices_with_uuid() {
		$devices = json_decode($this->input->post('devices'), true);
		for ($i=0; $i<sizeof($devices); $i++) {
			$device = $devices[$i];
			$this->db->where('uuid', $device['uuid']);
			$results = $this->db->get('devices');
			if (sizeof($results) <= 0) {
				$this->db->insert('devices', array(
					'user_id' => $device['user_id'],
					'uuid' => $device['uuid'],
					'device' => $device['device'],
					'model' => $device['model'],
					'type' => $device['type']
				));
			} else {
				$this->db->where('uuid', $device['uuid']);
				$this->db->update('devices', array(
					'user_id' => $device['user_id'],
					'device' => $device['device'],
					'model' => $device['model'],
					'type' => $device['type']
				));
			}
		}
	}

	public function sync_devices() {
		$devices = json_decode($this->input->post('devices'), true);
		for ($i=0; $i<sizeof($devices); $i++) {
			$device = $devices[$i];
			$this->db->where('device', $device['device'])->where('model', $device['model'])->where('type', $device['type']);
			$results = $this->db->get('devices');
			if (sizeof($results) <= 0) {
				$this->db->insert('devices', array(
					'device' => $device['device'],
					'model' => $device['model'],
					'type' => $device['type']
				));
			}
		}
	}
	
	public function get_buckets() {
		$userID = intval($this->input->post('user_id'));
		$buckets = $this->db->query("SELECT * FROM `buckets` WHERE `user_id`=" . $userID)->result_array();
		for ($i=0; $i<sizeof($buckets); $i++) {
			$bucket = $buckets[$i];
			$images = $this->db->query("SELECT * FROM `bucket_images` WHERE `bucket_id`=" . $bucket['id'])->result_array();
			$buckets[$i]['images'] = $images;
		}
		echo json_encode($buckets);
	}
	
	public function add_bucket() {
		$deviceUUID = $this->input->post('device_uuid');
		$userID = intval($this->input->post('user_id'));
		$this->db->insert('buckets', array(
			'user_id' => $userID,
			'device_uuid' => $deviceUUID
		));
		echo intval($this->db->insert_id());
	}
	
	public function upload_skin_image() {
		$bucketID = intval($this->input->post('bucket_id'));
		$note = $this->input->post('note');
		$date = $this->input->post('date');
		$type = intval($this->input->post('type'));
		$config = array(
	        'upload_path' => './userdata/',
	        'allowed_types' => "*",
	        'overwrite' => TRUE,
	        'max_size' => "2048000", 
	        'max_height' => "8192",
	        'max_width' => "8192"
        );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('file')) {
        	$this->db->insert('bucket_images', array(
        		'bucket_id' => $bucketID,
        		'path' => $this->upload->data()['file_name'],
        		'note' => $note,
        		'date' => $date,
        		'type' => $type
        	));
        	$id = intval($this->db->insert_id());
        	echo json_encode(array(
        		'id' => $id,
        		'path' => $this->upload->data()['file_name']
        	));
        }
	}

	public function send_password_reset_email() {
		$email = $this->input->post('email');
		$code = $this->input->post('code');
		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'skinmed.herca@gmail.com',
		    'smtp_pass' => 'rawatkulit123',
		    'mailtype'  => 'html', 
    		'charset'   => 'iso-8859-1'
		);
		$this->load->library('email', $config);
		$this->email->set_mailtype("html");
		$this->email->from('skinmed.herca@gmail.com', 'Skin Analyzer');
		$this->email->to($email);
		$this->email->subject('Atur Ulang Kata Sandi');
		$message = $this->load->view("email_template.php", "", true);
		$message = str_replace("[CODE]", $code, $message);
		$this->email->message($message);
		$this->email->send();
	}

	public function login() {
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$this->db->where('email', $email, 'password', $password);
		$users = $this->db->get('users')->result_array();
		if (sizeof($users) > 0) {
			$user = $users[0];
			$user['response_code'] = 1;
			echo json_encode($user);
		} else {
			echo json_encode(array('response_code' => -1));
		}
	}

	public function signup() {
		$firstName = $this->input->post('first_name');
		$lastName = $this->input->post('last_name');
		$address = $this->input->post('address');
		$phone = $this->input->post('phone');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$this->db->where('phone', $phone);
		$users = $this->db->get('users')->result_array();
		if (sizeof($users) > 0) {
			echo json_encode(array('response_code' => -1));
			return;
		}
		$users = $this->db->query("SELECT * FROM `users` WHERE `email`='" . $email . "'")->result_array();
		if (sizeof($users) > 0) {
			echo json_encode(array('response_code' => -2));
			return;
		}
		$this->db->insert('users', array(
			'first_name' => $firstName,
			'last_name' => $lastName,
			'address' => $address,
			'phone' => $phone,
			'email' => $email,
			'password' => $password
		));
		echo json_encode(array('response_code' => 1, 'user_id' => intval($this->db->insert_id())));
	}

	public function upload_image() {
		$userID = intval($this->input->post('user_id'));
		$deviceID = intval($this->input->post('device_id'));
		$config = array(
	        'upload_path' => './userdata/',
	        'allowed_types' => "*",
	        'overwrite' => TRUE,
	        'max_size' => "10485760", 
	        'max_height' => "8192",
	        'max_width' => "8192"
        );
        $this->load->library('upload', $config);
        if($this->upload->do_upload('file')) {
        	$path = $this->upload->data()['file_name'];
        	$this->db->insert('images', array(
        		'user_id' => $userID,
        		'device_id' => $deviceID,
        		'path' => $path,
        		'storage_method' => 'my_account'
        	));
        	echo json_encode(array('id' => intval($this->db->insert_id()), 'path' => $path));
        } else {
        	echo json_encode(array('error' => $this->upload->display_errors()));
        }
	}
	
	public function add_image() {
		$userID = intval($this->input->post('user_id'));
		$deviceID = intval($this->input->post('device_id'));
		$path = $this->input->post('path');
		$storageMethod = $this->input->post('storage_method');
		$this->db->insert('images', array(
        	'user_id' => $userID,
        	'device_id' => $deviceID,
        	'path' => $path,
        	'storage_method' => $storageMethod
        ));
        echo json_encode(array('id' => intval($this->db->insert_id()), 'path' => $path));
	}
}
