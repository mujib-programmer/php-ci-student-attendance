<?php
/**
 * Semester_model Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Semester_model extends CI_Model {
	/**
	 * Constructor
	 */
	function Semester_model()
	{
		parent::__construct();
	}
	
	// Inisialisasi nama tabel semester
	var $table = TABLE_SEMESTER;
	
	/**
	 * Mendapatkan semester yang aktif
	 */
	function get_active_semester()
	{
		$this->db->select('id_semester');
		$this->db->where('status', 1);
		return $this->db->get($this->table);
	}
	
	/**
	 * Mendapatkan semua data semester
	 */
	function get_semester()
	{
		$this->db->order_by('id_semester');
		return $this->db->get($this->table);
	}
	
	/**
	 * Mengaktifkan sebuah semester dan menonaktifkan lainnya, menggunakan transaksi
	 */
	function aktif($id_semester)
	{
		$sql1 = "UPDATE ".TABLE_SEMESTER."
				SET ".TABLE_SEMESTER.".status = '1'
				WHERE ".TABLE_SEMESTER.".id_semester = '$id_semester';
				";
		$sql2 = "UPDATE ".TABLE_SEMESTER."
				SET ".TABLE_SEMESTER.".status = '0'
				WHERE ".TABLE_SEMESTER.".id_semester != '$id_semester';
				";
		$this->db->trans_start();
		$this->db->query($sql1);
		$this->db->query($sql2);
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === TRUE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Menonaktifkan sebuah semester dan mengaktifkan lainnya, menggunakan transaksi
	 */
	 
	 function nonaktif($id_semester)
	{
		$sql1 = "UPDATE ".TABLE_SEMESTER."
				SET ".TABLE_SEMESTER.".status = '0'
				WHERE ".TABLE_SEMESTER.".id_semester = '$id_semester';
				";
		$sql2 = "UPDATE ".TABLE_SEMESTER."
				SET ".TABLE_SEMESTER.".status = '1'
				WHERE ".TABLE_SEMESTER.".id_semester != '$id_semester';
				";
		$this->db->trans_start();
		$this->db->query($sql1);
		$this->db->query($sql2);
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === TRUE)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
}
// END Semester_model Class

/* End of file attendance_model.php */
/* Location: ./system/application/models/semester_model.php */