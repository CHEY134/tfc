<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <style>
        #password-strength-meter {
            height: 10px;
            margin-top: 5px;
        }
        .password-weak {
            background-color: red;
        }
        .password-medium {
            background-color: orange;
        }
        .password-strong {
            background-color: green;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#password').on('input', function() {
                var password = $(this).val();
                var strength = 0;

                // Check the length of the password
                if (password.length >= 8) {
                    strength += 1;
                }

                // Check for uppercase letters
                if (password.match(/[A-Z]/)) {
                    strength += 1;
                }

                // Check for lowercase letters
                if (password.match(/[a-z]/)) {
                    strength += 1;
                }

                // Check for numbers
                if (password.match(/[0-9]/)) {
                    strength += 1;
                }

                // Check for special characters
                if (password.match(/[$@#&!]/)) {
                    strength += 1;
                }

                // Update the strength meter
                if (password.length === 0) {
                    $('#password-strength-meter').attr('class', '');
                } else if (strength <= 2) {
                    $('#password-strength-meter').attr('class', 'password-weak');
                } else if (strength === 3) {
                    $('#password-strength-meter').attr('class', 'password-medium');
                } else {
                    $('#password-strength-meter').attr('class', 'password-strong');
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["submit"])) {
           $fullName = $_POST["fullname"];
           $email = $_POST["email"];
           $password = $_POST["password"];
           $passwordRepeat = $_POST["repeat_password"];
           
           $passwordHash = password_hash($password, PASSWORD_DEFAULT);

           $errors = array();
           
           if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
            array_push($errors,"ყველა ველი საჭიროა");
           }
           if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email არასწორად არის შეყვანილი");
           }
           if (strlen($password)<8) {
            array_push($errors,"პაროლი უნდა შედგებოდეს მინიმუმ 8 სიმბოლოსგან");
           }
           if ($password!==$passwordRepeat) {
            array_push($errors,"პაროლები არ ემთხვევა");
           }
           require_once "database.php";
           $sql = "SELECT * FROM users WHERE email = '$email'";
           $result = mysqli_query($conn, $sql);
           $rowCount = mysqli_num_rows($result);
           if ($rowCount>0) {
            array_push($errors,"ასეთი Email უკვე არსებობს");
           }
           if (count($errors)>0) {
            foreach ($errors as  $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
           }else{
            
            $sql = "INSERT INTO users (full_name, email, password) VALUES ( ?, ?, ? )";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt,"sss",$fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>წარმატებით გაიარე რეგისტრაცია</div>";
            }else{
                die("ERROR");
            }
           }
        }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="სახელი">
            </div>
            <div class="form-group">
                <input type="emamil" class="form-control" name="email" placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder="პაროლი">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="repeat_password" placeholder="გაიმეორე პაროლი">
            </div>
            <div id="password-strength-meter"></div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="რეგისტრაცია" name="submit">
            </div>
        </form>
        <div>
            <div><p>უკვე ხარ რეგისტრირებული?<a href="login.php">შედი შენს ანგარიშზე</a></p></div>
        </div>
    </div>
</body>
</html>
