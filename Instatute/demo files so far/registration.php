<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function updateForm(role) {
      document.getElementById('roleSpecificFields').innerHTML = '';
      if (role === 'student') {
        document.getElementById('roleSpecificFields').innerHTML = `
          <label for="animal">Choose your animal:</label>
          <select id="animal" name="animal_id" required>
            <option value="1">Dog</option>
            <option value="2">Cat</option>
            <option value="3">Rabbit</option>
          </select><br>
          <label for="subjects">Select Subjects:</label>
          <select id="subjects" name="subjects[]" multiple required>
            <option value="1">Mathematics</option>
            <option value="2">English</option>
          </select><br>
        `;
      } else if (role === 'tutor') {
        document.getElementById('roleSpecificFields').innerHTML = `
          <label for="subjects">Select Subjects to Teach:</label>
          <select id="subjects" name="subjects[]" multiple required>
            <option value="1">Mathematics</option>
            <option value="2">English</option>
          </select><br>
        `;
      }
    }
  </script>
</head>
<body>
<header>
    <p id="cp_name">Instatute</p>
</header>
<p> Register here </p>
<form id="regist" method="post" action="registration_handler.php">
  <div class="reginfo">
    <label for="role"><b>Role:</b></label>
    <select id="role" name="role" onchange="updateForm(this.value)" required>
      <option value="">Select Role</option>
      <option value="student">Student</option>
      <option value="tutor">Tutor</option>
    </select><br>

    <label for="usn"><b>Username</b></label>
    <input type="text" placeholder="Enter Username" id="usn" name="usn" required><br>

    <label for="psw"><b>Password</b></label>
    <input type="password" placeholder="Enter Password" id="psw" name="psw" required><br>

    <div id="roleSpecificFields"></div>

    <button type="submit">Register</button>
    <label>
      <input type="checkbox" name="remember"> Remember me
    </label>
  </div>
</form>
<!--Container-->
<script src=""></script>
</body>
</html>