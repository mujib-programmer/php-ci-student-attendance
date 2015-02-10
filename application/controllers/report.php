<?php
/**
 * Rekap Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Report extends CI_Controller {
	/**
	 * Constructor
	 */
	function Report()
	{
		parent::__construct();
		$this->load->model('Report_model', '', TRUE);
		$this->load->model('Semester_model', '', TRUE);
		$this->load->model('Student_model', '', TRUE);
		$this->load->model('Grade_model', '', TRUE);
		
		// Load to_excel_pi plugins
		$this->load->helper('to_excel');
	}
	
	var $title = 'report';
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menjalankan fungsi main()
	 * jika tidak akan meredirect ke halaman login
	 */
	function index()
	{
		if ($this->session->userdata('login') == TRUE)
		{
			$this->main();
		}
		else
		{
			redirect('login');
		}
	}
	
	/**
	 * Menampilkan halaman utama report absen
	 */
	function main()
	{
		$data['title'] = $this->title;
		$data['h2_title'] = 'Report';
		$data['main_view'] = 'report/report';
		$data['form_action'] = site_url('report/get_report');
				
		// data kelas untuk dropdown menu
		$query_kelas = $this->Grade_model->get_kelas();
		$kelas = $query_kelas->result();
		$num_rows = $query_kelas->num_rows();
		
		if ($num_rows > 0)
		{		
			foreach($kelas as $row)
			{
				$data['options_kelas'][$row->id_kelas] = $row->kelas;
			}
		}
		else
		{
			$data['options_kelas'][''] = '';
		}
		
		// data semester yang aktif
		$id_semester = $this->Semester_model->get_active_semester()->result();
		foreach($id_semester as $row)
		{
			$this->session->set_userdata('id_semester', $row->id_semester);
		}
		
		$this->load->view('template', $data);
	}
	
	/**
	 * Mendapatkan data report absensi dari database, kemudian menampilkan di halaman
	 */
	function get_report($id_semester = 0, $id_kelas = 0)
	{		
		$data['title'] = $this->title;
		$data['h2_title'] = 'Report';
		$data['main_view'] = 'report/report';
		$data['form_action'] = site_url('report/get_report');
		
		// data kelas untuk dropdown menu
		$kelas = $this->Grade_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		// untuk kelas dan semester yang terpilih
		$data['default']['id_kelas'] = $id_kelas;
				
		// cek input parameter fungsi get_report()
		if ( ! ($id_semester == 0) && ! ($id_kelas == 0))
		{
			// data kelas, untuk kelas terpilih
			$kls = $this->Grade_model->get_kelas_by_id($id_kelas);
			$data['active_class'] = $kls->kelas;
			
			// semester yang dipilih
			$data['semester'] = $id_semester;
		
			// load data from database
			$attendance 		= $this->Report_model->get_report($id_semester, $id_kelas)->result();
			$num_rows 	= $this->Report_model->get_report($id_semester, $id_kelas)->num_rows();
			
			// jika query > 0
			if ($num_rows > 0)
			{
				// set table template for zebra row
				$tmpl = array('table_open'=>'<table border="0" cellpadding="0" cellspacing="0">',
							  'row_alt_start'=>'<tr class="zebra">',
							  'row_alt_end'=>'</tr>'
							  );
				$this->table->set_template($tmpl);
				
				// set table header
				$this->table->set_empty("&nbsp;");
				$this->table->set_heading('No', 'NIS', 'Name', 'S', 'I', 'A', 'T');
				$i = 0;
				
				foreach ($attendance as $row)
				{
					// sakit
					switch($row->Sakit)
					{
						case NULL:
							$S = 0;
							break;
						default:
							$S = $row->Sakit;
							break;
					}
					// alpa
					switch($row->Alpa)
					{
						case NULL:
							$A = 0;
							break;
						default:
							$A = $row->Alpa;
							break;
					}
					// ijin
					switch($row->Ijin)
					{
						case NULL:
							$I = 0;
							break;
						default:
							$I = $row->Ijin;
							break;
					}
					// telat
					switch($row->Telat)
					{
						case NULL:
							$T = 0;
							break;
						default:
							$T = $row->Telat;
							break;
					}					
					$this->table->add_row(++$i, $row->nis, $row->nama, $S, $I, $A, $T);
				}
								
				$data['table'] = $this->table->generate();
				$data['link'] = array('link_add' => anchor("report/download/$id_semester/$id_kelas",'download', array('class' => 'excel'))
								);
				
				// Load view
				$this->load->view('template', $data);
			}
			// jika query < 0
			else
			{
				$this->session->set_flashdata('message', 'Data not found!');
				redirect('report');
			}
		}
		else
		{
			$id_semester = $this->session->userdata('id_semester');
			$id_kelas = $this->input->post('id_kelas');
			
			if ( ! ($id_semester == 0) && ! ($id_kelas == 0))
			{
				redirect("report/get_report/$id_semester/$id_kelas/");
			}
			else
			{
				redirect('report');
			}
		}
				
	}
	
		
	// Download excel
	function download($id_semester, $id_kelas)
	{
		$file_name = 'report';
		$query = $this->Report_model->get_report($id_semester, $id_kelas);
		to_excel($query, $file_name);
	}
}
// END Report Class

/* End of file report.php */
/* Location: ./application/controllers/report.php */