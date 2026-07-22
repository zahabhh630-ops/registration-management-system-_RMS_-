<?php
// 1. DATABASE CONFIGURATION (Runs first on submission)
$host     = 'localhost';
$db_name  = 'form_db';        // The database name you created in phpMyAdmin
$username = 'root';           // XAMPP default username
$password = '';               // XAMPP default password is blank
$message  = '';               // Variable to store our success message

// Check if the submit button was clicked
if (isset($_POST['sub'])) {
    try {
        // Connect to the database
        $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Collect the form data using the unique name attributes
        $other_name   = $_POST['other_name'] ?? '';
        $surname      = $_POST['surname'] ?? '';
        $address      = $_POST['address'] ?? '';
        $national_id  = $_POST['national_id'] ?? '';
        $occupation   = $_POST['occupation'] ?? '';
        $nationality  = $_POST['nationality'] ?? '';

        // Prepare the SQL query
        $sql = "INSERT INTO form_submissions (other_name, surname, address, national_id, occupation, nationality) 
                VALUES (:other_name, :surname, :address, :national_id, :occupation, :nationality)";
        
        $stmt = $conn->prepare($sql);

        // Bind parameters safely to prevent SQL injection
        $stmt->bindParam(':other_name', $other_name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':national_id', $national_id);
        $stmt->bindParam(':occupation', $occupation);
        $stmt->bindParam(':nationality', $nationality);

        // Execute the query and set the success message
        if ($stmt->execute()) {
            $message = htmlspecialchars($surname) . ", You are welcome to our Online Services. Your data has been saved!";
        } else {
            $message = "Error submitting data to the database.";
        }

    } catch (PDOException $e) {
        $message = "Database connection failed: " . $e->getMessage();
    }
    
    // Close the connection
    $conn = null;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Registration Form</title>
</head>
<body>

    <p><b>Please Fill Out this Form</b></p>

    <!-- Changed action to empty string "" so it submits to itself, and method to "post" -->
    <form method="post" action="">
        <!-- Notice that every input field now has a UNIQUE name attribute -->
        <p><b>Other Name:</b> <input type="text" name="other_name" size="30"></p>
        <p><b>Surname:</b> <input type="text" name="surname" size="30"></p>
        <p><b>Address:</b> <input type="text" name="address" size="30"></p>
        <p><b>ID:</b> <input type="text" name="national_id" size="30"></p>
        <p><b>Occupation:</b> <input type="text" name="occupation" size="30"></p>
        <p><b>Nationality:</b> <input type="text" name="nationality" size="30"></p>
        <p><input type="submit" name="sub" value="Submit"></p>
    </form>

    <?php
    // Display the success or error message if it is not empty
    if (!empty($message)) {
        echo "<p><strong>" . $message . "</strong></p>";
    }
    ?>

</body>
</html>