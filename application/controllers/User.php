<?php

class User extends CI_Controller {

	public function clear() {
		$this->db->query("DELETE FROM `buckets`");
		$this->db->query("DELETE FROM `bucket_images`");
		$this->db->query("DELETE FROM `images`");
		$this->db->query("DELETE FROM `sessions`");
	}

	public function clear_buckets() {
		$this->db->query("DELETE FROM `buckets`");
		$this->db->query("DELETE FROM `bucket_images`");
		$this->db->query("DELETE FROM `images`");
	}

	public function add_patient() {
		$userID = intval($this->input->post('user_id'));
		$uuid = $this->input->post('uuid');
		$name = $this->input->post('name');
		$phone = $this->input->post('phone');
		$address = $this->input->post('address');
		$city = $this->input->post('city');
		$province = $this->input->post('province');
		$birthday = $this->input->post('birthday');
		$this->db->insert('patients', array(
			'user_id' => $userID,
			'uuid' => $uuid,
			'name' => $name,
			'phone' => $phone,
			'address' => $address,
			'city' => $city,
			'province' => $province,
			'birthday' => $birthday
		));
		$id = intval($this->db->insert_id());
		echo json_encode($this->db->get_where('users', array('id' => $id))->row_array());
	}

	public function get_sessions() {
		$sessions = $this->db->query("SELECT * FROM `sessions` ORDER BY `name`")->result_array();
		for ($i=0; $i<sizeof($sessions); $i++) {
			$session = $sessions[$i];
			$sessions[$i]['images'] = $this->db->query("SELECT * FROM `bucket_images` WHERE `session_uuid`='" . $session['uuid'] . "' LIMIT 5")->result_array();
		}
		echo json_encode($sessions);
	}

	public function get_session() {
		$uuid = $this->input->post('uuid');
		$session = $this->db->get_where('sessions', array(
			'uuid' => $uuid
		))->row_array();
		$session['images'] = $this->db->get_where('bucket_images', array(
			'session_uuid' => $uuid
		))->result_array();
		$session['patient_name'] = $this->db->get_where('patients', array(
			'id' => intval($session['patient_id'])
		))->row_array()['name'];
		echo json_encode($session);
	}
	
	private function get_boolean_value($value) {
		if ($value) {
			return 1;
		} else {
			return 0;
		}
	}
	
	private function get_real_string($array, $indexName) {
		if (isset($array[$indexName])) {
			return $array[$indexName];
		} else {
			return "";
		}
	}
	
	private function get_real_json_array($array, $indexName) {
		if (isset($array[$indexName])) {
			return json_decode($array[$indexName], true);
		} else {
			return json_decode(array());
		}
	}
	
	private function get_real_int($array, $indexName) {
		if (isset($array[$indexName])) {
			return intval($array[$indexName]);
		} else {
			return 0;
		}
	}
	
	public function sync_buckets() {
		$buckets = json_decode($this->input->post('buckets'), true);
		for ($i=0; $i<sizeof($buckets); $i++) {
			$bucket = $buckets[$i];
			if ($this->db->query("SELECT * FROM `buckets` WHERE `uuid`='" . $bucket['uuid'] . "'")->num_rows() > 0) {
				$this->db->where("uuid", $bucket['uuid']);
				$this->db->update("buckets", array(
					"user_id" => $this->get_real_int($bucket, 'user_id'),
					"session_uuid" => $this->get_real_string($bucket, 'session_uuid'),
					"device_uuid" => $this->get_real_string($bucket, 'device_uuid')
				));
			} else {
				$this->db->insert("buckets", array(
					"user_id" => $this->get_real_int($bucket, 'user_id'),
					"session_uuid" => $this->get_real_string($bucket, 'session_uuid'),
					"device_uuid" => $this->get_real_string($bucket, 'device_uuid')
				));
			}
			$images = json_decode($bucket['images'], true);
			for ($j=0; $j<sizeof($images); $j++) {
				$image = $images[$j];
				if ($this->db->query("SELECT * FROM `bucket_images` WHERE `uuid`='" . $image['uuid'] . "'")->num_rows() > 0) {
					$this->db->where("uuid", $image['uuid']);
					$this->db->update("bucket_images", array(
						"bucket_uuid" => $this->get_real_string($image, 'bucket_uuid'),
						"session_uuid" => $this->get_real_string($image, 'session_uuid'),
						"type" => $this->get_real_int($image, 'type'),
						"name" => $this->get_real_string($image, 'name'),
						"path" => $this->get_real_string($image, 'path'),
						"points" => json_encode($this->get_real_json_array($image, 'points')),
						"note" => $this->get_real_string($image, 'note'),
						"date" => $this->get_real_string($image, 'date'),
						"local" => $this->get_boolean_value($image['local'])
					));
				} else {
					$this->db->insert("bucket_images", array(
						"bucket_uuid" => $this->get_real_string($image, 'bucket_uuid'),
						"session_uuid" => $this->get_real_string($image, 'session_uuid'),
						"type" => $this->get_real_int($image, 'type'),
						"name" => $this->get_real_string($image, 'name'),
						"path" => $this->get_real_string($image, 'path'),
						"points" => json_encode($this->get_real_json_array($image, 'points')),
						"note" => $this->get_real_string($image, 'note'),
						"date" => $this->get_real_string($image, 'date'),
						"local" => $this->get_boolean_value($image['local'])
					));
				}
			}
		}
	}
	
	public function sync_devices() {
		$devices = json_decode($this->input->post('devices'), true);
		for ($i=0; $i<sizeof($devices); $i++) {
			$device = $devices[$i];
			if ($this->db->query("SELECT * FROM `devices` WHERE `uuid`='" . $device['uuid'] . "'")->num_rows() > 0) {
				$this->db->where("uuid", $device['uuid']);
				$this->db->update("devices", array(
					"user_id" => $this->get_real_int($device, 'user_id'),
					"device" => $this->get_real_string($device, 'device'),
					"model" => $this->get_real_string($device, 'model'),
					"type" => $this->get_real_string($device, 'type')
				));
			} else {
				$this->db->insert("devices", array(
					"user_id" => $this->get_real_int($device, 'user_id'),
					"device" => $this->get_real_string($device, 'device'),
					"model" => $this->get_real_string($device, 'model'),
					"type" => $this->get_real_string($device, 'type')
				));
			}
		}
	}
	
	public function sync_patients() {
		$patients = json_decode($this->input->post('patients'), true);
		for ($i=0; $i<sizeof($patients); $i++) {
			$patient = $patients[$i];
			if ($this->db->query("SELECT * FROM `patients` WHERE `uuid`='" . $patient['uuid'] . "'")->num_rows() > 0) {
				$this->db->where("uuid", $patient['uuid']);
				$this->db->update("patients", array(
					"user_id" => $this->get_real_int($patient, 'user_id'),
					"custom_id" => $this->get_real_string($patient, 'custom_id'),
					"name" => $this->get_real_string($patient, 'name'),
					"address" => $this->get_real_string($patient, 'address'),
					"city" => $this->get_real_string($patient, 'city'),
					"province" => $this->get_real_string($patient, 'province'),
					"birthday" => $this->get_real_string($patient, 'birthday'),
					"gender" => $this->get_real_string($patient, 'gender'),
					"email" => $this->get_real_string($patient, 'email'),
					"phone" => $this->get_real_string($patient, 'phone')
				));
			} else {
				$this->db->insert("patients", array(
					"user_id" => $this->get_real_int($patient, 'user_id'),
					"custom_id" => $this->get_real_string($patient, 'custom_id'),
					"name" => $this->get_real_string($patient, 'name'),
					"address" => $this->get_real_string($patient, 'address'),
					"city" => $this->get_real_string($patient, 'city'),
					"province" => $this->get_real_string($patient, 'province'),
					"birthday" => $this->get_real_string($patient, 'birthday'),
					"gender" => $this->get_real_string($patient, 'gender'),
					"email" => $this->get_real_string($patient, 'email'),
					"phone" => $this->get_real_string($patient, 'phone')
				));
			}
		}
	}
	
	public function sync_sessions() {
		$sessions = json_decode($this->input->post('sessions'), true);
		for ($i=0; $i<sizeof($sessions); $i++) {
			$session = $sessions[$i];
			if ($this->db->query("SELECT * FROM `sessions` WHERE `uuid`='" . $session['uuid'] . "'")->num_rows() > 0) {
				$this->db->where("uuid", $session['uuid']);
				$this->db->update("sessions", array(
					"name" => $this->get_real_string($session, 'name'),
					"date" => $this->get_real_string($session, 'date'),
					"patient_id" => $this->get_real_int($session, 'patient_id'),
				));
			} else {
				$this->db->insert("sessions", array(
					"name" => $this->get_real_string($session, 'name'),
					"date" => $this->get_real_string($session, 'date'),
					"patient_id" => $this->get_real_string($session, 'patient_id'),
				));
			}
		}
	}
	
	public function delete_bucket() {
		$uuid = $this->input->post('uuid');
		$images = $this->db->query("SELECT * FROM `bucket_images` WHERE `bucket_uuid`='" . $uuid . "'")->result_array();
		for ($i=0; $i<sizeof($images); $i++) {
			$image = $images[$i];
			unlink("./userdata/" . $image['path']);
			$this->db->query("DELETE FROM `bucket_images` WHERE `id`=" . $image['id']);
		}
		$this->db->query("DELETE FROM `buckets` WHERE `uuid`='" . $uuid . "'");
	}

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
	
	public function get_buckets() {
		$userID = intval($this->input->post('user_id'));
		$sessionUUID = $this->input->post('session_uuid');
		$buckets = $this->db->query("SELECT * FROM `buckets` WHERE `user_id`=" . $userID . " AND `session_uuid`='" . $sessionUUID . "'")->result_array();
		for ($i=0; $i<sizeof($buckets); $i++) {
			$bucket = $buckets[$i];
			$images = $this->db->query("SELECT * FROM `bucket_images` WHERE `bucket_uuid`='" . $bucket['uuid'] . "'")->result_array();
			$buckets[$i]['images'] = $images;
		}
		echo json_encode($buckets);
	}
	
	public function add_bucket() {
		$uuid = $this->input->post('uuid');
		$deviceUUID = $this->input->post('device_uuid');
		$sessionUUID = $this->input->post('session_uuid');
		$userID = intval($this->input->post('user_id'));
		$this->db->insert('buckets', array(
			'user_id' => $userID,
			'uuid' => $uuid,
			'session_uuid' => $sessionUUID,
			'device_uuid' => $deviceUUID
		));
		echo intval($this->db->insert_id());
	}
	
	public function upload_skin_image() {
		$bucketUUID = $this->input->post('bucket_uuid');
		$sessionUUID = $this->input->post('session_uuid');
		$note = $this->input->post('note');
		$points = $this->input->post('points');
		$type = intval($this->input->post('type'));
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
        		'bucket_uuid' => $bucketUUID,
        		'session_uuid' => $sessionUUID,
        		'path' => $this->upload->data()['file_name'],
        		'note' => $note,
        		'points' => $points,
        		'type' => $type,
        		'date' => $date,
        		'type' => $type
        	));
        	$id = intval($this->db->insert_id());
        	echo json_encode(array(
        		'id' => $id,
        		'path' => $this->upload->data()['file_name']
        	));
        } else {
        	echo json_encode($this->upload->display_errors());
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
