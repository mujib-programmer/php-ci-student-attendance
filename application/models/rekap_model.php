<?php
/**
 * Rekap_model Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Rekap_model extends CI_Model {
	/**
	 * Constructor
	 */
	function Rekap_model()
	{
		parent::__construct();
	}
	
	// Inisialisasi nama tabel absen
	var $table = TABLE_ATTENDANCE;
	
	/**
	 * Proses rekap data absensi dengan kriteria semester dan kelas tertentu
	 */
	function get_rekap($id_semester, $id_kelas)
	{
		$sql = "SELECT ".TABLE_STUDENT.".nis, ".TABLE_STUDENT.".nama,

				/* ----------- jumlah sakit ------------*/
				(SELECT COUNT(".TABLE_ATTENDANCE.".absen)
				FROM ".TABLE_ATTENDANCE."
				WHERE ".TABLE_ATTENDANCE.".absen = 'S'
				AND ".TABLE_ATTENDANCE.".id_semester = '$id_semester'
				AND ".TABLE_ATTENDANCE.".nis = ".TABLE_STUDENT.".nis
				AND ".TABLE_ATTENDANCE.".nis IN (SELECT ".TABLE_STUDENT.".nis
								  FROM ".TABLE_STUDENT."
								  WHERE ".TABLE_STUDENT.".id_kelas = '$id_kelas'
								  ORDER BY ".TABLE_STUDENT.".nis ASC)
				GROUP BY ".TABLE_ATTENDANCE.".nis
				ORDER BY ".TABLE_ATTENDANCE.".nis ASC) AS Sakit,

				/* ----------- jumlah ijin ------------*/
				(SELECT COUNT(".TABLE_ATTENDANCE.".absen)
				FROM ".TABLE_ATTENDANCE."
				WHERE ".TABLE_ATTENDANCE.".absen = 'I'
				AND ".TABLE_ATTENDANCE.".id_semester = '$id_semester'
				AND ".TABLE_ATTENDANCE.".nis = ".TABLE_STUDENT.".nis
				AND ".TABLE_ATTENDANCE.".nis IN (SELECT ".TABLE_STUDENT.".nis
								  FROM ".TABLE_STUDENT."
								  WHERE ".TABLE_STUDENT.".id_kelas = '$id_kelas'
								  ORDER BY ".TABLE_STUDENT.".nis ASC)
				GROUP BY ".TABLE_ATTENDANCE.".nis
				ORDER BY ".TABLE_ATTENDANCE.".nis ASC) AS Ijin,

				/* ----------- jumlah alpa ------------*/
				(SELECT COUNT(".TABLE_ATTENDANCE.".absen)
				FROM ".TABLE_ATTENDANCE."
				WHERE ".TABLE_ATTENDANCE.".absen = 'A'
				AND ".TABLE_ATTENDANCE.".id_semester = '$id_semester'
				AND ".TABLE_ATTENDANCE.".nis = ".TABLE_STUDENT.".nis
				AND ".TABLE_ATTENDANCE.".nis IN (SELECT ".TABLE_STUDENT.".nis
								  FROM ".TABLE_STUDENT."
								  WHERE ".TABLE_STUDENT.".id_kelas = '$id_kelas'
								  ORDER BY ".TABLE_STUDENT.".nis ASC)
				GROUP BY ".TABLE_ATTENDANCE.".nis
				ORDER BY ".TABLE_ATTENDANCE.".nis ASC) AS Alpa,

				/* ----------- jumlah telat ------------*/
				(SELECT COUNT(".TABLE_ATTENDANCE.".absen)
				FROM ".TABLE_ATTENDANCE."
				WHERE ".TABLE_ATTENDANCE.".absen = 'T'
				AND ".TABLE_ATTENDANCE.".id_semester = '$id_semester'
				AND ".TABLE_ATTENDANCE.".nis = ".TABLE_STUDENT.".nis
				AND ".TABLE_ATTENDANCE.".nis IN (SELECT ".TABLE_STUDENT.".nis
								  FROM ".TABLE_STUDENT."
								  WHERE ".TABLE_STUDENT.".id_kelas = '$id_kelas'
								  ORDER BY ".TABLE_STUDENT.".nis ASC)
				GROUP BY ".TABLE_ATTENDANCE.".nis
				ORDER BY ".TABLE_ATTENDANCE.".nis ASC) AS Telat

			FROM ".TABLE_STUDENT."
			WHERE ".TABLE_STUDENT.".id_kelas = '$id_kelas'
			GROUP BY ".TABLE_STUDENT.".nis
			ORDER BY ".TABLE_STUDENT.".nis ASC;";
			
		return $this->db->query($sql);
	}
}
// END Absen_model Class

/* End of file absen_model.php */
/* Location: ./system/application/models/absen_model.php */