<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CronQueucontroller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        set_include_path(get_include_path() . PATH_SEPARATOR . APPPATH.'third_party/phpseclib1.0.20');
		include('Net/SSH2.php');
		include('Net/SFTP.php');
		define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
        // Any constructor code you need to add
    }

public function runqueue()
{

    $oldestJobId = $this->projects_model->getOldestJobId(); 

    $this->checkcosmo();
    
    if($oldestJobId != NULL){
            /*---------------Step1 DEM file creation------------------*/
            $checkjob = $this->checkjob($oldestJobId);
            
            /*---------------Step2 Solubilty Calculation ------------------*/
            if($checkjob== true){
               $runact_newq =  $this->runact_newq($oldestJobId);

                /*----------------3rd step insert all data and optimization-------*/
                if($runact_newq== true){
                    $this->insertresultsAll($oldestJobId);
                }

            }

    }



}

public function checkcosmo() {
    // Execute the query to retrieve job IDs with the status "Processing"
    $query = $this->db->query("SELECT id FROM jobs_master WHERE cosmo_status IN ('Processing')");
   
    // Check if any rows were returned
    if ($query->num_rows() > 0) {
        // Loop through the results
        foreach ($query->result() as $row) {
            // Pass the job ID to your controller function
            //$this->yourOtherFunction($row->job_id);

            //echo $row->id;

            $this->checkjob($row->id);
        }
    }
}


public function checkjob($id)
	{
		//$connection = @ssh2_connect('128.199.31.121', 22);
	

		//$this->projects_model->update($id, ['status' => get('status') == 'true' ? 1 : 0 ]);
		$jobdetails = $this->projects_model->getJobstatus($id);
        $existingRecord = $this->db->get_where('tasks_queue', array('job_id' => $id,'status'=>'pending'))->row();
       
        if($jobdetails[0]->cosmo_status =='Cosmo File Generated'){
            if ($existingRecord) {
                if($existingRecord->job_type==''){
                    $data = array('status' => 'Completed');
                    $this->db->where('job_id', $id);
                    $this->db->update('tasks_queue', $data);
                }
            }
            return true;
        }
        if($jobdetails[0]->cosmo_status =='Processing'){
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
                if ($existingRecord) {
                    if($existingRecord->job_type==''){
                        $data = array('status' => 'Completed');
                        $this->db->where('job_id', $id);
                        $this->db->update('tasks_queue', $data);
                    }
                }

        	   return true;
            }
		
		}
            
        if($jobdetails[0]->cosmo_status =='Pending'){

            $data = array('execution_started_on' => date('Y-m-d H:m:i'));
            $this->db->where('job_id', $id);
            $this->db->update('tasks_queue', $data);
              
            $ssh = new Net_SSH2('128.199.31.121',22);
            if (!$ssh->login('chemistry1', 'Ravi@1234')) {
                exit('Login Failed');
            }
            $fullname=$jobdetails[0]->inp_filename;
            $Smile=$jobdetails[0]->smiles;
            $mvalue=$jobdetails[0]->inp_value;
            $fname = pathinfo($fullname, PATHINFO_FILENAME);
            $fcosmoname="COSMO_TZVPD";
            $cosmofile_name=$fname."_c000.orcacosmo";
           
            
            $sftp = new Net_SFTP('128.199.31.121',22);
            if (!$sftp->login('chemistry1', 'Ravi@1234')) {
            exit('Login Failed');
            }

            $directory_path = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$fname; // the path of the directory you want to check

            if ($sftp->file_exists($directory_path)) { // check if the directory exists
                  return false;
            }

            //chmod("/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/$fullname", 0777);
            $contentf = $fname."\t".$Smile."\t".$mvalue;
            $ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; touch '.$fullname.'; echo "'.$contentf.'" >> '.$fullname.'');
            $ssh->exec('cd /home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy; nohup python3 ConformerGenerator.py --structures_file '.$fullname.' --n_cores=16 > t.log 2>&1 & echo $!');


            $data = [
                'cosmo_status   ' => 'Processing',
            ];
            $this->db->where('id', $id);
            $this->db->update('jobs_master', $data);

            $directory_path_check = '/home/chemistry1/einnel/opencosmos/openCOSMO-RS_py/src/opencosmorspy/'.$fname.'/'.$fcosmoname.'/'.$cosmofile_name; // the path of the directory you want to check

            if ($sftp->file_exists($directory_path_check)) { // check if the directory exists
            //echo "The directory $directory_path exists.";
                $cosmo_data= $sftp->get($directory_path_check);
                
                $data = [
                    'process_end    ' => date('m/d/Y h:i:s a', time()),
                    'cosmo_status   ' => 'Cosmo File Generated',
                    'cosmo_data ' => $cosmo_data,
                ];
                $this->db->where('id', $id);
                $this->db->update('jobs_master', $data);
                return true;
                //echo 'order has successfully been updated';

                
           
           }
        }

		
	}


public function runact_newq($id) {


    $checkjobinsertornot = $this->projects_model->checkjobinsertornot($id);
    if($checkjobinsertornot){
        return true;
    }
    $existingRecord = $this->db->get_where('tasks_queue', array('job_id' => $id,'status'=>'pending'))->row();
    //print_r($existingRecord);
    if ($existingRecord) {
        $solvent_type=$existingRecord->job_type;
        $id = $existingRecord->job_id;
        //$stype=$existingRecord->job_type;
        if($existingRecord->execution_started_on == NULL){

            if($solvent_type==''){
                $data = array('status' => 'Completed');
                $this->db->where('job_id', $id);
                $this->db->update('tasks_queue', $data);
            }

            $data = array('execution_started_on' => date('Y-m-d H:m:i'));
            $this->db->where('job_id', $id);
            $this->db->update('tasks_queue', $data);

            /*----Custom solubility--------------------*/
            if($existingRecord->is_custom =='1'){
                $jid = $id;
                $ids = explode(',', $existingRecord->selected_solvents_ids);
                $jtype = $solvent_type;
                $this->customcalulation($ids,$jtype,$jid);

            } else {
                if($solvent_type=="Pure_68") {
                    $this->pure_68_new($id,$solvent_type,"10",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Pure_68");
                    $this->db->where('tempr', "10");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(10);

                    $this->pure_68_new($id,$solvent_type,"25",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Pure_68");
                    $this->db->where('tempr', "25");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(10);

                    $this->pure_68_new($id,$solvent_type,"50",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Pure_68");
                    $this->db->where('tempr', "50");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(10);

                    return true;
                }

                if($solvent_type=="Binary_1085") {

                    $this->pure_68_new($id,"Pure_68","10",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Pure_68");
                    $this->db->where('tempr', "10");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(10);

                    $this->pure_68_new($id,"Pure_68","25",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Pure_68");
                    $this->db->where('tempr', "25");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(10);

                    $this->pure_68_new($id,"Pure_68","50",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Pure_68");
                    $this->db->where('tempr', "50");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(10);

                    $this->binary_1085_new($id,$solvent_type,"10",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Binary_1085");
                    $this->db->where('tempr', "10");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(5);

                    $this->binary_1085_new($id,$solvent_type,"25",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Binary_1085");
                    $this->db->where('tempr', "25");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(5);

                    $this->binary_1085_new($id,$solvent_type,"50",0);
                    $this->db->where('job_id', $id);
                    $this->db->where('solvent_type', "Binary_1085");
                    $this->db->where('tempr', "50");
                    $this->db->update('job_results_count', array('status'=>'Completed'));
                    //sleep(5);

                    return true;
                    //echo $solvent_type;
                }
            }
            
        }
           
    }
}

public function insertresultsAll($id){

    $jobdetails  = $this->projects_model->getJobdetails1($id);
    $project_id  = $jobdetails[0]->project_id;
    $checkjobinserts = $this->projects_model->checkjobinserts($project_id);
    if($checkjobinserts){
        $data = array('status' => 'Completed');
        $this->db->where('job_id', $id);
        $this->db->update('tasks_queue', $data);
    }
    $this->projects_model->insertresults10($project_id);
    $this->projects_model->insertresults25($project_id);
    $this->projects_model->insertresults50($project_id);

    $data = array('status' => 'Completed');
    $this->db->where('job_id', $id);
    $this->db->update('tasks_queue', $data);
}

public function pure_68_new($id,$stype,$temp,$sv_id) {

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
//$solvents_master = $this->projects_model->getsolvents();
$solvents_master = $this->projects_model->getsolventsbs($stype,$sv_id);

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

public function binary_1085_new($id,$stype,$temp,$sv_id) {

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
        $solvents_master = $this->projects_model->getsolventsbs($stype,$sv_id);
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
    $this->db->where('id', $jid);
    $this->db->update('job_results_count', array('solvent_activity_finished' => $lastProcessedIndex + 1, 'process_end' => date('m/d/Y h:i:s a', time())));
    
    $ssh->disconnect();
    
    
}

public function updatedt()

{

    $this->db->where('job_results_count.status', 'Pending');
    $this->db->select_max('job_results_count.solvent_activity_finished');
    $this->db->get('job_results_count')->row()->solvent_activity_finished;

    $stuckJobs = $this->db->query("
        SELECT *, now()
        FROM job_results_count
        WHERE TIMESTAMPDIFF(SECOND, STR_TO_DATE(process_start, '%m/%d/%Y %h:%i:%s %p'), NOW()) >= 60
            AND status = 'Pending'
    ")->result_array();

    if(empty($stuckJobs[0]['process_end']))
    {
        $currentDateTime = date('m/d/Y h:i:s a');

        $data = array(
            'process_end' => $currentDateTime
         );
         
         $this->db->where('id', $stuckJobs[0]['id']);
         $this->db->update('job_results_count', $data);
         //print_r($this->db->last_query());
    }

}

    public function index() {
        
        $this->db->where('job_results_count.status', 'Pending');
				$this->db->select_max('job_results_count.solvent_activity_finished');
				$this->db->get('job_results_count')->row()->solvent_activity_finished;

				$stuckJobs = $this->db->query("
					SELECT *, now()
					FROM job_results_count
					WHERE TIMESTAMPDIFF(SECOND, STR_TO_DATE(process_end, '%m/%d/%Y %h:%i:%s %p'), NOW()) >= 60
						AND status = 'Pending'
				")->result_array();
               // print_r($this->db->last_query());
               // exit;


				if (!empty($stuckJobs)) {

                            $this->db->where('id', $stuckJobs[0]['id']);
							$this->db->update('job_results_count', array('status'=>'Completed'));
					
					$id = $stuckJobs[0]['job_id'];
					$stype = $stuckJobs[0]['solvent_type'];
					$temp = $stuckJobs[0]['tempr'];
					$sv_id = $stuckJobs[0]['sv_id']; 

                   
					//if ($stype=='Binary_1085') {
					
			
							$this->binary_1085_stopped($id,$stype,$temp,$sv_id);
							$this->db->where('job_id', $id);
							$this->db->where('solvent_type', $stype);
							$this->db->where('tempr', $temp);
							$this->db->update('job_results_count', array('status'=>'Completed'));
							//sleep(5);

                            if($temp=="10") {

                                $this->binary_1085_new($id,$stype,"25",0);
                                $this->db->where('job_id', $id);
                                $this->db->where('solvent_type', $stype);
                                $this->db->where('tempr', "25");
                                $this->db->update('job_results_count', array('status'=>'Completed'));

                                $this->binary_1085_new($id,$stype,"50",0);
                                $this->db->where('job_id', $id);
                                $this->db->where('solvent_type', $stype);
                                $this->db->where('tempr', "50");
                                $this->db->update('job_results_count', array('status'=>'Completed'));
                                
                            }
        
                            if($temp=="25") {
        
                                $this->binary_1085_new($id,$stype,"50",0);
                                $this->db->where('job_id', $id);
                                $this->db->where('solvent_type', $stype);
                                $this->db->where('tempr', "50");
                                $this->db->update('job_results_count', array('status'=>'Completed'));
                                
                            }
						
					//}


                    if ($stype=='Pure_68') {
					
                            $stype="Binary_1085";

                                $this->binary_1085_new($id,$stype,"10",0);
                                $this->db->where('job_id', $id);
                                $this->db->where('solvent_type', $stype);
                                $this->db->where('tempr', "25");
                                $this->db->update('job_results_count', array('status'=>'Completed'));

                                $this->binary_1085_new($id,$stype,"25",0);
                                $this->db->where('job_id', $id);
                                $this->db->where('solvent_type', $stype);
                                $this->db->where('tempr', "25");
                                $this->db->update('job_results_count', array('status'=>'Completed'));

                                $this->binary_1085_new($id,$stype,"50",0);
                                $this->db->where('job_id', $id);
                                $this->db->where('solvent_type', $stype);
                                $this->db->where('tempr', "50");
                                $this->db->update('job_results_count', array('status'=>'Completed'));
						
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
            $solvents_master = $this->projects_model->getsolventsbs($stype,$sstart+1);
            
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
        $this->db->where('id', $jid);
        $this->db->update('job_results_count', array('solvent_activity_finished' => $lastProcessedIndex + 1, 'process_end' => date('m/d/Y h:i:s a', time())));
        
        $ssh->disconnect();
        
        
    }
    public function customcalulation($ids,$jtype,$jid)
    {
       // session_write_close();

        $data = array('execution_started_on' => date('Y-m-d H:m:i'));
        $this->db->where('job_id', $jid);
        $this->db->update('tasks_queue', $data);
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

         return true;

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

         return true;
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
         return true;


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
            'process_start  ' => date('m/d/Y h:i:s a', time()),
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
            'input_temp_50  ' => $temp50,
            //'input_temp_20    ' => '',
            'solvent_result_name    ' =>$fname,
            'solvent_result ' => $stream,
            'processed_on   ' => date('m/d/Y h:i:s a', time()),
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
        'process_start  ' => date('m/d/Y h:i:s a', time()),
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
            'input_temp_50  ' => $temp50,
            //'input_temp_50    ' => 'Yes',
            'solvent_result_name    ' =>$fname,
            'solvent_result ' => $stream,
            'processed_on   ' => date('m/d/Y h:i:s a', time()),
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

}