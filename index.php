<?php
// Database configuration
$host = 'localhost'; // Database host
$dbname = 'project1'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form data and sanitize it
        $firstname = htmlspecialchars($_POST['firstname']);
        $secondname = htmlspecialchars($_POST['secondname']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $phone = htmlspecialchars($_POST['phone']);

        // Prepare an SQL statement for execution
        $stmt = $pdo->prepare("INSERT INTO user (firstname, secondname, lastname, phone) VALUES (:firstname, :secondname, :lastname, :phone)");

        // Bind the parameters
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':secondname', $secondname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':phone', $phone);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Data saved successfully!";
        } else {
            echo "Error saving data.";
        }
    }
} catch (PDOException $e) {
    // Display error message if there is a problem with the database connection
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Project1</title>
    <style>
        /* Basic Reset to remove default padding and margin for all elements */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* Ensures padding and border are included in element width and height */
        }

        /* Styling for the body to center everything and set basic fonts and colors */
        body {
            display: flex; /* Enables flexbox to align items */
            justify-content: center; /* Centers the content horizontally */
            align-items: center; /* Centers the content vertically */
            height: 100vh; /* Sets the height to full viewport height */
            font-family: Arial, sans-serif; /* Sets the font style */
            background-color: #f3f4f6; /* Light background color */
            color: #333; /* Dark gray color for text */
        }

        /* Container for the form, styled to look like a card with shadow and rounded corners */
        .container {
            width: 100%; /* Takes full width within max-width limit */
            max-width: 400px; /* Limits the width for larger screens */
            padding: 2rem; /* Adds space inside the container */
            background-color: #fff; /* White background for the container */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Soft shadow for a 3D effect */
            border-radius: 8px; /* Rounded corners */
        }

        /* Styling for the main heading */
        h1 {
            font-size: 24px; /* Increases font size */
            margin-bottom: 1.5rem; /* Adds space below the heading */
            text-align: center; /* Centers the heading text */
            color: #4a90e2; /* Blue color for the heading */
        }

        /* Form styling to organize inputs vertically */
        form {
            display: flex;
            flex-direction: column; /* Arranges items in a column */
        }

        /* Styling for form labels */
        label {
            font-size: 14px;
            margin-bottom: 0.5rem; /* Adds space below each label */
            color: #666; /* Light gray color for labels */
        }

        /* Styling for text and phone input fields */
        input[type="text"], input[type="tel"] {
            padding: 0.75rem; /* Adds padding inside the input */
            margin-bottom: 1.5rem; /* Adds space below each input */
            font-size: 16px; /* Sets font size */
            border: 1px solid #ccc; /* Light border color */
            border-radius: 4px; /* Rounds the corners slightly */
            transition: border 0.3s ease; /* Smooth transition for border color change */
        }

        /* Changes the border color of input fields when focused (clicked on) */
        input[type="text"]:focus, input[type="tel"]:focus {
            border-color: #4a90e2; /* Changes to blue border on focus */
            outline: none; /* Removes default outline */
        }

        /* Button styling */
        button {
            padding: 0.75rem; /* Adds padding inside the button */
            font-size: 16px; /* Sets font size */
            font-weight: bold; /* Makes text bold */
            color: #fff; /* White text color */
            background-color: #4a90e2; /* Blue background color */
            border: none; /* Removes the default border */
            border-radius: 4px; /* Rounds the button corners */
            cursor: pointer; /* Shows a pointer cursor on hover */
            transition: background-color 0.3s ease; /* Smooth color transition on hover */
        }

        /* Changes button background color when hovered */
        button:hover {
            background-color: #357ab8; /* Darker blue for hover effect */
        }
    </style>
</head>
<body>
    <!-- Main container to hold the form, centered on the page -->
    <div class="container">
        <h1>Submit Your Details</h1>
        
        <!-- Form to collect user's information -->
        <form action="" method="post">
            <!-- First name field -->
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required>

            <!-- Second name field -->
            <label for="secondname">Second Name:</label>
            <input type="text" id="secondname" name="secondname" required>

            <!-- Last name (surname) field -->
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required>

            <!-- Phone number field -->
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>

            <!-- Submit button to send the form data -->
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
