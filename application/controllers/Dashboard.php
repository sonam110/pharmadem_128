<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->page_data['page']->title = 'Dashboard';
		$this->page_data['page']->menu = 'dashboard';
		set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH.'third_party/phpseclib1.0.20');
		include('Net/SSH2.php');
		include('Net/SFTP.php');
		define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
	}


	public function index()
	{

		$this->page_data['customer_count'] = $this->db->count_all('customers');
        $this->page_data['project_count'] = $this->db->count_all('projects');
		$this->page_data['user_count'] = $this->db->count_all('users');
		$this->page_data['pending_projects'] = $this->projects_model->getPendingProjects();
		$this->page_data['pendingJobCount'] = $this->projects_model->getPendingqJobCount();
		$queryJobsMaster = $this->db->query("SELECT COUNT(*) AS num_records FROM jobs_master WHERE cosmo_status = 'Processing'");
		$this->page_data['pendingDMFileJobCount'] = $queryJobsMaster->row()->num_records;
       

		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}

		//$command = 'top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/"';
		$cpuUsageCommand = "top -bn1 | grep 'Cpu(s)' | awk '{print $2 + $4}'";

		$this->page_data['cpuUsage'] = $ssh->exec($cpuUsageCommand);


       //$this->load->view('dashboard', $data);

		$this->load->view('dashboard', $this->page_data);
	}

	public function getload()
	{
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}

// Run the command to retrieve server load details
$load = $ssh->exec('uptime');

// Close the SSH connection
$ssh->disconnect();

// Send the server load details back to the Ajax request
//echo $load;

		$this->page_data['load'] = $load;

		$this->load->view('dashboard', $this->page_data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */