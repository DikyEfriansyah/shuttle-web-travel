<?php
class Auth extends CI_Controller{
    public function register(){
        $data = $_POST;
        $this->form_validation->set_rules('full_name','Nama Lengkap','required|trim');
        $this->form_validation->set_rules('email','Alamat Email','required|valid_email|is_unique[costumer.email]');
        $this->form_validation->set_rules('password','Kata Sandi','required|min_length[6]');
        $this->form_validation->set_rules('confirm_password','Konfirmasi Kata Sandi','required|matches[password]');
        $this->form_validation->set_message('required','{field} belum diisi');
        $this->form_validation->set_message('is_unique','{field} sudah digunakan');
        $this->form_validation->set_message('matches','{field} tidak sama');
        $this->form_validation->set_message('valid_email','Harap masukkan email yang benar');
        $this->form_validation->set_message('min_length','{field} harus lebih dari {param} huruf');
        if($this->form_validation->run() == false){
            $errors = validation_errors();
            $res = json_encode(array('result' => 0, 'content' => $errors));
        }else{
            $data = $_POST;
            $token = base64_encode(random_bytes(32));
            
            $in = $this->m_general->iData('user',array('username'=>$_POST['email'],'password'=>md5($_POST['password']),'level_id'=>2));
            $in_cos = $this->m_general->iData('costumer',array('email'=>$_POST['email'],'full_name'=>$_POST['full_name'],'id_user'=>$in,'reg_date'=>date('Y-m-d')));
            $id_costumer = $this->m_general->gIDCostumer($_POST['email']);

            $user_token = [
                'email' => $_POST['email'],
                'token' => $token,
                'date_created' => time()
            ];
            
            $this->db->insert('user_token', $user_token);

            $this->_sendEmail($token, 'verify');

            $res = json_encode(array('result' => 1, 'content' => 'Registrasi Berhasil, Silahkan Cek Email Anda'));
        }
        $this->output->set_content_type('application/json')->set_output($res);
    }

    public function login(){
        $data = $_POST;
        $login = $this->m_general->auth_login($_POST['email'],md5($_POST['password']));
        $user = $this->db->get_where('user', ['username' => $_POST['email']])->row_array();
        if($user){
            if($user['active'] == 1){
               if($login){
               $id_costumer = $this->m_general->gIDCostumer($_POST['email']);
               $this->session->set_userdata('auth_user',$id_costumer);
               $res = json_encode(array('result' => 1, 'content' => 'Login berhasil, mengalihkan ...'));
               }else{
                $res = json_encode(array('result' => 0, 'content' => 'Email/kata sandi salah'));
               } 
            }else{
                $res = json_encode(array('result' => 0, 'content' => 'Akun belum aktif, silahkan aktivasi email terlebih dahulu!'));
            } 
        }else{
            $res = json_encode(array('result' => 0, 'content' => 'Akun tidak terdaftar'));
        }
        $this->output->set_content_type('application/json')->set_output($res);
    }
    
    public function logout(){
        $this->session->unset_userdata('auth_user');
        redirect('account/login');
    }


    private function _sendEmail($token, $type){
        $data= $_POST;
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.gmail.com',
            'smtp_user' => 'shuttlecompany75@gmail.com', //ganti email pengirim
            'smtp_pass' => ' ', //password email pengirim
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ]; 

        $this->load->library('email', $config);
        $this->email->initialize($config);
        $this->email->set_mailtype('html');
        $this->email->from('shuttlecompany75@gmail.com', 'Shuttle Company');
        $this->email->to($_POST['email']);

        if($type == 'verify'){
            $this->email->subject('Account Verification');
            $this->email->message('Click this link to verify you account : <a href="'. base_url() . 'auth/verify?email='. $_POST['email']. '&token='. urlencode($token). '"> Activate</a>');
        }
        

        if($this->email->send()){
            return true;
        }else{
            echo $this->email->print_debbuger();
            die;
        }
    }

    public function verify(){
        $email = $this->input->get('email');
        $token = $this->input->get('token');
        $user = $this->db->get_where('user', ['username' => $email])->row_array();

        if($user){
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            if($user_token){
                if(time() - $user_token['date_created'] < (60*60*24)){
                    $this->db->set('active', 1);
                    $this->db->where('username', $email);
                    $this->db->update('user');

                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="materialert success">
		            <div class="material-icons">check</div>'. $email .' Telah berhasil di verifikasi. Silahkan Kehalaman login untuk login</div>');
                    redirect('account/verify');
                }else{
                    $this->db->delete('user', ['username' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);
                    $this->db->delete('costumer', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="materialert error">
		            <div class="material-icons">error_outline</div>
                    Akun Verification Gagal! Token Kedaluwarsa. Silahkan Daftar Kembali</div>');
                    redirect('account/verify');
                }
            }else{
                $this->session->set_flashdata('message', '<div class="materialert error">
		        <div class="material-icons">error_outline</div>
                Akun Verification Gagal! Token Tidak Ditemukan. Silahkan Daftar Kembali</div>');
                redirect('account/verify');
            }
        }else{
            $this->session->set_flashdata('message', '<div class="materialert error">
		    <div class="material-icons">error_outline</div>
            Akun Verification Gagal! Email Tidak Ditemukan. Silahkan Daftar Kembali</div>');
            redirect('account/verify');
            
        }
    }
}