<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Projects extends MY_Controller {

	public function __construct()
	{
		ini_set('memory_limit', '-1');
		parent::__construct();
		$this->load->library('session');
		$this->page_data['page']->title = 'Projects Management';
		$this->page_data['page']->menu = 'projects';
		set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH.'third_party/phpseclib1.0.20');
		include('Net/SSH2.php');
		include('Net/SFTP.php');
		define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
	}

	public function index()
	{
		ifPermissions('projects_list');

		// Assuming $results contains the data fetched from your SQL query
		 // dd($this->projects_model->checkjobinsertsA('269'));

		$type = $this->input->get('type');
		$this->page_data['projects'] = [];
		if($type =='progress'){
			$query = $this->db->select('job_id')
                  ->from('job_results_count')
                  ->where('status','Pending')
                  ->get();
            $job_ids = array();
			if ($query->num_rows() > 0) {
			    // Fetch the result rows as an array of objects
			    $result = $query->result();

			    // Extract project IDs from the result array
			   
			    foreach ($result as $row) {
			        $job_ids[] = $row->job_id;
			    }

			    // Now $project_ids contains the project IDs associated with the job IDs in $ids_string
			   
			} 
			$project_ids = array();
			if(!empty($job_ids)){
				$job_string = implode(',',$job_ids);
				$query1 = $this->db->select('project_id')
	                  ->from('jobs_master')
	                  ->where("id IN ($job_string)")
	                  ->get();
	           
				if ($query1->num_rows() > 0) {
				    // Fetch the result rows as an array of objects
				    $result1 = $query1->result();

				    // Extract project IDs from the result array
				   
				    foreach ($result1 as $row) {
				        $project_ids[] = $row->project_id;
				    }

				    // Now $project_ids contains the project IDs associated with the job IDs in $ids_string
				   
				} 

			}
			if(!empty($project_ids)){
				$this->page_data['projects'] =  $this->projects_model->get_projects_by_ids($project_ids);
			}
			$this->load->view('projects/list', $this->page_data);

		}
		elseif($type =='dmfile'){
		
			$query11 = $this->db->select('project_id')
              ->from('jobs_master')
              ->where('cosmo_status','Processing')
              ->get();
           	$project_idsp = array();
			if ($query11->num_rows() > 0) {
			    // Fetch the result rows as an array of objects
			    $result11 = $query11->result();

			    // Extract project IDs from the result array
			   
			    foreach ($result11 as $row) {
			        $project_idsp[] = $row->project_id;
			    }

			    // Now $project_ids contains the project IDs associated with the job IDs in $ids_string
			   
			} 
			if(!empty($project_idsp)){
				$this->page_data['projects'] =  $this->projects_model->get_projects_by_ids($project_idsp);

			}
		    $this->load->view('projects/list', $this->page_data);
		}
		elseif($type =='queue'){
			$job_in_queue_ids = $this->projects_model->getQueueJobdsInex();
			
			if(!empty($job_in_queue_ids)){
				
				$queryp = $this->db->select('project_id')
	                  ->from('jobs_master')
	                  ->where_in('id', $job_in_queue_ids)
	                  ->get();
	            $project_idss = array();
	         
				if ($queryp->num_rows() > 0) {
				    // Fetch the result rows as an array of objects
				    $resultp = $queryp->result();
				   
				    // Extract project IDs from the result array
				   
				    foreach ($resultp as $row) {
				        $project_idss[] = $row->project_id;
				    }

				    // Now $project_ids contains the project IDs associated with the job IDs in $ids_string
				   
				} 
				
				if(!empty($project_idss)){

					$this->page_data['projects'] =  $this->projects_model->get_projects_by_ids($project_idss);

				}
			}
			$this->load->view('projects/list', $this->page_data);
		} else{
			$this->page_data['projects'] = $this->projects_model->get();
			$this->load->view('projects/list', $this->page_data);

		}

	}

	public function add()
	{
		ifPermissions('project_add');
		$this->load->view('projects/add', $this->page_data);
	}

	public function submit($id)
	{
		ifPermissions('project_submit');

		$this->page_data['Project'] = $this->projects_model->getById($id);
		$this->page_data['cdata'] = $this->projects_model->getsolvents_all();
		
		$this->load->view('projects/submit', $this->page_data);
	}

	


	public function check_records() {
		// Perform the database query to check if records exist
			// Query for jobs_master table
			$queryJobsMaster = $this->db->query("SELECT COUNT(*) AS num_records FROM jobs_master WHERE cosmo_status = 'Processing'");
			$numRecordsJobsMaster = $queryJobsMaster->row()->num_records;

			// Query for tasks_queue table
			$queryTasksQueue = $this->db->query("SELECT COUNT(*) AS num_records FROM job_results_count WHERE status = 'Pending'");
			$numRecordsTasksQueue = $queryTasksQueue->row()->num_records;

			// Prepare the response
			$response = array();
			$response['recordsExist'] = ($numRecordsJobsMaster > 0 || $numRecordsTasksQueue > 0);

			// Send the JSON response
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($response));
	  }

	  public function solvents()
	  {
		  $this->page_data['cdata'] = $this->projects_model->getsolvents_all();
		  
		  $this->load->view('projects/solvents', $this->page_data);
  
	  }

	public function custom($id) {

		$this->page_data['cdata'] = $this->projects_model->getsolvents_all();
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails1($id);
	
		$this->load->view('projects/custom', $this->page_data);

	}
public function customcalulation()
{
	session_write_close();
	

	$ids = $this->input->post('ids');
	$jtype = $this->input->post('jtype');
	$jid=$this->input->post('jobid'); 
	if($ids == NULL){
		echo "not done";
		die;
	}
	

	$pending_projects = $this->projects_model->getPendingProjects();

	$queryJobsMaster = $this->db->query("SELECT COUNT(*) AS num_records FROM jobs_master WHERE cosmo_status = 'Processing'");
    $numRecordsJobsMaster = $queryJobsMaster->row()->num_records;

	if($pending_projects || $numRecordsJobsMaster > 0) {
		echo "Pending"; //change to Pending to work
		die;
	}

	if($jtype=="Pure_68") {

	$this->customcalcpure($ids,$jtype,$jid,'50');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->customcalcpure($ids,$jtype,$jid,'25');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->customcalcpure($ids,$jtype,$jid,'10');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	//echo "done";

	}
	if($jtype=="Binary_1085") {

		
	$this->customcalcpure($ids,'Pure_68',$jid,'50');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Pure_68');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->customcalcpure($ids,'Pure_68',$jid,'25');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Pure_68');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->customcalcpure($ids,'Pure_68',$jid,'10');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Pure_68');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	//echo "done";
		
	$this->binary_1085_custom($ids,$jtype,$jid,'50');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->binary_1085_custom($ids,$jtype,$jid,'25');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->binary_1085_custom($ids,$jtype,$jid,'10');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	//echo "done";
	}

	if($jtype=="Tertiary-16400") {

	$this->customcalcpure($ids,'Pure_68',$jid,'50');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Pure_68');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->customcalcpure($ids,'Pure_68',$jid,'25');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Pure_68');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->customcalcpure($ids,'Pure_68',$jid,'10');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Pure_68');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->binary_1085_custom($ids,'Binary_1085',$jid,'50');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Binary_1085');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->binary_1085_custom($ids,'Binary_1085',$jid,'25');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Binary_1085');
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->binary_1085_custom($ids,'Binary_1085',$jid,'10');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', 'Binary_1085');
	$this->db->update('job_results_count', array('status'=>'Completed'));


	$this->tertiary_16400_custom($ids,$jtype,$jid,'50');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->tertiary_16400_custom($ids,$jtype,$jid,'25');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	$this->tertiary_16400_custom($ids,$jtype,$jid,'10');
	$this->db->where('job_id', $jid);
	$this->db->where('solvent_type', $jtype);
	$this->db->update('job_results_count', array('status'=>'Completed'));

	//$this->db->where('job_id', $jid);
	//$this->db->where('solvent_type', $jtype);
	//$this->db->update('job_results_count', array('status'=>'Completed'));
	echo "done";


	}
	//binary_1085_custom
}
	public function customcalcpure($ids,$stype,$jobid,$temp)
	{

		$selectedOptions = $ids;

		$temparatureMap = [
			"10" => ["283.15", "10", "", ""],
			"25" => ["298.15", "", "25", ""],
			"50" => ["323.15", "", "", "50"]
		];
		
		$temparature = "";
		$temp10 = "";
		$temp20 = "";
		$temp50 = "";
		
		if (isset($temparatureMap[$temp])) {
			[$temparature, $temp10, $temp20, $temp50] = $temparatureMap[$temp];
		}


			$info="";
			$jobdetails= $this->projects_model->getJobdetails1($jobid);


			$binary_py = $this->projects_model->getPython();

			$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
			
			$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
	
			$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
		
			$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';

			$file = strtotime("today")*1000+rand(10000,99999).".py";
		
			// Define file path and contents
			$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$file;

		
			// Open file and print contents
			$contents = $binary_py[0]->pure_sc;

		$i = 0;

		$jid = $this->projects_model->addactivity_log([
			'job_id' => $jobdetails[0]->id,
			'solvent_type' => $stype,
			'tempr' => $temp,
			'solvents_count' => count($ids),
			'solvent_activity_finished' => '',
			'process_start	' => date('m/d/Y h:i:s a', time()),
			'status' => 'Pending',
			'jtype' => 'Custom'
		
		]);

		
			foreach ($selectedOptions as $optionValue1) {
				//echo $optionValue1;
				$solvent = $this->db->select('solvent1_name')->where('s_id', $optionValue1)->get('solvents_master')->row();

				$item = $solvent->solvent1_name;
	
	// Define search and replace strings
	$search_string = "new content";
	$replace_string = "crs.add_molecule(['OC_solventDB_68_new/".$item."/COSMO_TZVPD/".$item."_c000.orcacosmo'])";
	
	$input_string = "INPUT_COSMO";
	$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
	// Read file contents

	$input_temp = "TEMPR";
	$replace_input_temp = $temparature;

	$file_contents = $binary_py[0]->pure_sc;
	
	// Replace search string with replace string
	$file_contents = str_replace($search_string, $replace_string, $file_contents);
	$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
	$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);


// Escape single quotes in the file contents
$fileContents = str_replace("'", "'\\''", $file_contents);

//echo $file_contents;


// Execute the Python script from the variable
// $command = 'cd /home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';

$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
$stream = $ssh->exec($command);

// Check if $stream has output
while (empty($stream)) {
    // Add a delay before checking again
    usleep(500000); // 0.5 seconds

    // Retrieve the updated $stream
    $stream = $ssh->exec($command);
}
//echo $stream;


		$dataj= json_decode($stream, true);
	
	// Accessing the value for "data1"
		$data1 = $dataj['data1'];
	
		$id = $this->projects_model->createjobresults([
			'job_id' => $jobdetails[0]->id,
			's_id' =>  $optionValue1,
			'solvents' => $item,
			'result_type' => $stype,
			'pure_data1' => $data1[0] . ", " . $data1[1] ,
			'input_temp_10' => $temp10,
			'input_temp_20' => $temp20,
			'input_temp_50	' => $temp50,
			//'input_temp_20	' => '',
			'solvent_result_name	' =>$fname,
			'solvent_result	' => $stream,
			'processed_on	' => date('m/d/Y h:i:s a', time()),
		]);
	
		$this->db->where('id', $jid);
		$this->db->update('job_results_count', array('solvent_activity_finished'=>$i+1,'sv_id'=>$optionValue1,'process_end'=>date('m/d/Y h:i:s a', time())));
		//echo $this->db->last_query();

		if(count($selectedOptions)==$i+1) {
			$this->db->where('id', $jid);
			$this->db->update('job_results_count', array('status'=>'Completed'));
	
		}

		$file_contents = str_replace($replace_string, $search_string, $file_contents);
		$file_contents = str_replace($replace_input_string, $input_string, $file_contents);
		$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
		//$file_contents="";
	
		$itemy='';
		$array_product [$i]= $item;
		$i++;
		
	
	
	}
	// Close connection
	$ssh->disconnect();
	//$sftp->disconnect();
	} 
	
	// Binary Custom

	public function binary_1085_custom($ids,$stype,$id,$temp) {

		$selectedOptions = $ids;


		$temparatureMap = [
			"10" => ["283.15", "10", "", ""],
			"25" => ["298.15", "", "25", ""],
			"50" => ["323.15", "", "", "50"]
		];
		
		$temparature = "";
		$temp10 = "";
		$temp20 = "";
		$temp50 = "";
		
		if (isset($temparatureMap[$temp])) {
			[$temparature, $temp10, $temp20, $temp50] = $temparatureMap[$temp];
		}
		
		$errorMessage = '';
		
		$info = "";
		$jobdetails = $this->projects_model->getJobdetails1($id);
		$binary_py = $this->projects_model->getPython();
		$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
		
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
		
		$logFile = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/err.log';
		file_put_contents($logFile, '');
		
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src');
		
		$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src';
		$file = strtotime("today") * 1000 + rand(10000, 99999) . ".py";
		
		$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/' . $file;
		
		$file_contents = $binary_py[0]->binary_sc;
		
		if ($file_contents = $binary_py[0]->binary_sc) {

			$solvents_master = $this->projects_model->getsolventscustom($selectedOptions);


			$jid = $this->projects_model->addactivity_log([
				'job_id' => $jobdetails[0]->id,
				'solvent_type' => $stype,
				'tempr' => $temp,
				'solvents_count' => count($solvents_master),
				'solvent_activity_finished' => '',
				'process_start' => date('m/d/Y h:i:s a', time()),
				'status' => 'Pending',
				'jtype' => 'Custom'
			]);
		
			$timeout = 60; // Maximum time in seconds for a loop iteration
			$startTime = time();
			$lastProcessedIndex = 0; // Initialize the index of the last successfully processed record
		
			try {
				for ($i = $lastProcessedIndex; $i < count($solvents_master); $i++) {
					$startTime = time(); // Reset the start time for each iteration
		
					$row = $solvents_master[$i];
					$item = $row['solvent1_name'];
					$search_string = "INPUT_COSMO";
					$replace_string = "mol_structure_list_0 = ['" . $fname . "/COSMO_TZVPD/" . $fname . "_c000.orcacosmo']";
					$input_string = "NEW_CONTENT1";
					$input_string1 = "NEW_CONTENT2";
					$replace_input_string = "crs.add_molecule(['OC_solventDB_68_new/" . $row['solvent1_name'] . "/COSMO_TZVPD/" . $row['solvent1_name'] . "_c000.orcacosmo'])";
					$replace_input_string1 = "crs.add_molecule(['OC_solventDB_68_new/" . $row['solvent2_name'] . "/COSMO_TZVPD/" . $row['solvent2_name'] . "_c000.orcacosmo'])";
					$input_temp = "TEMPR";
					$replace_input_temp = $temparature;
		
					$file_contents = $binary_py[0]->binary_sc;
					$file_contents = str_replace($search_string, $replace_string, $file_contents);
					$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
					$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
					$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);
					$fileContents = str_replace("'", "'\\''", $file_contents);
					//Azhar Commented
					//$command = 'cd /home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';

					$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';

					//$stream = $ssh->exec($command);
					$stream = $ssh->exec($command . ' &');
		
					while (empty($stream)) {
						usleep(500000); // 0.5 seconds
						$stream = $ssh->exec($command);
					}
		
					$file_contents = str_replace($replace_string, $search_string, $file_contents);
					$file_contents = str_replace($replace_input_string, $input_string, $file_contents);
					$file_contents = str_replace($replace_input_string1, $input_string1, $file_contents);
					$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
		
					$dataj = json_decode($stream, true);
					$data1 = $dataj['data1'];
					$data2 = $dataj['data2'];
					$data3 = $dataj['data3'];
					$data4 = $dataj['data4'];
					$data5 = $dataj['data5'];
		
					$id = $this->projects_model->createjobresults([
						'job_id' => $jobdetails[0]->id,
						's_id' => $row['s_id'],
						'solvents' => $row['solvent1_name'] . '->' . $row['solvent2_name'],
						'result_type' => $stype,
						'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2],
						'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2],
						'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data1[2],
						'pure_data4' => $data4[0] . ", " . $data4[1] . ", " . $data1[2],
						'pure_data5' => $data5[0] . ", " . $data5[1] . ", " . $data1[2],
						'input_temp_10' => $temp10,
						'input_temp_20' => $temp20,
						'input_temp_50' => $temp50,
						'solvent_result_name' => $fname,
						'solvent_result' => $stream,
						'processed_on' => date('m/d/Y h:i:s a', time()),
					]);
		
					$lastProcessedIndex = $i; // Update the index of the last successfully processed record
		
					$this->db->where('id', $jid);
					$this->db->update('job_results_count', array('solvent_activity_finished' => $i + 1, 'sv_id'=>$row['s_id'],'process_end' => date('m/d/Y h:i:s a', time())));
		
					if (count($solvents_master) == $i + 1) {
						$this->db->where('id', $jid);
						$this->db->update('job_results_count', array('status' => 'Completed'));
					}
		
					// Check if the loop is stuck
					$elapsedTime = time() - $startTime;
					if ($elapsedTime >= $timeout) {
						// Log an error and exit the loop
						$errorMessage = 'Loop stuck for JOBID ' . $jobdetails[0]->id;
						error_log($errorMessage, 3, $logFile);
						break; // Exit the loop
					}
				}
		
				//$this->db->trans_commit();
			} catch (Exception $e) {
		
				$errorMessage = 'Error during process for JOBID ' . $jobdetails[0]->id . ': ' . $e->getMessage();
				error_log($errorMessage, 3, $logFile);
				error_log("Error during process for JOBID " . $jobdetails[0]->id, 1, "evishy@gmail.com");
		
			}
		
			if (!empty($errorMessage)) {
				error_log($errorMessage, 3, $logFile);
			}
		}
		
		// Update the job results count table with the last processed index
		//$this->db->where('id', $jid);
		//$this->db->update('job_results_count', array('solvent_activity_finished' => $lastProcessedIndex + 1, 'process_end' => date('m/d/Y h:i:s a', time())));
		
		$ssh->disconnect();
		
		
}

// Terinary

public function tertiary_16400_custom($ids,$stype,$id,$temp) {

	$selectedOptions = $ids;


	$temparatureMap = [
		"10" => ["283.15", "10", "", ""],
		"25" => ["298.15", "", "25", ""],
		"50" => ["323.15", "", "", "50"]
	];
	
	$temparature = "";
	$temp10 = "";
	$temp20 = "";
	$temp50 = "";
	
	if (isset($temparatureMap[$temp])) {
		[$temparature, $temp10, $temp20, $temp50] = $temparatureMap[$temp];
	}
	ini_set('memory_limit', '-1');

	$info="";
			$jobdetails= $this->projects_model->getJobdetails1($id);

			$binary_py = $this->projects_model->getPython();


			//echo $jobdetails[0]->project_id;
			$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);

			$ssh = new Net_SSH2('128.199.31.121',22);
			if (!$ssh->login('chemistry1', 'Ravi@1234')) {
				exit('Login Failed');
			}
		
		//$logFile = '/home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy/err.log';
		$logFile = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/err.log';
		file_put_contents($logFile, '');
		
		//$ssh->exec('cd /home/mlladmin/ORCA/openCOSMO-RS_py/src');
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src');
		
		//$directory = '/home/mlladmin/ORCA/openCOSMO-RS_py/src';
		$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src';
		$file = strtotime("today") * 1000 + rand(10000, 99999) . ".py";
		//$file_path = '/home/mlladmin/ORCA/openCOSMO-RS_py/src/' . $file;
		$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src' . $file;
	
	
		$file_contents = $binary_py[0]->terinary_sc;

		if ($file_contents = $binary_py[0]->terinary_sc) {
		

	
	$i = 0;
	$solvents_master = $this->projects_model->getsolventscustomt($selectedOptions);


	$jid = $this->projects_model->addactivity_log([
		'job_id' => $jobdetails[0]->id,
		'solvent_type' => $stype,
		'tempr' => $temp,
		'solvents_count' => count($solvents_master),
		'solvent_activity_finished' => '',
		'process_start	' => date('m/d/Y h:i:s a', time()),
		'status' => 'Pending',
		'jtype' => 'Custom'
	
	]);

	
//print_r($solvents_master);
foreach ($solvents_master as $row) {
    //echo $row['solvent1_name'];
    
$item = $row['solvent1_name'];
	
	//foreach ($solvents as $item) {
	
		// Define search and replace strings
		$search_string = "INPUT_COSMO";
		//$replace_string = "mol_structure_list_0 = ['opencosmorspy/".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
		$replace_string = "mol_structure_list_0 = ['" . $fname . "/COSMO_TZVPD/" . $fname . "_c000.orcacosmo']";

		
		$input_string = "NEW_CONTENT1";
		$input_string1 = "NEW_CONTENT2";
		$input_string2 = "NEW_CONTENT3";
		//$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
		$replace_input_string ="crs.add_molecule(['OC_solventDB_68_new/".$row['solvent1_name']."/COSMO_TZVPD/".$row['solvent1_name']."_c000.orcacosmo'])";
		$replace_input_string1 ="crs.add_molecule(['OC_solventDB_68_new/".$row['solvent2_name']."/COSMO_TZVPD/".$row['solvent2_name']."_c000.orcacosmo'])";
		$replace_input_string2 ="crs.add_molecule(['OC_solventDB_68_new/".$row['solvent3_name']."/COSMO_TZVPD/".$row['solvent3_name']."_c000.orcacosmo'])";
		
		$input_temp = "TEMPR";
 		$replace_input_temp = $temparature;

		// Read file contents
		$file_contents = $binary_py[0]->terinary_sc;
		
		// Replace search string with replace string
		$file_contents = str_replace($search_string, $replace_string, $file_contents);
		$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
		$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
		$file_contents = str_replace($input_string2, $replace_input_string2, $file_contents);
		$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);
		$fileContents = str_replace("'", "'\\''", $file_contents);

				
		$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
		
		$stream = $ssh->exec($command . ' &');

		while (empty($stream)) {
			usleep(500000); // 0.5 seconds
			$stream = $ssh->exec($command);
		}

		
		$file_contents = str_replace($replace_string,$search_string, $file_contents);
		$file_contents = str_replace($replace_input_string,$input_string, $file_contents);
		$file_contents = str_replace($replace_input_string1,$input_string1, $file_contents);
		$file_contents = str_replace($replace_input_string2,$input_string2, $file_contents);
		$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
	
		$dataj= json_decode($stream, true);


		// Accessing the value for "data1"
		$data1 = $dataj['data1'];
		$data2 = $dataj['data2'];
		$data3 = $dataj['data3'];
		//$data4 = $dataj['data4'];
		//$data5 = $dataj['data5'];
		//$data6 = $dataj['data6'];
		
		$id = $this->projects_model->createjobresults([
			'job_id' => $jobdetails[0]->id,
			's_id' => $row['s_id'],
			'solvents' => $row['solvent1_name'].'->'.$row['solvent2_name'].'->'.$row['solvent3_name'],
			'result_type' => $stype,
			'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2] . ", " . $data1[3] ,
			'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data2[2] . ", " . $data2[3] ,
			'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data3[2] . ", " . $data3[3] ,
			//'pure_data4' => round($data4[0],4) . ", " . round($data4[1],4) . ", " . round($data1[2],4) . ", " . round($data1[3],4) ,
			//'pure_data5' => round($data5[0],4) . ", " . round($data5[1],4) . ", " . round($data1[2],4) . ", " . round($data1[3],4) ,
			//'pure_data6' => $data6[0] . ", " . $data6[1] ,
			'input_temp_10' => $temp10,
			'input_temp_20' => $temp20,
			'input_temp_50	' => $temp50,
			//'input_temp_50	' => 'Yes',
			'solvent_result_name	' =>$fname,
			'solvent_result	' => $stream,
			'processed_on	' => date('m/d/Y h:i:s a', time()),
		]);
	
		$this->db->where('id', $jid);
		$this->db->update('job_results_count', array('solvent_activity_finished'=>$i+1,'process_end'=>date('m/d/Y h:i:s a', time())));
		
		if(count($solvents_master)==$i+1) {
			$this->db->where('id', $jid);
			$this->db->update('job_results_count', array('status'=>'Completed'));
	
		}
	
		$array_product [$i]= $item;
		$i++;
		//}
	
	
	}
	// Close connection
	$ssh->disconnect();
	//$sftp->disconnect();
	} else {
		exit('File open failed');
	}

}

	// Pure Custom

	public function pure_68_custom($id,$stype,$temp) {

		$temparatureMap = [
			"10" => ["283.15", "10", "", ""],
			"25" => ["298.15", "", "25", ""],
			"50" => ["323.15", "", "", "50"]
		];
		
		$temparature = "";
		$temp10 = "";
		$temp20 = "";
		$temp50 = "";
		
		if (isset($temparatureMap[$temp])) {
			[$temparature, $temp10, $temp20, $temp50] = $temparatureMap[$temp];
		}


			$info="";
			$jobdetails= $this->projects_model->getJobdetails1($id);

			$binary_py = $this->projects_model->getPython();

			$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
			
			$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
	
			//$ssh->exec('cd /home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy');
			$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
	
	//$directory = '/home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy';
	$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';

	$file = strtotime("today")*1000+rand(10000,99999).".py";
	
	// Define file path and contents
	//$file_path = '/home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy/'.$file;
	$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$file;
	
	// Open file and print contents
	if ($contents = $binary_py[0]->pure_sc ) {

	$i = 0;
	$solvents_master = $this->projects_model->getsolvents();

	$jid = $this->projects_model->addactivity_log([
		'job_id' => $jobdetails[0]->id,
		'solvent_type' => $stype,
		'tempr' => $temp,
		'solvents_count' => count($solvents_master),
		'solvent_activity_finished' => '',
		'process_start	' => date('m/d/Y h:i:s a', time()),
		'status' => 'Pending'
	
	]);

foreach ($solvents_master as $row) {
    //echo $row['solvent1_name'];
    
$item = $row['solvent1_name'];
	
	// Define search and replace strings
	$search_string = "new content";
	$replace_string = "crs.add_molecule(['OC_solventDB_68_new/".$item."/COSMO_TZVPD/".$item."_c000.orcacosmo'])";
	
	$input_string = "INPUT_COSMO";
	$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
	// Read file contents

	$input_temp = "TEMPR";
	$replace_input_temp = $temparature;

	$file_contents = $binary_py[0]->pure_sc;
	
	// Replace search string with replace string
	$file_contents = str_replace($search_string, $replace_string, $file_contents);
	$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
	$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);


// Escape single quotes in the file contents
$fileContents = str_replace("'", "'\\''", $file_contents);

//echo $file_contents;

// Execute the Python script from the variable
//$command = 'cd /home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';

$stream = $ssh->exec($command);

// Check if $stream has output
while (empty($stream)) {
    // Add a delay before checking again
    usleep(500000); // 0.5 seconds

    // Retrieve the updated $stream
    $stream = $ssh->exec($command);
}

		$dataj= json_decode($stream, true);
	
	// Accessing the value for "data1"
		$data1 = $dataj['data1'];
	
		$id = $this->projects_model->createjobresults([
			'job_id' => $jobdetails[0]->id,
			's_id' =>  $row['s_id'],
			'solvents' => $item,
			'result_type' => $stype,
			'pure_data1' => $data1[0] . ", " . $data1[1] ,
			'input_temp_10' => $temp10,
			'input_temp_20' => $temp20,
			'input_temp_50	' => $temp50,
			//'input_temp_20	' => '',
			'solvent_result_name	' =>$fname,
			'solvent_result	' => $stream,
			'processed_on	' => date('m/d/Y h:i:s a', time()),
		]);
	
		$this->db->where('id', $jid);
		$this->db->update('job_results_count', array('solvent_activity_finished'=>$i+1,'sv_id'=>$row['s_id'],'process_end'=>date('m/d/Y h:i:s a', time())));
		
		if(count($solvents_master)==$i+1) {
			$this->db->where('id', $jid);
			$this->db->update('job_results_count', array('status'=>'Completed'));
	
		}

		$file_contents = str_replace($replace_string, $search_string, $file_contents);
		$file_contents = str_replace($replace_input_string, $input_string, $file_contents);
		$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
		//$file_contents="";
	
		$itemy='';
		$array_product [$i]= $item;
		$i++;
		
	
	
	}
	// Close connection
	$ssh->disconnect();
	//$sftp->disconnect();
	} else {
		exit('File open failed');
	}
	
	
	}

	// End Pure Custom
	
	public function getSolventDetails($solventId)
	{
    $this->load->library('curl'); // Load the cURL library

    $this->load->database();
    $solvent = $this->db->select('solvent1_name')->where('s_id', $solventId)->get('solvents_master')->row();

    if (!$solvent) {
        // Solvent not found, return an error response
        $response['error'] = 'Solvent not found.';
    } else {
        $solventName = urlencode($solvent->solvent1_name);
        $url = "https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/name/{$solventName}/property/CanonicalSMILES,MolecularWeight/JSON";
        $apiResponse = $this->curl->simple_get($url); // Make the API request

        if ($apiResponse) {
            $jsonResponse = json_decode($apiResponse, true);

            // Check if the API response is valid and contains the required data
            if (isset($jsonResponse['PropertyTable']['Properties'][0])) {
                $smilesCode = $jsonResponse['PropertyTable']['Properties'][0]['CanonicalSMILES'];
                $molecularWeight = $jsonResponse['PropertyTable']['Properties'][0]['MolecularWeight'];

                // Get the schema image URL
                $imageResponse = $this->curl->simple_get("https://pubchem.ncbi.nlm.nih.gov/rest/pug/compound/name/{$solventName}/PNG");
                $imageBase64 = base64_encode($imageResponse);

                $response = array(
                    'solvent_name' => $solvent->solvent1_name,
                    'smiles_code' => $smilesCode,
                    'molecular_weight' => $molecularWeight,
                    'schema_image' => $imageBase64
                );
            } else {
                // Data not found in the API response, return an error response
                $response['error'] = 'Solvent details not available.';
            }
        } else {
            // Error making the API request, return an error response
            $response['error'] = 'Failed to fetch solvent details.';
        }
    }

    // Send the JSON response
    $this->output->set_content_type('application/json');
    $this->output->set_output(json_encode($response));
}


	public function tempcalcs()
	{
		$this->page_data['cdata'] = $this->projects_model->gettempcalcs();
		
		$this->load->view('projects/tempcalcs', $this->page_data);
	}
	public function createdataforcharts(){
		$id = $this->input->post('data1');


		$this->projects_model->insertresults10($id);
		echo '10 done';
		$this->projects_model->insertresults25($id);
		echo '25 done';
		$this->projects_model->insertresults50($id);
		echo '50 done';

		

		//$this->load->view('projects/dataresults', $this->page_data);

	}

	public function deletedata($id)
	{
		$this->db->where('job_id', $id);
		$this->db->delete('job_results_count');

		$this->db->where('job_id', $id);
		$this->db->delete('job_results');

		$this->db->where('job_id', $id);
		$this->db->delete('results_data_10');

		$this->db->where('job_id', $id);
		$this->db->delete('results_data_25');

		$this->db->where('job_id', $id);
		$this->db->delete('results_data_50');

		$this->db->where('job_id', $id);
		$this->db->delete('solubility_corrected_predicted_data');

		$this->db->where('job_id', $id);
		$this->db->delete('job_status');

		$this->db->where('job_id', $id);
		$this->db->delete('tasks_queue');
		$existingRecord = $this->db->get_where('tasks_queue', array('job_id' => $id,'status'=>'pending'))->row();
	    //print_r($existingRecord);
	    if ($existingRecord) {
	    	$updatelastdata = array('execution_started_on' => NULL);
            $this->db->where('job_id', $id);
            $this->db->update('tasks_queue', $updatelastdata);
	    }
		
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Job Data Removed Successfully');


		redirect('projects');


	}
	public function deletedataph($id)
	{
		$this->db->where('job_id', $id);
		$this->db->delete('job_results_ph_count');

		$this->db->where('job_id', $id);
		$this->db->delete('job_ph_results');

		$this->db->where('job_id', $id);
		$this->db->delete('ph_solubility_results');

		$this->db->where('job_id', $id);
		$this->db->delete('job_status');
		
		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Job Data Removed Successfully');


		redirect('projects');


	}

	public function addqueue(){

		$jobid = $this->input->post('jobid');
		$stype= $this->input->post('stype');

		$existingRecord = $this->db->get_where('tasks_queue', array('job_id' => $jobid,'status'=>'pending'))->row();

if (!$existingRecord) {
    $data = array(
        'job_id' => $jobid,
        'job_type' => $stype,
        'is_custom' => 0,
        'added_to_queue' => 'Yes',
        'started_on' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    );

    $this->db->insert('tasks_queue', $data);
	//print_r($this->db->last_query());
	echo "Queue Added";
}
	

	}

	public function addcustomjobqueue(){

		$ids = $this->input->post('ids');
		$ids_string = implode(',',$ids);
		$jtype = $this->input->post('jtype');
		$jid=$this->input->post('jobid');

		$existingRecord = $this->db->get_where('tasks_queue', array('job_id' => $jid,'status'=>'pending'))->row();

		if (!$existingRecord) {
		    $data = array(
		        'job_id' => $jid,
		        'job_type' => $jtype,
		        'is_custom' => '1',
		        'selected_solvents_ids' => $ids_string,
		        'added_to_queue' => 'Yes',
		        'started_on' => date('Y-m-d H:i:s'),
		        'status' => 'pending'
		    );

		    $this->db->insert('tasks_queue', $data);
			//print_r($this->db->last_query());
			echo "Queue Added";
		}
			

	}

	public function insertresults10(){
		$id = $this->input->post('data1');
		
		$this->projects_model->insertresults10($id);
	}

	public function insertresults25(){
		$id = $this->input->post('data1');
		$this->projects_model->insertresults25($id);
	}

	public function insertresults50(){
		$id = $this->input->post('data1');
		$this->projects_model->insertresults50($id);


	}

	public function insertphresults10(){
		$id = $this->input->post('data1');
		
		$this->projects_model->insertphresults($id,'10');
	}

	public function insertphresults25(){
		$id = $this->input->post('data1');
		$this->projects_model->insertphresults($id,'25');
	}

	public function insertphresults50(){
		$id = $this->input->post('data1');
		$this->projects_model->insertphresults($id,'50');


	}

	public function showcreateddata($id)

	{
		$this->page_data['cdata10'] = $this->projects_model->getresultsdata10i($id);
		$this->page_data['cdata25'] = $this->projects_model->getresultsdata25i($id);
		$this->page_data['cdata50'] = $this->projects_model->getresultsdata50i($id);
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);

		$this->load->view('projects/showcreateddata', $this->page_data);
	}
	public function showphcreateddata($id)
	{

		$this->page_data['cdata10'] = $this->projects_model->getresultsphdatai($id,'10');
		$this->page_data['cdata25'] = $this->projects_model->getresultsphdatai($id,'25');
		$this->page_data['cdata50'] = $this->projects_model->getresultsphdatai($id,'50');
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);

		$this->load->view('projects/showphcreateddata', $this->page_data);
	}


	public function showtenfivty($id)

	{
		$this->page_data['cdata10'] = $this->projects_model->getResultsData10WithComparison($id);
		
		$this->page_data['cdata50'] = $this->projects_model->getresultsdata50i($id);
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);

		$this->load->view('projects/showtenfivty', $this->page_data);
	}

	public function createdata($id)
	{
		//ifPermissions('project_submit');
		//Insert// $this->projects_model->insertresults10($id);
		//////$this->page_data['cdata'] = $this->projects_model->getresultsdata($id);
		$this->page_data['cdata10'] = $this->projects_model->getresultsdata10($id);
		$this->page_data['cdata25'] = $this->projects_model->getresultsdata25($id);
		$this->page_data['cdata50'] = $this->projects_model->getresultsdata50($id);
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);

		$this->load->view('projects/createdata', $this->page_data);
	}
	
	public function result()
	{
		ifPermissions('project_result');
		$this->load->view('projects/result', $this->page_data);
	}
	
	public function del()
	{
	    $ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
	
		
		
		/*$kill_command = 'sh /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/python_kill.sh ConformerGenerator.py';
		$stream = $ssh->exec($kill_command);
		
		$kill_job = 'sh /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/kill_job.sh orca*';
		$stream1 = $ssh->exec($kill_job);*/
		
		//$ssh->exec("killall orca_gtoint_mpi");
		$ssh->exec("killall orca_scf_mpi");
		$ssh->exec("killall orca_scfgrad_mp");
		$ssh->exec("killall orca_scfhess_mp");
		
		sleep(10);	
			
		$this->db->query("DELETE FROM jobs_master WHERE cosmo_status = 'Processing'");
		$this->db->query("DELETE FROM job_results_count WHERE status = 'Pending'");
		
		
	    redirect('projects');
		
	       
	}

	public function killall()
	{
	    $ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
		$kill_command1 = 'sh /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/python_kill.sh ConformerGenerator.py';
		$stream1 = $ssh->exec($kill_command1);
		
		$kill_command = 'sh /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/python3_kill.sh python3.py';
		$stream = $ssh->exec($kill_command);
			
		sleep(180);
		
		$this->db->query("DELETE FROM jobs_master WHERE cosmo_status = 'Processing'");
		$this->db->query("DELETE FROM job_results_count WHERE status = 'Pending'");
		//$this->db->query("DELETE FROM  tasks_queue WHERE status = 'pending' and  execution_started_on != NULL");
		
	    redirect('projects');
		
	       
	}
	public function killqueueall()
	{
	    $ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
		$kill_command1 = 'sh /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/python_kill.sh ConformerGenerator.py';
		$stream1 = $ssh->exec($kill_command1);
		
		$kill_command = 'sh /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/python3_kill.sh python3.py';
		$stream = $ssh->exec($kill_command);
			
		sleep(50);
		
		$job_in_queue_ids = $this->projects_model->getQueueJobds();


		if (!empty($job_in_queue_ids)) {

		    // Convert array of IDs into comma-separated string
		    $ids_string = implode(',', $job_in_queue_ids);

		    // Delete rows from 'tasks_queue' table where 'id' is in the list of IDs
		    $this->db->query("DELETE FROM tasks_queue WHERE job_id IN ($ids_string)");
		    $this->db->query("DELETE FROM jobs_master WHERE id IN ($ids_string) AND cosmo_status IN ('Processing', 'Pending')");

		    $this->db->where_in('job_id', $job_in_queue_ids);
			$this->db->delete('job_results_count');

			$this->db->where_in('job_id', $job_in_queue_ids);
			$this->db->delete('job_results');

			$this->db->where_in('job_id', $job_in_queue_ids);
			$this->db->delete('results_data_10');

			$this->db->where_in('job_id', $job_in_queue_ids);
			$this->db->delete('results_data_25');

			$this->db->where_in('job_id', $job_in_queue_ids);
			$this->db->delete('results_data_50');

			$this->db->where_in('job_id', $job_in_queue_ids);
			$this->db->delete('job_status');
		
			$this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Job Data Removed Successfully');

		    
		}

	    redirect('projects');
	       
	}

	public function results($id)
	{
		ifPermissions('projects_list');
		$this->page_data['results_type'] = $this->projects_model->getresults($id);
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);
	
		$this->load->view('projects/results', $this->page_data);
	}
	public function resultsph($id)
	{
		ifPermissions('projects_list');
		$this->page_data['results_type'] = $this->projects_model->getresultsPh($id);
		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);
	
		$this->load->view('projects/resultsph', $this->page_data);
	}

	public function scatter_data($id){

		
		$query = $this->db->query("select *, jr.id as jbid from solvents_master sm, job_results jr, jobs_master jm where (jr.input_temp_10 = 10 && jr.input_temp_20='' && jr.input_temp_50='') and sm.s_id=jr.s_id and jm.id=jr.job_id and jm.project_id=".$id);
		
		$data = $query->result();

		$chart_data = array();
		
		foreach ($data as $row) {
			$chart_data[] = array(
				'label' => $row->w1_solvent_system,
				'value' => $this->projects_model->get10mgmlforscatter($row->pure_data1,$row->job_id),
			);
		}
		
		echo json_encode($chart_data);

	}


	public function actrun()
	{

		$connection = @ssh2_connect('128.199.31.121', 22);
		//include(APPPATH . 'third_party/phpseclib1.0.20/Net/SSH2.php');
		set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH.'third_party/phpseclib1.0.20');
		include('Net/SSH2.php');
		include('Net/SFTP.php');
		define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
		
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
		
		//$ssh->exec('cd einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch "tt.py"; echo "test" >> "tt.py"');
		// Directory and file details
$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';
$file = 'existing_file.py';

$sftp = new Net_SFTP('128.199.31.121',22);
if (!$sftp->login('chemistry1', 'Ravi@1234')) {
    exit('Login Failed');
}

// Define file path and contents
$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/existing_file.py';
$file_contents = 'This is some example content.1';

// Open file and print contents
if ($contents = $sftp->get($file_path)) {
    //echo $contents;

			// new code to insert
$solvents = array(
	'phenol',

	'1-Octanol',
	
	'methylcyclohexane',
	
	'n,n-dimethylacetamide',
	
	'methane1'
	
);

foreach ($solvents as $item) {

// Define search and replace strings
$search_string = "new content";
$replace_string = "crs.add_molecule(['".$item."/COSMO_TZVPD/".$item."_c000.orcacosmo'])";

// Read file contents
$file_contents = $sftp->get($file_path);

// Replace search string with replace string
$file_contents = str_replace($search_string, $replace_string, $file_contents);

// Upload updated contents to server
if (!$sftp->put($file_path, $file_contents)) {
    exit('Replace failed');
}

	$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 existing_file.py > /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$item.'.log 2>&1 & echo $!;sleep 5');
	$file_contents = str_replace($replace_string, $search_string, $file_contents);

	// Define file path and contents
	$file_pathlog = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$item.'.log';
	//$file_contents = 'This is some example content.1';
	if (!$sftp->put($file_path, $file_contents)) {
		exit('Replace failed');
	}
// Open file and print contents
	if ($contentslog = $sftp->get($file_pathlog)) {
	echo "<br><h2>".$item."</h2><br>.$contentslog.<br>";
	}

}
// Close connection
$sftp->disconnect();
} else {
    exit('File open failed');
}

// Close connection
//$sftp->disconnect();

//echo 'File write successful';


	}

	public function msavenew()
	{
		//print_r($_POST);
			
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}

		$fname=$this->input->post('mname');
		$mvalue=$this->input->post('mvalue');
		$Smile=$this->input->post('Smile');
		$kns=$this->input->post('kns');
		$hfvalue=$this->input->post('hfvalue');
		$molweight= $this->input->post('mweight');
		$s_name = $this->input->post('s_name');
		$s_value = $this->input->post('s_value');
		$temp = $this->input->post('temp');

		$ext='.inp';
		$fullname=$fname.$ext;
		$project_code=$this->input->post('project_code');
		$structure=$this->input->post('structure');

		
       
		$sftp = new Net_SFTP('128.199.31.121',22);
		if (!$sftp->login('chemistry1', 'Ravi@1234')) {
    	exit('Login Failed');
		}

		$directory_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$fname; // the path of the directory you want to check
		
		
		if ($sftp->file_exists($directory_path)) { // check if the directory exists
    	//echo "The directory $directory_path exists.";
		$this->session->set_flashdata('alert-type', 'danger');
		$this->session->set_flashdata('alert', 'Give Molecule Name (directory already exists on server)');
		
		redirect('projects/submit/'.$project_code);
		}

		/*-- if one job already is in process then this added in to queue*/

		$queryJobsMaster = $this->db->query("SELECT COUNT(*) AS num_records FROM jobs_master WHERE cosmo_status = 'Processing'");
        $numRecordsJobsMaster = $queryJobsMaster->row()->num_records;

        // Query for tasks_queue table
        $queryTasksQueue = $this->db->query("SELECT COUNT(*) AS num_records FROM job_results_count WHERE status = 'Pending'");
        $numRecordsTasksQueue = $queryTasksQueue->row()->num_records;

      	if($numRecordsJobsMaster > 0 || $numRecordsTasksQueue > 0){
      		$id = $this->projects_model->createjob([
				'project_id' => $project_code,
				'structure_code' => $structure,
				'inp_filename' => $fullname,
				'inp_value	' => (int) $mvalue,
				'smiles	' => $Smile,
				'know_solubility	' => $kns,
				'hfuss_value	' => $hfvalue,
				'mol_weight	' => $molweight,
				'process_start	' => date('m/d/Y h:i:s a', time()),
				'process_end	' => '',
				'cosmo_status	' => 'Pending',
			]);

			$this->activity_model->add('DEM File '.$fullname.' Created by User:'.logged('name'), logged('id'));

			$query = $this->db->query("SELECT id FROM jobs_master ORDER BY id DESC LIMIT 1");
			$row = $query->row();
			$jobbid = $row->id;

			if(is_array(@$s_name) && count(@$s_name) >0 ){
	            for ($i = 0;$i <= count($s_name);$i++) {
	                if (!empty($s_name[$i])) {
	                	$dataAdd = array(
				        'job_id' => $jobbid,
				        's_name' => $s_name[$i],
				        's_value' => $s_value[$i],
				        'temp' =>	$temp[$i],
				        'created_at' => date('Y-m-d H:i:s'),
				        'status' => 'Pending'
				    );

				    $this->db->insert('solubility_correction_data', $dataAdd);

	                }
	            }
	        }

			$existingRecord = $this->db->get_where('tasks_queue', array('job_id' => $jobbid,'status'=>'pending'))->row();
			
			
			if (!$existingRecord) {
			    $data = array(
			        'job_id' => $jobbid,
			        'added_to_queue' => 'Yes',
			        'is_custom' => '0',
			        'started_on' => date('Y-m-d H:i:s'),
			        'status' => 'pending'
			    );

			    $this->db->insert('tasks_queue', $data);
				//print_r($this->db->last_query());
				$this->session->set_flashdata('alert-type', 'success');
				$this->session->set_flashdata('alert', 'Job Added in Queue Succesfully...');

				redirect('projects');
				
			}


     	}
        /*------------------------end-------------------------------*/
		
		//$ssh->enablePTY();
		//$ssh->exec('nohup ./sr1 80 4 400 400 20 &');
		
		chmod("/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/$fullname", 0777);

		$contentf = $fname."\t".$Smile."\t".$mvalue;
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch '.$fullname.'; echo "'.$contentf.'" >> '.$fullname.'');
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 ConformerGenerator.py --structures_file '.$fullname.' --n_cores=4 > t.log 2>&1 & echo $!');

		$id = $this->projects_model->createjob([
			'project_id' => $project_code,
			'structure_code' => $structure,
			'inp_filename' => $fullname,
			'inp_value	' => (int) $mvalue,
			'smiles	' => $Smile,
			'know_solubility	' => $kns,
			'hfuss_value	' => $hfvalue,
			'mol_weight	' => $molweight,
			'process_start	' => date('m/d/Y h:i:s a', time()),
			'process_end	' => '',
			'cosmo_status	' => 'Processing',
		]);

		$queryn = $this->db->query("SELECT id FROM jobs_master ORDER BY id DESC LIMIT 1");
		$row_id = $queryn->row();
		$jobbid = $row_id->id;

		if(is_array(@$s_name) && count(@$s_name) >0 ){
            for ($i = 0;$i <= count($s_name);$i++) {
                if (!empty($s_name[$i])) {
                	$dataAdd = array(
			        'job_id' => $jobbid,
			        's_name' => $s_name[$i],
			        's_value' => $s_value[$i],
			        'created_at' => date('Y-m-d H:i:s'),
			        'status' => 'Pending'
			    );

			    $this->db->insert('solubility_correction_data', $dataAdd);

                }
            }
        }

		$this->activity_model->add('DEM File '.$fullname.' Created by User:'.logged('name'), logged('id'));


		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Job Created Succesfully...');

		redirect('projects');

	}

	public function savesolubilitydata(){
		$jobid= $this->input->post('jobid');
		$s_name = $this->input->post('s_name');
		$s_value = $this->input->post('s_value');
		$temp = $this->input->post('temp');
		$type = $this->input->post('type');

		if($type=='1') {
			if(is_array(@$s_name) && count(@$s_name) >0 ){
				/* Delete old---------------------*/
	            $this->db->where_in('job_id', $jobid);
				$this->db->delete('solubility_correction_data');
	            for ($i = 0;$i <= count($s_name);$i++) {

	                if (!empty($s_name[$i])) {
	                	$dataAdd = array(
				        'job_id' => $jobid,
				        's_name' => $s_name[$i],
				        's_value' => $s_value[$i],
				        'temp' => $temp[$i],
				        'created_at' => date('Y-m-d H:i:s'),
				        'status' => 'Pending'
				    );

			    	$this->db->insert('solubility_correction_data', $dataAdd);

                	}
            	}
	        }
	        $this->session->set_flashdata('alert-type', 'success');
			$this->session->set_flashdata('alert', 'Solubility Created Succesfully...');

			redirect('projects');
	    }
	    if($type=='2') {
	    	
	     	$fileName = $_FILES['file']['name'];
		    $uploadData = array();
			if (!empty($fileName)) {
				$config['upload_path'] = './uploads/';
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 2048;
				$config['remove_spaces'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
					$this->session->set_flashdata('alert-type', 'error');
					$this->session->set_flashdata('alert', $this->upload->display_errors());
					redirect('projects');
				    
				} else {
				    $uploadData = $this->upload->data();
				    $fileName = $uploadData['file_name'];
				    $filePath = FCPATH . 'uploads/' . $fileName; // Use FCPATH to get the physical path
				     
				    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
				    // Get the first worksheet
				    $worksheet = $spreadsheet->getActiveSheet();
				    // Get the highest row and column numbers to determine the data range
				    $highestRow = $worksheet->getHighestRow();
				    $highestColumn = $worksheet->getHighestColumn();
				    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

				    // Initialize an array to store the Excel data
				    $data = array();

				    // Iterate through each row and column to extract data
				    for ($row = 1; $row <= $highestRow; ++$row) {
				        $rowData = array();
				        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
				            // Get the cell value
				            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
				            // Add the cell value to the row data array
				            $rowData[] = $cellValue;
				        }
				       
				        $data[] = $rowData;
				    }
				    
			        $header = array_shift($data);
			 	
			        foreach ($data as $row) {
			            $row_data = array_combine($header, $row);
			          	$datasave['job_id'] = $jobid;
			            $datasave['s_name'] = $row_data['Solvent'];
			            $datasave['s_value'] = $row_data['Experimental'];
			            $datasave['temp'] = $row_data['Temp'];
			            $datasave['created_at'] =  date('Y-m-d H:i:s');
			            $datasave['status'] = 'Pending';

			           // Check if 's_name' already exists in the database
					    $existing_data = $this->db->get_where('solubility_correction_data', array('job_id' => $jobid,'s_name' => $row_data['Solvent'],'temp' => $row_data['Temp']))->row_array();
			
					    if (!empty($existing_data)) {
					        // 's_name' already exists, update the record
					        $this->db->where('job_id', $jobid);
					        $this->db->where('s_name', $row_data['Solvent']);
					        $this->db->where('temp', $row_data['Temp']);
					        $this->db->update('solubility_correction_data', $datasave);
					    } else {
					       
					        $this->db->insert('solubility_correction_data', $datasave);
					    }

					    /*--------update data in predicted table----------*/
					    $check_existing_data = $this->db->get_where('solubility_corrected_predicted_data', array('job_id' => $jobid,'ssystem_name' => $row_data['Solvent'],'temp' => $row_data['Temp']))->row_array();
					   
					    if (!empty($check_existing_data)) {
					        // 's_name' already exists, update the record
					        $this->db->where('job_id', $jobid);
					        $this->db->where('ssystem_name', $row_data['Solvent']);
					        $this->db->where('temp', $row_data['Temp']);
					        $this->db->update('solubility_corrected_predicted_data',['known_solubility'=> $row_data['Experimental']]);
					    } 

					    
			        }
			        
					$this->session->set_flashdata('alert-type', 'success');
					$this->session->set_flashdata('alert', 'Uploaded Successfully...');
					redirect('projects');

				}

		       

	    	} else{
	    		$this->session->set_flashdata('alert-type', 'error');
				$this->session->set_flashdata('alert', 'No file uploaded.');
				redirect('projects');
	    	}
	    }
	    if($type=='3') {
	    	
	     	$fileName = $_FILES['file']['name'];

		    $uploadData = array();
			if (!empty($fileName)) {
				$config['upload_path'] = './uploads/';
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = 2048;
				$config['remove_spaces'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('file')) {
				    json_error("File Upload Error", $this->upload->display_errors(), 400);
				} else {
				    $uploadData = $this->upload->data();
				    $fileName = $uploadData['file_name'];
				    $filePath = FCPATH . 'uploads/' . $fileName; // Use FCPATH to get the physical path
				     
				    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
				    // Get the first worksheet
				    $worksheet = $spreadsheet->getActiveSheet();
				    // Get the highest row and column numbers to determine the data range
				    $highestRow = $worksheet->getHighestRow();
				    $highestColumn = $worksheet->getHighestColumn();
				    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

				    // Initialize an array to store the Excel data
				    $data = array();

				    // Iterate through each row and column to extract data
				    for ($row = 1; $row <= $highestRow; ++$row) {
				        $rowData = array();
				        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
				            // Get the cell value
				            $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
				            // Add the cell value to the row data array
				            $rowData[] = $cellValue;
				        }
				       
				        $data[] = $rowData;
				    }
				    
			        $header = array_shift($data);
			 
			        foreach ($data as $row) {
			            $row_data = array_combine($header, $row);
			            $datasave['job_id'] = $jobid;
			            $datasave['s_name'] = $row_data['Solvent'];
			            $datasave['s_value'] = $row_data['Experimental'];
			            $datasave['temp'] = $row_data['Temp'];
			            $datasave['created_at'] =  date('Y-m-d H:i:s');
			            $datasave['status'] = 'Pending';
			          
			           // Check if 's_name' already exists in the database
					    $existing_data = $this->db->get_where('solubility_correction_data', array('job_id' => $jobid,'s_name' => $row_data['Solvent'],'temp' => $row_data['Temp']))->row_array();

					    if (!empty($existing_data)) {
					        // 's_name' already exists, update the record
					        $this->db->where('job_id', $jobid);
					        $this->db->where('s_name', $row_data['Solvent']);
					        $this->db->where('temp', $row_data['Temp']);
					        $this->db->update('solubility_correction_data', $datasave);
					    } else {
					        
					

					        $this->db->insert('solubility_correction_data', $datasave);
					    }

					    /*--------update data in predicted table----------*/
					    $check_existing_data = $this->db->get_where('solubility_corrected_predicted_data', array('job_id' => $jobid,'ssystem_name' => $row_data['Solvent'],'temp' => $row_data['Temp']))->row_array();
					   
					    if (!empty($check_existing_data)) {
					        // 's_name' already exists, update the record
					        $this->db->where('job_id', $jobid);
					        $this->db->where('ssystem_name', $row_data['Solvent']);
					        $this->db->where('temp', $row_data['Temp']);
					        $this->db->update('solubility_corrected_predicted_data',['known_solubility'=> $row_data['Experimental']]);
					    } 
			        }
			        
					$this->session->set_flashdata('alert-type', 'success');
					$this->session->set_flashdata('alert', 'Uploaded Successfully...');
					redirect('projects');

				}

		       

	    	} else{
	    		$this->session->set_flashdata('alert-type', 'error');
				$this->session->set_flashdata('alert', 'No file uploaded.');
				redirect('projects');
	    	}
	    }

	}
	public function downloadSampleExcel() {
        // Load the file helper
        $this->load->helper('file');

        // Set the file path
        $file_path = 'uploads/sample.xlsx'; // Adjust the path as needed
        //dd($file_path);

        // Check if the file exists
        if (file_exists($file_path)) {
            // Set headers for force download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            // File not found, handle the error or redirect as needed
            echo 'File not found!';
        }
    }

	public function getpredictedSol(){
		$job_id = $this->input->post('job_id');
		$this->db->select('job_id,ssystem_name,
               MAX(CASE WHEN temp = "10" THEN known_solubility END) AS known_solubility_10,
               MAX(CASE WHEN temp = "10" THEN predicted_solubility END) AS predicted_solubility_10,
               MAX(CASE WHEN temp = "10" THEN corrected_solubility END) AS corrected_solubility_10,
               MAX(CASE WHEN temp = "25" THEN known_solubility END) AS known_solubility_25,
               MAX(CASE WHEN temp = "25" THEN predicted_solubility END) AS predicted_solubility_25,
               MAX(CASE WHEN temp = "25" THEN corrected_solubility END) AS corrected_solubility_25,
               MAX(CASE WHEN temp = "50" THEN known_solubility END) AS known_solubility_50,
               MAX(CASE WHEN temp = "50" THEN predicted_solubility END) AS predicted_solubility_50,
               MAX(CASE WHEN temp = "50" THEN corrected_solubility END) AS corrected_solubility_50');
			$this->db->from('solubility_corrected_predicted_data');
			$this->db->where('job_id', $job_id);
			$this->db->group_by('ssystem_name');
			$this->db->order_by('ssystem_name', 'ASC');
			$query = $this->db->get();

			if (!$query) {
			    $error = $this->db->error();
			    echo 'Query error: ' . $error['message'];
			    // Handle the error as needed
			} else {
			    $data = $query->result();

			    // Set the response header to ensure JSON output
			    header('Content-Type: application/json');

			    // Echo the JSON-encoded data
			    echo json_encode($data, JSON_PRETTY_PRINT);
			}
		
					

	}
	public function getpredictedSolnew(){
		$job_id = $this->input->post('job_id');
		$filterValues = $this->input->post('filterValues');
		// Check if filterValues is not null
		if (!empty($filterValues)) {
		    // Construct the SQL query with dynamic filtering based on filterValues
		    $this->db->select('job_id,ssystem_name,
		                       MAX(CASE WHEN temp = "10" THEN known_solubility END) AS known_solubility_10,
		                       MAX(CASE WHEN temp = "10" THEN predicted_solubility END) AS predicted_solubility_10,
		                       FORMAT(MAX(CASE WHEN temp = "10" THEN corrected_solubility END), 3) AS corrected_solubility_10,
		                       MAX(CASE WHEN temp = "25" THEN known_solubility END) AS known_solubility_25,
		                       MAX(CASE WHEN temp = "25" THEN predicted_solubility END) AS predicted_solubility_25,
		                       FORMAT(MAX(CASE WHEN temp = "10" THEN corrected_solubility END), 3) AS corrected_solubility_25,
		                       MAX(CASE WHEN temp = "50" THEN known_solubility END) AS known_solubility_50,
		                       MAX(CASE WHEN temp = "50" THEN predicted_solubility END) AS predicted_solubility_50,
		                      FORMAT(MAX(CASE WHEN temp = "10" THEN corrected_solubility END), 3) AS corrected_solubility_50');
		    $this->db->from('solubility_corrected_predicted_data');
		    $this->db->where('job_id', $job_id);
		    $this->db->group_by('ssystem_name');
		    $this->db->order_by('ssystem_name', 'ASC');

		    // Apply filters dynamically based on filterValues
		    foreach ($filterValues as $column => $values) {
		    	$parts = explode('_', $column);
		    	$columnName = @$parts[0] . '_' . @$parts[1];
		    	$temp = @$parts[2];
		        $max = @$values['max'];
		        $min = @$values['min'];
		        // Check if max and min values are not null
		        if ($max !== null && $min !== null) {
		            // Add filter conditions to the query
		            $this->db->where("$columnName >= $min");
		            $this->db->where("$columnName <= $max");
		            //$this->db->where_in('temp', [$temp, '10', '25', '50']);
		            
		        }
		    }

		    $query = $this->db->get();

		    if (!$query) {
		        $error = $this->db->error();
		        echo 'Query error: ' . $error['message'];
		        // Handle the error as needed
		    } else {
		        $data = $query->result();

		        // Set the response header to ensure JSON output
		        header('Content-Type: application/json');

		        // Echo the JSON-encoded data
		        echo json_encode($data, JSON_PRETTY_PRINT);
		    }
		} else{


			$this->db->select('job_id,ssystem_name,
			                   MAX(CASE WHEN temp = "10" THEN known_solubility END) AS known_solubility_10,
			                   MAX(CASE WHEN temp = "10" THEN predicted_solubility END) AS predicted_solubility_10,
			                   FORMAT(MAX(CASE WHEN temp = "10" THEN corrected_solubility END), 3) AS corrected_solubility_10,
			                   MAX(CASE WHEN temp = "25" THEN known_solubility END) AS known_solubility_25,
			                   MAX(CASE WHEN temp = "25" THEN predicted_solubility END) AS predicted_solubility_25,
			                   FORMAT(MAX(CASE WHEN temp = "10" THEN corrected_solubility END), 3) AS corrected_solubility_25,
			                   MAX(CASE WHEN temp = "50" THEN known_solubility END) AS known_solubility_50,
			                   MAX(CASE WHEN temp = "50" THEN predicted_solubility END) AS predicted_solubility_50,
			                   FORMAT(MAX(CASE WHEN temp = "10" THEN corrected_solubility END), 3) AS corrected_solubility_50');
			$this->db->from('solubility_corrected_predicted_data');
			$this->db->where('job_id', $job_id);
			$this->db->group_by('ssystem_name');
			$this->db->order_by('ssystem_name', 'ASC');
			$query = $this->db->get();

			if (!$query) {
			    $error = $this->db->error();
			    echo 'Query error: ' . $error['message'];
			    // Handle the error as needed
			} else {
			    $data = $query->result();

			    // Set the response header to ensure JSON output
			    header('Content-Type: application/json');

			    // Echo the JSON-encoded data
			    echo json_encode($data, JSON_PRETTY_PRINT);
			}
		}
					

	}

	public function run_solubility_correction($id){
		$jobdetails= $this->projects_model->getJobdetails1($id);

		$projectName = $this->projects_model->getProjectName($jobdetails[0]->project_id);

		$solu_correction = $this->projects_model->getPython();
		$this->db->select('id, job_id, ssystem_name, known_solubility, predicted_solubility, corrected_solubility, temp, created_at');
		$this->db->from('solubility_corrected_predicted_data');
		
		$this->db->where('job_id', $id);
		$this->db->order_by('id', 'asc'); // Then by temperature ascending
		$query = $this->db->get();
		$data = $query->result();

		if ($data) {
			$ssh = new Net_SSH2('128.199.31.121',22);
			if (!$ssh->login('chemistry1', 'Ravi@1234')) {
				exit('Login Failed');
			}
	
			$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
		
			$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';
        	
			$file_path = $this->createExcel($data,$projectName);
			$new_file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/'.$projectName.'_new_predictions.xlsx'.
			
			$input_file_path = "FILEFULLPATH";
			$replace_input_file_path = "'".$file_path."'";

			$input_new_file_path = "NEWFILENAME";
			$replace_new_file_path = "'".$new_file_path."'";


			$file_contents = $solu_correction[0]->solu_correction;
			
			$file_contents = str_replace($input_file_path, $replace_input_file_path, $file_contents);
			
			$file_contents = str_replace($input_new_file_path, $replace_new_file_path, $file_contents);
			$file_contents = str_replace('FILEFULLPATH','', $file_contents);
			//dd($file_contents);
			
			
			

			// Escape single quotes in the file contents
			 $fileContents = str_replace("'", "'\\''", $file_contents);

			//echo $file_contents;

			// Execute the Python script from the variable
			$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src; nohup python3 -c \'' . $fileContents . '\'';
			//dd($command);

			$stream = $ssh->exec($command);
			// Check if $stream has output
			while (empty($stream)) {
			    // Add a delay before checking again
			    usleep(500000); // 0.5 seconds

			    // Retrieve the updated $stream
			    $stream = $ssh->exec($command);
			}
			// Extract numeric values from the JSON string using regular expression
			preg_match_all('/[\d.]+/', $stream, $matches);

			// Combine the matched values into a single string
			$cleanedString = implode(', ', $matches[0]);


			$finalDatas = explode(',',$cleanedString);


			$successCount = 0;
			if (!empty($finalDatas)) {
			    foreach ($finalDatas as $i => $value) {
			        $index = $i; // Assuming your index starts from 0

			        // Check if the index exists in your $data array
			        if (isset($data[$index])) {
			            $row = $data[$index];
			            //dd($row);
			            // Update the record with the corresponding index from $dataj
			            $updated_data = [
			                'corrected_solubility' => $value,
			            ];

			            $this->db->where('id', $row->id);
			            $result = $this->db->update('solubility_corrected_predicted_data', $updated_data);
			            if ($result) {
			                $successCount++;
			            }

			        } else {
			            // Handle the case where the index does not exist in $data
			            echo "";
			        }
			    }
			 if ($successCount == count($finalDatas)) {
		        echo "Done";
		    } else {
		        echo "Done";
		    }
    
			} else {
			    // Handle the case where $dataj is empty or not valid JSON
			    echo "Error decoding JSON data from $stream.";
			}
	    } else {
	        // No data found, display error message or handle accordingly
	        echo "No data found in the table.";
	    }

	}
	private function createExcel($data,$projectName) {
        // Create a new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        
        // Set the active sheet
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set column headers
        $sheet->setCellValue('A1', 'Experimental');
        $sheet->setCellValue('B1', 'Predicted');
        
        //cted solubility array
        
        // Add data to the Excel file
        $row = 2; // Start from row 2 for data
        foreach ($data as $solubility) {
        	$known_solubility  = ($solubility->known_solubility =='0') ? '0' : $solubility->known_solubility;
        	$predicted_solubility  = ($solubility->predicted_solubility =='0') ? '0' : $solubility->predicted_solubility;
            $sheet->setCellValue('A' . $row, $known_solubility);
            $sheet->setCellValue('B' . $row, $predicted_solubility);
            $row++;
        }
        
 
        // Save the Excel file
        $fname = $projectName.'.xlsx';
        $directory_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/'.$fname; //
        $writer = new Xlsx($spreadsheet);
        $file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/'.$fname; // Adjust the path and file name as needed
        $writer->save($file_path);
        chmod("/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/$fname", 0777);

        /*------------Save blanl excel------------*/
        // Create a new spreadsheet
	    $blankSpreadsheet = new Spreadsheet();

	    // Get the active sheet
	    $sheet = $blankSpreadsheet->getActiveSheet();

	    // Set column headers
	    $sheet->setCellValue('A1', 'Experimental');
	    $sheet->setCellValue('B1', 'Predicted');
	    $sheet->setCellValue('C1', 'Corrected Predicted');
	     // Add data to the Excel file
        $row = 2; // Start from row 2 for data
        foreach ($data as $solubility) {
        	$known_solubility  = ($solubility->known_solubility =='0') ? '0' : $solubility->known_solubility;
        	$predicted_solubility  = ($solubility->predicted_solubility =='0') ? '0' : $solubility->predicted_solubility;
            $sheet->setCellValue('A' . $row, $known_solubility);
            $sheet->setCellValue('B' . $row, $predicted_solubility);
            $sheet->setCellValue('C' . $row, NULL);
            $row++;
        }
	    $blankWriter = new Xlsx($blankSpreadsheet);

	    // Save the blank Excel file
	    $blankFileName = $projectName .'_new_predictions.xlsx';
	    $blankFilePath = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/' . $blankFileName;
	    $blankWriter->save($blankFilePath);
	    chmod($blankFilePath, 0777);
        return $file_path;
    }
	private function validateColumns($requiredColumns, $actualColumns) {
        return empty(array_diff($requiredColumns, $actualColumns));
    }

    private function insertData($sheet) {
        // Assuming your table name is 'excel_data'
        $data = [];
        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data[] = $rowData;
        }
        $this->db->insert_batch('excel_data', $data);
    }

	public function addSolubiltiy($jobid){

		$this->page_data['jobDetail'] = $this->projects_model->getJobdetails1($jobid);

		$this->page_data['cdata'] = $this->projects_model->getsolvents_all();
		$this->page_data['solubiltyData'] = $this->projects_model->solubiltyDataforCorrection($jobid);
		$this->load->view('projects/addSolubiltiy', $this->page_data);

	}

	public function msave()
	{
		//print_r($_POST);
	

		$connection = @ssh2_connect('128.199.31.121', 22);
		
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}

		$fname=$this->input->post('mname');
		$mvalue=$this->input->post('mvalue');
		$Smile=$this->input->post('Smile');
		$kns=$this->input->post('kns');
		$hfvalue=$this->input->post('hfvalue');
		$molweight=$this->input->post('mweight');

		$ext='.inp';
		$fullname=$fname.$ext;
		$project_code=$this->input->post('project_code');
		$structure=$this->input->post('structure');

		$sftp = new Net_SFTP('128.199.31.121',22);
		if (!$sftp->login('chemistry1', 'Ravi@1234')) {
    	exit('Login Failed');
		}

		$directory_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$fname; // the path of the directory you want to check
		if ($sftp->file_exists($directory_path)) { // check if the directory exists
    	//echo "The directory $directory_path exists.";
		$this->session->set_flashdata('alert-type', 'danger');
		$this->session->set_flashdata('alert', 'Give Molecule Name (directory already exists on server)');
		
		redirect('projects/submit/'.$project_code);
		}
		
		//$ssh->enablePTY();
		//$ssh->exec('nohup ./sr1 80 4 400 400 20 &');
		
		chmod("/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/$fullname", 0777);
		
		$contentf = $fname."\t".$Smile."\t".$mvalue;
		$ssh->exec('cd einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch '.$fullname.'; echo "'.$contentf.'" >> '.$fullname.'');
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 ConformerGenerator.py --structures_file '.$fullname.' --n_cores=4 > t.log 2>&1 & echo $!');
		//$command = ('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 ConformerGenerator.py --structures_file '.$fullname.' --n_cores=4 > t.log 2>&1 & echo $!');
		//$pid = $ssh->exec($command);

		$id = $this->projects_model->createjob([
			'project_id' => $project_code,
			'structure_code' => $structure,
			'inp_filename' => $fullname,
			'inp_value	' => (int) $mvalue,
			'smiles	' => $Smile,
			'know_solubility	' => $kns,
			'hfuss_value	' => $hfvalue,
			'mol_weight	' => $molweight,
			'process_start	' => date('m/d/Y h:i:s a', time()),
			'process_end	' => '',
			'cosmo_status	' => 'Processing',
		]);

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Job Created Succesfully...');

		redirect('projects/submit/'.$project_code);

	}
public function activity($id) {


	$this->page_data['jstatus'] = $this->projects_model->getJobdetails1($id);
		

	$this->load->view('projects/activity', $this->page_data);

}
	public function jstatus($id)
	{

		//ifPermissions('users_view');

		$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);
		
		

		$this->load->view('projects/jstatus', $this->page_data);

	}

//Activity For Pure_68

public function pure_68($id,$stype) {

	//echo $id;
		//print_r($_POST);
		$info="";
		$jobdetails= $this->projects_model->getJobdetails1($id);
		//echo $jobdetails[0]->project_id;
		$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
		

		$connection = @ssh2_connect('128.199.31.121', 22);
		//include(APPPATH . 'third_party/phpseclib1.0.20/Net/SSH2.php');
		
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
		
		//$ssh->exec('cd einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch "tt.py"; echo "test" >> "tt.py"');
		// Directory and file details
$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';
$file = 'existing_file.py';

$sftp = new Net_SFTP('128.199.31.121',22);
if (!$sftp->login('chemistry1', 'Ravi@1234')) {
    exit('Login Failed');
}

// Define file path and contents
$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/existing_file.py';
$file_contents = 'This is some example content.1';

// Open file and print contents
if ($contents = $sftp->get($file_path)) {
    //echo $contents;

			// new code to insert
$solvents = array("phenol", "1-Octanol", "methylcyclohexane", "n,n-dimethylacetamide", "methane1", "methylacetate", "tetrachloroethane", "methane", "propylene_glycol", "chcl3", "n-propylamine", "propanol", "cyclohexanone", "2-chlorotoluene", "aceticacid", "1-chlorobutane", "1-butanol", "1,2-dichlorobenzene", "1,2-Dimethoxyethane", "hexane", "acetonitrile", "ethanol");
//$solvents = array("phenol", "1-Octanol");
$i = 0;

$jid = $this->projects_model->addactivity_log([
	'job_id' => $jobdetails[0]->id,
	'solvent_type' => $stype,
	'solvents_count' => count($solvents),
	'solvent_activity_finished' => '',
	'process_start	' => date('m/d/Y h:i:s a', time()),
	'status' => 'Pending'

]);

foreach ($solvents as $item) {

// Define search and replace strings
$search_string = "new content";
$replace_string = "crs.add_molecule(['".$item."/COSMO_TZVPD/".$item."_c000.orcacosmo'])";

$input_string = "INPUT_COSMO";
$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
// Read file contents
$file_contents = $sftp->get($file_path);

// Replace search string with replace string
$file_contents = str_replace($search_string, $replace_string, $file_contents);
$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
// Upload updated contents to server
if (!$sftp->put($file_path, $file_contents)) {
    exit('Replace failed');
}

	$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 existing_file.py > /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$item.'.log 2>&1 & echo $!;sleep 5');
	$file_contents = str_replace($replace_string, $search_string, $file_contents);

	// Define file path and contents
	$file_pathlog = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$item.'.log';
	//$file_contents = 'This is some example content.1';
	if (!$sftp->put($file_path, $file_contents)) {
		exit('Replace failed');
	}
// Open file and print contents
	if ($contentslog = $sftp->get($file_pathlog)) {
	//echo "<br><h2>".$item."</h2><br>.$contentslog.<br>";
	//$rdetails = print("<pre>".print_r($contentslog,true)."</pre>");
	//$obj = json_decode($contentslog);
	//$dataj = json_decode($contentslog, true);

	$dataj= json_decode($contentslog, true);

// Accessing the value for "data1"
$data1 = $dataj['data1'];
$data2 = $dataj['data2'];
$data3 = $dataj['data3'];
$data4 = $dataj['data4'];
$data5 = $dataj['data5'];
$data6 = $dataj['data6'];


	$id = $this->projects_model->createjobresults([
		'job_id' => $jobdetails[0]->id,
		'solvents' => $item,
		'result_type' => $stype,
		'pure_data1' => $data1[0] . ", " . $data1[1] ,
		'pure_data2' => $data2[0] . ", " . $data2[1] ,
		'pure_data3' => $data3[0] . ", " . $data3[1] ,
		'pure_data4' => $data4[0] . ", " . $data4[1] ,
		'pure_data5' => $data5[0] . ", " . $data5[1] ,
		'pure_data6' => $data6[0] . ", " . $data6[1] ,
		
		'input_temp_10' => 'Yes',
		'input_temp_20	' => 'Yes',
		'input_temp_50	' => 'Yes',
		'solvent_result_name	' =>$fname,
		'solvent_result	' => $contentslog,
		'processed_on	' => date('m/d/Y h:i:s a', time()),
	]);

	$this->db->where('id', $jid);
	$this->db->update('job_results_count', array('solvent_activity_finished'=>$i+1,'process_end'=>date('m/d/Y h:i:s a', time())));
	
	if(count($solvents)==$i+1) {
		$this->db->where('id', $jid);
		$this->db->update('job_results_count', array('status'=>'Completed'));

	}
	$array_product [$i]= $item;
	$i++;
	}


}
// Close connection
$ssh->disconnect();
$sftp->disconnect();
} else {
    exit('File open failed');
}


}

//Activity For Binary_1085

public function binary_1085($id,$stype) {

	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

	$info="";
	$jobdetails= $this->projects_model->getJobdetails1($id);
	//echo $jobdetails[0]->project_id;
	$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
	

	$connection = @ssh2_connect('128.199.31.121', 22);
	//include(APPPATH . 'third_party/phpseclib1.0.20/Net/SSH2.php');

	
	$ssh = new Net_SSH2('128.199.31.121',22);
	if (!$ssh->login('chemistry1', 'Ravi@1234')) {
		exit('Login Failed');
	}
	$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
	
	//$ssh->exec('cd einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch "tt.py"; echo "test" >> "tt.py"');
	// Directory and file details
$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';
$file = 'existing_file.py';

$sftp = new Net_SFTP('128.199.31.121',22);
if (!$sftp->login('chemistry1', 'Ravi@1234')) {
exit('Login Failed');
}

// Define file path and contents
$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/binary_file.py';
$file_contents = 'This is some example content.1';

// Open file and print contents
if ($contents = $sftp->get($file_path)) {
//echo $contents;

		// new code to insert
$solvents = array("phenol", "1-Octanol", "methylcyclohexane", "n,n-dimethylacetamide", "methane1", "methylacetate", "tetrachloroethane", "methane", "propylene_glycol", "chcl3", "n-propylamine", "propanol", "cyclohexanone", "2-chlorotoluene", "aceticacid", "1-chlorobutane", "1-butanol", "1,2-dichlorobenzene", "1,2-Dimethoxyethane", "hexane", "acetonitrile", "ethanol");
$//solvents = array("phenol", "1-Octanol");
$i2 = 0;

$jid = $this->projects_model->addactivity_log([
'job_id' => $jobdetails[0]->id,
'solvent_type' => $stype,
'solvents_count' => count($solvents),
'solvent_activity_finished' => '',
'process_start	' => date('m/d/Y h:i:s a', time()),
'status' => 'Pending'

]);

//foreach ($solvents as $item) {

	// Define the number of rows and columns in the matrix
$rows = count($solvents);
$cols = count($solvents);

// Define an empty matrix array

// Loop through each row in the matrix
for ($i = 0; $i < $rows; $i++) {
    // Define an empty row array
    $row = array();
    $rowe= array();
    // Loop through each column in the matrix
    for ($j = 0; $j < $cols; $j++) {
        // Concatenate the corresponding values and add to the row array
       if ($solvents[$i]!=$solvents[$j]) {
        //$row[] = $solvents[$i] .'->'. $solvents[$j]."<br>";

    
// Define search and replace strings
$search_string = "INPUT_COSMO";
$replace_string = "mol_structure_list_0 = ['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";

$input_string = "NEW_CONTENT1";
$input_string1 = "NEW_CONTENT2";
//$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
$replace_input_string ="crs.add_molecule(['".$solvents[$i]."/COSMO_TZVPD/".$solvents[$i]."_c000.orcacosmo'])";
$replace_input_string1 ="crs.add_molecule(['".$solvents[$j]."/COSMO_TZVPD/".$solvents[$j]."_c000.orcacosmo'])";
// Read file contents
$file_contents = $sftp->get($file_path);

// Replace search string with replace string
$file_contents = str_replace($search_string, $replace_string, $file_contents);
$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);

//$file_contents = $sftp->get($file_path);
//
//echo $file_contents;
// Upload updated contents to server
if (!$sftp->put($file_path, $file_contents)) {
exit('Replace failed');
}

$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 binary_file.py > /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$solvents[$i].'.log 2>&1 & echo $!;sleep 5');
$file_contents = str_replace($replace_string, $search_string, $file_contents);
$file_contents = str_replace($replace_string,$search_string, $file_contents);
$file_contents = str_replace($replace_input_string,$input_string, $file_contents);
$file_contents = str_replace($replace_input_string1,$input_string1, $file_contents);
// Define file path and contents
$file_pathlog = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$solvents[$i].'.log';
//$file_contents = 'This is some example content.1';
if (!$sftp->put($file_path, $file_contents)) {
	exit('Replace failed');
}
// Open file and print contents
if ($contentslog = $sftp->get($file_pathlog)) {
//echo "<br><h2>".$item."</h2><br>.$contentslog.<br>";
//$rdetails = print("<pre>".print_r($contentslog,true)."</pre>");

$dataj= json_decode($contentslog, true);


// Accessing the value for "data1"
$data1 = $dataj['data1'];
$data2 = $dataj['data2'];
$data3 = $dataj['data3'];
$data4 = $dataj['data4'];
$data5 = $dataj['data5'];
//$data6 = $dataj['data6'];

$id = $this->projects_model->createjobresults([
	'job_id' => $jobdetails[0]->id,
	'solvents' => $solvents[$i].'->'.$solvents[$j],
	'result_type' => $stype,
	'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2] ,
	'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2] ,
	'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data1[2] ,
	'pure_data4' => $data4[0] . ", " . $data4[1] . ", " . $data1[2] ,
	'pure_data5' => $data5[0] . ", " . $data5[1] . ", " . $data1[2] ,
	//'pure_data6' => $data6[0] . ", " . $data6[1] ,
	'input_temp_10' => 'Yes',
	'input_temp_20	' => 'Yes',
	'input_temp_50	' => 'Yes',
	'solvent_result_name	' =>$fname,
	'solvent_result	' => $contentslog,
	'processed_on	' => date('m/d/Y h:i:s a', time()),
]);

$this->db->where('id', $jid);
$this->db->update('job_results_count', array('solvent_activity_finished'=>$i2+1,'process_end'=>date('m/d/Y h:i:s a', time())));

if(count($solvents)==$i2+1) {
	$this->db->where('id', $jid);
	$this->db->update('job_results_count', array('status'=>'Completed'));

}
$replace_input_string ="";
$array_product [$i]= $solvents[$i];
$i2++;

}
}
}


}
// Close connection
$ssh->disconnect();
$sftp->disconnect();
} else {
exit('File open failed');
}


}

//Activity For Tertiary-16400

public function tertiary_16400($id,$stype) {

	error_reporting(E_ALL);
ini_set('display_errors', 'On');

	
	$info="";
	$jobdetails= $this->projects_model->getJobdetails1($id);
	//echo $jobdetails[0]->project_id;
	$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
	

	$connection = @ssh2_connect('128.199.31.121', 22);
	//include(APPPATH . 'third_party/phpseclib1.0.20/Net/SSH2.php');

	
	$ssh = new Net_SSH2('128.199.31.121',22);
	if (!$ssh->login('chemistry1', 'Ravi@1234')) {
		exit('Login Failed');
	}
	$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
	
	//$ssh->exec('cd einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch "tt.py"; echo "test" >> "tt.py"');
	// Directory and file details
$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';
$file = 'existing_file.py';

$sftp = new Net_SFTP('128.199.31.121',22);
if (!$sftp->login('chemistry1', 'Ravi@1234')) {
exit('Login Failed');
}

// Define file path and contents
$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/tertiary-16400.py';
$file_contents = 'This is some example content.1';

// Open file and print contents
if ($contents = $sftp->get($file_path)) {
//echo $contents;

		// new code to insert
$solvents = array("phenol", "1-Octanol", "methylcyclohexane", "n,n-dimethylacetamide", "methane1", "methylacetate", "tetrachloroethane", "methane", "propylene_glycol", "chcl3", "n-propylamine", "propanol", "cyclohexanone", "2-chlorotoluene", "aceticacid", "1-chlorobutane", "1-butanol", "1,2-dichlorobenzene", "1,2-Dimethoxyethane", "hexane", "acetonitrile", "ethanol");
//$solvents = array("phenol", "1-Octanol","methylcyclohexane");
$k2 = 0;

$jid = $this->projects_model->addactivity_log([
'job_id' => $jobdetails[0]->id,
'solvent_type' => $stype,
'solvents_count' => count($solvents),
'solvent_activity_finished' => '',
'process_start	' => date('m/d/Y h:i:s a', time()),
'status' => 'Pending'

]);

//foreach ($solvents as $item) {

	// Initialize an empty matrix
$matrix = array();

// Create all possible combinations of array elements
$combinations = array();
foreach ($solvents as $element1) {
    foreach ($solvents as $element2) {
        foreach ($solvents as $element3) {
            $combinations[] = array($element1, $element2, $element3);
        }
    }
}

// Split the combinations into chunks of 3 and add them to the matrix
foreach (array_chunk($combinations, 3) as $chunk) {
    $matrix[] = $chunk;
}

// Print the matrix
foreach ($matrix as $row) {
    foreach ($row as $element) {
        if(($element[0]<>$element[1]) && ($element[0]<>$element[2]) && ($element[1]<>$element[2]))  {
       // echo "XYZ ".$element[0] . " -> " . $element[1] . " -> " . $element[2] . " <br>";
        //echo  "crs.add_molecule(['".$element[0]."/COSMO_TZVPD/".$element[0]."_c000.orcacosmo'])<br>";
        //echo  "crs.add_molecule(['".$element[1]."/COSMO_TZVPD/".$element[1]."_c000.orcacosmo'])<br>";
        //echo  "crs.add_molecule(['".$element[2]."/COSMO_TZVPD/".$element[2]."_c000.orcacosmo'])<br>";
       // echo "<hr>";
    
 
		// Define search and replace strings
$search_string = "INPUT_COSMO";
$replace_string = "mol_structure_list_0 = ['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";

$input_string = "NEW_CONTENT1";
$input_string1 = "NEW_CONTENT2";
$input_string2 = "NEW_CONTENT3";
//$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
$replace_input_string ="crs.add_molecule(['".$element[0]."/COSMO_TZVPD/".$element[0]."_c000.orcacosmo'])";
$replace_input_string1 ="crs.add_molecule(['".$element[1]."/COSMO_TZVPD/".$element[1]."_c000.orcacosmo'])";
$replace_input_string2 ="crs.add_molecule(['".$element[2]."/COSMO_TZVPD/".$element[2]."_c000.orcacosmo'])";
// Read file contents
$file_contents = $sftp->get($file_path);

// Replace search string with replace string
$file_contents = str_replace($search_string, $replace_string, $file_contents);
$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
$file_contents = str_replace($input_string2, $replace_input_string2, $file_contents);
		
// Read file contents
//$file_contents = $sftp->get($file_path);

//echo $file_contents;

// Upload updated contents to server
if (!$sftp->put($file_path, $file_contents)) {
exit('Replace failed');
}


$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 tertiary-16400.py > /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$element[0].'.log 2>&1 & echo $!;sleep 5');

//$file_contents = str_replace($replace_string, $search_string, $file_contents);

$file_contents = str_replace($replace_string,$search_string, $file_contents);
$file_contents = str_replace($replace_input_string,$input_string, $file_contents);
$file_contents = str_replace($replace_input_string1,$input_string1, $file_contents);
$file_contents = str_replace($replace_input_string2,$input_string2, $file_contents);
// Define file path and contents
$file_pathlog = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$element[0].'.log';
//$file_contents = 'This is some example content.1';
if (!$sftp->put($file_path, $file_contents)) {
	exit('Replace failed');
}
// Open file and print contents
if ($contentslog = $sftp->get($file_pathlog)) {
//echo "<br><h2>".$item."</h2><br>.$contentslog.<br>";
//$rdetails = print("<pre>".print_r($contentslog,true)."</pre>");

$dataj= json_decode($contentslog, true);


// Accessing the value for "data1"
$data1 = $dataj['data1'];
$data2 = $dataj['data2'];
$data3 = $dataj['data3'];
$data4 = $dataj['data4'];
$data5 = $dataj['data5'];
//$data6 = $dataj['data6'];

$id = $this->projects_model->createjobresults([
	'job_id' => $jobdetails[0]->id,
	'solvents' => $element[0].'->'.$element[1].'->'.$element[2],
	'result_type' => $stype,
	'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2] . ", " . $data1[3] ,
	'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2] . ", " . $data1[3] ,
	'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data1[2] . ", " . $data1[3] ,
	'pure_data4' => $data4[0] . ", " . $data4[1] . ", " . $data1[2] . ", " . $data1[3] ,
	'pure_data5' => $data5[0] . ", " . $data5[1] . ", " . $data1[2] . ", " . $data1[3] ,
	//'pure_data6' => $data6[0] . ", " . $data6[1] ,
	'input_temp_10' => 'Yes',
	'input_temp_20	' => 'Yes',
	'input_temp_50	' => 'Yes',
	'solvent_result_name	' =>$fname,
	'solvent_result	' => $contentslog,
	'processed_on	' => date('m/d/Y h:i:s a', time()),
]);

$this->db->where('id', $jid);
$this->db->update('job_results_count', array('solvent_activity_finished'=>$k2+1,'process_end'=>date('m/d/Y h:i:s a', time())));
echo $element[0].'->'.$element[1].'->'.$element[2];

//if(count($solvents)==$k2+1) {
//	$this->db->where('id', $jid);
//	$this->db->update('job_results_count', array('status'=>'Completed'));

//}
$array_product [$k2]= $element[0].'->'.$element[1].'->'.$element[2];
$k2++;
}
}
        
}
}


// Close connection
$ssh->disconnect();
$sftp->disconnect();
} else {
exit('File open failed');
}


}

public function close_session()
{
	session_write_close();

}
public function runact($id) {

session_write_close();


$stype=$this->input->get('status');
$solvent_type=$this->input->post('status');


	if($solvent_type=="Pure_68") {
		$this->pure_68($id,$solvent_type);
		$this->db->where('job_id', $jid);
		$this->db->where('solvent_type', "Pure_68");
		$this->db->update('job_results_count', array('status'=>'Completed'));

		echo 'done';
	}

	if($solvent_type=="Binary_1085") {
		$this->pure_68($id,'Pure_68');
		$this->db->where('job_id', $jid);
		$this->db->where('solvent_type', "Pure_68");
		$this->db->update('job_results_count', array('status'=>'Completed'));


		$this->binary_1085($id,$solvent_type);
		$this->db->where('job_id', $jid);
		$this->db->where('solvent_type', "Binary_1085");
		$this->db->update('job_results_count', array('status'=>'Completed'));
		echo 'done';
		//echo $solvent_type;
	}
	if($solvent_type=="Tertiary-16400") {
		//pure_68($id);
		$this->pure_68($id,'Pure_68');
		$this->db->where('job_id', $jid);
		$this->db->where('solvent_type', "Pure_68");
		$this->db->update('job_results_count', array('status'=>'Completed'));

		$this->binary_1085($id,'Binary_1085');
		$this->db->where('job_id', $jid);
		$this->db->where('solvent_type', "Binary_1085");
		$this->db->update('job_results_count', array('status'=>'Completed'));
		
		$this->tertiary_16400($id,$solvent_type);
		$this->db->where('job_id', $jid);
		$this->db->where('solvent_type', "Tertiary-16400");
		$this->db->update('job_results_count', array('status'=>'Completed'));
		echo 'done';
		
	}


}

public function get_record_count($id)
{

   // $count = $this->db->count_all('job_results'); // replace my_table with the name of your table
   $last_count = $this->session->userdata('last_count');

	$this->db->where('job_id',$id);
	$this->db->from("job_results");
	$count = $this->db->count_all_results();
	//echo $this->db->last_query();
	$this->session->set_userdata('last_count', $count);

    if ($count > $last_count) {
		echo $count;
	} else {
		echo $count;
	}
}

public function get_record_countp($id)
{

   // $count = $this->db->count_all('job_results'); // replace my_table with the name of your table
   $last_count = $this->session->userdata('last_count');

	$this->db->where('job_id',$id);
	$this->db->where('solvent_result =','0');
	$this->db->from("job_results");
	$count = $this->db->count_all_results();
	//echo $this->db->last_query();
	$this->session->set_userdata('last_count', $count);

    if ($count > $last_count) {
		echo $count;
	} else {
		echo '';
	}
}

public function fetch_records($id) {
	//$this->load->model('ExampleModel');
	//$records = $this->ExampleModel->get_records();
	$records = $this->projects_model->check_records_status($id);
	// Send the records back as a JSON response
	//header('Content-Type: application/json');
	echo $records;
}

	public function checkjob($id)
	{
		//$connection = @ssh2_connect('128.199.31.121', 22);
	

		//$this->projects_model->update($id, ['status' => get('status') == 'true' ? 1 : 0 ]);
		$jobdetails = $this->projects_model->getJobstatus($id);
		//print_r($jobdetails);
		$filename=$jobdetails[0]->inp_filename;
		$fname = pathinfo($filename, PATHINFO_FILENAME);
		$fcosmoname="COSMO_TZVPD";
		$cosmofile_name=$fname."_c000.orcacosmo";


		$sftp = new Net_SFTP('128.199.31.121',22);
		if (!$sftp->login('chemistry1', 'Ravi@1234')) {
		exit('Login Failed');
		}

		$directory_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$fname.'/'.$fcosmoname.'/'.$cosmofile_name; // the path of the directory you want to check

		if ($sftp->file_exists($directory_path)) { // check if the directory exists
    	//echo "The directory $directory_path exists.";
		$cosmo_data= $sftp->get($directory_path);

		$data = [
			'process_end	' => date('m/d/Y h:i:s a', time()),
			'cosmo_status	' => 'Cosmo File Generated',
			'cosmo_data	' => $cosmo_data,
        ];
        $this->db->where('id', $id);
        $this->db->update('jobs_master', $data);
        //echo 'order has successfully been updated';
		echo 'done';
		//$this->session->set_flashdata('alert-type', 'danger');
		//$this->session->set_flashdata('alert', 'Give Molecule Name (directory already exists on server)');
		
		//redirect('projects/submit/'.$project_code);
		}
		else {echo "not done";}

		
	}

public function dpdf($id) {

	$data_user = array();
	
	$jobdetails = $this->projects_model->getJobstatus($id);

	$project_details = $this->projects_model->getById($jobdetails[0]->project_id);

	$jbdetails ="<h2>Project Name :".$project_details->project_name.", Project Code : ".$project_details->project_code."</h2>";
	$jbdetails .="<h2>PDF Generated on :".date('m/d/Y h:i:s a', time())."</h2>";
	$jbdetails .= "<pre>".print_r($jobdetails[0]->cosmo_data,true)."</pre>";
	 
	$html = $jbdetails;
	$this->load->library('pdf');
	$this->dompdf->loadHtml($html);
	$this->dompdf->setPaper('A4', 'landscape');
	$this->dompdf->render();
	$this->dompdf->stream($project_details->project_name.".pdf", array("Attachment"=>0));

}

public function dsheet($id) {
	
	$this->load->library('excel');
echo $id;

}

public function cview()
    {
       $this->load->view('projects/chart');
    }

public function scatter() {

	$data['data'] = $this->projects_model->get_scatterdata();
	$this->load->view('projects/scatter', $data);

}

public function cosmo()
{
	//ifPermissions('projects_list');
	//$this->page_data['projects'] = $this->projects_model->get();
	$this->load->view('projects/cosmo');
}

public function connectsshpara() {
	$data['output'] = '';
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
// Set the connection parameters
$host = 'paramutkarsh.cdac.in';
$username = 'ravikumar';
$port = 22;
$private_key = '/var/www/html/smt/param.ppk';
$passphrase = 'ravikumar@@321'; // leave blank if none

// Connect to the remote server
$connection = ssh2_connect($host, $port);
if (!$connection) {
    echo "Could not connect to the remote server.";
    exit();
}

// Authenticate using the private key
if (!ssh2_auth_pubkey_file($connection, $username, $private_key . '.pub', $private_key, $passphrase)) {
    echo "Authentication failed.";
    exit();
}

echo "Successfully connected to the remote server!";



	// Get the SSH credentials from the user input
	$host = $this->input->post('host');
	$username = $this->input->post('username');
	$password = "ravikumar@@321";
	$path = $this->input->post('path');
	//$command = $this->input->post('command');

	// Connect to the remote server via SSH
	$connection = ssh2_connect($host, 22);
	$private_key_path = '/var/www/html/smt';


	

	ssh2_auth_pubkey_file($connection, 'ravikumar', $private_key_path . 'param.ppk', $private_key_path, $password);

	$sftp = ssh2_sftp($connection);

	//PREssh2_auth_password($connection, $username, $password);

	//PRE$sftp = ssh2_sftp($connection);

	$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src; python3 ConformerGenerator.py --structures_file h20.inp --n_cores=4';
$stream = ssh2_exec($connection, $command);
stream_set_blocking($stream, true);
$output = stream_get_contents($stream);
fclose($stream);
echo $output;

$data['output']=$output;


	$this->load->view('projects/remote', $data);
}

public function terminal() 
{

	$this->load->view('projects/terminal');
}

public function remote()
{
	$data['output'] = '';
	$this->load->view('projects/remote', $data);
}


public function deploy_test() {

	
	$connection = @ssh2_connect('128.199.31.121', 22);
	
if (@ssh2_auth_password($connection, 'chemistry1', 'Ravi@1234')) {
 

        $directory = 'einnel/opencosmos/openCOSMO-RS_py/src';

		// Change the working directory
//$directory = '/path/to/directory';
ssh2_exec($connection, "cd $directory");

// Run the command and show the progress
//$python_executable = '/usr/bin/python3'; // Replace with the path to your Python executable
$command = "python3 ConformerGenerator.py --structures_file h20.inp 4";
$stream = ssh2_exec($connection, $command);
stream_set_blocking($stream, true);
while ($line = fgets($stream)) {
    echo $line;
}
fclose($stream);

// Close the connection
ssh2_disconnect($connection);

} else {
    die('Authentication Failed...');
}
    
}



public function connect($hostname, $username, $password)
{
    $ssh = ssh2_connect($hostname);
    ssh2_auth_password($ssh, $username, $password);

    $stream = ssh2_exec($ssh, 'ls -la');
    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream);

    ssh2_disconnect($ssh);

    return $output;
}


public function sshcon()
{

	//$this->load->library('ssh2');
	
	$output = ssh2_connect('128.199.31.121', 'chemistry1', 'Ravi@1234');
	echo $output;
exit;
// Set the connection details
$config = array(
    'hostname' => '128.199.31.121',
    'username' => 'chemistry1',
    'password' => 'Ravi@1234'
);

// Connect to the remote server
$this->ssh->connect($config);

// Execute the command with progress output
$command = 'ls -l';
$stream = $this->ssh->exec($command);

// Parse the progress information
$total_time = 0;
while (!$this->ssh->eof($stream)) {
    $output = $this->ssh->read($stream);
    if (preg_match('/^progress:(\d+)\/(\d+)/', $output, $matches)) {
        $current_time = (int)$matches[1];
        $total_time = (int)$matches[2];
        $progress = ($current_time / $total_time) * 100;
        echo "Progress: " . number_format($progress, 2) . "%\n";
    } else {
        // Display the output
        echo $output;
    }
}

// Disconnect from the remote server
$this->ssh->disconnect();


}

public function sshcosmo()
{

	// Load SSH Library
$this->load->library('ssh');

// SSH Configuration
$config = array(
    'hostname' => 'your_server_ip_or_hostname',
    'username' => 'your_ssh_username',
    'password' => 'your_ssh_password',
    'port'     => 22,
    'debug'    => FALSE
);

// Connect to Server
if (!$this->ssh->connect($config)) {
    echo 'Unable to connect to server.';
}

// Run Command
$command = 'your_command_here';
$output = $this->ssh->exec($command, function($data){
    // Progress percentage of command execution time
    echo round($data['percentage'], 2) . '% complete';
});

// Show Final Result
echo $output;

// Close Connection
$this->ssh->close();


}

public function generate()
    {
        $set1Start = $this->input->post('set1_start');
	$selapi_10_operator=$this->input->post('selapi_10_operator');
	$selapi_50_operator=$this->input->post('selapi_50_operator');

	$selimp1_10_operator =$this->input->post('selimp1_10_operator');
	$selimp1_50_operator =$this->input->post('selimp1_50_operator');

	$selimp2_10_operator =$this->input->post('selimp2_10_operator');
	$selimp2_50_operator =$this->input->post('selimp2_50_operator');

        $set1End = $this->input->post('set1_end');
        $set2Start = $this->input->post('set2_start');
        $set2End = $this->input->post('set2_end');
        $set3Start = $this->input->post('set3_start');
        $set3End = $this->input->post('set3_end');

		$recordsp = $this->input->post('recordsp');


		$data = array();
    $this->load->database();
    //$query = $this->db->query("SELECT Solvent_System, log10_10C, log10_50C, IMP1_mgml_10C,IMP1_50C_mg_ml,IMP2_mgml_10C,IMP2_mgml_50C FROM sresults");
    //$result = $query->result();
 

        $chartModel = new projects_model();

        $set1Data = $chartModel->getRangeData(1, $set1Start, $selapi_10_operator, $set1End, $selapi_50_operator,$set2Start, $selimp1_10_operator,$set2End, $selimp1_50_operator,$set3Start, $selimp2_10_operator,$set3End, $selimp2_50_operator,$recordsp);
 

		$cdata = $set1Data;
		


$rows = array();
foreach ($cdata as $row) {
    $temp = array();
    $temp[] = array('v' => (string) str_replace('/', '_', $row['Solvent_System']));
    $temp[] = array('v' => (int) $row['10c_mg_ml']);
    $temp[] = array('v' => (int) $row['50C_mg_ml']);
    $temp[] = array('v' => (int) $row['IMP1_mgml_10C']);
	$temp[] = array('v' => (int) $row['IMP1_50C_mg_ml']);
	$temp[] = array('v' => (int) $row['IMP2_mgml_10C']);
	$temp[] = array('v' => (int) $row['IMP2_mgml_50C']);
    $rows[] = array('c' => $temp);
}
$table['rows'] = $rows;
$jsonTable = json_encode($table);
$data['chart_data'] = $jsonTable;

foreach ($cdata as $rowd) {
	$data['sname'][] = $rowd['Solvent_System'];
	$data['l10c_mg_ml'][] = $rowd['10c_mg_ml'];
	$data['l50C_mg_ml'][] = $rowd['50C_mg_ml'];
	$data['IMP1_mgml_10C'][] = $rowd['IMP1_mgml_10C'];
	$data['IMP1_50C_mg_ml'][] = $rowd['IMP1_50C_mg_ml'];
	$data['IMP2_mgml_10C'][] = $rowd['IMP2_mgml_10C'];
	$data['IMP2_mgml_50C'][] = $rowd['IMP2_mgml_50C'];

}

$data['selectq'] ='10C mg_ml : '. $set1Start . ', Range : '. $selapi_10_operator .'<br>50C mg_ml: '. $set1End. ', Range : '.$selapi_50_operator.'<br>IMP1_mgml_10C: '. $set2Start. ', Range : '.$selimp1_10_operator.'<br>IMP1_mgml_50C: '. $set2End. ', Range : '.$selimp1_50_operator.'<br>IMP2_mgml_10C: '. $set3Start. ', Range : '.$selimp2_10_operator.'<br>IMP2_mgml_50C: '. $set3End. ', Range : '.$selimp2_50_operator;

$this->load->view('projects/chart', $data);

       // $this->load->view('projects/chart', ['chartData' => $chartData,'cdata'=>$cdata,'chart_data' => $data, 'options' => $options]);
    }

public function splot()
 {
	$results = $this->projects_model->get_chart_data();
        $data['chart_data'] = $results['chart_data'];
    	//print_r($data['chart_data']);

        $this->load->view('projects/splot', $data);

}

public function cchart()
 {
	$results = $this->projects_model->get_chart_data();
        $data['chart_data'] = $results['chart_data'];
    	//print_r($data['chart_data']);

        $this->load->view('projects/cdata', $data);

}

	public function save()
	{
		ifPermissions('project_add');
		postAllowed();

		$id = $this->projects_model->create([
			'project_name' => post('name'),
			'project_code' => post('pcode'),
			'customer_id' => post('customer'),
			'status' => (int) post('status'),
			
		]);

		
		$this->activity_model->add('New Project '.$id.' Project Created by User:'.logged('name'), logged('id'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'New Project Created Successfully');
		
		redirect('projects');

	}

	public function view($id)
	{

		ifPermissions('users_view');

		$this->page_data['User'] = $this->users_model->getById($id);
		$this->page_data['User']->role = $this->roles_model->getByWhere([
			'id'=> $this->page_data['User']->role
		])[0];
		$this->page_data['User']->activity = $this->activity_model->getByWhere([
			'user'=> $id
		], [ 'order' => ['id', 'desc'] ]);
		$this->load->view('users/view', $this->page_data);

	}

	public function edit($id)
	{

		ifPermissions('users_edit');

		$this->page_data['User'] = $this->users_model->getById($id);
		$this->load->view('users/edit', $this->page_data);

	}


	public function pie_chart_js() {
  
      $query =  $this->db->query("SELECT created_at as y_date, DAYNAME(created_at) as day_name, COUNT(id) as count  FROM users WHERE date(created_at) > (DATE(NOW()) - INTERVAL 7 DAY) AND MONTH(created_at) = '" . date('m') . "' AND YEAR(created_at) = '" . date('Y') . "' GROUP BY DAYNAME(created_at) ORDER BY (y_date) ASC"); 

      $record = $query->result();
      $data = [];

      foreach($record as $row) {
            $data['label'][] = $row->day_name;
            $data['data'][] = (int) $row->count;
      }
      $data['chart_data'] = json_encode($data);
      $this->load->view('pie_chart',$data);
    } 

	public function update($id)
	{

		ifPermissions('users_edit');
		
		postAllowed();

		$data = [
			'role' => post('role'),
			'name' => post('name'),
			'username' => post('username'),
			'email' => post('email'),
			'phone' => post('phone'),
			'address' => post('address'),
		];

		$password = post('password');

		if(logged('id')!=$id)
			$data['status'] = post('status')==1;

		if(!empty($password))
			$data['password'] = hash( "sha256", $password );

		$id = $this->users_model->update($id, $data);

		if (!empty($_FILES['image']['name'])) {

			$path = $_FILES['image']['name'];
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$this->uploadlib->initialize([
				'file_name' => $id.'.'.$ext
			]);
			$image = $this->uploadlib->uploadImage('image', '/users');

			if($image['status']){
				$this->users_model->update($id, ['img_type' => $ext]);
			}

		}

		$this->activity_model->add("User #$id Updated by User:".logged('name'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Client Profile has been Updated Successfully');
		
		redirect('users');

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

		ifPermissions('projects_delete');

		if($id!==1 && $id!=logged($id)){ }else{
			redirect('/','refresh');
			return;
		}

		$id = $this->projects_model->delete($id);

		$this->activity_model->add("User #$id Deleted by User:".logged('name'));

		$this->session->set_flashdata('alert-type', 'success');
		$this->session->set_flashdata('alert', 'Project has been Deleted Successfully');
		
		redirect('projects');

	}

	public function change_status($id)
	{
		$this->projects_model->update($id, ['status' => get('status') == 'true' ? 1 : 0 ]);
		echo 'done';
	}

	public function binary_patch() {

		session_write_close();

		$id=$this->input->post('status');

		$pending_projects = $this->projects_model->getPendingProjects();

		if($pending_projects) {
			echo "Pending";
		} else {
				$this->binary_1085_newp($id);
				echo "done";
		}
	}

	public function binary_1085_newp($id) {

			$info="";
			$jobdetails= $this->projects_model->getJobdetails($id);

			$binary_py = $this->projects_model->getPython();
		
			$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
			
			//$ssh = new Net_SSH2('172.16.1.148',22);
			$ssh = new Net_SSH2('128.199.31.121',22);
			if (!$ssh->login('chemistry1', 'Ravi@1234')) {
				exit('Login Failed');
			}

			$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src');
			
			$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src';
		
			$file = strtotime("today")*1000+rand(10000,99999).".py";
			
			$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/'.$file;
			$file_contents = 'This is some example content.1';
	
		
			if ($contents = $binary_py[0]->binary_sc ) {
		
			$solvents_master = $this->projects_model->getsolventsbpatch($jobdetails[0]->id);

		foreach ($solvents_master as $row) {    
		$item = $row['solvent1_name'];


		if($row['input_temp_10']=="10") {
			$temparature="283.15";
			$temp10="10";
			$temp20="";
			$temp50="";
		}
		if($row['input_temp_20']=="25") {
			$temparature="298.15";
			$temp10="";
			$temp20="25";
			$temp50="";
		}
		
		else if ($row['input_temp_50']=="50") {
			$temparature="323.15";
			$temp10="";
			$temp20="";
			$temp50="50";
		}

		if($row['result_type']=='Pure_68') {
			$file_contents = $binary_py[0]->pure_sc;

			
	$search_string = "new content";
	$replace_string = "crs.add_molecule(['OC_solventDB_68_new/".$item."/COSMO_TZVPD/".$item."_c000.orcacosmo'])";
	
	$input_string = "INPUT_COSMO";
	$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
	

	$input_temp = "TEMPR";
	$replace_input_temp = $temparature;

		
		$file_contents = str_replace($search_string, $replace_string, $file_contents);
		$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
		
		$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);
			}
	
			if($row['result_type']=='Binary_1085') {
				$file_contents = $binary_py[0]->binary_sc;

			
		$search_string = "INPUT_COSMO";
		$replace_string = "mol_structure_list_0 = ['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";

		$input_string = "NEW_CONTENT1";
		$input_string1 = "NEW_CONTENT2";
		
		$replace_input_string ="crs.add_molecule(['OC_solventDB_68_new/".$row['solvent1_name']."/COSMO_TZVPD/".$row['solvent1_name']."_c000.orcacosmo'])";
		$replace_input_string1 ="crs.add_molecule(['OC_solventDB_68_new/".$row['solvent2_name']."/COSMO_TZVPD/".$row['solvent2_name']."_c000.orcacosmo'])";

		$input_temp = "TEMPR";
		$replace_input_temp = $temparature;

			
			$file_contents = str_replace($search_string, $replace_string, $file_contents);
			$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
			$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
			$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);
			}

			
				$fileContents = str_replace("'", "'\\''", $file_contents);

				
				$command = 'cd /home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
				$stream = $ssh->exec($command);

			
				while (empty($stream)) {
					
					usleep(500000); 

					$stream = $ssh->exec($command);
				}
	
			$dataj= json_decode($stream, true);

			if($row['result_type']=='Binary_1085') {
				
					$data1 = $dataj['data1'];
					$data2 = $dataj['data2'];
					$data3 = $dataj['data3'];
					$data4 = $dataj['data4'];
					$data5 = $dataj['data5'];
					

					$datapatch = [
						
						'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2] ,
						'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2] ,
						'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data1[2] ,
						'pure_data4' => $data4[0] . ", " . $data4[1] . ", " . $data1[2] ,
						'pure_data5' => $data5[0] . ", " . $data5[1] . ", " . $data1[2] ,
						'solvent_result	' => $stream,
						'processed_on	' => date('m/d/Y h:i:s a', time())
					];
			}

			if($row['result_type']=='Pure_68') {
				
				$data1 = $dataj['data1'];
		

				$datapatch = [
					
					'pure_data1' => $data1[0] . ", " . $data1[1],
					'solvent_result	' => $stream,
					'processed_on	' => date('m/d/Y h:i:s a', time())
				];
		}
	
		//print_r($datapatch);

			$this->db->where('id', $row['id']);
			$this->db->update('job_results', $datapatch);
	
	}
	
	$ssh->disconnect();

	} else {
		exit('File open failed');
	}
		
}

public function checkstopped()
{

 

				$this->db->where('job_results_count.status', 'Pending');
				$this->db->select_max('job_results_count.solvent_activity_finished');
				$this->db->get('job_results_count')->row()->solvent_activity_finished;

				$stuckJobs = $this->db->query("
					SELECT *, now()
					FROM job_results_count
					WHERE TIMESTAMPDIFF(SECOND, STR_TO_DATE(process_end, '%m/%d/%Y %h:%i:%s %p'), NOW()) >= 60
						AND status = 'Pending'
				")->result_array();



				if (!empty($stuckJobs)) {

					
					$id = $stuckJobs[0]['job_id'];
					$stype = $stuckJobs[0]['solvent_type'];
					$temp = $stuckJobs[0]['tempr'];
					$sv_id = $stuckJobs[0]['sv_id'];
					if ($stype=='Binary_1085') {
					
					
							$this->binary_1085_stopped($id,$stype,$temp,$sv_id);
							$this->db->where('job_id', $id);
							$this->db->where('solvent_type', "Binary_1085");
							$this->db->where('tempr', "10");
							$this->db->update('job_results_count', array('status'=>'Completed'));
							//sleep(5);
					
						
											}

				}

}

public function binary_1085_stopped($id,$stype,$temp,$sstart) {

	$temparatureMap = [
		"10" => ["283.15", "10", "", ""],
		"25" => ["298.15", "", "25", ""],
		"50" => ["323.15", "", "", "50"]
	];
	
	$temparature = "";
	$temp10 = "";
	$temp20 = "";
	$temp50 = "";
	
	if (isset($temparatureMap[$temp])) {
		[$temparature, $temp10, $temp20, $temp50] = $temparatureMap[$temp];
	}
	
	$errorMessage = '';
	
	$info = "";
	$jobdetails = $this->projects_model->getJobdetails1($id);
	$binary_py = $this->projects_model->getPython();
	$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
	
	$ssh = new Net_SSH2('128.199.31.121', 22);
	if (!$ssh->login('chemistry1', 'Ravi@1234')) {
		exit('Login Failed');
	}
	
	$logFile = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/err.log';
	file_put_contents($logFile, '');
	
	$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src');
	
	$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src';
	$file = strtotime("today") * 1000 + rand(10000, 99999) . ".py";
	$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/' . $file;
	
	$file_contents = $binary_py[0]->binary_sc;
	
	if ($file_contents = $binary_py[0]->binary_sc) {
		$solvents_master = $this->projects_model->getsolventsbs($sstart);
		$jid = $this->projects_model->addactivity_log([
			'job_id' => $jobdetails[0]->id,
			'solvent_type' => $stype,
			'tempr' => $temp,
			'solvents_count' => count($solvents_master),
			'solvent_activity_finished' => '',
			'process_start' => date('m/d/Y h:i:s a', time()),
			'status' => 'Pending'
		]);
	
		$timeout = 60; // Maximum time in seconds for a loop iteration
		$startTime = time();
		$lastProcessedIndex = 0; // Initialize the index of the last successfully processed record
	
		try {
			for ($i = $lastProcessedIndex; $i < count($solvents_master); $i++) {
				$startTime = time(); // Reset the start time for each iteration
	
				$row = $solvents_master[$i];
				$item = $row['solvent1_name'];
				$search_string = "INPUT_COSMO";
				$replace_string = "mol_structure_list_0 = ['" . $fname . "/COSMO_TZVPD/" . $fname . "_c000.orcacosmo']";
				$input_string = "NEW_CONTENT1";
				$input_string1 = "NEW_CONTENT2";
				$replace_input_string = "crs.add_molecule(['OC_solventDB_68_new/" . $row['solvent1_name'] . "/COSMO_TZVPD/" . $row['solvent1_name'] . "_c000.orcacosmo'])";
				$replace_input_string1 = "crs.add_molecule(['OC_solventDB_68_new/" . $row['solvent2_name'] . "/COSMO_TZVPD/" . $row['solvent2_name'] . "_c000.orcacosmo'])";
				$input_temp = "TEMPR";
				$replace_input_temp = $temparature;
	
				$file_contents = $binary_py[0]->binary_sc;
				$file_contents = str_replace($search_string, $replace_string, $file_contents);
				$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
				$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
				$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);
				$fileContents = str_replace("'", "'\\''", $file_contents);
				$command = 'cd /home/mlladmin/ORCA/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
				//$stream = $ssh->exec($command);
				$stream = $ssh->exec($command . ' &');
	
				while (empty($stream)) {
					usleep(500000); // 0.5 seconds
					$stream = $ssh->exec($command);
				}
	
				$file_contents = str_replace($replace_string, $search_string, $file_contents);
				$file_contents = str_replace($replace_input_string, $input_string, $file_contents);
				$file_contents = str_replace($replace_input_string1, $input_string1, $file_contents);
				$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
	
				$dataj = json_decode($stream, true);
				$data1 = $dataj['data1'];
				$data2 = $dataj['data2'];
				$data3 = $dataj['data3'];
				$data4 = $dataj['data4'];
				$data5 = $dataj['data5'];
	
				$id = $this->projects_model->createjobresults([
					'job_id' => $jobdetails[0]->id,
					's_id' => $row['s_id'],
					'solvents' => $row['solvent1_name'] . '->' . $row['solvent2_name'],
					'result_type' => $stype,
					'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2],
					'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2],
					'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data1[2],
					'pure_data4' => $data4[0] . ", " . $data4[1] . ", " . $data1[2],
					'pure_data5' => $data5[0] . ", " . $data5[1] . ", " . $data1[2],
					'input_temp_10' => $temp10,
					'input_temp_20' => $temp20,
					'input_temp_50' => $temp50,
					'solvent_result_name' => $fname,
					'solvent_result' => $stream,
					'processed_on' => date('m/d/Y h:i:s a', time()),
				]);
	
				$lastProcessedIndex = $i; // Update the index of the last successfully processed record
	
				$this->db->where('id', $jid);
				$this->db->update('job_results_count', array('solvent_activity_finished' => $i + 1, 'process_end' => date('m/d/Y h:i:s a', time())));
	
				if (count($solvents_master) == $i + 1) {
					$this->db->where('id', $jid);
					$this->db->update('job_results_count', array('status' => 'Completed'));
				}
	
				// Check if the loop is stuck
				$elapsedTime = time() - $startTime;
				if ($elapsedTime >= $timeout) {
					// Log an error and exit the loop
					$errorMessage = 'Loop stuck for JOBID ' . $jobdetails[0]->id;
					error_log($errorMessage, 3, $logFile);
					break; // Exit the loop
				}
			}
	
			//$this->db->trans_commit();
		} catch (Exception $e) {
	
			$errorMessage = 'Error during process for JOBID ' . $jobdetails[0]->id . ': ' . $e->getMessage();
			error_log($errorMessage, 3, $logFile);
			error_log("Error during process for JOBID " . $jobdetails[0]->id, 1, "evishy@gmail.com");
	
		}
	
		if (!empty($errorMessage)) {
			error_log($errorMessage, 3, $logFile);
		}
	}
	
	// Update the job results count table with the last processed index
	$this->db->where('id', $jid);
	$this->db->update('job_results_count', array('solvent_activity_finished' => $lastProcessedIndex + 1, 'process_end' => date('m/d/Y h:i:s a', time())));
	
	$ssh->disconnect();
	
	
}



	public function runact_new($id) {

		session_write_close();
		
		
		$stype=$this->input->get('status');
		$solvent_type=$this->input->post('status');

		$pending_projects = $this->projects_model->getPendingProjects();

		$queryJobsMaster = $this->db->query("SELECT COUNT(*) AS num_records FROM jobs_master WHERE cosmo_status = 'Processing'");
        $numRecordsJobsMaster = $queryJobsMaster->row()->num_records;

		if($pending_projects || $numRecordsJobsMaster > 0) {
			echo "Pending"; //change to Pending to work
		}
		else {
		
			if($solvent_type=="Pure_68") {
				$this->pure_68_new($id,$solvent_type,"10");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Pure_68");
				$this->db->where('tempr', "10");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				//sleep(10);

				$this->pure_68_new($id,$solvent_type,"25");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Pure_68");
				$this->db->where('tempr', "25");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				//sleep(10);

				$this->pure_68_new($id,$solvent_type,"50");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Pure_68");
				$this->db->where('tempr', "50");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				//sleep(10);
		
				echo 'done';
			}
		
			if($solvent_type=="Binary_1085") {

				$this->pure_68_new($id,"Pure_68","10");
                $this->db->where('job_id', $id);
                $this->db->where('solvent_type', "Pure_68");
                $this->db->where('tempr', "10");
                $this->db->update('job_results_count', array('status'=>'Completed'));
                //sleep(10);

                $this->pure_68_new($id,"Pure_68","25");
                $this->db->where('job_id', $id);
                $this->db->where('solvent_type', "Pure_68");
                $this->db->where('tempr', "25");
                $this->db->update('job_results_count', array('status'=>'Completed'));
                //sleep(10);

                $this->pure_68_new($id,"Pure_68","50");
                $this->db->where('job_id', $id);
                $this->db->where('solvent_type', "Pure_68");
                $this->db->where('tempr', "50");
                $this->db->update('job_results_count', array('status'=>'Completed'));
                //sleep(10);
		
				$this->binary_1085_new($id,$solvent_type,"10");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Binary_1085");
				$this->db->where('tempr', "10");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				//sleep(5);

				$this->binary_1085_new($id,$solvent_type,"25");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Binary_1085");
				$this->db->where('tempr', "25");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				//sleep(5);

				$this->binary_1085_new($id,$solvent_type,"50");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Binary_1085");
				$this->db->where('tempr', "50");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				//sleep(5);

				echo 'done';
				//echo $solvent_type;
			}
			if($solvent_type=="Tertiary-16400") {
				//pure_68($id);
			
				$this->pure_68_new($id,"Pure_68","10");
				$this->db->where('job_id', $jid);
				$this->db->where('solvent_type', "Pure_68");
				$this->db->where('tempr', "10");
				$this->db->update('job_results_count', array('status'=>'Completed'));

				$this->pure_68_new($id,"Pure_68","25");
				$this->db->where('job_id', $jid);
				$this->db->where('solvent_type', "Pure_68");
				$this->db->where('tempr', "25");
				$this->db->update('job_results_count', array('status'=>'Completed'));

				$this->pure_68_new($id,"Pure_68","50");
				$this->db->where('job_id', $jid);
				$this->db->where('solvent_type', "Pure_68");
				$this->db->where('tempr', "50");
				$this->db->update('job_results_count', array('status'=>'Completed'));
		
		
				$this->binary_1085_new($id,"Binary_1085","10");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Binary_1085");
				$this->db->where('tempr', "10");
				$this->db->update('job_results_count', array('status'=>'Completed'));

				$this->binary_1085_new($id,"Binary_1085","25");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Binary_1085");
				$this->db->where('tempr', "25");
				$this->db->update('job_results_count', array('status'=>'Completed'));

				$this->binary_1085_new($id,"Binary_1085","50");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Binary_1085");
				$this->db->where('tempr', "50");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				
				$this->tertiary_16400_new($id,$solvent_type,"10");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Tertiary-16400");
				$this->db->where('tempr', "10");
				$this->db->update('job_results_count', array('status'=>'Completed'));

				$this->tertiary_16400_new($id,$solvent_type,"25");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Tertiary-16400");
				$this->db->where('tempr', "25");
				$this->db->update('job_results_count', array('status'=>'Completed'));

				$this->tertiary_16400_new($id,$solvent_type,"50");
				$this->db->where('job_id', $id);
				$this->db->where('solvent_type', "Tertiary-16400");
				$this->db->where('tempr', "50");
				$this->db->update('job_results_count', array('status'=>'Completed'));
				echo 'done';
				
			}
		
			echo "No Projects Pending";
		}

		
		
		}

		public function cheke() {


// outputs the username that owns the running php/httpd process
// (on a system with the "whoami" executable in the path)
$output=null;
$retval=null;

$pid1 = exec("http://172.16.1.148/app/smt/projects/binary_1085_new/5/Binary_1085/10 > /dev/null 2>&1 &",$output, $retval);


exec('whoami', $output, $retval);
echo "Returned with status $retval and output:\n";
print_r($output);

		}

	public function pure_68_new($id,$stype,$temp) {


		if($temp=="10") {
			$temparature="283.15";
			$temp10="10";
			$temp20="";
			$temp50="";
		}
		if($temp=="25") {
			$temparature="298.15";
			$temp10="";
			$temp20="25";
			$temp50="";
		}
		
		else if ($temp=="50") {
			$temparature="323.15";
			$temp10="";
			$temp20="";
			$temp50="50";
		}


			$info="";
			$jobdetails= $this->projects_model->getJobdetails1($id);

			$binary_py = $this->projects_model->getPython();

			$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
			
			$ssh = new Net_SSH2('128.199.31.121',22);
			if (!$ssh->login('chemistry1', 'Ravi@1234')) {
				exit('Login Failed');
			}
	
			$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');
	
	$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';

	$file = strtotime("today")*1000+rand(10000,99999).".py";
	
	// Define file path and contents
	$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$file;

	
	// Open file and print contents
	if ($contents = $binary_py[0]->pure_sc ) {

	$i = 0;
	$solvents_master = $this->projects_model->getsolvents();

	$jid = $this->projects_model->addactivity_log([
		'job_id' => $jobdetails[0]->id,
		'solvent_type' => $stype,
		'tempr' => $temp,
		'solvents_count' => count($solvents_master),
		'solvent_activity_finished' => '',
		'process_start	' => date('m/d/Y h:i:s a', time()),
		'status' => 'Pending'
	
	]);

foreach ($solvents_master as $row) {
    //echo $row['solvent1_name'];
    
$item = $row['solvent1_name'];
	
	// Define search and replace strings
	$search_string = "new content";
	$replace_string = "crs.add_molecule(['OC_solventDB_68_new/".$item."/COSMO_TZVPD/".$item."_c000.orcacosmo'])";
	
	$input_string = "INPUT_COSMO";
	$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
	// Read file contents

	$input_temp = "TEMPR";
	$replace_input_temp = $temparature;

	$file_contents = $binary_py[0]->pure_sc;
	
	// Replace search string with replace string
	$file_contents = str_replace($search_string, $replace_string, $file_contents);
	$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
	$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);


// Escape single quotes in the file contents
$fileContents = str_replace("'", "'\\''", $file_contents);

//echo $file_contents;

// Execute the Python script from the variable
$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
$stream = $ssh->exec($command);

// Check if $stream has output
while (empty($stream)) {
    // Add a delay before checking again
    usleep(500000); // 0.5 seconds

    // Retrieve the updated $stream
    $stream = $ssh->exec($command);
}

		$dataj= json_decode($stream, true);
	
	// Accessing the value for "data1"
		$data1 = $dataj['data1'];
	
		$id = $this->projects_model->createjobresults([
			'job_id' => $jobdetails[0]->id,
			's_id' =>  $row['s_id'],
			'solvents' => $item,
			'result_type' => $stype,
			'pure_data1' => $data1[0] . ", " . $data1[1] ,
			'input_temp_10' => $temp10,
			'input_temp_20' => $temp20,
			'input_temp_50	' => $temp50,
			//'input_temp_20	' => '',
			'solvent_result_name	' =>$fname,
			'solvent_result	' => $stream,
			'processed_on	' => date('m/d/Y h:i:s a', time()),
		]);
	
		$this->db->where('id', $jid);
		$this->db->update('job_results_count', array('solvent_activity_finished'=>$i+1,'sv_id'=>$row['s_id'],'process_end'=>date('m/d/Y h:i:s a', time())));
		
		if(count($solvents_master)==$i+1) {
			$this->db->where('id', $jid);
			$this->db->update('job_results_count', array('status'=>'Completed'));
	
		}

		$file_contents = str_replace($replace_string, $search_string, $file_contents);
		$file_contents = str_replace($replace_input_string, $input_string, $file_contents);
		$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
		//$file_contents="";
	
		$itemy='';
		$array_product [$i]= $item;
		$i++;
		
	
	
	}
	// Close connection
	$ssh->disconnect();
	//$sftp->disconnect();
	} else {
		exit('File open failed');
	}
	
	
	}

	public function binary_1085_new($id,$stype,$temp) {

		$temparatureMap = [
			"10" => ["283.15", "10", "", ""],
			"25" => ["298.15", "", "25", ""],
			"50" => ["323.15", "", "", "50"]
		];
		
		$temparature = "";
		$temp10 = "";
		$temp20 = "";
		$temp50 = "";
		
		if (isset($temparatureMap[$temp])) {
			[$temparature, $temp10, $temp20, $temp50] = $temparatureMap[$temp];
		}
		
		$errorMessage = '';
		
		$info = "";
		$jobdetails = $this->projects_model->getJobdetails1($id);
		$binary_py = $this->projects_model->getPython();
		$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
		
		$ssh = new Net_SSH2('128.199.31.121', 22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}
		
		$logFile = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/err.log';
		file_put_contents($logFile, '');
		
		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src');
		
		$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src';
		$file = strtotime("today") * 1000 + rand(10000, 99999) . ".py";
		$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/' . $file;
		
		$file_contents = $binary_py[0]->binary_sc;
		
		if ($file_contents = $binary_py[0]->binary_sc) {
			$solvents_master = $this->projects_model->getsolventsb();
			$jid = $this->projects_model->addactivity_log([
				'job_id' => $jobdetails[0]->id,
				'solvent_type' => $stype,
				'tempr' => $temp,
				'solvents_count' => count($solvents_master),
				'solvent_activity_finished' => '',
				'process_start' => date('m/d/Y h:i:s a', time()),
				'status' => 'Pending'
			]);
		
			$timeout = 60; // Maximum time in seconds for a loop iteration
			$startTime = time();
			$lastProcessedIndex = 0; // Initialize the index of the last successfully processed record
		
			try {
				for ($i = $lastProcessedIndex; $i < count($solvents_master); $i++) {
					$startTime = time(); // Reset the start time for each iteration
		
					$row = $solvents_master[$i];
					$item = $row['solvent1_name'];
					$search_string = "INPUT_COSMO";
					$replace_string = "mol_structure_list_0 = ['" . $fname . "/COSMO_TZVPD/" . $fname . "_c000.orcacosmo']";
					$input_string = "NEW_CONTENT1";
					$input_string1 = "NEW_CONTENT2";
					$replace_input_string = "crs.add_molecule(['OC_solventDB_68_new/" . $row['solvent1_name'] . "/COSMO_TZVPD/" . $row['solvent1_name'] . "_c000.orcacosmo'])";
					$replace_input_string1 = "crs.add_molecule(['OC_solventDB_68_new/" . $row['solvent2_name'] . "/COSMO_TZVPD/" . $row['solvent2_name'] . "_c000.orcacosmo'])";
					$input_temp = "TEMPR";
					$replace_input_temp = $temparature;
		
					$file_contents = $binary_py[0]->binary_sc;
					$file_contents = str_replace($search_string, $replace_string, $file_contents);
					$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
					$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
					$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);
					$fileContents = str_replace("'", "'\\''", $file_contents);
					$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 -c \'' . $fileContents . '\'';
					//$stream = $ssh->exec($command);
					$stream = $ssh->exec($command . ' &');
		
					while (empty($stream)) {
						usleep(500000); // 0.5 seconds
						$stream = $ssh->exec($command);
					}
		
					$file_contents = str_replace($replace_string, $search_string, $file_contents);
					$file_contents = str_replace($replace_input_string, $input_string, $file_contents);
					$file_contents = str_replace($replace_input_string1, $input_string1, $file_contents);
					$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);
		
					$dataj = json_decode($stream, true);
					$data1 = $dataj['data1'];
					$data2 = $dataj['data2'];
					$data3 = $dataj['data3'];
					$data4 = $dataj['data4'];
					$data5 = $dataj['data5'];
		
					$id = $this->projects_model->createjobresults([
						'job_id' => $jobdetails[0]->id,
						's_id' => $row['s_id'],
						'solvents' => $row['solvent1_name'] . '->' . $row['solvent2_name'],
						'result_type' => $stype,
						'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2],
						'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2],
						'pure_data3' => $data3[0] . ", " . $data3[1] . ", " . $data1[2],
						'pure_data4' => $data4[0] . ", " . $data4[1] . ", " . $data1[2],
						'pure_data5' => $data5[0] . ", " . $data5[1] . ", " . $data1[2],
						'input_temp_10' => $temp10,
						'input_temp_20' => $temp20,
						'input_temp_50' => $temp50,
						'solvent_result_name' => $fname,
						'solvent_result' => $stream,
						'processed_on' => date('m/d/Y h:i:s a', time()),
					]);
		
					$lastProcessedIndex = $i; // Update the index of the last successfully processed record
		
					$this->db->where('id', $jid);
					$this->db->update('job_results_count', array('solvent_activity_finished' => $i + 1, 'sv_id'=>$row['s_id'],'process_end' => date('m/d/Y h:i:s a', time())));
		
					if (count($solvents_master) == $i + 1) {
						$this->db->where('id', $jid);
						$this->db->update('job_results_count', array('status' => 'Completed'));
					}
		
					// Check if the loop is stuck
					$elapsedTime = time() - $startTime;
					if ($elapsedTime >= $timeout) {
						// Log an error and exit the loop
						$errorMessage = 'Loop stuck for JOBID ' . $jobdetails[0]->id;
						error_log($errorMessage, 3, $logFile);
						break; // Exit the loop
					}
				}
		
				//$this->db->trans_commit();
			} catch (Exception $e) {
		
				$errorMessage = 'Error during process for JOBID ' . $jobdetails[0]->id . ': ' . $e->getMessage();
				error_log($errorMessage, 3, $logFile);
				error_log("Error during process for JOBID " . $jobdetails[0]->id, 1, "evishy@gmail.com");
		
			}
		
			if (!empty($errorMessage)) {
				error_log($errorMessage, 3, $logFile);
			}
		}
		
		// Update the job results count table with the last processed index
		//$this->db->where('id', $jid);
		//$this->db->update('job_results_count', array('solvent_activity_finished' => $lastProcessedIndex + 1, 'process_end' => date('m/d/Y h:i:s a', time())));
		
		$ssh->disconnect();
		
		
}


public function tertiary_16400_new($id,$stype,$temp) {

	if($temp=="10") {
		$temparature="283.15";
		$temp10="10";
		$temp20="";
		$temp50="";
	}
	if($temp=="25") {
		$temparature="298.15";
		$temp10="";
		$temp20="25";
		$temp50="";
	}
	
	else if ($temp=="50") {
		$temparature="323.15";
		$temp10="";
		$temp20="";
		$temp50="50";
	}
	
	ini_set('memory_limit', '-1');

	$info="";
			$jobdetails= $this->projects_model->getJobdetails1($id);

			$binary_py = $this->projects_model->getPython();


			//echo $jobdetails[0]->project_id;
			$fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
			
	
			$ssh = new Net_SSH2('128.199.31.121',22);
			if (!$ssh->login('chemistry1', 'Ravi@1234')) {
				exit('Login Failed');
			}

			$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src');
			
			//$ssh->exec('cd einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch "tt.py"; echo "test" >> "tt.py"');
			// Directory and file details
	$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src';
	//$file = 'existing_file.py';
	$file = strtotime("today")*1000+rand(10000,99999).".py";
	
	//$sftp = new Net_SFTP('165.232.178.194',22);
	$sftp = new Net_SFTP('128.199.31.121',22);
	//if (!$sftp->login('chemistry2', 'Ravi@1234')) {
	if (!$sftp->login('chemistry1', 'Ravi@1234')) {
	exit('Login Failed');
	}
	
	// Define file path and contents
	//$file_path = '/home/chemistry2/ORCA/openCOSMO-RS_py/src/'.$file;
	$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/'.$file;
	
	$file_contents = 'This is some example content.1';
	
	// Open file and print contents
	if ($contents = $binary_py[0]->terinary_sc ) {
		

	
				// new code to insert
	$solvents = array("phenol", "1-Octanol", "methylcyclohexane", "n,n-dimethylacetamide");
	//$solvents = array("phenol", "1-Octanol");
	$i = 0;
	$solvents_master = $this->projects_model->getsolventst();
	$jid = $this->projects_model->addactivity_log([
		'job_id' => $jobdetails[0]->id,
		'solvent_type' => $stype,
		'tempr' => $temp,
		'solvents_count' => count($solvents_master),
		'solvent_activity_finished' => '',
		'process_start	' => date('m/d/Y h:i:s a', time()),
		'status' => 'Pending'
	
	]);

	
//print_r($solvents_master);
foreach ($solvents_master as $row) {
    //echo $row['solvent1_name'];
    
$item = $row['solvent1_name'];

	
	//foreach ($solvents as $item) {
	
		// Define search and replace strings
		$search_string = "INPUT_COSMO";
		$replace_string = "mol_structure_list_0 = ['opencosmorspy/".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
		
		$input_string = "NEW_CONTENT1";
		$input_string1 = "NEW_CONTENT2";
		$input_string2 = "NEW_CONTENT3";
		//$replace_input_string= "['".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
		$replace_input_string ="crs.add_molecule(['opencosmorspy/OC_solventDB_68_new/".$row['solvent1_name']."/COSMO_TZVPD/".$row['solvent1_name']."_c000.orcacosmo'])";
		$replace_input_string1 ="crs.add_molecule(['opencosmorspy/OC_solventDB_68_new/".$row['solvent2_name']."/COSMO_TZVPD/".$row['solvent2_name']."_c000.orcacosmo'])";
		$replace_input_string2 ="crs.add_molecule(['opencosmorspy/OC_solventDB_68_new/".$row['solvent3_name']."/COSMO_TZVPD/".$row['solvent3_name']."_c000.orcacosmo'])";
		
		$input_temp = "TEMPR";
 		$replace_input_temp = $temparature;

		// Read file contents
		$file_contents = $binary_py[0]->terinary_sc;
		
		// Replace search string with replace string
		$file_contents = str_replace($search_string, $replace_string, $file_contents);
		$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
		$file_contents = str_replace($input_string1, $replace_input_string1, $file_contents);
		$file_contents = str_replace($input_string2, $replace_input_string2, $file_contents);
		$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);

				
		// Read file contents
		//$file_contents = $sftp->get($file_path);
		
		//echo $file_contents;
		
		// Upload updated contents to server
		if (!$sftp->put($file_path, $file_contents)) {
		exit('Replace failed');
		}
		
		$stream = $ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src; nohup python3 '.$file.'');
		//sleep(2);
		//stream_set_blocking($stream, true);
		//$output = stream_get_contents($stream);


		
		//$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 '.$file.' > /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$item.'.log 2>&1 & echo $!;sleep 5');
		
		//$file_contents = str_replace($replace_string, $search_string, $file_contents);
		
		$file_contents = str_replace($replace_string,$search_string, $file_contents);
		$file_contents = str_replace($replace_input_string,$input_string, $file_contents);
		$file_contents = str_replace($replace_input_string1,$input_string1, $file_contents);
		$file_contents = str_replace($replace_input_string2,$input_string2, $file_contents);
		$file_contents = str_replace($replace_input_temp, $input_temp, $file_contents);

		// Define file path and contents
		//$file_pathlog = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/actlog/'.$item.'.log';
		//$file_contents = 'This is some example content.1';
		//if (!$sftp->put($file_path, $file_contents)) {
			//exit('Replace failed');
		//}
		
	// Open file and print contents
		//if ($contentslog = $sftp->get($file_pathlog)) {
		//echo "<br><h2>".$item."</h2><br>.$contentslog.<br>";
		//$rdetails = print("<pre>".print_r($contentslog,true)."</pre>");
		//$obj = json_decode($contentslog);
		//$dataj = json_decode($contentslog, true);
		//echo $contentslog;
		//echo $stream;
	
		$dataj= json_decode($stream, true);


		// Accessing the value for "data1"
		$data1 = $dataj['data1'];
		$data2 = $dataj['data2'];
		//$data3 = $dataj['data3'];
		//$data4 = $dataj['data4'];
		//$data5 = $dataj['data5'];
		//$data6 = $dataj['data6'];
		
		$id = $this->projects_model->createjobresults([
			'job_id' => $jobdetails[0]->id,
			's_id' => $row['s_id'],
			'solvents' => $row['solvent1_name'].'->'.$row['solvent2_name'].'->'.$row['solvent3_name'],
			'result_type' => $stype,
			'pure_data1' => $data1[0] . ", " . $data1[1] . ", " . $data1[2] . ", " . $data1[3] ,
			'pure_data2' => $data2[0] . ", " . $data2[1] . ", " . $data1[2] . ", " . $data1[3] ,
			//'pure_data3' => round($data3[0],4) . ", " . round($data3[1],4) . ", " . round($data1[2],4) . ", " . round($data1[3],4) ,
			//'pure_data4' => round($data4[0],4) . ", " . round($data4[1],4) . ", " . round($data1[2],4) . ", " . round($data1[3],4) ,
			//'pure_data5' => round($data5[0],4) . ", " . round($data5[1],4) . ", " . round($data1[2],4) . ", " . round($data1[3],4) ,
			//'pure_data6' => $data6[0] . ", " . $data6[1] ,
			'input_temp_10' => $temp10,
			'input_temp_20' => $temp20,
			'input_temp_50	' => $temp50,
			//'input_temp_50	' => 'Yes',
			'solvent_result_name	' =>$fname,
			'solvent_result	' => $stream,
			'processed_on	' => date('m/d/Y h:i:s a', time()),
		]);
	
		$this->db->where('id', $jid);
		$this->db->update('job_results_count', array('solvent_activity_finished'=>$i+1,'process_end'=>date('m/d/Y h:i:s a', time())));
		
		if(count($solvents_master)==$i+1) {
			$this->db->where('id', $jid);
			$this->db->update('job_results_count', array('status'=>'Completed'));
	
		}
	
		$array_product [$i]= $item;
		$i++;
		//}
	
	
	}
	// Close connection
	$ssh->disconnect();
	$sftp->disconnect();
	} else {
		exit('File open failed');
	}

}


public function phSolubility() {
	$id= $this->input->post('job_id');
	
	
	$this->ph_solubility($id,'10');
	$this->db->where('job_id', $id);
	$this->db->where('solvent_type','ph_12');
	$this->db->where('tempr', "10");
	$this->db->update('job_results_ph_count', array('status'=>'Completed'));

	$this->ph_solubility($id,'25');
	$this->db->where('job_id', $id);
	$this->db->where('solvent_type','ph_12');
	$this->db->where('tempr', "25");
	$this->db->update('job_results_ph_count', array('status'=>'Completed'));

	$this->ph_solubility($id,'50');
	$this->db->where('job_id', $id);
	$this->db->where('solvent_type','ph_12');
	$this->db->where('tempr', "50");
	$this->db->update('job_results_ph_count', array('status'=>'Completed'));

	$this->ph_solubility($id,'50');


	
	

	

}

/*---------------------Ph SOLUBILITY-----------------------------------*/
public function ph_solubility($id,$temp) {
		
		
		if($temp=="10") {
			$temparature="283.15";
			$temp10="10";
			$temp20="";
			$temp50="";
		}
		if($temp=="25") {
			$temparature="298.15";
			$temp10="";
			$temp20="25";
			$temp50="";
		}
		
		else if ($temp=="50") {
			$temparature="323.15";
			$temp10="";
			$temp20="";
			$temp50="50";
		}
	

		$info="";
		$jobdetails= $this->projects_model->getJobdetails1($id);
		$ssh = new Net_SSH2('128.199.31.121',22);
		if (!$ssh->login('chemistry1', 'Ravi@1234')) {
			exit('Login Failed');
		}


		$checkjobphinsertornot = $this->projects_model->checkjobphinsertcheck($id);
	    if($checkjobphinsertornot){
	       	$this->projects_model->insertphresults($jobdetails[0]->project_id,'10');
	        $this->projects_model->insertphresults($jobdetails[0]->project_id,'25');
	        $this->projects_model->insertphresults($jobdetails[0]->project_id,'50');
	        echo "done";
	        $ssh->disconnect();
	        exist();
	    }


		$this->db->select('*');
	    $this->db->from('ph_solvents_commands');
	    $this->db->order_by('id', 'ASC');
	    $query = $this->db->get();

	    $fname = pathinfo($jobdetails[0]->inp_filename, PATHINFO_FILENAME);
		
		

		$ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy');

		$directory = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy';

		$file = strtotime("today")*1000+rand(10000,99999).".py";

		// Define file path and contents
		$file_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$file;
		$i = 0;
	    if ($query->num_rows() > 0) {
	    	$jid = $this->projects_model->addphactivity_log([
				'job_id' => $jobdetails[0]->id,
				'solvent_type' => 'ph_12',
				'tempr' => $temp,
				'solvents_count' => 12,
				'solvent_activity_finished' => '',
				'process_start	' => date('m/d/Y h:i:s a', time()),
				'status' => 'Pending'
			
			]);
	        foreach ($query->result() as  $key => $row ) {
	           // Open file and print contents

	        	
				if (!empty($row->command )) {
					

					$input_string = "INPUT_COSMO";
					$replace_input_string= "['opencosmorspy/".$fname."/COSMO_TZVPD/".$fname."_c000.orcacosmo']";
					// Read file contents

					$input_temp = "TEMPR";
					$replace_input_temp = $temparature;

					$file_contents = $row->command;
					
					// Replace search string with replace string
					$file_contents = str_replace($input_string, $replace_input_string, $file_contents);
					$file_contents = str_replace($input_temp, $replace_input_temp, $file_contents);


					// Escape single quotes in the file contents
					$fileContents = str_replace("'", "'\\''", $file_contents);

					//echo $file_contents;

					// Execute the Python script from the variable
					$command = 'cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src; nohup python3 -c \'' . $fileContents . '\'';
					$stream = shell_exec($command);
					//dd($response);
					/*$stream = $ssh->exec($command);
					
					// Check if $stream has output
					while (empty($stream)) {
					    // Add a 1 before checking again
					    sleep(1); // 0.5 seconds

					    // Retrieve the updated $stream
					    $stream = $ssh->exec($command);
					}*/
					//dd($stream);
					//die;
					//$filePath = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/output.txt';
					//$written = file_put_contents($filePath, $stream, FILE_APPEND);

					$dataj= json_decode($stream, true);

			
					// Accessing the value for "data1"
					
					$data1 = @$dataj['data1'];
					$data2 = @$dataj['data2'];
					
					$id = $this->projects_model->createjobPhresults([
						'job_id' => $jobdetails[0]->id,
						's_id' => $row->id,
						'solvents' => $row->solvent,
						'result_type' => 'ph_12',
						'pure_data1' => @$data1[0] . ", " . @$data1[1] . ", " . @$data1[2] . ", " . @$data1[3] ,
						'pure_data2' => @$data2[0] . ", " . @$data2[1] . ", " . @$data1[2] . ", " . @$data1[3] ,
						'input_temp_10' => $temp10,
						'input_temp_20' => $temp20,
						'input_temp_50	' => $temp50,
						'solvent_result_name	' =>$fname,
						'solvent_result	' => $stream,
						'processed_on	' => date('m/d/Y h:i:s a', time()),
					]);
				
					$this->db->where('id', $jid);
					$this->db->update('job_results_ph_count', array('solvent_activity_finished'=>$key+1,'process_end'=>date('m/d/Y h:i:s a', time())));
					
					if($query->num_rows() == $key+1) {
						$this->db->where('id', $jid);
						$this->db->update('job_results_ph_count', array('status'=>'Completed'));
				
					}

			
					 //echo "done";
				} else {
					exit('File open failed');
				}
	        }


	        
	    }

		//$ssh->disconnect();
	
	}
	public function generatePdf($id)
	{
		set_time_limit(120);
		ini_set('max_exection_time', 60);
		ini_set("pcre.backtrack_limit", "50000000");
		$this->page_data['cdata'] =  $this->projects_model->getDataForPdf($id);
		$this->page_data['jobDetail'] = $this->projects_model->getJobDetail($id);
		$project_details = $this->projects_model->getById($this->page_data['jobDetail']->project_id);
		// $this->page_data['solventData'] = $this->projects_model->getresultsdata($id);
		$mpdf = new \Mpdf\Mpdf();
		$html = $this->load->view('projects/pdf',$this->page_data,true);
		$mpdf->WriteHTML($html);
		$pdfFilename = $project_details->{'project_name'}.".pdf";
		$mpdf->Output($pdfFilename, "D");     
	}	
	public function generateExcel($id)
	{
		set_time_limit(120);
		$this->page_data['cdata'] =  $this->projects_model->getDataForExcel($id);
		// dd($this->page_data['cdata'][0]['result_job_id']);
		$this->page_data['jobDetail'] = $this->projects_model->getJobDetail($id);
		$project_details = $this->projects_model->getById($this->page_data['jobDetail']->project_id);
		$file_name = $project_details->{'project_name'}.'.xlsx';

		$spreadsheet = new Spreadsheet();

		$sheet = $spreadsheet->getActiveSheet();
		$from = "A1"; // or any value
		$to = "A11"; // or any value
		$sheet->getStyle("$from:$to")->getFont()->setBold( true );

		$from1 = "A17"; // or any value
		$to1 = "Q17"; // or any value
		$sheet->getStyle("$from1:$to1")->getFont()->setBold( true );

		$sheet->getStyle("B12")->getFont()->setBold( true );
		$sheet->getStyle("C12")->getFont()->setBold( true );
		$sheet->getStyle("D12")->getFont()->setBold( true );

		$sheet->getStyle("C12")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) ->getStartColor()->setARGB('00a0bfe0');
		$sheet->getStyle("D12")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) ->getStartColor()->setARGB('00a0bfe0');
		$sheet->getStyle("$from1:$to1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) ->getStartColor()->setARGB('00a0bfe0');

		$sheet->setCellValue('A1', 'Project Name');
		$sheet->setCellValue('B1', '');
		$sheet->setCellValue('C1', $project_details->{'project_name'});

		$sheet->setCellValue('A2', 'Developed By');
		$sheet->setCellValue('B2', '');
		$sheet->setCellValue('C2', 'PharmaDEM Solutions Logo');

		$sheet->setCellValue('A3', 'Job Submission Date');
		$sheet->setCellValue('B3', '');
		$sheet->setCellValue('C3', $this->page_data['jobDetail']->process_start);

		$sheet->setCellValue('A4', 'Submitted By');
		$sheet->setCellValue('B4', '');
		$sheet->setCellValue('C4', 'Ravi');

		$sheet->setCellValue('A5', 'Api Name');
		$sheet->setCellValue('B5', '');
		$sheet->setCellValue('C5', $project_details->{'project_name'});

		$sheet->setCellValue('A6', 'Expt Imformation');
		$sheet->setCellValue('B6', '');
		$sheet->setCellValue('C6', 'hfuss_value = '.$this->page_data['jobDetail']->hfuss_value);
		

		$sheet->setCellValue('A7', '');
		$sheet->setCellValue('B7', '');
		$sheet->setCellValue('C7', 'MP = ');

		$sheet->setCellValue('A8', '');
		$sheet->setCellValue('B8', '');
		$sheet->setCellValue('C8', 'MW = '.$this->page_data['jobDetail']->mol_weight);

		$sheet->setCellValue('A9', 'Known Solubility Imformation');
		$sheet->setCellValue('B9', '');
		$sheet->setCellValue('C9', $this->page_data['jobDetail']->know_solubility);

		$sheet->setCellValue('A10', 'Structure Smiles');
		$sheet->setCellValue('B10', '');
		$sheet->setCellValue('C10', $this->page_data['jobDetail']->structure_code.' '.$this->page_data['jobDetail']->smiles);

		$sheet->setCellValue('A11', 'General Notes');
		$sheet->setCellValue('B11', '');
		$sheet->setCellValue('C11', 'PharmaDEM Solutions Logo');

		$sheet->setCellValue('A12', '');
		$sheet->setCellValue('B12', 'Solubility Class');
		$sheet->setCellValue('C12', 'Category');
		$sheet->setCellValue('D12', 'Mg/ml');

		$sheet->setCellValue('A13', '');
		$sheet->setCellValue('B13', '');
		$sheet->setCellValue('C13', 'Highly Soluble');
		$sheet->setCellValue('D13', '>100');

		$sheet->setCellValue('A14', '');
		$sheet->setCellValue('B14', '');
		$sheet->setCellValue('C14', 'Soluble');
		$sheet->setCellValue('D14', '50 to 100');

		$sheet->setCellValue('A15', '');
		$sheet->setCellValue('B15', '');
		$sheet->setCellValue('C15', 'Moderate');
		$sheet->setCellValue('D15', '10 to 50');

		$sheet->setCellValue('A16', '');
		$sheet->setCellValue('B16', '');
		$sheet->setCellValue('C16', 'Insoluble');
		$sheet->setCellValue('D16', '<10');

		$sheet->setCellValue('A17', 'Solvent Id');
		$sheet->setCellValue('B17', 'Solvent System');
		$sheet->setCellValue('C17', 'Solvent 1');
		$sheet->setCellValue('D17', 'Solvent 2');
		$sheet->setCellValue('E17', 'Solvent 3');
		$sheet->setCellValue('F17', 'Comp 1');
		$sheet->setCellValue('G17', 'Comp 2');
		$sheet->setCellValue('H17', 'Comp 3');
		$sheet->setCellValue('I17', '10CMGML');
		$sheet->setCellValue('J17', '10CVL');
		$sheet->setCellValue('K17', '10CYL');
		$sheet->setCellValue('L17', '25CMGML');
		$sheet->setCellValue('M17', '25CVL');
		$sheet->setCellValue('N17', '25CYL');
		$sheet->setCellValue('O17', '50CMGML');
		$sheet->setCellValue('P17', '50CVL');
		$sheet->setCellValue('Q17', '50CYL');

		$count = 18;
		$pureDataArray = [
            'pure_data1' => '(0.0, 0.1, 0.9)',
            'pure_data2' => '(0.0, 0.25, 0.75)',
            'pure_data3' => '(0.0, 0.5, 0.5)',
            'pure_data4' => '(0.0, 0.75, 0.25)',
            'pure_data5' => '(0.0, 0.9, 0.1)',
        ];

        $terDataArray = [
            'pure_data1' => '(0.0, 0.1, 0.75, 0.15)',
            'pure_data2' => '(0.0, 0.25, 0.50, 0.25)',
            'pure_data3' => '(0.0, 0.5, 0.25, 0.25)'
        ];
		foreach($this->page_data['cdata'] as $key=>$value)
		{
			
			$solventName = $value['Solvent_System'];
			//dd($value);
         	$wt_fraction = $value['wt_fraction'];
         	$Comp_1 = $value['Comp_1'];
         	$Comp_2 = $value['Comp_2'];
         	$Comp_3 = $value['Comp_3'];
         
            if ($value['result_type'] != "Pure_68" && $value['result_type'] != "Tertiary-16400") {
              
                $solventName = $solventName . "-" . str_replace("-", $pureDataArray[$value['wt_fraction']], $pureDataArray[$value['wt_fraction']]);
                $wt_fraction =  str_replace("-", $pureDataArray[$value['wt_fraction']], $pureDataArray[$value['wt_fraction']]);

                $trim_wt_fraction =  trim($wt_fraction, "()");
	            $expload_fraction = explode(',',$trim_wt_fraction);
	            
	            $Comp_1 = @$expload_fraction[1] ;
	            $Comp_2 = @$expload_fraction[2] ;
	            $Comp_3 = @$expload_fraction[3] ;
            } elseif ($value['result_type'] === "Tertiary-16400") {
              
                $solventName = $solventName . "-" . str_replace("-", $terDataArray[$value['wt_fraction']], $terDataArray[$value['wt_fraction']]);
                $wt_fraction = str_replace("-", $terDataArray[$value['wt_fraction']], $terDataArray[$value['wt_fraction']]);
                $trim_wt_fraction =  trim($wt_fraction, "()");
	            $expload_fraction = explode(',',$trim_wt_fraction);
	           
	            $Comp_1 = @$expload_fraction[1] ;
	            $Comp_2 = @$expload_fraction[2] ;
	            $Comp_3 = @$expload_fraction[3] ;
            } else {
                $solventName = $value['Solvent_System'];
                $wt_fraction = $value['wt_fraction'];
            }
           

			// $solvent = $this->projects_model->getSolventDataBySid($value['result_job_id']);
			$sheet->setCellValue('A' . $count, $key + 1);
			$sheet->setCellValue('B' . $count, $solventName);
			$sheet->setCellValue('C' . $count, $value['Solvent_1']);
			$sheet->setCellValue('D' . $count, $value['Solvent_2']);
			$sheet->setCellValue('E' . $count, $value['Solvent_3']);
			$sheet->setCellValue('F' . $count, $Comp_1);
			$sheet->setCellValue('G' . $count, $Comp_2);
			$sheet->setCellValue('H' . $count, $Comp_3);
			$sheet->setCellValue('I' . $count,  number_format(((float)$value['10_cmgml']),2,'.',''));
			$sheet->setCellValue('J' . $count, number_format(((float)$value['10_cvl']),2,'.',''));
			$sheet->setCellValue('K' . $count, number_format(((float)$value['10_cyl']),2,'.',''));
			$sheet->setCellValue('L' . $count,  number_format(((float)$value['25_cmgml']),2,'.',''));
			$sheet->setCellValue('M' . $count, number_format(((float)$value['25_cvl']),2,'.',''));
			$sheet->setCellValue('N' . $count, number_format(((float)$value['25_cyl']),2,'.',''));
			$sheet->setCellValue('O' . $count,  number_format(((float)$value['50_cmgml']),2,'.',''));
			$sheet->setCellValue('P' . $count, number_format(((float)$value['50_cvl']),2,'.',''));
			$sheet->setCellValue('Q' . $count, number_format(((float)$value['50_cyl']),2,'.',''));
			$count++;
		}

		$writer = new Xlsx($spreadsheet);

		$writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");

		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');

		header('Expires: 0');

		header('Cache-Control: must-revalidate');

		header('Pragma: public');

		header('Content-Length:' . filesize($file_name));

		flush();

		readfile($file_name);

		exit;   
	}	
	
	public function generateCsv()
	{
		set_time_limit(120);

		$selectedProjects = [3,1,2];

		$data = [];

		if (!empty($selectedProjects)) {
			foreach ($selectedProjects as $projectId) {
		        // Fetch data for $projectId using your queries
						$jbd = $this->projects_model->getJobDetail($projectId);
		        // Define arrays to hold results for each table for the current project
				$results_10 = [];
				$results_25 = [];
				$results_50 = [];
		        $solvent_w1_system = []; // Initialize an array to hold solvent_w1_solvent_system values

		        // Loop through the three tables
		        $tables = ['results_data_10', 'results_data_25', 'results_data_50'];
		        foreach ($tables as $table) {
		            // Create a new query for each table
		        	$this->db->select('
		        		' . $table . '.*, 
		        		job_results.s_id AS job_s_id, job_results.id AS job_res_id,
		        		solvents_master.w1_solvent_system AS solvent_w1_solvent_system
		        		');
		        	$this->db->from($table);
		        	$this->db->join('job_results', 'job_results.id = ' . $table . '.result_job_id', 'left');
		        	$this->db->join('solvents_master', 'solvents_master.s_id = job_results.s_id', 'left');
		        	$this->db->where($table . '.job_id', $jbd->id);
		            //$this->db->limit(10);

		            // Execute the query and append results to the respective array
		        	$query = $this->db->get();
		        	$results = $query->result();
		            //print_r($this->db->last_query());    
		        	if ($table === 'results_data_10') {
		        		$results_10 = $results;
		        	} elseif ($table === 'results_data_25') {
		        		$results_25 = $results;
		        	} elseif ($table === 'results_data_50') {
		        		$results_50 = $results;
		        	}

		            // Extract solvent_w1_solvent_system values and store them in the solvent_w1_system array
		        	$solvent_w1_system = array_column($results, 'solvent_w1_solvent_system');
		        }

		        // Check if solvent_w1_solvent_system exists in all three arrays
		        if (!empty($solvent_w1_system) && count(array_filter($solvent_w1_system)) === count($solvent_w1_system)) {
		            // Get the project name
		        	$projectName = $this->projects_model->getProjectName($projectId);

		        	foreach ($solvent_w1_system as $index => $solvent) {
		        		$data[$solvent][$projectName] = [
		        			'result_10' => $results_10[$index]->{'10cmgml'},
		        			'result_25' => $results_25[$index]->{'25cmgml'},
		        			'result_50' => $results_50[$index]->{'50cmgml'},
		        		];
		        	}
		        }
		    }
		}
		// dd($data);

		// Generate CSV data

		$delimiter = ','; // Define the delimiter

		$csvData = "Solvent Name" . $delimiter;
		$projects = array_keys($data[array_key_first($data)]);
		foreach ($projects as $project) {
		    $csvData .= "$project-Result 10{$delimiter}$project-Result 25{$delimiter}$project-Result 50{$delimiter}";
		}
		$csvData = rtrim($csvData, $delimiter) . "\n";
		foreach ($data as $solvent => $projects) {
		    // Encapsulate entire CSV field containing the solvent name in double quotes
		    $csvData .= '"' . str_replace('"', '""', $solvent) . '"' . $delimiter;
		    foreach ($projects as $project => $results) {
		        $csvData .= "{$results['result_10']}{$delimiter}{$results['result_25']}{$delimiter}{$results['result_50']}{$delimiter}";
		    }
		    $csvData = rtrim($csvData, $delimiter) . "\n";
		}

		// Set headers for CSV download
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="project_results.csv"');

		// Output CSV data
		echo $csvData;
	}

	public function generatePhExcel($id)
	{
		set_time_limit(120);
		$this->page_data['cdata'] =  $this->projects_model->getDataForPhExcel($id);

		// dd($this->page_data['cdata'][0]['result_job_id']);
		$this->page_data['jobDetail'] = $this->projects_model->getJobDetail($id);
		$project_details = $this->projects_model->getById($this->page_data['jobDetail']->project_id);
		$file_name = 'Ph-'.$project_details->{'project_name'}.'.xlsx';

		$spreadsheet = new Spreadsheet();

		$sheet = $spreadsheet->getActiveSheet();
		$from = "A1"; // or any value
		$to = "A11"; // or any value
		$sheet->getStyle("$from:$to")->getFont()->setBold( true );

		$from1 = "A17"; // or any value
		$to1 = "Q17"; // or any value
		$sheet->getStyle("$from1:$to1")->getFont()->setBold( true );

		$sheet->getStyle("B12")->getFont()->setBold( true );
		$sheet->getStyle("C12")->getFont()->setBold( true );
		$sheet->getStyle("D12")->getFont()->setBold( true );

		$sheet->getStyle("C12")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) ->getStartColor()->setARGB('00a0bfe0');
		$sheet->getStyle("D12")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) ->getStartColor()->setARGB('00a0bfe0');
		$sheet->getStyle("$from1:$to1")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID) ->getStartColor()->setARGB('00a0bfe0');

		$sheet->setCellValue('A1', 'Project Name');
		$sheet->setCellValue('B1', '');
		$sheet->setCellValue('C1', $project_details->{'project_name'});

		$sheet->setCellValue('A2', 'Developed By');
		$sheet->setCellValue('B2', '');
		$sheet->setCellValue('C2', 'PharmaDEM Solutions Logo');

		$sheet->setCellValue('A3', 'Job Submission Date');
		$sheet->setCellValue('B3', '');
		$sheet->setCellValue('C3', $this->page_data['jobDetail']->process_start);

		$sheet->setCellValue('A4', 'Submitted By');
		$sheet->setCellValue('B4', '');
		$sheet->setCellValue('C4', 'Ravi');

		$sheet->setCellValue('A5', 'Api Name');
		$sheet->setCellValue('B5', '');
		$sheet->setCellValue('C5', $project_details->{'project_name'});

		$sheet->setCellValue('A6', 'Expt Imformation');
		$sheet->setCellValue('B6', '');
		$sheet->setCellValue('C6', 'hfuss_value = '.$this->page_data['jobDetail']->hfuss_value);
		

		$sheet->setCellValue('A7', '');
		$sheet->setCellValue('B7', '');
		$sheet->setCellValue('C7', 'MP = ');

		$sheet->setCellValue('A8', '');
		$sheet->setCellValue('B8', '');
		$sheet->setCellValue('C8', 'MW = '.$this->page_data['jobDetail']->mol_weight);

		$sheet->setCellValue('A9', 'Known Solubility Imformation');
		$sheet->setCellValue('B9', '');
		$sheet->setCellValue('C9', $this->page_data['jobDetail']->know_solubility);

		$sheet->setCellValue('A10', 'Structure Smiles');
		$sheet->setCellValue('B10', '');
		$sheet->setCellValue('C10', $this->page_data['jobDetail']->structure_code.' '.$this->page_data['jobDetail']->smiles);

		$sheet->setCellValue('A11', 'General Notes');
		$sheet->setCellValue('B11', '');
		$sheet->setCellValue('C11', 'PharmaDEM Solutions Logo');

		$sheet->setCellValue('A12', '');
		$sheet->setCellValue('B12', 'Solubility Class');
		$sheet->setCellValue('C12', 'Category');
		$sheet->setCellValue('D12', 'Mg/ml');

		$sheet->setCellValue('A13', '');
		$sheet->setCellValue('B13', '');
		$sheet->setCellValue('C13', 'Highly Soluble');
		$sheet->setCellValue('D13', '>100');

		$sheet->setCellValue('A14', '');
		$sheet->setCellValue('B14', '');
		$sheet->setCellValue('C14', 'Soluble');
		$sheet->setCellValue('D14', '50 to 100');

		$sheet->setCellValue('A15', '');
		$sheet->setCellValue('B15', '');
		$sheet->setCellValue('C15', 'Moderate');
		$sheet->setCellValue('D15', '10 to 50');

		$sheet->setCellValue('A16', '');
		$sheet->setCellValue('B16', '');
		$sheet->setCellValue('C16', 'Insoluble');
		$sheet->setCellValue('D16', '<10');

		$sheet->setCellValue('A17', 'Solvent Id');
		$sheet->setCellValue('B17', 'Solvent System');
		$sheet->setCellValue('I17', '10CMGML');
		$sheet->setCellValue('L17', '25CMGML');
		$sheet->setCellValue('O17', '50CMGML');

		$count = 18;

		foreach($this->page_data['cdata'] as $key=>$value)
		{
			// $solvent = $this->projects_model->getSolventDataBySid($value['result_job_id']);
			$sheet->setCellValue('A' . $count, $key + 1);
			$sheet->setCellValue('B' . $count, $value['Solvent_System']);
			$sheet->setCellValue('I' . $count,  number_format(((float)$value['10_cmgml']),2,'.',''));
			$sheet->setCellValue('L' . $count,  number_format(((float)$value['25_cmgml']),2,'.',''));
			$sheet->setCellValue('O' . $count,  number_format(((float)$value['50_cmgml']),2,'.',''));

			$count++;
		}

		$writer = new Xlsx($spreadsheet);

		$writer->save($file_name);

		header("Content-Type: application/vnd.ms-excel");

		header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');

		header('Expires: 0');

		header('Cache-Control: must-revalidate');

		header('Pragma: public');

		header('Content-Length:' . filesize($file_name));

		flush();

		readfile($file_name);

		exit;   
	}

	public function generatePhPdf($id)
	{
		set_time_limit(120);
		ini_set('max_exection_time', 60);
		ini_set("pcre.backtrack_limit", "50000000");
		$this->page_data['cdata'] =  $this->projects_model->getDataForPhExcel($id);
		$this->page_data['jobDetail'] = $this->projects_model->getJobDetail($id);
		$project_details = $this->projects_model->getById($this->page_data['jobDetail']->project_id);
		// $this->page_data['solventData'] = $this->projects_model->getresultsdata($id);
		$mpdf = new \Mpdf\Mpdf();
		$html = $this->load->view('projects/pdf_ph',$this->page_data,true);
		$mpdf->WriteHTML($html);
		$pdfFilename = 'Ph-'.$project_details->{'project_name'}.".pdf";
		$mpdf->Output($pdfFilename, "D");     
	}	


	public function solubilityCorrection($id) {

      	$this->page_data['jstatus'] = $this->projects_model->getJobdetails($id);
        $this->load->view('projects/solubility_correction', $this->page_data);
     
     }



}

/* End of file Users.php */
/* Location: ./application/controllers/Users.php */