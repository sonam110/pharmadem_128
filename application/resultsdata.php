<?php
public function insertresults10knownsolu($id) {


		$pureDataFields = array('pure_data1', 'pure_data2', 'pure_data3', 'pure_data4', 'pure_data5');

		foreach ($pureDataFields as $pureDataField) {
	
			$query = $this->db->query("SELECT sm.w1_density,jr.solvents, sm.w1_solvent_system,jr.result_type, jr.$pureDataField as pdata, jr.job_id, jr.id as jbid 
				FROM solvents_master sm, job_results jr, jobs_master jm 
				WHERE jr.input_temp_10 = 10 AND sm.s_id=jr.s_id 
					AND jr.solvent_result NOT LIKE '%Traceback%' AND jr.solvent_result <>'' 
					AND jm.id=jr.job_id AND jm.project_id=".$id);

		//$query = $this->db->query("select sm.w1_density,jr.solvents, sm.w1_solvent_system, jr.pure_data1,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr, jobs_master jm where jr.input_temp_10 = 10  and sm.s_id=jr.s_id and jr.solvent_result not like '%Traceback%' and jr.solvent_result <>'' and jm.id=jr.job_id  and jm.project_id=".$id);
		$results_10 = $query->result();
		//echo $this->db->last_query();
		$jbd = $this->projects_model->getJobdetails($id);
		$addSolubility = array();
	
		foreach ($results_10 as $row)  {  // Call function to get values
		$result_job_id = $row->jbid;
		$job_id = $row->job_id;
		$ssystem_name = $row->solvents;
		if (!empty($row->pdata)) {
		$tencmgml = $this->projects_model->get10mgmlcal($row->pdata,$row->job_id);

   	 	/*----------Solubility prediction data for 10-------------*/

		$existing_data = $this->db->get_where('solubility_correction_data', array(
		    'job_id' => $job_id,
		    's_name' => $ssystem_name,
		    'temp' => '10' // Assuming 'temp' is another column you want to match
		))->row_array();
		if (!empty($existing_data)) {
		    $known_solubility = $existing_data['s_value'];
		} else {
		    $known_solubility = 0;
		}
		$addSolubility[] = array(
      	 	 'result_job_id' =>  (int)$result_job_id,
       		 'job_id' => (int)$job_id,
       		 'ssystem_name' => $ssystem_name,
			 'known_solubility' => $known_solubility,
			 'predicted_solubility' => $tencmgml,
			 'temp' => '10',
			 'created_at' => date('Y-m-d H:i:s'),
			 

   		 );
		
		}
		}	
		// Insert the data using batch insert

	
		/*----------Solubility prediction data for 10-------------*/
		$this->db->insert_batch('solubility_corrected_predicted_data', $addSolubility);


		
		}
		echo "10 Done ";
	}

	public function insertresults25knownsolu($id) {


		$pureDataFields = array('pure_data1', 'pure_data2', 'pure_data3', 'pure_data4', 'pure_data5');

		foreach ($pureDataFields as $pureDataField) {

		$query = $this->db->query("select sm.w1_density,jr.solvents, sm.w1_solvent_system,jr.result_type, jr.$pureDataField as pdata,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr, jobs_master jm where jr.input_temp_20 = 25  and sm.s_id=jr.s_id and jr.solvent_result not like '%Traceback%' and jr.solvent_result <>'' and jm.id=jr.job_id  and jm.project_id=".$id);
		$results_10 = $query->result();
		$jbd = $this->projects_model->getJobdetails($id);
		
		$addSolubility = array();
	
		foreach ($results_10 as $row)  {  // Call function to get values
  
		$result_job_id = $row->jbid;
		$job_id = $row->job_id;
		$ssystem_name = $row->solvents;
		if (!empty($row->pdata)) {
		$tencmgml = $this->projects_model->get10mgmlcal($row->pdata,$row->job_id);
	
   	 	/*----------Solubility prediction data for 10-------------*/

		$existing_data = $this->db->get_where('solubility_correction_data', array(
		    'job_id' => $job_id,
		    's_name' => $ssystem_name,
		    'temp' => '25' // Assuming 'temp' is another column you want to match
		))->row_array();
		if (!empty($existing_data)) {
		    $known_solubility = $existing_data['s_value'];
		} else {
		    $known_solubility = 0;
		}
		$addSolubility[] = array(
      	 	 'result_job_id' =>  (int)$result_job_id,
       		 'job_id' => (int)$job_id,
       		 'ssystem_name' => $ssystem_name,
			 'known_solubility' => $known_solubility,
			 'predicted_solubility' => $tencmgml,
			 'temp' => '25',
			 'created_at' => date('Y-m-d H:i:s'),
			 

   		 );
	}
	}	
		
		$this->db->insert_batch('solubility_corrected_predicted_data', $addSolubility);

		

	}		
		echo "25 Done ";
		
	}

	public function insertresults50knownsolu($id) {

		$pureDataFields = array('pure_data1', 'pure_data2', 'pure_data3', 'pure_data4', 'pure_data5');

		foreach ($pureDataFields as $pureDataField) {

		$query = $this->db->query("select sm.w1_density,jr.solvents, sm.w1_solvent_system,jr.result_type, jr.$pureDataField as pdata,jr.job_id,jr.id as jbid from solvents_master sm, job_results jr, jobs_master jm where jr.input_temp_50 = 50  and sm.s_id=jr.s_id and jr.solvent_result not like '%Traceback%' and jr.solvent_result <>'' and jm.id=jr.job_id  and jm.project_id=".$id);
		$results_10 = $query->result();
		$jbd = $this->projects_model->getJobdetails($id);
		
		$addSolubility = array();
	
		foreach ($results_10 as $row)  {  // Call function to get values
			

		$result_job_id = $row->jbid;
		$job_id = $row->job_id;
		$ssystem_name = $row->solvents;
		if (!empty($row->pdata)) {
		$tencmgml = $this->projects_model->get10mgmlcal($row->pdata,$row->job_id);
		/*---get 10 results data /This code return by sonam for swapping 50 ->10 data--*/
  		$results_data_10 = $this->db->select('*');
		$this->db->from('results_data_10');
		$this->db->where('job_id ', $job_id);
		$this->db->where('ssystem_name', $row->solvents);
		$query = $this->db->get();    
		$valdata = $query->row();

		
		$mg_ml_ten_val = $valdata->{'10cmgml'};
		$yield_ten_val = $valdata->{'10cyl'};
		/*---compare 10 ,50 mg/ml value results data--*/
		

		if($mg_ml_ten_val > $tencmgml)  {
			
			$fmgml = $mg_ml_ten_val;	
			$fmgyield = $yield_ten_val;
			$this->db->update('results_data_10', array('10cmgml' => $tencmgml,'10cyl'=> $tencyl), array('id' => $valdata->id));
		} else{
			$fmgml = $tencmgml;
			$fmgyield = $tencyl;
		}
	

   	 	/*----------Solubility prediction data for 10-------------*/

		$existing_data = $this->db->get_where('solubility_correction_data', array(
		    'job_id' => $job_id,
		    's_name' => $ssystem_name,
		    'temp' => '50' // Assuming 'temp' is another column you want to match
		))->row_array();
		if (!empty($existing_data)) {
		    $known_solubility = $existing_data['s_value'];
		} else {
		    $known_solubility = 0;
		}
		$addSolubility[] = array(
      	 	 'result_job_id' =>  (int)$result_job_id,
       		 'job_id' => (int)$job_id,
       		 'ssystem_name' => $ssystem_name,
			 'known_solubility' => $known_solubility,
			 'predicted_solubility' => $fmgml,
			 'temp' => '50',
			 'created_at' => date('Y-m-d H:i:s'),
			 

   		 );
	}
	}
		
		$this->db->insert_batch('solubility_corrected_predicted_data', $addSolubility);

		


		
	}
		
		echo "50 Done ";
		
	}