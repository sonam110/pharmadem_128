<!DOCTYPE html>
<html>
<head>
    <title>Virtual Command Line</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#terminal').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "<?php echo site_url('terminal/execute_command'); ?>",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#output').append(response);
                    }
                });
                $('#command').val('');
            });
        });
    </script>
</head>
<body>
    <h1>Virtual Command Line - SSH</h1>
    <hr>
    <p>Connected to SSH server: ssh.example.com</p>
    <form id="terminal">
        <label for="command">Command:</label>
        <input type="text" id="command" name="command">
        <input type="submit" value="Submit">
    </form>
    <hr>
    <div id="output"></div>
</body>
</html>
