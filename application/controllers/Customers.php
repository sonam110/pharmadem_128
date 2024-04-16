<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customers extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->page_data['page']->title = 'Customers Management';
		$this->page_data['page']->menu = 'customers';
	}

	public function index()
	{
		ifPermissions('customers_list');
		$this->page_data['customers'] = $this->customers_model->get();
		$this->load->view('customers/list', $this->page_data);
	}

	public function add()
	{
		ifPermissions('customers_add');
		$this->load->view('customers/add', $this->page_data);
	}

	public function save()
	{
		ifPermissions('customers_add');
		postAllowed();

		$id = $this->customers_model->create([
			//'role' => post('role'),
			'name' => post('name'),
			//'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
			'status' => (int) post('status'),
			//'password' => hash( "sha256", post('password') ),
		]);

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id.'.'.$ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/customers');

			if($image['status']){
				$this->customers_model->update($id, ['img_type' => $ext]);
			}else{
				copy(FCPATH.'uploads/customers/default.png', 'uploads/customers/'.$id.'.png');
			}

		}else{

			copy(FCPATH.'uploads/customers/default.png', 'uploads/customers/'.$id.'.png');

		}

		$this->activity_model->add('New Customer $'.$id.' Created by User:'.logged('name'), logged('id'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'New Customer Created Successfully');
		
		redirect('customers');

	}

	public function view($id)
	{

		ifPermissions('customers_view');

		$this->page_data['Customer'] = $this->customers_model->getById($id);
		
		$this->page_data['Customer']->activity = $this->activity_model->getByWhere([
			'user'=> $id
		], [ 'order' => ['id', 'desc'] ]);
		$this->load->view('customers/view', $this->page_data);

	}

	public function edit($id)
	{

		ifPermissions('customers_edit');

		$this->page_data['Customer'] = $this->customers_model->getById($id);
		$this->load->view('customers/edit', $this->page_data);

	}


	public function update($id)
	{

		ifPermissions('customers_edit');
		
		postAllowed();

		$data = [
			//'role' => post('role'),
			'name' => post('name'),
			//'username' => post('username'),
			//'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
		];

		//$password = post('password');

		if(logged('id')!=$id)
			$data['status'] = post('status')==1;


		$id = $this->customers_model->update($id, $data);

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id.'.'.$ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/customers');

			if($image['status']){
				$this->customers_model->update($id, ['img_type' => $ext]);
			}

		}

		$this->activity_model->add("Customer #$id Updated by User:".logged('name'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Customer Profile has been Updated Successfully');
		
		redirect('customers');

	}

	public function check()
	{
		$email = !empty(get('email')) ? get('email') : false;
		$username = !empty(get('username')) ? get('username') : false;
		$notId = !empty($this->input->get('notId')) ? $this->input->get('notId') : 0;

		if($email)
			$exists = count($this->users_model->getByWhere([
					'email' => $email,
					'id !=' => $notId,
				])) > 0 ? true : false;

		if($username)
			$exists = count($this->users_model->getByWhere([
					'username' => $username,
					'id !=' => $notId,
				])) > 0 ? true : false;

		echo $exists ? 'false' : 'true';
	}

	public function delete($id)
	{

		ifPermissions('users_delete');

		if($id!==1 && $id!=logged($id)){ }else{
			redirect('/','refresh');
			return;
		}

		//$id = $this->customers_model->delete($id);

		$this->activity_model->add("User #$id Deleted by User:".logged('name'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Customer has been Deleted Successfully');
		
		redirect('users');

	}

	public function change_status($id)
	{
		$this->customers_model->update($id, ['status' => get('status') == 'true' ? 1 : 0 ]);
		echo 'done';
	}

}

/* End of file Customers.php */
/* Location: ./application/controllers/Customers.php */