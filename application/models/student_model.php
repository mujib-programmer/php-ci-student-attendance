<?php
class Student_model extends CI_Model {
	/**
	 * Constructor
	 */
	function Student_model()
	{
		parent::__construct();
	}
	
	// Inisialisasi nama tabel siswa
	var $table = TABLE_STUDENT;
	
	/**
	 * Mendapatkan data semua siswa
	 */
	function get_all($limit, $offset)
	{
		$this->db->select(TABLE_STUDENT.'.nis, '.TABLE_STUDENT.'.name, '.TABLE_GRADE.'.grade');
		$this->db->from($this->table);
		$this->db->join(TABLE_GRADE, TABLE_GRADE.'.id = '.TABLE_STUDENT.'.id_grade');
		$this->db->limit($limit, $offset);
		$this->db->order_by('nis', 'asc');
		return $this->db->get()->result();
	}
	
	/**
	 * Mendapatkan data seorang siswa dengan NIS tertentu
	 */
	function get_siswa_by_id($nis)
	{
		return $this->db->get_where($this->table, array('nis' => $nis))->row();
	}
	
	/**
	 * Menghitung jumlah baris tabel siswa
	 */
	function count_all()
	{
		return $this->db->count_all($this->table);
	}
	
	/**
	 * Menghapus data siswa tertentu
	 */
	function delete($nis)
	{
		$this->db->delete($this->table, array('nis' => $nis));
	}
	
	/**
	 * Menambah data siswa
	 */
	function add($siswa)
	{
		$this->db->insert($this->table, $siswa);
	}
	
	/**
	 * Update data siswa
	 */
	function update($nis, $siswa)
	{
		$this->db->where('nis', $nis);
		$this->db->update($this->table, $siswa);
	}
	
	/**
	 * Cek NIS agar tidak ada data siswa yang sama
	 */
	function valid_nis($nis)
	{
		$query = $this->db->get_where($this->table, array('nis' => $nis));
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
// END Student_model Class

/* End of file student_model.php */
/* Location: ./system/application/models/student_model.php */