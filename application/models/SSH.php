<?php
class SSH extends CI_Model {
    private $connection;
    private $cwd = '/';

    private $current_directory;
    
    public function __construct() {
        // Replace with your SSH server credentials
        $host = '128.199.31.121';
        $port = 22;
        $username = 'chemistry1';
        $password = 'Ravi@1234';
        
        // Create SSH connection
        $this->connection = ssh2_connect($host, $port);
        ssh2_auth_password($this->connection, $username, $password);
        // Set initial current directory to home directory
        $this->current_directory = $this->execute('pwd');
    }
    
    public function execute($command) {
        // Change working directory if needed
        if (substr($command, 0, 3) === 'cd ') {
            $this->change_working_directory(substr($command, 3));
            return '';
        }

        // Execute command on the SSH server
        $stream = ssh2_exec($this->connection, 'cd ' . $this->cwd . ' && ' . $command);
        stream_set_blocking($stream, true);
        $output = stream_get_contents($stream);
        fclose($stream);

        return $output;
    }

    private function change_working_directory($new_cwd) {
        // Normalize new_cwd
        $new_cwd = $this->normalize_path($new_cwd);

        // If new_cwd is absolute, use it as is
        if (substr($new_cwd, 0, 1) === '/') {
            $this->cwd = $new_cwd;
        } else {
            // Otherwise, resolve new_cwd relative to the current working directory
            $this->cwd = $this->normalize_path($this->cwd . '/' . $new_cwd);
        }
    }

    private function normalize_path($path) {
        $parts = explode('/', $path);
        $new_parts = array();

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if ($part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($new_parts);
                continue;
            }

            $new_parts[] = $part;
        }

        $new_path = implode('/', $new_parts);

        if (substr($path, 0, 1) === '/') {
            return '/' . $new_path;
        } else {
            return $new_path;
        }
    }
}
?>