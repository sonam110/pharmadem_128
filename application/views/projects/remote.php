<!-- remote_view.php -->

<html>
<head>
    <title>Remote Server Access</title>
</head>
<body>
    <h2>Remote Server Access</h2>
    <form method="post" action="<?=base_url('projects/connectsshpara')?>">
        <label>Host:</label>
        <input type="text" name="host" required><br><br>
        <label>Username:</label>
        <input type="text" name="username" required><br><br>
        
        <label>Path:</label>
        <input type="text" name="path" required><br><br>
        <label>Command:</label>
        <input type="text" name="command" required><br><br>
        <input type="submit" value="Execute">
    </form>
    <br>
    <div>
        <?php echo $output; ?>
    </div>
</body>
</html>
