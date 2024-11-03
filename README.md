PHP Script: Handling Data Submission and Database Interaction
1. Database Configuration
php
$host = 'localhost'; // Database host
$dbname = 'project1'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password
•	These variables hold the necessary information for connecting to the database.
o	$host: The server where the database is hosted (usually localhost when testing locally).
o	$dbname: The name of the database we are working with (project1 in this case).
o	$username and $password: Credentials to log in to the database. Here, the username is root, and there is no password.
2. Connecting to the Database with PDO (PHP Data Objects)
php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
•	PDO: A PHP extension for secure database interaction.
•	new PDO(): Creates a new PDO instance to connect to the database.
o	"mysql:host=$host;dbname=$dbname": The connection string tells PHP which database server and database to connect to.
•	setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION):
o	This attribute configures the PDO to throw an error (exception) if a database problem occurs, helping us catch and troubleshoot it.
3. Checking if the Form was Submitted
php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
•	$_SERVER["REQUEST_METHOD"]: This variable checks how the form was submitted. If it’s POST, the data has been submitted securely, often used for sending sensitive data.
•	If the request is POST, the code inside this condition is executed, meaning the user has submitted the form.
4. Retrieving and Sanitizing Form Data
php
$firstname = htmlspecialchars($_POST['firstname']);
$secondname = htmlspecialchars($_POST['secondname']);
$lastname = htmlspecialchars($_POST['lastname']);
$phone = htmlspecialchars($_POST['phone']);
•	$_POST: An array that stores data sent by the form using the POST method.
•	htmlspecialchars(): This function converts special characters into HTML entities. For example, < becomes &lt;, making it safe to display data without risking HTML injection attacks.
5. Preparing and Binding the SQL Statement
php
$stmt = $pdo->prepare("INSERT INTO user (firstname, secondname, lastname, phone) VALUES (:firstname, :secondname, :lastname, :phone)");
•	$pdo->prepare(): Prepares an SQL command to prevent SQL injection. Using placeholders like :firstname, it sets up the values safely.
•	INSERT INTO user: The SQL command here is an INSERT statement, adding new rows to the user table with columns firstname, secondname, lastname, and phone.
Binding the Parameters
php
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':secondname', $secondname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':phone', $phone);
•	bindParam(): This securely binds PHP variables ($firstname, $secondname, etc.) to the SQL statement placeholders (:firstname, :secondname, etc.).
6. Executing the SQL Statement
php
if ($stmt->execute()) {
    echo "Data saved successfully!";
} else {
    echo "Error saving data.";
}
•	$stmt->execute(): Runs the SQL statement. If successful, it will output "Data saved successfully!"; otherwise, it will show an error message.
7. Catching Errors (Exception Handling)
php
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
•	catch (PDOException $e): If there’s a problem with the database connection or an error with the SQL command, the catch block will output an error message, using $e->getMessage() to display what went wrong.
________________________________________
HTML Structure: Displaying the Form
1. Basic Structure
html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Project1</title>
    <style> /* CSS styling goes here */ </style>
</head>
<body>
    <!-- Content here -->
</body>
</html>
•	DOCTYPE: Declares the document as an HTML5 document.
•	<html lang="en">: Specifies the language as English.
•	<head>: Contains metadata and the <style> block for CSS styling.
•	<body>: Main container for all visible content on the page.
2. Form Structure
html
<div class="container">
    <h1>Submit Your Details</h1>
    <form action="" method="post">
        <!-- Form fields and submit button go here -->
    </form>
</div>
•	<div class="container">: Wrapper for the form content, helping with layout and styling.
•	<form action="" method="post">: The action is empty, meaning it submits to the same page. method="post" sends data securely.
•	<h1>: Heading for the form.
3. Input Fields
html
<label for="firstname">First Name:</label>
<input type="text" id="firstname" name="firstname" required>
•	<label>: Labels make the form accessible, describing each input field.
•	<input type="text">: Creates a text input for the user’s first name.
•	required: Ensures that the field must be filled before the form can be submitted.
________________________________________
CSS Styling: Enhancing the Form’s Appearance
1. Global Reset
css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
•	Removes default margin and padding, and sets box-sizing to include padding and border in the element’s total width and height.
2. Styling the Body
css
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: Arial, sans-serif;
    background-color: #f3f4f6;
    color: #333;
}
•	display: flex: Allows for centering content in both directions.
•	height: 100vh: Takes the full height of the viewport for centering.
•	font-family and color: Sets the font style and text color.
3. Container Styling
css
.container {
    max-width: 400px;
    padding: 2rem;
    background-color: #fff;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}
•	max-width and padding: Centers and limits the width, adding padding inside.
•	box-shadow: Adds a shadow effect for depth.
•	border-radius: Rounds corners for a softer look.
4. Form Styling
css
form {
    display: flex;
    flex-direction: column;
}
label {
    font-size: 14px;
    margin-bottom: 0.5rem;
    color: #666;
}
input[type="text"], input[type="tel"] {
    padding: 0.75rem;
    margin-bottom: 1.5rem;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    transition: border 0.3s ease;
}
input[type="text"]:focus, input[type="tel"]:focus {
    border-color: #4a90e2;
    outline: none;
}
•	flex-direction: column: Stacks form elements vertically.
•	input styles adjust padding, border, and focus effect to enhance usability and interactivity.
5. Button Styling
css
button {
    padding: 0.75rem;
    font-size: 16px;
    font-weight: bold;
    color: #fff;
    background-color: #4a90e2;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
button:hover {
    background-color: #357ab8;
}
•	background-color and color: Sets the button’s color scheme.
•	hover effect: Changes the background color on hover, adding interactivity.

