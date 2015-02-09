<ul id="menu_tab">	
	<li id="tab_absen"><?php echo anchor('attendance', 'Attendance');?></li>
	<li id="tab_rekap"><?php echo anchor('report', 'Report');?></li>
	<li id="tab_siswa"><?php echo anchor('student', 'Student');?></li>
	<li id="tab_semester"><?php echo anchor('semester', 'Semester');?></li>
	<li id="tab_kelas"><?php echo anchor('grade', 'Grade');?></li>
	<li id="tab_logout"><?php echo anchor('login/process_logout', 'Logout', array('onclick' => "return confirm('Anda yakin akan logout?')"));?></li>
</ul>