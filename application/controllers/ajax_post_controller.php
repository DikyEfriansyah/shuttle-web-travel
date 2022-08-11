<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax_Post_Controller extends CI_Controller {

// Show view Page
public function index(){
$this->load->view("account/register");
}

// This function call from AJAX
public function user_data_submit() {
$data = array(
'full_name' => $this->input->post('full_name'),
'email'=>$this->input->post('email'),
'password'=>$this->input->post('password'),
''
);

//Either you can print value or you can send value to database
echo json_encode($data);
}
}