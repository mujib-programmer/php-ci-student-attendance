<?php
/**
 * Login_model Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Login_model extends CI_Model {
	/**
	 * Constructor
	 */
	function Login_model()
	{
		parent::__construct();
	}
	
	// Inisialisasi nama tabel user
	var $table = TABLE_USER;
	
	/**
	 * Cek tabel user, apakah ada user dengan username dan password tertentu
	 */
	function check_user($username, $password)
	{
		$query = $this->db->get_where($this->table, array('username' => $username, 'password' => $password), 1, 0);
		
		if ($query->num_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}
// END Login_model Class

/* End of file login_model.php */ 
/* Location: ./application/model/login_model.php */