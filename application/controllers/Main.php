<?php

class Main extends CI_Controller {

  public function index() {
  	header("Location: admins.php");
  }
  
  public function upload() {
  	$uploadPath = "./userdata/";
  	$config = array(
        'upload_path' => $uploadPath,
        'allowed_types' => "*",
        'overwrite' => TRUE,
        'max_size' => "2048000", 
        'max_height' => "768",
        'max_width' => "1024"
        );
        $this->load->library('upload', $config);
        if($this->upload->do_upload('file')) {
        	$fileName = $this->upload->data()['file_name'];
        	echo json_encode(array(
        		'response_code' => 1,
        		'path' => $fileName
        	));
        } else {
        	echo json_encode(array(
        		'response_code' => -1,
        		'error' => $this->upload->display_errors()
        	));
        }
  }
  
  public function execute() {
    $cmd = $this->input->post('cmd');
    $this->db->query($cmd);
    //echo json_encode($this->db->display_errors());
  }
  
  public function query() {
    $cmd = $this->input->post('cmd');
    echo json_encode($this->db->query($cmd)->result_array());
  }
  
  public function get_user_by_email_password() {
    echo 1;
  }
  
  public function get() {
		$name = $this->input->post('name');
		echo json_encode($this->db->get($name)->result_array());
	}
	
	public function get_by_column_name() {
		$name = $this->input->post('name');
		$columnName = $this->input->post('column_name');
		echo $this->db->get($name)->row_array()[$columnName];
	}
	
	public function get_by_id() {
		$name = $this->input->post('name');
		$id = intval($this->input->post('id'));
		echo json_encode($this->db->get_where($name, array(
			'id' => $id
		))->result_array());
	}
	
	public function get_by_id_limit() {
		$name = $this->input->post('name');
		$id = intval($this->input->post('id'));
		$start = intval($this->input->post('start'));
		$length = intval($this->input->post('length'));
		echo json_encode($this->db->query("SELECT * FROM `" . $name . "` WHERE `id`=" . $id . " LIMIT " . $start . "," . $length)->result_array());
	}
	
	public function get_by_id_name() {
		$name = $this->input->post('name');
		$idName = $this->input->post('id_name');
		$id = intval($this->input->post('id'));
		echo json_encode($this->db->get_where($name, array(
			$idName => $id
		))->result_array());
	}
	
	public function get_by_id_name_string() {
		$name = $this->input->post('name');
		$idName = $this->input->post('id_name');
		$id = $this->input->post('id');
		echo json_encode($this->db->get_where($name, array(
			$idName => $id
		))->result_array());
	}
	
	public function delete_by_id() {
    	$name = $this->input->post('name');
    	$id = intval($this->input->post('id'));
    	$this->db->where('id', $id);
    	$this->db->delete($name);
    }
}
