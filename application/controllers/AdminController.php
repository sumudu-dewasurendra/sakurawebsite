<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller{

	public function index(){

		$this->isLoggedIn();
		
	}

	// this function checks whether the user is logged in or not
	function isLoggedIn(){

		$isLoggedIn = $this->session->userdata('isLoggedIn');

		if(!isset($isLoggedIn) || $isLoggedIn!=TRUE){
			$this->load->view('admin/loginView');
		}else{
			
			  redirect('displayHome');
			// $this->load->view('admin/homeView');
		}

	}


	function logMe(){

		$this->form_validation->set_rules('email','Email','trim|required|valid_email|is_unique[admins.admin_email]');
		$this->form_validation->set_rules('password','User Password','trim|required');

		if($this->form_validation == false){

			$this->index();
			
		}else{
	

			
			$email = $_POST['email'];
			$password = $_POST['password'];

			
			  $userInfo['email'] = $email;
			  $userInfo['password'] = $password;



			$this->load->model('adminModel');
			
			$res = $this->adminModel->authenticateUser($userInfo);
			

			if($res){


				$sessionArray = array('email' =>$res[0]->admin_email,
										'id' =>$res[0]->admin_id,
										'contact' =>$res[0]->admin_contact,
										'fname' =>$res[0]->admin_fname,
										'nic' =>$res[0]->admin_nic,										
										'isLoggedIn' => true );

				$this->session->set_userdata($sessionArray);

				

				redirect('displayHome');

			}else{

				$this->session->set_flashdata('error', 'Email or password mismatch');
                
                
                $this->load->view('admin/loginView');
			}

		}
	}

	function logOut(){
		$this->session->sess_destroy();
		redirect('admin');
	}


	function registerUser(){

		$this->form_validation->set_rules('admin_fname','Admin first name','required');
		$this->form_validation->set_rules('admin_lname','Admin last name','');
		$this->form_validation->set_rules('admin_photo','Admin photo','');
		$this->form_validation->set_rules('admin_contact','Admin contact number','trim|required');
		$this->form_validation->set_rules('admin_nic','Admin NIC number','trim|required');
		$this->form_validation->set_rules('admin_email','Admin email','trim|required|valid_email|is_unique[admins.admin_email]');
		$this->form_validation->set_rules('admin_password','Admin password','required');
		$this->form_validation->set_rules('admin_cpassword','Admin confirm pwd','required|matches[admin_password]');


		if($this->form_validation->run()==TRUE){

			$admin_fname = $this->input->post('admin_fname');
			$admin_lname = $this->input->post('admin_lname');
			$admin_photo = $this->input->post('admin_photo');
			$admin_contact = $this->input->post('admin_contact');
			$admin_nic = $this->input->post('admin_nic');
			$admin_email = $this->input->post('admin_email');
			$admin_password = $this->input->post('admin_password');


			$userInfo = array('admin_fname'=>$admin_fname, 
							 'admin_lname'=>$admin_lname, 
							 'admin_contact'=>$admin_contact,
							 'admin_nic'=> $admin_nic,							 
							 'admin_password'=>getHashedPassword($admin_password),
							 'admin_photo'=>$admin_photo,  
							 'admin_email'=>$admin_email,                            
                             'admin_createdDtm'=>date('Y-m-d H:i:s'),
                             'admin_createdBy'=>'1',
                             );

			$this->load->model('adminModel');

            $result = $this->adminModel->registerNewUser($userInfo);
            
            if($result > 0)
            {
                $this->session->set_flashdata('success', 'New User created successfully');
            }
            else
            {
                $this->session->set_flashdata('error', 'User creation failed');
            }
            
            
            redirect('addAdmin');


		}else{
			redirect('addAdmin');
		}

	}




}