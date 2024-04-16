<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SSH2 extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!extension_loaded('ssh2')) {
            show_error('The SSH2 PHP extension is not loaded.');
        }
    }

    public function index()
    {
        // Your code here
    }

}
