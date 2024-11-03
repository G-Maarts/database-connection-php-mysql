<?php
// Check if the requested URL is /project1/index or /project1/index/ (with or without trailing slash)
if ($_SERVER['REQUEST_URI'] === '/project1/index' || $_SERVER['REQUEST_URI'] === '/project1/index/') {
    // Redirect to the root of the project without showing 'index.php' in the URL
    header("Location: /project1/"); 
    exit(); // Stop further script execution after the redirect
}

// Database configuration: constants for connecting to the database
define('DB_HOST', 'localhost'); // Host where the database server is located
define('DB_NAME', 'project1'); // Name of the database to connect to
define('DB_USER', 'root'); // Username for database access
define('DB_PASS', ''); // Password for database access (empty for default XAMPP)

// Initialize variables for messages
$message = ''; // Variable to hold success or error messages
$messageClass = ''; // Variable to hold the CSS class for styling messages

try {
    // Create a new PDO instance to connect to the MySQL database
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the error mode to exceptions for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    // Check if the form data has been submitted via POST method
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate and sanitize form input data
        $firstname = !empty($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : null; // Sanitize input
        $secondname = !empty($_POST['secondname']) ? htmlspecialchars($_POST['secondname']) : null; // Sanitize input
        $lastname = !empty($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : null; // Sanitize input
        $phone = !empty($_POST['phone']) ? htmlspecialchars($_POST['phone']) : null; // Sanitize input

        // Basic validation to check if any fields are empty
        if (!$firstname || !$secondname || !$lastname || !$phone) {
            $message = "All fields are required."; // Set an error message for empty fields
            $messageClass = "error"; // Set class for error message styling
        } else {
            // Prepare a SQL statement to check if the phone number already exists in the database
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE phone = :phone");
            $checkStmt->bindParam(':phone', $phone); // Bind the phone number to the prepared statement
            $checkStmt->execute(); // Execute the prepared statement

            // If the phone number exists in the database
            if ($checkStmt->fetchColumn() > 0) {
                // Set an error message indicating the phone number is already registered
                $message = "The phone number $phone is already registered. Please use a different number.";
                $messageClass = "error"; // Set class for error message styling
            } else {
                // Prepare an SQL statement to insert the new user data into the database
                $stmt = $pdo->prepare("INSERT INTO user (firstname, secondname, lastname, phone) VALUES (:firstname, :secondname, :lastname, :phone)");

                // Bind the user input data parameters to the prepared statement
                $stmt->bindParam(':firstname', $firstname);
                $stmt->bindParam(':secondname', $secondname);
                $stmt->bindParam(':lastname', $lastname);
                $stmt->bindParam(':phone', $phone);

                // Execute the prepared statement to insert data into the database
                if ($stmt->execute()) {
                    // If the insertion is successful, set a success message
                    $message = "Data saved successfully!";
                    $messageClass = "success"; // Set class for success message styling
                } else {
                    // If there is an error during insertion, set an error message
                    $message = "Error saving data.";
                    $messageClass = "error"; // Set class for error message styling
                }
            }
        }
    }
} catch (PDOException $e) {
    // If there is a connection error, catch the exception and set an error message
    $message = "Connection failed: " . $e->getMessage(); // Display connection error
    $messageClass = "error"; // Set class for error message styling
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Set character encoding for the document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design viewport -->
    <title>Welcome to Project1</title> <!-- Title of the web page -->
    <style>
        /* Basic Reset */
        * {
            margin: 0; /* Reset margin for all elements */
            padding: 0; /* Reset padding for all elements */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
        }

        /* Body styling */
        body {
            display: flex; /* Use flexbox for layout */
            justify-content: center; /* Center content horizontally */
            align-items: center; /* Center content vertically */
            height: 100vh; /* Full viewport height */
            font-family: Arial, sans-serif; /* Set font family */
            background-color: #f3f4f6; /* Light gray background color */
            color: #333; /* Dark text color */
        }

        /* Container styling */
        .container {
            width: 100%; /* Full width */
            max-width: 400px; /* Maximum width of 400px */
            padding: 2rem; /* Padding around the container */
            background-color: #fff; /* White background color */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
            border-radius: 8px; /* Rounded corners */
        }

        /* Heading styling */
        h1 {
            font-size: 24px; /* Font size for the heading */
            margin-bottom: 1.5rem; /* Margin below the heading */
            text-align: center; /* Center the heading text */
            color: #4a90e2; /* Blue color for the heading */
        }

        /* Form styling */
        form {
            display: flex; /* Use flexbox for form layout */
            flex-direction: column; /* Stack form elements vertically */
        }

        /* Label styling */
        label {
            font-size: 14px; /* Font size for labels */
            margin-bottom: 0.5rem; /* Margin below each label */
            color: #666; /* Gray color for labels */
        }

        /* Input styling */
        input[type="text"], input[type="tel"] {
            padding: 0.75rem; /* Padding inside input fields */
            margin-bottom: 1.5rem; /* Margin below each input field */
            font-size: 16px; /* Font size for input text */
            border: 1px solid #ccc; /* Light gray border */
            border-radius: 4px; /* Rounded corners for input fields */
            transition: border 0.3s ease; /* Smooth transition for border color change */
        }

        input[type="text"]:focus, input[type="tel"]:focus {
            border-color: #4a90e2; /* Change border color on focus */
            outline: none; /* Remove outline on focus */
        }

        /* Button styling */
        .button-container {
            display: flex; /* Use flexbox for button layout */
            justify-content: space-between; /* Space between buttons */
        }

        button {
            padding: 0.75rem; /* Padding inside buttons */
            font-size: 16px; /* Font size for button text */
            font-weight: bold; /* Bold font weight for buttons */
            color: #fff; /* White text color */
            background-color: #4a90e2; /* Blue background color */
            border: none; /* Remove border */
            border-radius: 4px; /* Rounded corners for buttons */
            cursor: pointer; /* Change cursor to pointer on hover */
            transition: background-color 0.3s ease; /* Smooth transition for background color */
            width: 48%; /* Makes buttons equal width */
        }

        button:hover {
            background-color: #357ab8; /* Darker blue on hover */
        }
    </style>
    <script>
        // Function to display the message as an alert
        function showAlert(message) {
            alert(message); // Display the message in a JavaScript alert box
        }

        // Call the showAlert function if there is a message when the window loads
        window.onload = function() {
            <?php if (!empty($message)): ?> <!-- Check if there is a message to display -->
                showAlert("<?= addslashes($message) ?>"); // Call showAlert with the message
            <?php endif; ?>
        };
    </script>
</head>
<body>

    <div class="container"> <!-- Container for the form -->
        <h1>Submit Your Details</h1> <!-- Form heading -->

        <form action="" method="post"> <!-- Form with POST method -->
            <label for="firstname">First Name:</label> <!-- Label for first name input -->
            <input type="text" id="firstname" name="firstname" required> <!-- Input for first name -->

            <label for="secondname">Second Name:</label> <!-- Label for second name input -->
            <input type="text" id="secondname" name="secondname" required> <!-- Input for second name -->

            <label for="lastname">Last Name:</label> <!-- Label for last name input -->
            <input type="text" id="lastname" name="lastname" required> <!-- Input for last name -->

            <label for="phone">Phone Number:</label> <!-- Label for phone number input -->
            <input type="tel" id="phone" name="phone" required> <!-- Input for phone number -->

            <div class="button-container"> <!-- Container for buttons -->
                <button type="submit">Submit</button> <!-- Submit button -->
                <button type="button" onclick="window.location.href='records'">Display Records</button> <!-- Button to display records -->
            </div>
        </form>
    </div>
</body>
</html>
