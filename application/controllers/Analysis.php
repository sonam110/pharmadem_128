<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analysis extends MY_Controller {

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
		ifPermissions('analysis');
		
		//$this->page_data['projects'] = $this->projects_model->get();
		$this->load->view('analysis/details', $this->page_data);
	}

    public function scatter() {

      

        $id=47;
        //$data['data'] = $this->projects_model->get_scatterdata();
        //$this->page_data['analysis'] = $this->projects_model->get_scatterdata();
        $this->page_data['analysis'] = $this->projects_model->get_scatterdatap($id);

        $this->load->view('analysis/scatter', $this->page_data);
    
    }

    public function scatternew() {

      

        $id=47;
        //$data['data'] = $this->projects_model->get_scatterdata();
        //$this->page_data['analysis'] = $this->projects_model->get_scatterdata();
        $this->page_data['analysis'] = $this->projects_model->get_scatterdatap($id);

        $this->load->view('analysis/scatternew', $this->page_data);
    
    }

    public function get_scatter_data() {
        $project_id = $this->input->post('project');
        $temperature = $this->input->post('temperature');

        //$this->load->model('data_model');
        $data = $this->projects_model->get_temperature_data($project_id, $temperature);

        // Return the data as JSON
    //header('Content-Type: application/json');
    echo json_encode($data);
    }


    public function bar() {

      
       // $this->page_data['analysis'] = $this->projects_model->get_scatterdatap($id);

        $this->load->view('analysis/bar', $this->page_data);
    
    }

    public function barnew() {

      
        // $this->page_data['analysis'] = $this->projects_model->get_scatterdatap($id);
 
         $this->load->view('analysis/barnew', $this->page_data);
     
     }

     public function barnew1() {

      
        // $this->page_data['analysis'] = $this->projects_model->get_scatterdatap($id);
 
         $this->load->view('analysis/barnew1', $this->page_data);
     
     }


     
     public function getfecthdata()
     {
        $selectedProjects = $this->input->post('selectedProjects');
       

        $data = [];
        
        if (!empty($selectedProjects)) {
            foreach ($selectedProjects as $projectId) {
                // Fetch data for $projectId using your queries
                $jbd = $this->projects_model->getJobdetails($projectId);
        
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
                        job_results.s_id AS job_s_id, 
                        solvents_master.w1_solvent_system AS solvent_w1_solvent_system
                    ');
                    $this->db->from($table);
                    $this->db->join('job_results', 'job_results.id = ' . $table . '.result_job_id', 'left');
                    $this->db->join('solvents_master', 'solvents_master.s_id = job_results.s_id', 'left');
                    $this->db->where($table . '.job_id', $jbd[0]->id);
                    //$this->db->limit(150);
        
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
                    //$solvent_w1_system = array_column($results, 'solvent_w1_solvent_system');

                    $solvent_w1_system = array_column($results, 'solvent_w1_solvent_system');

                }
        
                // Check if solvent_w1_solvent_system exists in all three arrays
                if (!empty($solvent_w1_system) && count(array_filter($solvent_w1_system)) === count($solvent_w1_system)) {
					
                    // Get the project name
                    $projectName = $this->projects_model->getProjectName($projectId);
        
                    $data[] = [
                        'projectName' => $projectName,
                        'results_10' => array_column($results_10, '10cmgml'),
                        'results_10_ssystem_name' => array_column($results_10, 'ssystem_name'),
                        'results_10_10cvl' => array_column($results_10, '10cvl'),
                        'results_10_10cyl' => array_column($results_10, '10cyl'),



                        'results_25' => array_column($results_25, '25cmgml'),
                        'results_25_ssystem_name' => array_column($results_25, 'ssystem_name'),
                        'results_25_10cvl' => array_column($results_25, '25cvl'),
                        'results_25_25cyl' => array_column($results_25, '25cyl'),



                        'results_50' => array_column($results_50, '50cmgml'),
                        'results_50_ssystem_name' => array_column($results_50, 'ssystem_name'),
                        'results_50_50cvl' => array_column($results_50, '50cvl'),
                        'results_50_50cyl' => array_column($results_50, '50cyl'),



                        'results_10_id' => array_column($results_10, 'id'),
                        'results_50_id' => array_column($results_50, 'id'),

                        'solvent_w1_solvent_system' => $solvent_w1_system,
                    ];
                }
            }
        }
echo json_encode(['data' => $data], JSON_PRETTY_PRINT);


        

     }

public function generateCsv()
    {

	set_time_limit(120);
    	$selectedProjects = $this->input->post('selectedProjects');

    	$data = [];

    	if (!empty($selectedProjects)) {
    		foreach ($selectedProjects as $projectId) {
		        // Fetch data for $projectId using your queries
    			$jbd = $this->projects_model->getJobDetailByproject($projectId);
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
		        			'10_cmgml' => $results_10[$index]->{'10cmgml'},
		        			'10_cvl' => $results_10[$index]->{'10cvl'},
		        			'10_cyl' => $results_10[$index]->{'10cyl'},
		        			'25_cmgml' => $results_25[$index]->{'25cmgml'},
		        			'25_cvl' => $results_25[$index]->{'25cvl'},
		        			'25_cyl' => $results_25[$index]->{'25cyl'},
		        			'50_cmgml' => $results_50[$index]->{'50cmgml'},
		        			'50_cvl' => $results_50[$index]->{'50cvl'},
		        			'50_cyl' => $results_50[$index]->{'50cyl'},
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
			$csvData .= "$project-10 C mgml{$delimiter}$project-10 Cvl{$delimiter}$project-10 Cyl{$delimiter}$project-25C mgml{$delimiter}$project-25 Cvl{$delimiter}$project-25 Cyl{$delimiter}$project-50C mgml{$delimiter}$project-50 Cvl{$delimiter}$project-50 Cyl{$delimiter}";
		}
		$csvData = rtrim($csvData, $delimiter) . "\n";
		foreach ($data as $solvent => $projects) {
		    // Encapsulate entire CSV field containing the solvent name in double quotes
			$csvData .= '"' . str_replace('"', '""', $solvent) . '"' . $delimiter;
			foreach ($projects as $project => $results) {
				// $csvData .= "{$results['result_10']}{$delimiter}{$results['result_25']}{$delimiter}{$results['result_50']}{$delimiter}";
				$csvData .= "{$results['10_cmgml']}{$delimiter}{$results['10_cvl']}{$delimiter}{$results['10_cyl']}{$delimiter}{$results['25_cmgml']}{$delimiter}{$results['25_cvl']}{$delimiter}{$results['25_cyl']}{$delimiter}{$results['50_cmgml']}{$delimiter}{$results['50_cvl']}{$delimiter}{$results['50_cyl']}{$delimiter}";
			}
			$csvData = rtrim($csvData, $delimiter) . "\n";
		}

		// Set headers for CSV download
		// header('Content-Type: application/csv');
		header('Content-Type: application/json');
		// header('Content-Disposition: attachment; filename="project_results.csv"');

		// Output CSV data
		echo json_encode(['csvData' => $csvData]);
		// echo $csvData;
	}     
     public function get1050() {


        $rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);



$processedResults = array();
    $allBatchesProcessed = false; // Initialize a flag

$batchSize = 100; // Define your desired batch size

if (is_array($data)) {
    $i = 0;
    $jbd = array(); // Initialize an array to store $jbd values
    $allBatchesProcessed = false; // Initialize a flag

    foreach ($data as $item) {
        foreach ($item as $result) {

            
            $results_10_id = trim($result['results_10_id']);
            $results_50_id = trim($result['results_50_id']);

           // Fetch data from results_data_10 table based on results_10_id
           $query = $this->db->get_where('results_data_10', array('id' => $results_10_id));
           $result_10_data = $query->row_array();

           // Fetch data from results_data_50 table based on results_50_id
           $query = $this->db->get_where('results_data_50', array('id' => $results_50_id));
           $result_50_data = $query->row_array();


           // Swap 10cmgml and 50cmgml values
           $temp_10cmgml = $result_10_data['10cmgml'];
           $temp_50cmgml = $result_50_data['50cmgml'];
           $jid = $result_10_data['job_id'];

           //echo $jid;
           
    
             // Update results_data_10 with 50cmgml
             $this->db->update('results_data_10', array('10cmgml' => $temp_50cmgml), array('id' => $results_10_id));

             // Update results_data_50 with 10cmgml
             $this->db->update('results_data_50', array('50cmgml' => $temp_10cmgml), array('id' => $results_50_id));
             print_r($this->db->last_query());	
           //echo $jid;

           //$jbd = $this->projects_model->getJobdetails1($jid);

           //$jbd[] = $this->projects_model->getJobdetails1($jid);

           
          // echo $jbd[0]->project_id;
           //exit;

           $query = $this->db->get_where('results_data_10', array('id' => $results_10_id));
           $tenr = $query->row();

           $query = $this->db->get_where('results_data_50', array('id' => $results_50_id));
           $fenr = $query->row();

           //$query = $this->db->query("select sm.w1_density,jr.solvents, sm.w1_solvent_system, jr.pure_data1,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr, jobs_master jm where jr.input_temp_10 = 10  and sm.s_id=jr.s_id and jr.solvent_result not like '%Traceback%' and jr.solvent_result <>'' and jm.id=jr.job_id  and jm.id=".$jid);
           $query = $this->db->query("select sm.w1_density,jr.solvents, sm.w1_solvent_system, jr.pure_data1,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr where jr.input_temp_10 = 10  and sm.s_id=jr.s_id and jr.id=" . $tenr->result_job_id);
           $results_10 = $query->row();

           $query = $this->db->query("select sm.w1_density,jr.solvents, sm.w1_solvent_system, jr.pure_data1,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr where jr.input_temp_50 = 50  and sm.s_id=jr.s_id and jr.id=" . $fenr->result_job_id);
           $results_50 = $query->row();

           //$tencvl = $this->projects_model->get10cvlinsert($results_10->pure_data1,$results_10->w1_density,$results_10->job_id);
           $tencyl = $this->projects_model->get10cYNinsert($results_10->pure_data1,$results_10->job_id,$results_10->job_id,$results_10->solvents);

           //echo $tencyl;
                 // Update results_data_10 with 50cmgml
                 $this->db->update('results_data_10', array('10cyl' => $tencyl), array('id' => $results_10_id));
                 //print_r($this->db->last_query());	
                 $fcyl = $this->projects_model->get10cYNinsert($results_50->pure_data1,$results_50->job_id,$results_50->job_id,$results_50->solvents);

                 // Update results_data_50 with 10cmgml
                 $this->db->update('results_data_50', array('50cyl' => $fcyl), array('id' => $results_50_id));
                // print_r($this->db->last_query());
            // Store $jbd values in the array
            $jbd[] = $this->projects_model->getJobdetails1($jid);

            $i++;
            
            // Check if the batch size has been reached or if it's the last iteration
            if ($i % $batchSize === 0 || $i === count($data)) {
                $this->db->trans_start(); // Start a database transaction
                
                // Loop through the $jbd array and update projects table
                foreach ($jbd as $jb) {
                    $this->db->update('projects', array('optimised' => 'Yes'), array('id' => $jb[0]->project_id));
                }

                $this->db->trans_complete(); // End the database transaction
                
                // Reset the $jbd array for the next batch
                $jbd = array();
            }

            
        }
    }

    $allBatchesProcessed = true; // Initialize a flag

}



if($allBatchesProcessed) {
    return 'Done'; 
}

    }
    
    

     public function newfecth()
{
    $selectedProjects = $this->input->post('selectedProjects');
    $data = [];

    if (!empty($selectedProjects)) {
        foreach ($selectedProjects as $projectId) {
            // Fetch data for $projectId using your queries
            $jbd = $this->projects_model->getJobdetails($projectId);
            // Fetch data from results_data_10, results_data_25, and results_data_50 using your queries
            $this->db->select('results_data_10.*, CAST(at10 AS float), job_results.s_id, solvents_master.w1_solvent_system');
            $this->db->where('results_data_10.job_id', $jbd[0]->id);
            $this->db->from('results_data_10');
            $this->db->join('job_results', 'job_results.id = results_data_10.result_job_id', 'left');
            $this->db->join('solvents_master', 'solvents_master.s_id = job_results.s_id', 'left');
            $data[] = $this->db->get()->result();



            $this->db->select('results_data_25.*,CAST(at25 AS float), job_results.s_id, solvents_master.w1_solvent_system');
            $this->db->where('results_data_25.job_id =', $jbd[0]->id);
            //$this->db->limit($recordsp);
            $this->db->from('results_data_25');
            $this->db->join('job_results', 'job_results.id = results_data_25.result_job_id', 'left');
            $this->db->join('solvents_master', 'solvents_master.s_id = job_results.s_id', 'left');
            $data[] = $this->db->get()->result();


            $this->db->select('results_data_50.*,CAST(at50 AS float), job_results.s_id, solvents_master.w1_solvent_system');
            $this->db->where('results_data_50.job_id =', $jbd[0]->id);
            $this->db->from('results_data_50');
            $this->db->join('job_results', 'job_results.id = results_data_50.result_job_id', 'left');
            $this->db->join('solvents_master', 'solvents_master.s_id = job_results.s_id', 'left');

            $data[] = $this->db->get()->result();

        }
    }


    echo json_encode(['data' => $data]);
    // You can format the $data as HTML or JSON based on your needs
   //// $this->load->view('analysis/barnew', $data);

   // $this->load->view('data_view', ['data' => $data]);
}



    public function generate()
    {
        $set1Start = $this->input->post('set1_start');
        $set2Start = $this->input->post('set2_start');
        $set3Start = $this->input->post('set3_start');
	 
	    $selapi_50_operator=$this->input->post('selapi_50_operator');
        $selapi_20_operator=$this->input->post('selapi_25_operator');
        $selapi_10_operator=$this->input->post('selapi_10_operator');
        $project_id = $this->input->post('project_id');
        
    
		$recordsp = $this->input->post('recordsp');

		$data = array();
  
        $chartModel = new projects_model();

        $set1Data = $chartModel->getRangeDataA1(1, $set1Start,$set2Start,$set3Start, $selapi_50_operator, $selapi_20_operator, $selapi_10_operator,$project_id,$recordsp);
 
		$cdata = $set1Data;


        $query10 = $this->db->last_query();
        $query10 = $this->db->query($query10);

        $cdata10= $query10->result_array();


       // print_r($this->db->last_query());	

        $set2Data = $chartModel->getRangeDataA1(2, $set1Start,$set2Start,$set3Start, $selapi_50_operator, $selapi_20_operator, $selapi_10_operator,$project_id,$recordsp);
 
		$cdata2 = $set2Data;

        $query25 = $this->db->last_query();
        $query25 = $this->db->query($query25);
        
        $cdata25= $query25->result_array();

       //print_r($this->db->last_query());	

        $set3Data = $chartModel->getRangeDataA1(3, $set1Start,$set2Start,$set3Start, $selapi_50_operator, $selapi_20_operator, $selapi_10_operator,$project_id,$recordsp);
 
		$cdata3 = $set3Data;

        $query50 = $this->db->last_query();
        $query50 = $this->db->query($query50);

        
        $cdata50= $query50->result_array();

        //print_r( $cdata50);

    //print_r($this->db->last_query());	

       $jbd = $this->projects_model->getprojectdetails($project_id);
       $_SESSION['projectname'] = $jbd[0]->project_name;

        $solventNames10 = [];
        $values10 = [];
        $s_ids = [];

        
       // print_r($cdata);

        foreach ($cdata as $row) {
            $solventNames10[] = $row['w1_solvent_system'];
            $values10[] = $row['10cmgml'];
            $s_ids[]  =  $row['s_id'];
        }
        
        $alignedValues10 = array();
        foreach ($solventNames10 as $index => $label10) {
            $alignedValues10[$label10] = $values10[$index];
        }
        $this->page_data['solventNames10'] = $solventNames10;
        $this->page_data['alignedValues10'] = $alignedValues10;
        $this->page_data['sids'] = $s_ids;
        $this->page_data['scount'] = count($cdata);

       
        
        $solventNames25 = [];
        $values25 = [];
        
        foreach ($cdata2 as $row2) {
            $solventNames25[] = $row2['w1_solvent_system'];
            $values25[] = $row2['25cmgml'];
        }
        
        $alignedValues25 = array();
        foreach ($solventNames25 as $index => $label25) {
            $alignedValues25[$label25] = $values25[$index];
        }
        $this->page_data['solventNames25'] = $solventNames25;
        $this->page_data['alignedValues25'] = $alignedValues25;
        
        $solventNames50 = [];
        $values50 = [];
        
        foreach ($cdata3 as $row3) {
            $solventNames50[] = $row3['w1_solvent_system'];
            $values50[] = $row3['50cmgml'];
        }
        
        $alignedValues50 = array();
        foreach ($solventNames50 as $index => $label50) {
            $alignedValues50[$label50] = $values50[$index];
        }

        //print_r($solventNames50);
        $this->page_data['solventNames50'] = $solventNames50;
        $this->page_data['alignedValues50'] = $alignedValues50;
        //echo 'Project Name: ' . $jbd[0]->project_name . '<br>';

// Create an associative array with the data
$this->page_data['datadet'] = array(
            'set1Start' => $set1Start,
            'set2Start' => $set2Start,
            'set3Start' => $set3Start,
            'selapi_50_operator' => $selapi_50_operator,
            'selapi_20_operator' => $selapi_20_operator,
                 'selapi_10_operator' => $selapi_10_operator,
             'project_id' => $project_id
             
        );

        //print_r($alignedValues10 );
        
        //$this->page_data['cdata10'] = $cdata10;
		//$this->page_data['cdata25'] = $cdata25;
		//$this->page_data['cdata50'] = $cdata50;

        if (!isset($_SESSION['s_ids'])) {
            // Session doesn't exist, retrieve the s_id values from the first query
            $sIds = array();
        
            // Store the s_id values in the session
            $_SESSION['s_ids'] = $s_ids;
                } else {
            // Session exists, retrieve the s_id values from the session
            $s_ids = $_SESSION['s_ids'];
            }

        
            //$this->page_data['datadet'] = $datadet;
            

            $this->load->view('analysis/bar', $this->page_data);


      

    }



    public function clearss()
    {
        session_start();
    
    // Clear the session for additionalDatasets
    unset($_SESSION['additionalDatasets']);
    unset($_SESSION['projectname']);
    unset($_SESSION['s_ids']);
    unset($_SESSION['commonLabels']);
    unset($_SESSION['projectDetails']);


    
    redirect('analysis/bar');
    }

     

    // Assuming this code is inside a controller method
public function generate_chart()
{
    session_start();
    $set1Start = $this->input->post('set1_start');
    $set2Start = $this->input->post('set2_start');
    $set3Start = $this->input->post('set3_start');
    $selapi_50_operator = $this->input->post('selapi_50_operator');
    $selapi_20_operator = $this->input->post('selapi_25_operator');
    $selapi_10_operator = $this->input->post('selapi_10_operator');
    $project_id = $this->input->post('project_id');
    $recordsp = $this->input->post('recordsp');
    
    $chartModel = new Projects_model();

    $set1Data = $chartModel->getRangeDataA1(1, $set1Start, $set2Start, $set3Start, $selapi_50_operator, $selapi_20_operator, $selapi_10_operator, $project_id, $recordsp);
    $set2Data = $chartModel->getRangeDataA1(2, $set1Start, $set2Start, $set3Start, $selapi_50_operator, $selapi_20_operator, $selapi_10_operator, $project_id, $recordsp);
    $set3Data = $chartModel->getRangeDataA1(3, $set1Start, $set2Start, $set3Start, $selapi_50_operator, $selapi_20_operator, $selapi_10_operator, $project_id, $recordsp);
  
    $jbd = $this->projects_model->getprojectdetails($project_id);

    $solventNames10 = [];
    $values10 = [];
    foreach ($set1Data as $row) {
        $solventNames10[] = $jbd[0]->project_name . '->' . $row['ssystem_name'];
        $values10[] = $row['10cmgml'];
    }

    $solventNames25 = [];
    $values25 = [];
    foreach ($set2Data as $row) {
        $solventNames25[] = $jbd[0]->project_name . '->' . $row['ssystem_name'];
        $values25[] = $row['25cmgml'];
    }

    $solventNames50 = [];
    $values50 = [];
    foreach ($set3Data as $row) {
        $solventNames50[] = $jbd[0]->project_name . '->' . $row['ssystem_name'];
        $values50[] = $row['50cmgml'];
    }

    $alignedData10 = array_combine($solventNames10, $values10);
    $alignedData25 = array_combine($solventNames25, $values25);
    $alignedData50 = array_combine($solventNames50, $values50);

    $additionalDatasets = isset($_SESSION['additionalDatasets']) ? $_SESSION['additionalDatasets'] : [];

    $newDataset10 = [
        'label' => '10c mg/ml',
        'data' => array_values($alignedData10),
        'backgroundColor' => '#3A1078',
        'borderColor' => '#34495E',
        'borderWidth' => 1
    ];

    $newDataset25 = [
        'label' => '25c mg/ml',
        'data' => array_values($alignedData25),
        'backgroundColor' => '#39B5E0',
        'borderColor' => '#285C6F',
        'borderWidth' => 1
    ];

    $newDataset50 = [
        'label' => '50c mg/ml',
        'data' => array_values($alignedData50),
        'backgroundColor' => '#D61355',
        'borderColor' => '#006D90',
        'borderWidth' => 1
    ];

    $additionalDatasets[] = $newDataset10;
    $additionalDatasets[] = $newDataset25;
    $additionalDatasets[] = $newDataset50;

    $_SESSION['additionalDatasets'] = $additionalDatasets;

    //$this->page_data['additionalDatasets'] = $additionalDatasets;

    $this->page_data['commonLabels'] = $commonLabels;
    $this->page_data['additionalDatasets'] = $additionalDatasets;
    $this->page_data['alignedData10'] = $alignedData10;
    $this->page_data['alignedData25'] = $alignedData25;
    $this->page_data['alignedData50'] = $alignedData50;
    $this->page_data['commonLabels'] = $commonLabels;


    // Load the "bar" view
    $this->load->view('analysis/barone', $this->page_data);

    // Redirect to the chart view page
   // redirect('barone');
}



    public function getscatter()

    {
       $project_id=$this->input->post('projectid');;
       $tempa=$this->input->post('tempa');

      // echo $project_id;

       if($tempa==50) {
        $tm="jr.input_temp_50 = 50";
       }
       if($tempa==25) {
        $tm="jr.input_temp_20 = 25";
       }
       if($tempa==10) {
        $tm="jr.input_temp_10 = 10";
       }


       $query = $this->db->query("select sm.w1_density, sm.w1_solvent_system, jr.pure_data1,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr, jobs_master jm where ".$tm." and sm.s_id=jr.s_id and jr.solvent_result not like '%Traceback%' and jm.id=jr.job_id and jm.project_id=".$project_id."");
       //echo $this->db->last_query();
       $analysis = $query->result_array();
       
    
$da="";
$la="";
$dd="";
$datap =array();
$labels = array();
foreach ($analysis as $row):
  $da.='{x: '.$this->projects_model->get10cvlscatter($row['pure_data1'],$row['w1_density'],$row['job_id']).','. ' y: '.$this->projects_model->get10cYforscatter($row['pure_data1']) .'},';
   $la.="'".$row['w1_solvent_system']."' ,";

  // $da.='{x: '.$row["API_Cooling_yield"].','. ' y: '. $row["API_Solvent_vol_50C"].'},';
  // $la.="'".$row['Solvent_System']."' ,";

  //$dd.="['x' => ".$this->projects_model->get10cvlscatter($row['pure_data1'],$row['w1_density']).", 'y' => ".$this->projects_model->get10cYforscatter($row['pure_data1'])."],";
 //$labels[] array($row['w1_solvent_system']);
  $datap[] = array('label:'.$row['w1_solvent_system'],'x' => $this->projects_model->get10cvlscatter($row['pure_data1'],$row['w1_density'],$row['job_id']), 'y' => $this->projects_model->get10cYforscatter($row['pure_data1'],$row['jbid'],$row['job_id'],$row['w1_density']));
 endforeach; 
 


   $yourString = rtrim($da, ",");
   $youLa = rtrim($la, ",");
//echo $youLa;

//echo $yourString;

$data = [
    ['x' => 10, 'y' => 20],
    ['x' => 15, 'y' => 30],
    ['x' => 20, 'y' => 40],
 

    // ... more data points ...
  ];

  //echo $dd;
  echo json_encode($datap);
        

    }


    public function getscatterfinal()
    {

        $project_id=$this->input->post('projectid');;
        $tempa=$this->input->post('tempa');
        //$xcvl=$this->input->post('xRange');
        //$ycyl=$this->input->post('yRange');
          $ycyl=$this->input->post('xRange');
          $xcvl=$this->input->post('yRange');
          
		$jbd = $this->projects_model->getJobdetails($project_id);


        if($tempa==50) {

     $this->db->select('sm.w1_solvent_system, 50cvl, 50cyl');
     $this->db->join('job_results jr', 'result_job_id = jr.id', 'inner');

     $this->db->join('solvents_master sm', 'jr.s_id = sm.s_id', 'inner');
$this->db->where('results_data_50.job_id', $jbd[0]->id);
$this->db->where('results_data_50.50cyl >=', (float) $xcvl);
$this->db->where('results_data_50.50cvl <=', (float) $ycyl);



        

        //$this->db->limit(10);

        $query = $this->db->get('results_data_50'); // Replace 'your_table_name' with the actual table name
        //echo $this->db->last_query();
       // $this->db->where('results_data_50.50cvl >=', 50);

        $results = $query->result();
        $dataPoints = array();
        foreach ($results as $row) {
           $x = $row->{'50cvl'};
          $y = $row->{'50cyl'};
          $label = $row->w1_solvent_system;
         $dataPoints[] = array('x' => $x, 'y' => $y, 'label' => $label);
        }

        echo json_encode($dataPoints);


            //$tm="jr.input_temp_50 = 50";
           }
           if($tempa==25) {
            $this->db->select('sm.w1_solvent_system, 25cvl, 25cyl');
            $this->db->join('job_results jr', 'result_job_id = jr.id', 'inner');
       
            $this->db->join('solvents_master sm', 'jr.s_id = sm.s_id', 'inner');
            $this->db->where('results_data_25.job_id', $jbd[0]->id);
            $this->db->where('results_data_25.25cvl >=', (float) $xcvl);
            $this->db->where('results_data_25.25cyl <=', (float) $ycyl);
            //$this->db->limit(10);
    
            $query = $this->db->get('results_data_25'); // Replace 'your_table_name' with the actual table name
            $results = $query->result();
            $dataPoints = array();
            foreach ($results as $row) {
               $x = $row->{'25cvl'};
              $y = $row->{'25cyl'};
              $label = $row->w1_solvent_system;
             $dataPoints[] = array('x' => $x, 'y' => $y, 'label' => $label);
            }
    
            echo json_encode($dataPoints);
           }
           if($tempa==10) {
            $this->db->select('sm.w1_solvent_system, 10cvl, 10cyl');
            $this->db->join('job_results jr', 'result_job_id = jr.id', 'inner');
       
            $this->db->join('solvents_master sm', 'jr.s_id = sm.s_id', 'inner');
        $this->db->where('results_data_10.job_id', $jbd[0]->id);
        $this->db->where('results_data_10.10cvl >=', (float) $xcvl);
            $this->db->where('results_data_10.10cyl <=', (float) $ycyl);
       // $this->db->limit(10);

        $query = $this->db->get('results_data_10'); // Replace 'your_table_name' with the actual table name
        $results = $query->result();
        $dataPoints = array();
        foreach ($results as $row) {
           $x = $row->{'10cvl'};
          $y = $row->{'10cyl'};
          $label = $row->w1_solvent_system;
         $dataPoints[] = array('x' => $x, 'y' => $y, 'label' => $label);
        }

        echo json_encode($dataPoints);
           }

    //print_r($this->db->last_query());	

    }


}