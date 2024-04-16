<?php
$project_details = $this->projects_model->getById($jobDetail->project_id);
ini_set('memory_limit', '-1');
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>pharmadem</title>    
	<link href='https://fonts.googleapis.com/css?family=Source Sans Pro' rel='stylesheet'><!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">

<!-- jQuery library -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> -->

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
	<style type="text/css">


		.clearfix:after {
			content: "";
			display: table;
			clear: both;
		}
		a {
			color: #0087C3;
			text-decoration: none;
		}

		body {
			position: relative;
			width: 21cm;  
			margin: 0 auto; 
			color: #555555;
			background: #FFFFFF; 
			font-family: Arial, sans-serif; 
			font-size: 14px; 
			font-family: Source Sans Pro;
		}

		header {
			padding: 10px 0;
			margin-bottom: 20px;
			border-bottom: 1px solid #AAAAAA;
		}

		#logo {
			float: left;
			margin-top: 8px;
		}

		#logo img {
			height: 70px;
		}

		#company {
			float: right;
			text-align: right;
		}


		#details {
			margin-bottom: 50px;
		}

		#client {
			padding-left: 6px;
			border-left: 6px solid #0087C3;
			float: left;
		}

		#client .to {
			color: #777777;
		}

		h2.name {
			font-size: 1.4em;
			font-weight: normal;
			margin: 0;
		}

		#invoice {
			float: right;
			text-align: right;
		}

		#invoice h1 {
			color: #0087C3;
			font-size: 2.4em;
			line-height: 1em;
			font-weight: normal;
			margin: 0  0 10px 0;
		}

		#invoice .date {
			font-size: 1.1em;
			color: #777777;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			border-spacing: 0;
			margin-bottom: 20px;
			color:  black !important;
		}

		table th,
		table td {
			padding: 20px;
			/*background: #EEEEEE;*/
			text-align: center;
			border-bottom: 1px solid #FFFFFF;
			color:  black !important;
		}

		table th {
			white-space: nowrap;        
			font-weight: bold !important;
			color: blue !important;
		}

		table td {
			text-align: right;
		}

		table td h3{
			color: #ffad33;
			font-size: 1.2em;
			font-weight: normal;
			margin: 0 0 0.2em 0;
		}

		table .no {
			color:  black !important;
			font-size: 1.6em;
			/*background: #ffad33;*/
		}

		table .desc {
			text-align: left;
		}

		table .unit {
			/*background: #DDDDDD;*/
		}

		table .qty {

		}

		table .total {
			/*background: #ffad33;*/
			color: #FFFFFF;
		}

		table td.unit,
		table td.qty,
		table td.total {
			font-size: 1.2em;
		}

		table tbody tr:last-child td {
			border: none;
		}

		table tfoot td {
			padding: 10px 20px;
			border-bottom: none;
			font-size: 1.2em;
			white-space: nowrap; 
			border-top: 1px solid #AAAAAA; 
		}

		table tfoot tr:first-child td {
			border-top: none; 
		}

		table tfoot tr:last-child td {
			color: #ffad33;
			font-size: 1.4em;
			border-top: 1px solid #ffad33; 

		}

		table tfoot tr td:first-child {
			border: none;
		}

		#thanks{
			font-size: 2em;
			margin-bottom: 50px;
		}

		#notices{
			padding-left: 6px;
			border-left: 6px solid #0087C3;  
		}

		#notices .notice {
			font-size: 1.2em;
		}

		footer {
			color: #777777;
			width: 100%;
			height: 30px;
			position: absolute;
			bottom: 0;
			border-top: 1px solid #AAAAAA;
			padding: 8px 0;
			text-align: center;
		}
		 .title {
            color: blue; /* Set color to blue */
            font-weight: bold; /* Make text bold */
            text-decoration: underline; /* Add underline */
        }

         .justified {
            text-align: justify;
        }

	</style>
</head>
<body>
	<header class="clearfix">
		<div id="details" class="clearfix">
			<div id="client">
				<h3><strong style="color:green;font-weight: bold;">Solu</strong><strong style="color:blue;font-weight: bold;">DEM</strong></h3>
				<strong style="color:blue;font-weight: bold;">SoluDEM Solubility prediction report</strong>
				<p>Project Name : <?= $project_details->project_name ?></p>
				<p>author name : Ravi S A</p>
			</div>
			<div id="invoice" style="margin: -110px 0px 0px 0px;">
				<img src="<?= $url->assets ?>/img/soludem.png" style="max-width:240px;" />
				<p>submission date : <?= $jobDetail->process_start ?></p>
				<p>start date : <?= $jobDetail->process_end ?></p>
			</div>
		</div>
	</header>
	<main>
		<div style="border-radius: 5px;width: 100%;background-color: #cccccc;color:black;">
			<h4 style="margin: 10px;">Input Data</h4>
		</div>
		<br>
		<table border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="desc">Api Name</th>
					<th class="desc">Smiles</th>
					<th class="qty">Hfus (Ki/mol)</th>
					<th class="qty">Melting Point (C)</th>
				</tr>
			</thead>
			<tbody>
					<tr>
						<td class="desc">
							<?= $project_details->project_name ?>
						</td>
						<td class="desc"><?= $jobDetail->smiles ?> </td>
						<td class="qty"> </td>
						<td class="qty"></td>
					</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8"></td>
				</tr>
			</tfoot>
		</table>

		<div style="border-radius: 5px;width: 100%;background-color: #cccccc;color:black;">
			<h4 style="margin: 10px;">Known Solubility Data</h4>
		</div>
		<br>
		<table border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="desc">S.No.</th>
					<th class="desc">Solvent Name</th>
					<th class="qty">Temperature</th>
					<th class="qty">Value</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th class="desc"></th>
					<th class="desc"></th>
					<th class="qty"></th>
					<th class="qty"></th>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8"></td>
				</tr>
			</tfoot>
		</table>

		<div style="border-radius: 5px;width: 100%;background-color: #0056b3;color:white; ">
			<h4 style="margin: 10px;">SoluDEM Output</h4>
		</div>
		<br>
		<table border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="desc">Id</th>
					<th class="desc"><div>SS Name</div></th>
					<th class="qty">10 C_mg/ml</th>
					<th class="qty">25 C_mg/ml</th>
					<th class="qty">50 C_mg/ml</th>
					
				</tr>
			</thead>
			<tbody>
				<?php if($cdata) { 
					?>
					<?php foreach ($cdata as $key => $value): ?>
						<tr>
							<td class="desc"><?= $key+1;?></td>
							<td class="desc">
								<?php 
									echo $this->projects_model->getsolvent_byjbid($value['result_job_id']);
								?> 
							</td>
							<td class="qty"> <?= number_format(((float)$value['10_cmgml']),2,'.','');?> </td>
					
							<td class="qty"> <?= number_format(((float)$value['25_cmgml']),2,'.','');?> </td>
			
							<td class="qty"> <?= number_format(((float)$value['50_cmgml']),2,'.','');?> </td>
						
						</tr>
					<?php endforeach ?>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8"></td>
				</tr>
			</tfoot>
		</table>
		<div style="text-align: center;">Thank you!</div>
		<div style="border-radius: 5px;width: 100%;background-color: #0056b3;color:white; ">
			<h4 style="margin: 10px;">SoluDEM methodology</h4>
		</div>
		<br>
		<div class="justified"> 
			<div class="notice">SoluDEM uses an Integrated Quantum Mechanics (QM) and Machine Learning (ML) techniques are utilized to precisely predict solubility across various solvent environments. Quantum mechanical descriptors derived from DFT calculations, encompassing molecular energetics and geometry, are amalgamated with molecular fingerprints and solvation-relevant properties like solute-solvent interaction energies. These descriptors, alongside molecular structural features, are employed as input for the machine learning model. The SoluDEM model discerns intricate correlations between molecular attributes and solubility, facilitating swift and precise predictions across diverse chemical systems.</div> <br>
			<h4 class="title">SoluDEM follows the equation:</h4>
			<br>
			<h2 class="name">∆Gsol = ∆Gvdw + ∆Gelec+∆Gcav+∆Ghb+Gfus/RT ln(10)</h2><br>
			<ul>
			<li>
				Where ∆Gvdw is Vander Walls interaction between solute and solvent
			</li><br>
			<li>
				∆Gelec is Vander Walls interaction between solute and solvent
			</li><br>
			<li>∆Gcav is Free energy required to form the complex</li><br>
			<li>∆Ghb is an explicit hydrogen bonding term</li><br>
			<li>∆Gfus is Gibbs free energy of crystallization</li><br>
			<li>RT is constant and temperature</li><br>
		</ul>
		<br>
		<p>All the results are relative solubility prediction. SoluDEM is useful to rank order the solvents for reaction setup, impurity rejection, formulation solvent, and yield improvement.</p>
		<br>
			<h4 class="title">References:</h4>
			<ul>
				<li>
					1. Riniker, S., & Landrum, G. A. (2015). Better Informed Distance Geometry: Using What We Know To Improve Conformation Generation. Journal of Chemical Information and Modeling, 55(12), 2562–2574.
				</li>	<br>
				<li>
					2. Smith, J. S., Isayev, O., & Roitberg, A. E. (2017). ANI-1: an extensible neural network potential with DFT accuracy at force field computational cost. Chemical Science, 8(4), 3192–3203.
				</li>	<br>
				<li>3. Gómez-Bombarelli, R., Wei, J. N., Duvenaud, D. & Aspuru-Guzik, A. (2018). Automatic chemical design using a data-driven continuous representation of molecules. ACS Central Science, 4(2), 268–276.</li>	<br>
				<li>4.Michael A. Lovette. Solubility Model to Guide Solvent Selection in Synthetic Process Development. Cryst. Growth Des. 2022, 22, 7,    4404–4420</li>	<br>
				<li>5. Ravi S. Ananthula et all, Evaluation of Predictive Solubility Models in Pharmaceutical Process Development: an Enabling Technologies Consortium Collaboration. Cryst. Growth Des. 2022, 22, 9, 5239–5263.</li>
			</ul>
			
			<h4 style="text-align: center;color: #0056b3;">--End of the Report--</h4>
		</div>
		<br><br>
		<div style="color: #0056b3;">
			This is computer generated report using FormDEM software developed by PharmaDEM solutions
			DO NOT COP
		</div>
	</main>
	
</body>
</html>