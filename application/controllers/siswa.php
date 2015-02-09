<?php
/**
 * Siswa Class
 *
 * @author	Awan Pribadi Basuki <awan_pribadi@yahoo.com>
 */
class Siswa extends CI_Controller {
	/**
	 * Constructor, load Semester_model, Kelas_model
	 */
	function Siswa()
	{
		parent::__construct();
		$this->load->model('Siswa_model', '', TRUE);
		$this->load->model('Kelas_model', '', TRUE);
	}
	
	/**
	 * Inisialisasi variabel untuk $title(untuk id element <body>), dan 
	 * $limit untuk membatasi penampilan data di tabel
	 */
	var $limit = 10;
	var $title = 'siswa';
	
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
	 * Mendapatkan semua data siswa di database dan menampilkannya di tabel
	 */
	function get_all($offset = 0)
	{
		$data['title'] = $this->title;
		$data['h2_title'] = 'Siswa';
		$data['main_view'] = 'siswa/siswa';
		
		// Offset
		$uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// Load data
		$siswa = $this->Siswa_model->get_all($this->limit, $offset);
		$num_rows = $this->Siswa_model->count_all();
		
		if ($num_rows > 0)
		{
			// Generate pagination			
			$config['base_url'] = site_url('siswa/get_all');
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
			$this->table->set_heading('No', 'NIS', 'Nama', 'Kelas', 'Actions');
			$i = 0 + $offset;
			
			foreach ($siswa as $row)
			{
				$this->table->add_row(++$i, $row->nis, $row->nama, $row->kelas,
										anchor('siswa/update/'.$row->nis,'update',array('class' => 'update')).' '.
										anchor('siswa/delete/'.$row->nis,'hapus',array('class'=> 'delete','onclick'=>"return confirm('Anda yakin akan menghapus data ini?')"))
										);
			}
			$data['table'] = $this->table->generate();
		}
		else
		{
			$data['message'] = 'Tidak ditemukan satupun data absensi!';
		}		
		
		$data['link'] = array('link_add' => anchor('siswa/add/','tambah data', array('class' => 'add'))
								);
		
		// Load view
		$this->load->view('template', $data);
	}
	
	/**
	 * Menghapus data siswa dengan NIS tertentu
	 */
	function delete($nis)
	{
		$this->Siswa_model->delete($nis);
		$this->session->set_flashdata('message', '1 data absen berhasil dihapus');
		
		redirect('siswa');
	}
	
	/**
	 * Menampilkan form tambah siswa
	 */
	function add()
	{		
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Siswa > Tambah Data';
		$data['main_view'] 		= 'siswa/siswa_form';
		$data['form_action']	= site_url('siswa/add_process');
		$data['link'] 			= array('link_back' => anchor('siswa','kembali', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Kelas_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		$this->load->view('template', $data);
	}
	/**
	 * Proses tambah data siswa
	 */
	function add_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Siswa > Tambah Data';
		$data['main_view'] 		= 'siswa/siswa_form';
		$data['form_action']	= site_url('siswa/add_process');
		$data['link'] 			= array('link_back' => anchor('absen/','kembali', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Kelas_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		// Set validation rules
		$this->form_validation->set_rules('nis', 'NIS', 'required|exact_length[4]|numeric|callback_valid_nis');
		$this->form_validation->set_rules('nama', 'Nama', 'required|max_length[50]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'required');
		
		if ($this->form_validation->run() == TRUE)
		{
			// save data
			$siswa = array('nis' 		=> $this->input->post('nis'),
							'nama'		=> $this->input->post('nama'),
							'id_kelas'	=> $this->input->post('id_kelas')
						);
			$this->Siswa_model->add($siswa);
			
			$this->session->set_flashdata('message', 'Satu data siswa berhasil disimpan!');
			redirect('siswa/add');
		}
		else
		{	
			$data['default']['id_kelas'] = $this->input->post('id_kelas');
			$this->load->view('template', $data);
		}		
	}
	
	/**
	 * Menampilkan form update data siswa
	 */
	function update($nis)
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Siswa > Update';
		$data['main_view'] 		= 'siswa/siswa_form';
		$data['form_action']	= site_url('siswa/update_process');
		$data['link'] 			= array('link_back' => anchor('siswa','kembali', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Kelas_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
		
		// cari data dari database
		$siswa = $this->Siswa_model->get_siswa_by_id($nis);
		
		// buat session untuk menyimpan data primary key (nis)
		$this->session->set_userdata('nis', $siswa->nis);
		
		// Data untuk mengisi field2 form
		$data['default']['nis'] 		= $siswa->nis;
		$data['default']['nama'] 		= $siswa->nama;		
		$data['default']['id_kelas']	= $siswa->id_kelas;
				
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses update data siswa
	 */
	function update_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Siswa > Update';
		$data['main_view'] 		= 'siswa/siswa_form';
		$data['form_action']	= site_url('siswa/update_process');
		$data['link'] 			= array('link_back' => anchor('siswa','kembali', array('class' => 'back'))
										);
										
		// data kelas untuk dropdown menu
		$kelas = $this->Kelas_model->get_kelas()->result();
		foreach($kelas as $row)
		{
			$data['options_kelas'][$row->id_kelas] = $row->kelas;
		}
			
		// Set validation rules
		$this->form_validation->set_rules('nis', 'NIS', 'required|exact_length[4]|numeric|callback_valid_nis2');
		$this->form_validation->set_rules('nama', 'Nama', 'required|max_length[50]');
		$this->form_validation->set_rules('id_kelas', 'Kelas', 'required');
		
		// jika proses validasi sukses, maka lanjut mengupdate data
		if ($this->form_validation->run() == TRUE)
		{
			// save data
			$absen = array('nis' 		=> $this->input->post('nis'),
							'nama'		=> $this->input->post('nama'),
							'id_kelas'	=> $this->input->post('id_kelas')
						);
			$this->Siswa_model->update($this->session->userdata('nis'), $absen);
			// $this->Absen_model->update($id_absen, $absen);
			
			// set pesan
			$this->session->set_flashdata('message', 'Satu data siswa berhasil diupdate!');
			
			redirect('siswa');
		}
		else
		{
			$data['default']['id_kelas'] = $this->input->post('id_kelas');
			$this->load->view('template', $data);
		}
	}
	
	/**
	 * Validasi untuk nis, agar tidak ada siswa dengan NIS sama
	 */
	function valid_nis($nis)
	{
		if ($this->Siswa_model->valid_nis($nis) == TRUE)
		{
			$this->form_validation->set_message('valid_nis', "siswa dengan NIS $nis sudah terdaftar");
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
			if($this->Siswa_model->valid_nis($new_nis) === TRUE) // cek database untuk entry yang sama memakai valid_entry()
			{
				$this->form_validation->set_message('valid_nis2', "Siswa dengan nis $new_nis sudah terdaftar");
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}

}
// END Siswa Class

/* End of file siswa.php */
/* Location: ./system/application/controllers/siswa.php */