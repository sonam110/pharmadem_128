<!DOCTYPE html>
<html lang="en">
<!--  -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Validation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-group .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group .btn:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }

        .success-message {
            color: green;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Enter your email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email">
                <button type="submit" name="inputform" class="btn">Validate</button>
            </div>
        </form>
         <form method="POST" action="">
        <div class="form-group">
            <label for="file">Upload file:</label>
            <input type="file" id="file" name="file">
            <button class="btn" name="uplaod">Upload File</button>
        </div>
         </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if the form was submitted
            if (isset($_POST['email'])) {
                $email = $_POST['email'];
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    echo "<div class='error-message'>Invalid email format.</div>";
                } else {
                    // Process the valid email (for example, save to database or send confirmation email)

                    $key = "Z56xVu57UwqwGKtbzsvE2";

                    $url = "https://app.emaillistvalidation.com/api/verifEmailv2?secret=".$key."&email=".$email;


                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );

                    $response = curl_exec($ch);

                    if ($response === false) {
                         echo "Error: cURL request failed.";
                    } else {
                        $result = json_decode($response, true);
                        if ($result['Result'] === 'valid') {
                            echo "<div class='success-message'>Email is valid.</div>";
                        } else {
                            echo "<div class='error-message'>Email is not valid.</div>";
                        }
                    }
                }
            }

            if (isset($_FILES['file'])) {
                $file = $_FILES['file'];

                // Check if file was uploaded without errors
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    echo "<div class='error-message'>File upload error: " . $file['error'] . "</div>";
                } else {
                    // Specify the directory where you want to save the uploaded file
                    $uploadDir = 'uploads/';

                    // Create the directory if it doesn't exist
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $uploadPath = $uploadDir . $file['name'];

                    // Move the uploaded file to the specified directory
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                           
                    } else {
                        echo "<div class='error-message'>Failed to upload file.</div>";
                    }
                }
            }
        }
        ?>
    </div>
</body>

</html>
