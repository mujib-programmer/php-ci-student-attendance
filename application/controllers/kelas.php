<?php
/**
 * Kelas Class
 *
 * @author	Awan Pribadi Basuki <awan_pribadi@yahoo.com>
 */
class Kelas extends CI_Controller {
	/**
	 * Constructor
	 */
	function Kelas()
	{
		parent::__construct();
		$this->load->model('Kelas_model', '', TRUE);
	}
	
	/**
	 * Inisialisasi variabel untuk $title(untuk id element <body>)
	 */
	var $title = 'kelas';
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menampilkan halaman kelas,
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
	 * Tampilkan semua data kelas
	 */
	function get_all()
	{
		$data['title'] = $this->title;
		$data['h2_title'] = 'Kelas';
		$data['main_view'] = 'kelas/kelas';
		
		// Load data
		$query = $this->Kelas_model->get_all();
		$kelas = $query->result();
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
			$this->table->set_heading('No', 'Kode Kelas', 'Kelas', 'Actions');
			$i = 0;
			
			foreach ($kelas as $row)
			{
				$this->table->add_row(++$i, $row->id_kelas, $row->kelas,
										anchor('kelas/update/'.$row->id_kelas,'update',array('class' => 'update')).' '.
										anchor('kelas/delete/'.$row->id_kelas,'hapus',array('class'=> 'delete','onclick'=>"return confirm('Anda yakin akan menghapus data ini?')"))
										);
			}
			$data['table'] = $this->table->generate();
		}
		else
		{
			$data['message'] = 'Tidak ditemukan satupun data kelas!';
		}		
		
		$data['link'] = array('link_add' => anchor('kelas/add/','tambah data', array('class' => 'add'))
								);
		
		// Load view
		$this->load->view('template', $data);
	}
		
	/**
	 * Hapus data kelas
	 */
	function delete($id_kelas)
	{
		$this->Kelas_model->delete($id_kelas);
		$this->session->set_flashdata('message', '1 data kelas berhasil dihapus');
		
		redirect('kelas');
	}
	
	/**
	 * Pindah ke halaman tambah kelas
	 */
	function add()
	{		
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Kelas > Tambah Data';
		$data['main_view'] 		= 'kelas/kelas_form';
		$data['form_action']	= site_url('kelas/add_process');
		$data['link'] 			= array('link_back' => anchor('kelas','kembali', array('class' => 'back'))
										);
		
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses tambah data kelas
	 */
	function add_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Kelas > Tambah Data';
		$data['main_view'] 		= 'kelas/kelas_form';
		$data['form_action']	= site_url('kelas/add_process');
		$data['link'] 			= array('link_back' => anchor('kelas','kembali', array('class' => 'back'))
										);
	
		// Set validation rules
		$this->form_validation->set_rules('id_kelas', 'Kode Kelas', 'required|numeric|max_length[2]|callback_valid_id');
		$this->form_validation->set_rules('kelas', 'Kelas', 'required|max_length[32]');
		
		// Jika validasi sukses
		if ($this->form_validation->run() == TRUE)
		{
			// Persiapan data
			$kelas = array('id_kelas'	=> $this->input->post('id_kelas'),
							'kelas'		=> $this->input->post('kelas')
						);
			// Proses penyimpanan data di table kelas
			$this->Kelas_model->add($kelas);
			
			$this->session->set_flashdata('message', 'Satu data kelas berhasil disimpan!');
			redirect('kelas/add');
		}
		// Jika validasi gagal
		else
		{		
			$this->load->view('template', $data);
		}		
	}
	
	/**
	 * Pindah ke halaman update kelas
	 */
	function update($id_kelas)
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Kelas > Update';
		$data['main_view'] 		= 'kelas/kelas_form';
		$data['form_action']	= site_url('kelas/update_process');
		$data['link'] 			= array('link_back' => anchor('siswa','kembali', array('class' => 'back'))
										);
	
		// cari data dari database
		$kelas = $this->Kelas_model->get_kelas_by_id($id_kelas);
				
		// buat session untuk menyimpan data primary key (id_kelas)
		$this->session->set_userdata('id_kelas', $kelas->id_kelas);
		
		// Data untuk mengisi field2 form
		$data['default']['id_kelas'] 	= $kelas->id_kelas;		
		$data['default']['kelas']		= $kelas->kelas;
				
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses update data kelas
	 */
	function update_process()
	{
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Kelas > Update';
		$data['main_view'] 		= 'kelas/kelas_form';
		$data['form_action']	= site_url('kelas/update_process');
		$data['link'] 			= array('link_back' => anchor('kelas','kembali', array('class' => 'back'))
										);
										
		// Set validation rules
		$this->form_validation->set_rules('id_kelas', 'Kode Kelas', 'required|numeric|max_length[2]|callback_valid_id2');
		$this->form_validation->set_rules('kelas', 'Kelas', 'required|max_length[32]');
		
		if ($this->form_validation->run() == TRUE)
		{
			// save data
			$kelas = array('id_kelas'	=> $this->input->post('id_kelas'),
							'kelas'		=> $this->input->post('kelas')
						);
			$this->Kelas_model->update($this->session->userdata('id_kelas'), $kelas);
			
			$this->session->set_flashdata('message', 'Satu data kelas berhasil diupdate!');
			redirect('kelas');
		}
		else
		{		
			$this->load->view('template', $data);
		}
	}
	
	/**
	 * Cek apakah $id_kelas valid, agar tidak ganda
	 */
	function valid_id($id_kelas)
	{
		if ($this->Kelas_model->valid_id($id_kelas) == TRUE)
		{
			$this->form_validation->set_message('valid_id', "kelas dengan Kode $id_kelas sudah terdaftar");
			return FALSE;
		}
		else
		{			
			return TRUE;
		}
	}
	
	/**
	 * Cek apakah $id_kelas valid, agar tidak ganda. Hanya untuk proses update data kelas
	 */
	function valid_id2()
	{
		// cek apakah data tanggal pada session sama dengan isi field
		// tidak mungkin seorang siswa diabsen 2 kali pada tanggal yang sama
		$current_id 	= $this->session->userdata('id_kelas');
		$new_id			= $this->input->post('id_kelas');
				
		if ($new_id === $current_id)
		{
			return TRUE;
		}
		else
		{
			if($this->Kelas_model->valid_id($new_id) === TRUE) // cek database untuk entry yang sama memakai valid_entry()
			{
				$this->form_validation->set_message('valid_id2', "Kelas dengan kode $new_id sudah terdaftar");
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}
}
// END Kelas Class

/* End of file kelas.php */
/* Location: ./system/application/controllers/kelas.php */