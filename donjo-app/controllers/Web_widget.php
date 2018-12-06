<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Web_widget extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		session_start();

		// Jika offline_mode dalam level yang menyembunyikan website,
		// tidak perlu menampilkan halaman website
		if ($this->setting->offline_mode >= 2)
		{
			redirect('hom_desa');
			exit;
		}

		$this->load->model('user_model');
		$grup = $this->user_model->sesi_grup($_SESSION['sesi']);
		if ($grup != 1 AND $grup != 2 AND $grup != 3 AND $grup != 4)
		{
			if (empty($grup))
				$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
			else
				unset($_SESSION['request_uri']);
			redirect('siteman');
		}
		$this->load->model('header_model');
		$this->load->model('web_widget_model');
		$this->modul_ini = 13;
	}

	public function clear()
	{
		unset($_SESSION['cari']);
		unset($_SESSION['filter']);
		unset($_SESSION['table_curpage']);
 		$this->session->per_page = 20;
		redirect('web_widget');
	}

	public function pager()
	{
		if (isset($_POST['per_page']))
			$_SESSION['per_page'] = $_POST['per_page'];
		redirect("web_widget");
	}

	public function index($page=0, $o=0)
	{
		if (!$page)
		{
		 	$page = $this->session->table_curpage;
		}

		$data['paging'] = $this->web_widget_model->paging($page, $o);
		$data['p'] = $data['paging']->page;
		$data['o'] = $o;
		$this->session->table_curpage = $data['paging']->page;

		if (isset($_SESSION['cari']))
			$data['cari'] = $_SESSION['cari'];
		else $data['cari'] = '';

		if (isset($_SESSION['filter']))
			$data['filter'] = $_SESSION['filter'];
		else $data['filter'] = '';

		if (isset($_POST['per_page']))
			$_SESSION['per_page'] = $_POST['per_page'];
		$data['per_page'] = $_SESSION['per_page'];

		$data['main'] = $this->web_widget_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
		$data['keyword'] = $this->web_widget_model->autocomplete();

		$header = $this->header_model->get_data();
		$nav['act'] = 13;
		$nav['act_sub'] = 48;

		$this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('web/artikel/widget', $data);
		$this->load->view('footer');
	}

	public function form($p = 1, $o = 0, $id = '')
	{
		$data['p'] = $p;
		$data['o'] = $o;

		if ($id)
		{
			$data['widget'] = $this->web_widget_model->get_widget($id);
			$data['form_action'] = site_url("web_widget/update/$id/$p/$o");
		}
		else
		{
			$data['widget'] = null;
			$data['form_action'] = site_url("web_widget/insert");
		}

		$header = $this->header_model->get_data();
		$nav['act'] = 13;
		$nav['act_sub'] = 48;

		$this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('web/artikel/widget-form', $data);
		$this->load->view('footer');
	}

	public function search()
	{
		$cari = $this->input->post('cari');
		if ($cari != '')
			$_SESSION['cari'] = $cari;
		else unset($_SESSION['cari']);
		redirect("web_widget");
	}

	public function filter()
	{
		$filter = $this->input->post('filter');
		if ($filter != 0)
			$_SESSION['filter'] = $filter;
		else unset($_SESSION['filter']);
		redirect("web_widget");
	}

	public function admin($widget)
	{
		$header = $this->header_model->get_data();
		$header['minsidebar'] = 1;
		$nav['act'] = 13;
		$nav['act_sub'] = 48;
		$data['form_action'] = site_url("web_widget/update_setting/".$widget);
		$data['setting'] = $this->web_widget_model->get_setting($widget);
		$this->load->view('header', $header);
		$this->load->view('nav', $nav);
		$this->load->view('widgets/admin_'.$widget, $data);
		$this->load->view('footer');
	}

	public function update_setting($widget)
	{
		$setting = $this->input->post('setting');
		$this->web_widget_model->update_setting($widget, $setting);
		redirect("web_widget/admin/$widget");
	}

	public function insert()
	{
		$this->web_widget_model->insert();
		redirect("web_widget");
	}

	public function update($id = '', $p = 1, $o = 0)
	{
		$this->web_widget_model->update($id);
		redirect("web_widget");
	}

	public function delete($p = 1, $o = 0, $id = '')
	{
		$this->web_widget_model->delete($id);
		redirect("web_widget");
	}

	public function delete_all($p = 1,$o = 0)
	{
		$this->web_widget_model->delete_all();
		redirect("web_widget");
	}

	public function urut($id = 0, $arah = 0)
	{
		$this->web_widget_model->urut($id, $arah);
		redirect("web_widget");
	}

	public function lock($id = 0)
	{
		$this->web_widget_model->lock($id, 1);
		redirect("web_widget");
	}

	public function unlock($id = 0)
	{
		$this->web_widget_model->lock($id, 2);
		redirect("web_widget");
	}

}
