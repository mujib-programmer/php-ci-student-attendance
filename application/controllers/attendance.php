<?php
/**
 * Absen Class
 *
 * @author	Mujibur Rochman<mujib.programmer@gmail.com>
 */
class Attendance extends CI_Controller {
	/**
	 * Constructor
	 */
	function Attendance()
	{
		parent::__construct();
		$this->load->model('Absen_model', '', TRUE);
		$this->load->model('Semester_model', '', TRUE);
		$this->load->model('Siswa_model', '', TRUE);
	}
	
	/**
	 * Inisialisasi variabel untuk $limit dan $title(untuk id element <body>)
	 */
	var $limit = 10;
	var $title = 'absen';
	
	/**
	 * Memeriksa user state, jika dalam keadaan login akan menampilkan halaman absen,
	 * jika tidak akan meredirect ke halaman login
	 */
	function index()
	{
		// Hapus data session yang digunakan pada proses update data absen
		$this->session->unset_userdata('id_absen');
		$this->session->unset_userdata('tanggal');
			
		if ($this->session->userdata('login') == TRUE)
		{
			$this->get_last_ten_absen();
		}
		else
		{
			redirect('login');
		}
	}
	
	/**
	 * Menampilkan 10 data absen terkini
	 */
	function get_last_ten_absen($offset = 0)
	{
		$data['title'] = $this->title;
		$data['h2_title'] = 'Absen';
		$data['main_view'] = 'absen/absen';
		
		// Offset
		$uri_segment = 3;
		$offset = $this->uri->segment($uri_segment);
		
		// Load data dari tabel absen
		$absens = $this->Absen_model->get_last_ten_absen($this->limit, $offset)->result();
		$num_rows = $this->Absen_model->count_all_num_rows();
		
		if ($num_rows > 0) // Jika query menghasilkan data
		{
			// Membuat pagination			
			$config['base_url'] = site_url('absen/get_last_ten_absen');
			$config['total_rows'] = $num_rows;
			$config['per_page'] = $this->limit;
			$config['uri_segment'] = $uri_segment;
			$this->pagination->initialize($config);
			$data['pagination'] = $this->pagination->create_links();
			
			// Set template tabel, untuk efek selang-seling tiap baris
			$tmpl = array( 'table_open'    => '<table border="0" cellpadding="0" cellspacing="0">',
						  'row_alt_start'  => '<tr class="zebra">',
							'row_alt_end'    => '</tr>'
						  );
			$this->table->set_template($tmpl);

			// Set heading untuk tabel
			$this->table->set_empty("&nbsp;");
			$this->table->set_heading('No', 'Hari, Tanggal', 'No Induk', 'Nama', 'Kelas', 'Absen', 'Actions');
			
			// Penomoran baris data
			$i = 0 + $offset;
			
			foreach ($absens as $absen)
			{
				// Konversi hari dan tanggal ke dalam format Indonesia
				$hari_array = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
				$hr = date('w', strtotime($absen->tanggal));
				$hari = $hari_array[$hr];
				$tgl = date('d-m-Y', strtotime($absen->tanggal));
				$hr_tgl = "$hari, $tgl";
				
				// Penyusunan data baris per baris, perhatikan pembuatan link untuk updat dan delete
				$this->table->add_row(++$i, $hr_tgl, $absen->nis, $absen->nama, $absen->kelas, $absen->absen,
										anchor('absen/update/'.$absen->id_absen,'update',array('class' => 'update')).' '.
										anchor('absen/delete/'.$absen->id_absen,'hapus',array('class'=> 'delete','onclick'=>"return confirm('Anda yakin akan menghapus data ini?')"))
									);
			}
			$data['table'] = $this->table->generate();
		}
		else
		{
			$data['message'] = 'Tidak ditemukan satupun data absensi!';
		}		
		
		$data['link'] = array('link_add' => anchor('absen/add/','tambah data', array('class' => 'add')));
		
		// Load default view
		$this->load->view('template', $data);
	}
	
	/**
	 * Menghapus data absen
	 */
	function delete($id_absen)
	{
		$this->Absen_model->delete($id_absen);
		$this->session->set_flashdata('message', '1 data absen berhasil dihapus');
		
		redirect('absen');
	}
	
	/**
	 * Berpindah ke form untuk entry data absensi baru
	 */
	function add()
	{		
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Absen > Tambah Data';
		$data['main_view'] 		= 'absen/absen_form';
		$data['form_action']	= site_url('absen/add_process');
		$data['link'] 			= array('link_back' => anchor('absen/','kembali', array('class' => 'back')));
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses untuk entry data absensi baru
	 */
	function add_process()
	{
		// Inisialisasi data umum
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Absen > Tambah Data';
		$data['main_view'] 		= 'absen/absen_form';
		$data['form_action']	= site_url('absen/add_process');
		$data['link'] 			= array('link_back' => anchor('absen/','kembali', array('class' => 'back')));
		
		// Set validation rules
		$this->form_validation->set_rules('nis', 'NIS', 'required|exact_length[4]|callback_valid_nis');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'required|callback_valid_date|callback_valid_entry');
		$this->form_validation->set_rules('absen', 'Absen', 'required');
		
		if ($this->form_validation->run() == TRUE)
		{
			// Cek semester yang sedang aktif
			$semesters = $this->Semester_model->get_active_semester()->row();
			$id_semester = $semesters->id_semester;
						
			// Prepare data untuk disimpan di tabel
			$absen = array('nis' 			=> $this->input->post('nis'),
							'id_semester'	=> $id_semester,
							'tanggal'		=> date('Y-m-d', strtotime($this->input->post('tanggal'))),
							'absen' 		=> $this->input->post('absen')
						);
			// Proses simpan data absensi
			$this->Absen_model->add($absen);
			
			$this->session->set_flashdata('message', 'Satu data absen berhasil disimpan!');
			redirect('absen/add');
		}
		else
		{		
			$this->load->view('template', $data);
		}		
	}
	
	/**
	 * Berpindah ke form untuk update data absensi
	 */
	function update($id_absen)
	{
		// Inisialisasi data umum
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Absen > Update Data';
		$data['main_view'] 		= 'absen/absen_form';
		$data['form_action']	= site_url('absen/update_process');
		$data['link'] 			= array('link_back' => anchor('absen/','kembali', array('class' => 'back'))
										);
		
		// cari data dari database
		$absen = $this->Absen_model->get_absen_by_id($id_absen)->row();
		
		// buat session untuk menyimpan data primary key (id_absen)
		$this->session->set_userdata('id_absen', $absen->id_absen);
		
		// Data untuk mengisi field2 form
		$data['default']['nis'] 		= $absen->nis;
		$data['default']['id_semester'] = $absen->id_semester;		
		$data['default']['tanggal'] 	= date('d-m-Y', strtotime($absen->tanggal));
		$data['default']['absen'] 		= $absen->absen;
		
		// buat session untuk menyimpan data tanggal yang sedang diupdate
		$this->session->set_userdata('tanggal', $absen->tanggal);
			
		$this->load->view('template', $data);
	}
	
	/**
	 * Proses untuk update data absensi
	 */
	function update_process()
	{
		// Inisialisasi data umum
		$data['title'] 			= $this->title;
		$data['h2_title'] 		= 'Absen > Update Data';
		$data['main_view'] 		= 'absen/absen_form';
		$data['form_action']	= site_url('absen/update_process');
		$data['link'] 			= array('link_back' => anchor('absen/','kembali', array('class' => 'back'))
										);
			
		// Set validation rules
		$this->form_validation->set_rules('nis', 'NIS', 'required|exact_length[4]|callback_valid_nis');
		$this->form_validation->set_rules('tanggal', 'Tanggal', 'required|callback_valid_date|callback_valid_entry2');
		$this->form_validation->set_rules('absen', 'Absen', 'required');
		
		// jika proses validasi sukses, maka lanjut update absen
		if ($this->form_validation->run() == TRUE)
		{
			// Cek semester yang sedang aktif
			$semesters = $this->Semester_model->get_active_semester()->row();
			$id_semester = $semesters->id_semester;
						
			// Simpan data
			$absen = array('nis' 			=> $this->input->post('nis'),
							'id_semester'	=> $id_semester,
							'tanggal'		=> date('Y-m-d', strtotime($this->input->post('tanggal'))),
							'absen' 		=> $this->input->post('absen')
						);
			$this->Absen_model->update($this->session->userdata('id_absen'), $absen);
						
			// set pesan
			$this->session->set_flashdata('message', 'Satu data absen berhasil diupdate!');
			
			redirect('absen');
		}
		else
		{		
			$this->load->view('template', $data);
		}
	}	
	
	/**
	 * Mengecek apakah ada siswa dengan NIS $nis, 
	 * memastikan hanya siswa yang terdaftar yang bisa diabsen
	 */
	function valid_nis($nis)
	{
		if ($this->Siswa_model->valid_nis($nis) == TRUE)
		{
			return TRUE;
		}
		else
		{
			$this->form_validation->set_message('valid_nis', "siswa dengan NIS $nis tidak terdaftar");
			return FALSE;
		}
	}
	
	/**
	 * Cek format tanggal agar sesuai untuk penyimpanan di database
	 */
	function valid_date($str)
	{
		if(!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$", $str))
		{
			$this->form_validation->set_message('valid_date', 'Format tanggal tidak valid. dd-mm-yyyy');
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Cek agar tidak terjadi siswa dengan NIS yang sama diabsen 2 kali
	 */
	function valid_entry()
	{
		$nis 		= $this->input->post('nis');
		$tanggal	= date('Y-m-d', strtotime($this->input->post('tanggal')));
		
		if($this->Absen_model->valid_entry($nis, $tanggal) == FALSE)
		{
			$this->form_validation->set_message('valid_entry', 'Siswa ini sudah tercatat absen pada tanggal ' . $this->input->post('tanggal'));
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * Cek agar tidak terjadi siswa dengan NIS yang sama diabsen 2 kali, hanya untuk proses update
	 */
	function valid_entry2()
	{
		$current_date 	= $this->session->userdata('tanggal');
		$new_date		= date('Y-m-d', strtotime($this->input->post('tanggal')));
		$nis 			= $this->input->post('nis');
		
		if ($new_date === $current_date)
		{
			return TRUE;
		}
		else
		{
			if($this->Absen_model->valid_entry($nis, $new_date) === FALSE) // cek database untuk entry yang sama memakai valid_entry()
			{
				$this->form_validation->set_message('valid_entry2', 'Siswa ini sudah tercatat absen pada tanggal ' . $this->input->post('tanggal'));
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
	}	
}
// END Attendance Class

/* End of file absen.php */
/* Location: ./application/controllers/attendance.php */