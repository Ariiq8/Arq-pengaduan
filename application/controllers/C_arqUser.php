<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_arqUser extends CI_Controller

{

    public function index()

    {
        $data['user'] = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();

        $u= $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();
      
        $proses = $this->db->get_where('pengaduan',['status'=>'proses','nik'=>$u['nik']])->num_rows();

        $pengaduan =$this->db->get_where('pengaduan',['nik'=>$u['nik']])->num_rows();

        $selesai= $this->db->get_where('pengaduan',['status'=>'selesai','nik'=>$u['nik']])->num_rows();

        $data['bar_graph']=array(

            'proses'=>$proses,

            'pengaduan'=>$pengaduan,

            'selesai'=>$selesai,

        );

        $this->load->view('template/V_arqHeader');

        $this->load->view('template/V_arqSideUser');

        $this->load->view('template/V_arqTopbarUser', $data);

        $this->load->view('user/V_arqDashboard',$data);

        $this->load->view('template/V_arqFooterUser');

    }

    public function pengaduan()

    {

        $data['user'] = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();

		$data['pengaduan'] = $this->M_arqUser->pengaduan()->result_array();

        $this->load->view('template/V_arqHeader');

        $this->load->view('template/V_arqSideUser');

        $this->load->view('template/V_arqTopbarUser', $data);

        $this->load->view('user/V_arqPengaduan',$data);

        $this->load->view('template/V_arqFooterUser');

    }

    public function mengadu()
    {
        $data['user'] = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();


        $this->load->model('M_arqAdmin');

        $data['subkategori'] = $this->M_arqAdmin->subkategori()->result_array();

        $data['kategori'] = $this->M_arqAdmin->kategori()->result_array();

        $this->load->view('template/V_arqHeader');

        $this->load->view('template/V_arqSideUser');

        $this->load->view('template/V_arqTopbarUser', $data);

        $this->load->view('user/V_arqMengadu');

        $this->load->view('template/V_arqFooterUser');

    }

    public function insertpengaduan()

    {

        $this->load->model('M_arqAdmin');

        $data = [

            'nik' => $this->session->userdata('nik'),

            'id_subkategori' => $this->input->post('subkategori'),

            'tgl_pengaduan' => $this->input->post('tgl_pengaduan'),

            'isi_laporan' => $this->input->post('isi'),

            'foto' => $this->input->post('foto'),

            'status' => 0

        ];

        $this->M_arqUser->insertpengaduan($data);

        $this->session->set_flashdata('pengaduan', '<div class="alert alert-success" role="alert"> Berhasil ditambahkan! </div>');

        redirect('C_arqUser/pengaduan');

    }

    public function get_sub_kategori()

    {

        $id_kategori = $this->input->post('id');

        $sub_kategori = $this->db->get_where('subkategori', ['id_kategori' => $id_kategori])->result();

        echo json_encode($sub_kategori);

    }

    public function tambahmengadu()

    {

        $user = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();

        $kategori = $this->input->post('kategori');

        $subkategori = $this->input->post('subkategori');

        $laporan = $this->input->post('laporan');

        $config['upload_path'] = './assets/img/uploads/';

        $config['allowed_types']        = 'gif|jpg|png';

        // $config['max_size']             = 100;

        // $config['max_width']            = 1024;

        // $config['max_height']           = 768;

        $this->load->library('upload', $config);

        $this->upload->do_upload('image');

        $uploaded_data = $this->upload->data('file_name');

        echo $uploaded_data;

        $add = array(         

            'nik' => $user['nik'],

            'kategori' => $kategori,

            'subkategori' => $subkategori,

            'tanggal_pengaduan' =>date('Y-m-d'),

            'isi_laporan' => $laporan,

            'foto' => $uploaded_data,

        );

        $this->M_arqUser->insertpengaduan($add);

        $this->session->set_flashdata('pengaduan', '<div class="alert alert-success" role="alert"> Berhasil ditambahkan! </div>');

        redirect('C_arqUser/pengaduan');

    }

    public function profile()

    {

        $data['user'] = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();

        $this->load->view('template/V_arqHeader');

        $this->load->view('template/V_arqSideUser');

        $this->load->view('template/V_arqTopbarUser', $data);

        $this->load->view('user/V_arqSetting');

        $this->load->view('template/V_arqFooterUser');

    }

    public function editprofile()

    {

        $data['user'] = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();

		$user = $this->db->get_where('masyarakat', ['username' => $this->session->userdata('username')])->row_array();

		$nama = $this->input->post('nama');

		$username = $this->input->post('username');

		$telepon = $this->input->post('telepon');

		$nik = $this->input->post('nik');

		$update = [

			'nama' => $nama,

			'username' => $username,

			'telepon' => $telepon,

			'nik' => $nik,

		];

		$this->db->where('id', $user['id']);

		$this->M_arqUser->editProfil($update);

		$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">

		Profil berhasil di edit !

		  </div>');

		redirect('C_arqUser/profile');
        
    }

}
