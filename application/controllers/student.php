<?php
/**
 * Siswa Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Student extends CI_Controller {
	/**
	 * Constructor, load Semester_model, Grade_model
	 */
	function Student()
	{
		parent::__construct();
		$this->load->model('Student_model', '', TRUE);
		$this->load->model('Grade_model', '', TRUE);
	}
	
	/**
	 * Inisialisasi variabel untuk $title(untuk id element <body>), dan 
	 * $limit untuk membatasi penampilan data di tabel
	 */
	var $limit = 10;
	var $title = 'student';
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menjalankan fungsi get_all()
	 * jika tidak akan meredirect ke halaman login
	 */
	function index()
	{
		if ($this->session->userdata('login') == TRUE)
		{
			$this->get_all();
		}
		else
		{
			redirect('login');
		}
	}
	
	/**
	 * Mendapatkan semua data student di database dan menampilkannya di tabel
	 */
	function get_all($offset = 0)
	{
		$data['title'] = $this->title;
		$data['h2_title'] = 'Student';
		$data['main_view'] = 'student/student';
		
		// Offset
		$uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// Load data
		$student = $this->Student_model->get_all($this->limit, $offset);
		$num_rows = $this->Student_model->count_all();
		
		if ($num_rows > 0)
		{
			// Generate pagination			
			$config['base_url'] = site_url('student/get_all');
			$config['total_rows'] = $num_rows;
			$config['per_page'] = $this->limit;
			$config['uri_segment'] = $uri_segment;
			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
			
			// Table
			/*Set table template for alternating row 'zebra'*/
			$tmpl = array( 'table_open'    => '<table border="0" cellpadding="0" cellspacing="0">',
						  'row_alt_start'  => '<tr class="zebra">',
							'row_alt_end'    => '</tr>'
						  );
			$this->table->set_template($tmpl);

			/*Set table heading */
			$this->table->set_empty("&nbsp;");
			$this->table->set_heading('No', 'NIS', 'Name', 'Grade', 'Actions');
			$i = 0 + $offset;
			
			foreach ($student as $row)
			{
				$this->table->add_row(++$i, $row->nis, $row->name, $row->grade,
										anchor('student/update/'.$row->nis,'update',array('class' => 'update')).' '.
										anchor('student/delete/'.$row->nis,'delete',array('class'=> 'delete','onclick'=>"return confirm('Are you sure you want to delete this data?')"))
										);
			}
			$data['table'] = $this->table->generate();
		}
		else
		{
			$data['message'] = 'Attendance data is not found!';
		}		
		
		$data['link'] = array('link_add' => anchor('student/add/','add data', array('class' => 'add'))
								);
		
		// Load view
		$this->load->view('template', $data);
	}
	
	/**
	 * Menghapus data student dengan NIS tertentu
	 */
	function delete($nis)
	{
		$this->Student_model->delete($nis);
		$this->session->set_flashdata('message', '1 attendance data successfully deleted');
		
		redirect('student');
	}
	
	/**
	 * Menampilkan form tambah student
	 */
	function add()
	{		
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Student > Add Data';
		$data['main_view'] 		= 'student/student_form';
		$data['form_action']	= site_url('student/add_process');
		$data['link'] 			= array('link_back' => anchor('student','back', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Grade_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		$this->load->view('template', $data);
	}
	/**
	 * Proses tambah data student
	 */
	function add_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Student > Add Data';
		$data['main_view'] 		= 'student/student_form';
		$data['form_action']	= site_url('student/add_process');
		$data['link'] 			= array('link_back' => anchor('absen/','back', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Grade_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		// Set validation rules
		$this->form_validation->set_rules('nis', 'NIS', 'required|exact_length[4]|numeric|callback_valid_nis');
		$this->form_validation->set_rules('nama', 'Name', 'required|max_length[50]');
		$this->form_validation->set_rules('id_kelas', 'Grade', 'required');
		
		if ($this->form_validation->run() == TRUE)
		{
			// save data
			$student = array('nis' 		=> $this->input->post('nis'),
							'nama'		=> $this->input->post('nama'),
							'id_kelas'	=> $this->input->post('id_kelas')
						);
			$this->Student_model->add($student);
			
			$this->session->set_flashdata('message', '1 student data succesfully added!');
			redirect('student/add');
		}
		else
		{	
			$data['default']['id_kelas'] = $this->input->post('id_kelas');
			$this->load->view('template', $data);
		}		
	}
	
	/**
	 * Menampilkan form update data student
	 */
	function update($nis)
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Student > Update';
		$data['main_view'] 		= 'student/student_form';
		$data['form_action']	= site_url('student/update_process');
		$data['link'] 			= array('link_back' => anchor('student','back', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Grade_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		// cari data dari database
		$student = $this->Student_model->get_student_by_id($nis);
		
		// buat session untuk menyimpan data primary key (nis)
		$this->session->set_userdata('nis', $student->nis);
		
		// Data untuk mengisi field2 form
		$data['default']['nis'] 		= $student->nis;
		$data['default']['nama'] 		= $student->nama;		
		$data['default']['id_kelas']	= $student->id_kelas;
				
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses update data student
	 */
	function update_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Student > Update';
		$data['main_view'] 		= 'student/student_form';
		$data['form_action']	= site_url('student/update_process');
		$data['link'] 			= array('link_back' => anchor('student','back', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Grade_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
			
		// Set validation rules
		$this->form_validation->set_rules('nis', 'NIS', 'required|exact_length[4]|numeric|callback_valid_nis2');
		$this->form_validation->set_rules('nama', 'Name', 'required|max_length[50]');
		$this->form_validation->set_rules('id_kelas', 'Grade', 'required');
		
		// jika proses validasi sukses, maka lanjut mengupdate data
		if ($this->form_validation->run() == TRUE)
		{
			// save data
			$absen = array('nis' 		=> $this->input->post('nis'),
							'nama'		=> $this->input->post('nama'),
							'id_kelas'	=> $this->input->post('id_kelas')
						);
			$this->Student_model->update($this->session->userdata('nis'), $absen);
			// $this->Attendance_model->update($id_absen, $absen);
			
			// set pesan
			$this->session->set_flashdata('message', '1 student data successfully updated!');
			
			redirect('student');
		}
		else
		{
			$data['default']['id_kelas'] = $this->input->post('id_kelas');
			$this->load->view('template', $data);
		}
	}
	
	/**
	 * Validasi untuk nis, agar tidak ada student dengan NIS sama
	 */
	function valid_nis($nis)
	{
		if ($this->Student_model->valid_nis($nis) == TRUE)
		{
			$this->form_validation->set_message('valid_nis', "student with NIS $nis already enrolled");
			return FALSE;
		}
		else
		{			
			return TRUE;
		}
	}
	
	// cek apakah valid untuk update?
	function valid_nis2()
	{
		// cek agar tidak ada nis ganda, khusus untuk proses update
		$current_nis 	= $this->session->userdata('nis');
		$new_nis		= $this->input->post('nis');
				
		if ($new_nis === $current_nis)
		{
			return TRUE;
		}
		else
		{
			if($this->Student_model->valid_nis($new_nis) === TRUE) // cek database untuk entry yang sama memakai valid_entry()
			{
				$this->form_validation->set_message('valid_nis2', "Student with nis $new_nis already enrolled");
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}

}
// END Student Class

/* End of file student.php */
/* Location: ./application/controllers/student.php */