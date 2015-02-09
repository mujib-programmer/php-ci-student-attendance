<?php
/**
 * Semester Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Semester extends CI_Controller {
	/**
	 * Constructor, load Semester_model
	 */
	function Semester()
	{
		parent::__construct();
		$this->load->model('Semester_model', '', TRUE);
	}
	
	/**
	 * Inisialisasi variabel untuk $title(untuk id element <body>)
	 */
	var $title = 'semester';
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menjalankan fungsi get_semester()
	 * jika tidak akan meredirect ke halaman login
	 */
	function index()
	{
		if ($this->session->userdata('login') == TRUE)
		{
			$this->get_semester();
		}
		else
		{
			redirect('login');
		}
	}
		
	/**
	 * Mengambil data dari database, menampilkan data dalam tabel
	 */
	function get_semester($offset = 0) {
		$data['title'] = $this->title;
		$data['h2_title'] = 'Semester';
		$data['main_view'] = 'semester/semester';		
		
		// Load data
		$query = $this->Semester_model->get_semester();
		$semester = $query->result();
		$num_rows = $query->num_rows();
		
		if ($num_rows > 0)
		{
			// Table
			/*Set table template for alternating row 'zebra'*/
			$tmpl = array( 'table_open'    => '<table border="0" cellpadding="0" cellspacing="0">',
						  'row_alt_start'  => '<tr class="zebra">',
							'row_alt_end'  => '</tr>'
						  );
			$this->table->set_template($tmpl);
			
			/*Set table heading */
			$this->table->set_empty("&nbsp;");
			$this->table->set_heading('Semester', 'Status', 'Actions');
			
			foreach ($semester as $row)
			{				
				// status
				$sts = $row->status;
				if($sts == 1)
				{
					$status = 'Aktif';
					// link actions
					$link_status = anchor('semester/nonaktif/' . $row->id_semester,'non aktifkan',array('class'=>'aktif','onclick'=>"return confirm('Anda yakin akan ubah status?')"));
				}
				else
				{
					$status = 'Tidak aktif';
					// link actions
					$link_status = anchor('semester/aktif/' . $row->id_semester,'aktifkan',array('class'=>'nonaktif','onclick'=>"return confirm('Anda yakin akan ubah status?')"));
				}
				
				$this->table->add_row($row->id_semester, $status, $link_status);
			}
			$data['table'] = $this->table->generate();			
		}
		else
		{
			$data['message'] = 'Data tidak ditemukan!';
		}
		// Load view
		$this->load->view('template', $data);
	}
	
	/**
	 * Mengaktifkan sebuah semester
	 */	
	function aktif($id_semester)
	{
		$aktif = $this->Semester_model->aktif($id_semester);
		if($aktif == TRUE)
		{
			$this->session->set_flashdata('message', 'Proses berhasil...');
			redirect('semester');
		}
		else
		{	
			$this->session->set_flashdata('message', 'Proses gagal...');
			redirect('semester');
		}
	}
	
	/**
	 * menonaktifkan sebuah semester
	 */	
	function nonaktif($id_semester)
	{
		$nonaktif = $this->Semester_model->nonaktif($id_semester);
		if($nonaktif == TRUE)
		{
			$this->session->set_flashdata('message', 'Proses berhasil...');
			redirect('semester');
		}
		else
		{	
			$this->session->set_flashdata('message', 'Proses gagal...');
			redirect('semester');
		}
	}	
}
// END Semester Class

/* End of file semester.php */
/* Location: ./system/application/controllers/semester.php */