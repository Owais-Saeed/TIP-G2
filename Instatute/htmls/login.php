<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" > 
    <meta name="description" content="TIP">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="" rel="stylesheet" type="text/css">
    <link href="" rel="icon" type="image/jpg">

    <script type="module" src="https://pyscript.net/releases/2024.1.1/core.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Login Page</title> 
</head>
<body>
    <header>
        <nav class="p-3 text-bg-dark">
            <div class="container-fluid">
                
                <h1 id="cp_name">Instatute</h1>
            
            </div>
        </nav>
    </header>

    <?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $username = $_POST['usn'];
    $password = $_POST['psw'];

    // Prepare data for JSON request
    $data = array(
    'username' => $username,
    'password' => $password
    );

    // Replace with your service URL and port
    $url = 'http://localhost:8080/login_service';

    // Send POST request with JSON data
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) 
    {
        echo "Error communicating with login service: " . $curl_error;
    } 
    else 
    {
    $login_response = json_decode($response, true);

        if ($login_response['success']) 
        {
            // Login successful, store user information in session
            $_SESSION['user_id'] = $login_response['user']['user_id'];
            $_SESSION['role'] = $login_response['user']['role'];

            // Redirect based on user role
            if ($_SESSION['role'] == 'student') {
            header('Location: student_index.php');
            } elseif ($_SESSION['role'] == 'tutor') {
            header('Location: tutor_index.php');
            } else {
            echo "Invalid role";
            }
            exit(); // Stop further execution
        } 
        else 
        {
        // Login failed, display error message
        echo "Invalid username/password";
        }
    }
}
?>
    <main class="form-signin w-100 m-auto">
        <form id="loginframe" method="post" action="../python/login.py">
        
            <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

            <div class="form-floating">
            <input type="text" class="form-control" id="floatingInput" placeholder="name@example.com">
            <label for="floatingInput">User Name</label>
            </div>
            <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
            </div>

            <div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Remember me
            </label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
        </form>
</main>

<!--Container-->
<script src=""></script>

</body>
</html>