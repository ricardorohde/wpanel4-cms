<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Este é o controller de categorias, usado principalmente
 * no painel de controle do site.
 *
 * @author Eliel de Paula <elieldepaula@gmail.com>
 * @since 21/10/2014
 */

class Categorias extends MX_Controller 
{

	function __construct() 
    {
		$this->auth->protect('posts');
		$this->form_validation->set_error_delimiters('<p><span class="label label-danger">', '</span></p>');
	}

	public function index() 
    {

		$this->load->model('categoria');
		$this->load->library('table');

		$layout_vars = array();
		$content_vars = array();
        $posts_views = config_item('posts_views');

		// Template da tabela
		$this->table->set_template(array('table_open' => '<table id="grid" class="table table-striped">'));
		$this->table->set_heading('#', 'Título', 'Categoria-pai', 'Visão', 'Ações');
		$query = $this->categoria->get_list();

		foreach ($query->result() as $row) {
			$this->table->add_row(
				$row->id,
				$row->title,
				$this->categoria->get_title_by_id($row->category_id),
				$posts_views[$row->view],
				div(array('class' => 'btn-group btn-group-sm')) .
				anchor('admin/categorias/edit/' . $row->id, glyphicon('edit'), array('class' => 'btn btn-default')) .
				'<button class="btn btn-default" onClick="return confirmar(\''.site_url('admin/categorias/delete/' . $row->id).'\');">'.glyphicon('trash').'</button>' .
				div(null, true)
			);
		}

		$content_vars['listagem'] = $this->table->generate();
		$this->wpanel->load_view('categorias/index', $content_vars);
	}

	public function add() 
    {
		$this->load->model('categoria');
		$layout_vars = array();
		$content_vars = array();

		$this->form_validation->set_rules('title', 'Título', 'required');

		if ($this->form_validation->run() == FALSE) {
			// Prepara a lista de categorias.
			$query = $this->categoria->get_list();
			$options = array();
			$options[0] = 'Sem categoria';
			foreach ($query->result() as $row) {
				$options[$row->id] = $row->title;
			}

			$content_vars['options'] = $options;
			$this->wpanel->load_view('categorias/add', $content_vars);

		} else {

			$dados_save = array();
			$dados_save['title'] = $this->input->post('title');
			$dados_save['description'] = $this->input->post('description');
			$dados_save['link'] = url_title(convert_accented_characters($this->input->post('title')));
			$dados_save['category_id'] = $this->input->post('category_id');
			$dados_save['view'] = $this->input->post('view');

			if ($this->categoria->save($dados_save)) {
				$this->session->set_flashdata('msg_sistema', 'Categoria salva com sucesso.');
				redirect('admin/categorias');
			} else {
				$this->session->set_flashdata('msg_sistema', 'Erro ao salvar a categoria.');
				redirect('admin/categorias');
			}
		}
	}

	public function edit($id = null) 
    {
		$this->load->model('categoria');
		$layout_vars = array();
		$content_vars = array();

		$this->form_validation->set_rules('title', 'Título', 'required');

		if ($this->form_validation->run() == FALSE) {

			if ($id == null) {
				$this->session->set_flashdata('msg_sistema', 'Categoria inexistente.');
				redirect('admin/categorias');
			}

			// Prepara a lista de categorias.
			$query = $this->categoria->get_list();
			$options = array();
			$options[0] = 'Sem categoria';
			foreach ($query->result() as $row) {
				$options[$row->id] = $row->title;
			}

			$content_vars['row'] = $this->categoria->get_by_id($id)->row();
			$content_vars['options'] = $options;
			$this->wpanel->load_view('categorias/edit', $content_vars);

		} else {

			$dados_save = array();
			$dados_save['title'] = $this->input->post('title');
			$dados_save['description'] = $this->input->post('description');
			$dados_save['link'] = url_title(convert_accented_characters($this->input->post('title')));
			$dados_save['category_id'] = $this->input->post('category_id');
			$dados_save['view'] = $this->input->post('view');

			if ($this->categoria->update($id, $dados_save)) {
				$this->session->set_flashdata('msg_sistema', 'Categoria salva com sucesso.');
				redirect('admin/categorias');
			} else {
				$this->session->set_flashdata('msg_sistema', 'Erro ao salvar a categoria.');
				redirect('admin/categorias');
			}
		}
	}

	public function delete($id = null) 
    {
		if ($id == null) {
			$this->session->set_flashdata('msg_sistema', 'Categoria inexistente.');
			redirect('admin/categorias');
		}
		$this->load->model('categoria');
		if ($this->categoria->delete($id)) {
			$this->session->set_flashdata('msg_sistema', 'Categoria excluída com sucesso.');
			redirect('admin/categorias');
		} else {
			$this->session->set_flashdata('msg_sistema', 'Erro ao excluir a categoria.');
			redirect('admin/categorias');
		}
	}
}