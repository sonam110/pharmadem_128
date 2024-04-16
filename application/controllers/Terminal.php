<?php
class Terminal extends CI_Controller {
    public function index() {
        $this->load->view('terminal');
    }

    public function execute_command() {
        $command = $this->input->post('command');
        $this->load->model('SSH');
        $output = $this->SSH->execute($command);
        echo $output;
    }
}
?>
