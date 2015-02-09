<?php
/**
 * Kelas_model Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Kelas_model extends CI_Model {
	/**
	 * Constructor
	 */
	function Kelas_model()
	{
		parent::__construct();
	}
	
	// Inisialisasi nama tabel yang digunakan
	var $table = TABLE_GRADE;
	
	/**
	 * Mendapatkan semua data kelas, diurutkan berdasarkan id_kelas
	 */
	function get_kelas()
	{
		$this->db->order_by('id_kelas');
		return $this->db->get(TABLE_GRADE);
	}
	
	/**
	 * Mendapatkan data sebuah kelas
	 */
	function get_kelas_by_id($id_kelas)
	{
		return $this->db->get_where($this->table, array('id_kelas' => $id_kelas), 1)->row();
	}
	
	function get_all()
	{
		$this->db->order_by('id_kelas');
		return $this->db->get($this->table);
	}
	
	/**
	 * Menghapus sebuah data kelas
	 */
	function delete($id_kelas)
	{
		$this->db->delete($this->table, array('id_kelas' => $id_kelas));
	}
	
	/**
	 * Tambah data kelas
	 */
	function add($kelas)
	{
		$this->db->insert($this->table, $kelas);
	}
	
	/**
	 * Update data kelas
	 */
	function update($id_kelas, $kelas)
	{
		$this->db->where('id_kelas', $id_kelas);
		$this->db->update($this->table, $kelas);
	}
	
	/**
	 * Validasi agar tidak ada kelasd dengan id ganda
	 */
	function valid_id($id_kelas)
	{
		$query = $this->db->get_where($this->table, array('id_kelas' => $id_kelas));
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
// END Siswa_model Class

/* End of file kelas_model.php */
/* Location: ./system/application/models/kelas_model.php */