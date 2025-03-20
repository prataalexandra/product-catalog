<?php
///function for cleaning the data
///eliminate the spaces and the slashes
///converting special characters to html entities
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$error_message = "";

///verify the request method to be POST and make the conection to db
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $con = mysqli_connect("localhost", "root", "", "bd");

    if (!$con) {
        die('Could not connect: ' . mysqli_error($con));
    }
///login action 
    if ($action == "login") {
        $email = test_input($_POST["email"]);
        $password = test_input($_POST["password"]);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
            $result = mysqli_query($con, $sql);
            ///if the account is valid, redirecting to managing categories page

            if (mysqli_num_rows($result) == 1) {
                echo "<script>window.location.href='http://localhost/practica/categorii.php';</script>";
                exit();
            } else {
                ///if not, an error message alert
                $error_message = "Account doesn't exist.";
            }
        } else {
            $error_message = "Invalid email format.";
        }
///signup action
    } elseif ($action == "signup") {
        $new_email = test_input($_POST["new_email"]);
        $new_password = test_input($_POST["new_password"]);
        ///every account has an valid email- yahoo.com or gmail.com and a password
    
        if (preg_match("/^[a-zA-Z0-9._%+-]+@(yahoo|gmail)\.com$/", $new_email)) {
            $sql = "SELECT count(*) FROM users WHERE email = '$new_email'";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($result);
            ///insert only if the email is valid and there is no account with this email(row==0)

            if ($row[0] == 0) {
                $sql = "INSERT INTO users (email, password) VALUES ('$new_email', '$new_password')";
                if (mysqli_query($con, $sql)) {
                    echo "<script>showAlert('New account created successfully');</script>";
                } else {
                    $error_message = "Error: " . mysqli_error($con);
                }
            } else {
                $error_message = "Email already exists.";
            }
        } else {
            $error_message = "Invalid email format. Please use @yahoo.com or @gmail.com.";
        }
    }

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" type="text/css" href="mystyle.css">
    <style>
        .form-container {
            display: none;
        }
    </style>
    <script>
        ///display the chosen form and hide the other one
        function showForm(formId) {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('signup-form').style.display = 'none';
            document.getElementById(formId).style.display = 'block';
        }
        ///show an alert with a message
        function showAlert(message) {
            alert(message);
        }
        ///event listener to run JavaScript
        document.addEventListener('DOMContentLoaded', (event) => {
            <?php if ($error_message != ""): ?>
                showAlert("<?php echo $error_message; ?>");
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Welcome to admin page</h1>
        <hr>
        <button onclick="showForm('login-form')">Login</button>
        <button onclick="showForm('signup-form')">Signup</button>
        <hr>

        <form id="login-form" class="form-container" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label>Email</label><br>
            <input type="text" name="email" placeholder="Enter Email" required><br>
            <label>Password</label><br>
            <input type="password" name="password" placeholder="Enter Password" required>
            <br><br>
            <button type="submit" name="action" value="login">Login</button>
        </form>

        <form id="signup-form" class="form-container" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label>Email</label><br>
            <input type="text" name="new_email" placeholder="Enter Email" required><br>
            <label>Password</label><br>
            <input type="password" name="new_password" placeholder="Enter Password" required><br>
            <br>
            <button type="submit" name="action" value="signup">Create your account!</button>
        </form>
    </div>
</body>
</html>
