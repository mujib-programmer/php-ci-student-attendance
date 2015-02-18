<?php
/**
 * Attendance_model Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Attendance_model extends CI_Model {
	/**
	 * Constructor
	 */
	function Attendance_model()
	{
		parent::__construct();
	}
	
	// Inisialisasi nama tabel yang digunakan
	var $table = TABLE_ATTENDANCE;
	
	/**
	 * Menghitung jumlah baris dalam sebuah tabel, ada kaitannya dengan pagination
	 */
	function count_all_num_rows()
	{
		return $this->db->count_all($this->table);
	}
	
	/**
	 * Tampilkan 10 baris absen terkini, diurutkan berdasarkan tanggal (Descending)
	 */
	function get_last_ten_absen($limit, $offset)
	{
		$this->db->select(TABLE_ATTENDANCE .'.id, '. TABLE_ATTENDANCE .'.date, '. TABLE_ATTENDANCE.'.nis, '. TABLE_STUDENT .'.name, '. TABLE_GRADE .'.grade, '. TABLE_ATTENDANCE .'.attendance');
		$this->db->from(TABLE_ATTENDANCE. ', '. TABLE_STUDENT .', '. TABLE_GRADE .', ' . TABLE_SEMESTER);
		$this->db->where(TABLE_STUDENT.'.id_grade = '. TABLE_GRADE .'.id');
		$this->db->where(TABLE_ATTENDANCE .'.nis = '. TABLE_STUDENT .'.nis');
		$this->db->where(TABLE_SEMESTER .'.id_semester = '. TABLE_ATTENDANCE .'.id_semester');
		$this->db->order_by(TABLE_ATTENDANCE .'.date', 'desc');
		$this->db->limit($limit, $offset);
		return $this->db->get();
	}
	
	/**
	 * Menghapus sebuah entry data absen
	 */
	function delete($id_absen)
	{
		$this->db->where('id', $id_absen);
		$this->db->delete($this->table);
	}
	
	/**
	 * Menambahkan sebuah data ke tabel absen
	 */
	function add($absen)
	{
		$this->db->insert($this->table, $absen);
	}
	
	/**
	 * Dapatkan data absen dengan id_absen tertentu, untuk proses update
	 */
	function get_absen_by_id($id_absen)
	{
		$this->db->select('id, nis, id_semester, date, attendance');
		$this->db->where('id', $id_absen);
		return $this->db->get($this->table);
	}
	
	/**
	 * Update data absensi
	 */
	function update($id_absen, $absen)
	{
		$this->db->where('id', $id_absen);
		$this->db->update($this->table, $absen);
	}
	
	/**
	 * Cek apakah ada entry data yang sama pada tanggal tertentu untuk siswa dengan NIS tertentu pula
	 */
	function valid_entry($nis, $tanggal)
	{
		$this->db->where('nis', $nis);
		$this->db->where('date', $tanggal);
		$query = $this->db->get($this->table)->num_rows();
						
		if($query > 0)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}	
}
// END Attendance_model Class

/* End of file attendance_model.php */
/* Location: ./application/models/attendance_model.php */