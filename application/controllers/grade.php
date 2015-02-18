<?php
/**
 * Kelas Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Grade extends CI_Controller {
	/**
	 * Constructor
	 */
	function Grade()
	{
		parent::__construct();
		$this->load->model('Grade_model', '', TRUE);
	}
	
	/**
	 * Inisialisasi variabel untuk $title(untuk id element <body>)
	 */
	var $title = 'grade';
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menampilkan halaman grade,
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
	 * Tampilkan semua data grade
	 */
	function get_all()
	{
		$data['title'] = $this->title;
		$data['h2_title'] = 'Grade';
		$data['main_view'] = 'grade/grade';
		
		// Load data
		$query = $this->Grade_model->get_all();
		$grade = $query->result();
		$num_rows = $query->num_rows();
		
		if ($num_rows > 0)
		{
			// Table
			/*Set table template for alternating row 'zebra'*/
			$tmpl = array( 'table_open'    => '<table border="0" cellpadding="0" cellspacing="0">',
						  'row_alt_start'  => '<tr class="zebra">',
							'row_alt_end'    => '</tr>'
						  );
			$this->table->set_template($tmpl);

			/*Set table heading */
			$this->table->set_empty("&nbsp;");
			$this->table->set_heading('No', 'Grade Code', 'Grade', 'Actions');
			$i = 0;
			
			foreach ($grade as $row)
			{
				$this->table->add_row(++$i, $row->id, $row->grade,
										anchor('grade/update/'.$row->id,'update',array('class' => 'update')).' '.
										anchor('grade/delete/'.$row->id,'delete',array('class'=> 'delete','onclick'=>"return confirm('Are you sure you want to delete this data?')"))
										);
			}
			$data['table'] = $this->table->generate();
		}
		else
		{
			$data['message'] = 'No grade data founded!';
		}		
		
		$data['link'] = array('link_add' => anchor('grade/add/','add data', array('class' => 'add'))
								);
		
		// Load view
		$this->load->view('template', $data);
	}
		
	/**
	 * Hapus data grade
	 */
	function delete($id_grade)
	{
		$this->Grade_model->delete($id_grade);
		$this->session->set_flashdata('message', '1 grade data successfully deleted');
		
		redirect('grade');
	}
	
	/**
	 * Pindah ke halaman tambah grade
	 */
	function add()
	{		
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Grade > Add Data';
		$data['main_view'] 		= 'grade/grade_form';
		$data['form_action']	= site_url('grade/add_process');
		$data['link'] 			= array('link_back' => anchor('grade','back', array('class' => 'back'))
										);
		
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses tambah data grade
	 */
	function add_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Grade > Add Data';
		$data['main_view'] 		= 'grade/grade_form';
		$data['form_action']	= site_url('grade/add_process');
		$data['link'] 			= array('link_back' => anchor('grade','back', array('class' => 'back'))
										);
	
		// Set validation rules
		$this->form_validation->set_rules('id_grade', 'Grade Code', 'required|numeric|max_length[2]|callback_valid_id');
		$this->form_validation->set_rules('grade', 'Grade', 'required|max_length[32]');
		
		// Jika validasi sukses
		if ($this->form_validation->run() == TRUE)
		{
			// Persiapan data
			$grade = array('id_grade'	=> $this->input->post('id_grade'),
							'grade'		=> $this->input->post('grade')
						);
			// Proses penyimpanan data di table grade
			$this->Grade_model->add($grade);
			
			$this->session->set_flashdata('message', '1 grade data successfully added!');
			redirect('grade/add');
		}
		// Jika validasi gagal
		else
		{		
			$this->load->view('template', $data);
		}		
	}
	
	/**
	 * Pindah ke halaman update grade
	 */
	function update($id_grade)
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Grade > Update';
		$data['main_view'] 		= 'grade/grade_form';
		$data['form_action']	= site_url('grade/update_process');
		$data['link'] 			= array('link_back' => anchor('student','back', array('class' => 'back'))
										);
	
		// cari data dari database
		$grade = $this->Grade_model->get_grade_by_id($id_grade);
				
		// buat session untuk menyimpan data primary key (id_grade)
		$this->session->set_userdata('id_grade', $grade->id);
		
		// Data untuk mengisi field2 form
		$data['default']['id_grade'] 	= $grade->id;		
		$data['default']['grade']		= $grade->grade;
				
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses update data grade
	 */
	function update_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Grade > Update';
		$data['main_view'] 		= 'grade/grade_form';
		$data['form_action']	= site_url('grade/update_process');
		$data['link'] 			= array('link_back' => anchor('grade','back', array('class' => 'back'))
										);
										
		// Set validation rules
		$this->form_validation->set_rules('id_grade', 'Grade Code', 'required|numeric|max_length[2]|callback_valid_id2');
		$this->form_validation->set_rules('grade', 'Grade', 'required|max_length[32]');
		
		if ($this->form_validation->run() == TRUE)
		{
			// save data
			$grade = array('id_grade'	=> $this->input->post('id_grade'),
							'grade'		=> $this->input->post('grade')
						);
			$this->Grade_model->update($this->session->userdata('id_grade'), $grade);
			
			$this->session->set_flashdata('message', '1 grade data successfully updated!');
			redirect('grade');
		}
		else
		{		
			$this->load->view('template', $data);
		}
	}
	
	/**
	 * Cek apakah $id_grade valid, agar tidak ganda
	 */
	function valid_id($id_grade)
	{
		if ($this->Grade_model->valid_id($id_grade) == TRUE)
		{
			$this->form_validation->set_message('valid_id', "grade with code $id_grade already in registered");
			return FALSE;
		}
		else
		{			
			return TRUE;
		}
	}
	
	/**
	 * Cek apakah $id_grade valid, agar tidak ganda. Hanya untuk proses update data grade
	 */
	function valid_id2()
	{
		// cek apakah data tanggal pada session sama dengan isi field
		// tidak mungkin seorang student diabsen 2 kali pada tanggal yang sama
		$current_id 	= $this->session->userdata('id_grade');
		$new_id			= $this->input->post('id_grade');
				
		if ($new_id === $current_id)
		{
			return TRUE;
		}
		else
		{
			if($this->Grade_model->valid_id($new_id) === TRUE) // cek database untuk entry yang sama memakai valid_entry()
			{
				$this->form_validation->set_message('valid_id2', "grade with code $id_grade already in registered");
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
}
// END Grade Class

/* End of file grade.php */
/* Location: ./application/controllers/grade.php */