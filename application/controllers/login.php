<?php
/**
 * Login Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Login extends CI_Controller {
	/**
	 * Constructor
	 */
	function login()
	{
		parent::__construct();
		$this->load->model('Login_model', '', TRUE);
	}
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menampilkan halaman absen,
	 * jika tidak akan meload halaman login
	 */
	function index()
	{
		if ($this->session->userdata('login') == TRUE)
		{
			redirect('attendance');
		}
		else
		{
			$this->load->view('login/login_view');
		}
	}
	
	/**
	 * Memproses login
	 */
	function process_login()
	{
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		
		if ($this->form_validation->run() == TRUE)
		{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			
			if ($this->Login_model->check_user($username, $password) == TRUE)
			{
				$data = array('username' => $username, 'login' => TRUE);
				$this->session->set_userdata($data);
				redirect('absen');
			}
			else
			{
				$this->session->set_flashdata('message', 'Sorry, Your username and or password are wrong');
				redirect('login/index');
			}
		}
		else
		{
			$this->load->view('login/login_view');
		}
	}
	
	/**
	 * Memproses logout
	 */
	function process_logout()
	{
		$this->session->sess_destroy();
		redirect('login', 'refresh');
	}
}
// END Login Class

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */