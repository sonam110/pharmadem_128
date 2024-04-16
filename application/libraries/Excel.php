<?php defined('BASEPATH') OR exit('No direct script access allowed');
// Dompdf namespace use Dompdf\Dompdf;
class Excel{ public function __construct(){

    require_once dirname(__FILE__).'/PhpOffice/PhpSpreadsheet/Spreadsheet.php';
    
    $excel = new Spreadsheet(); // instantiate Spreadsheet
    $CI = & get_instance();
    $CI->excel = $excel; 
    }
} 
?>