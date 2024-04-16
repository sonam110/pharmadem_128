
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>jQuery multi.js: Dual List Box Plugin Demo</title>
<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
        <!-- Include multi.js -->
        <link rel="stylesheet" type="text/css" href="../multi.min.css">

        <style>
            html { height:100vh;}
            body {
                font-family: 'Roboto';
                height:100vh;
                background: linear-gradient(to right, #EF629F, #EECDA3);
            }

            .container {
                box-sizing: border-box;;
                margin: 150px auto;
                max-width: 960px;
                padding: 0 20px;
                width: 100%;
            }
        </style>
    </head>
    <body>
    <div id="jquery-script-menu">
<div class="jquery-script-center">


<div class="jquery-script-clear"></div>
</div>
</div>
        <div class="container">
           

          <select multiple="multiple" name="Programming-Languages" id="example">
                <option selected="selected" disabled="disabled">Disabled</option>
                <option>Python</option>
                <option>C</option>
                <option>Java</option>
                <option>C++</option>
                <option>C#</option>
                <option>R</option>
                <option>GO</option>
                <option>JavaScript</option>
                <option>PHP</option>
                <option>Ruby</option>
                <option>Swift</option>
            </select>
        </form>
        </div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="../multi.min.js"></script>
    <script>
		$( '#example' ).multi({ non_selected_header: 'Languages',
                selected_header: 'Selected Languages'});
        </script>
    </body>

</html>
