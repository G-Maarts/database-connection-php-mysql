<?php
// Redirect if accessing /project1/index
if ($_SERVER['REQUEST_URI'] === '/project1/index' || $_SERVER['REQUEST_URI'] === '/project1/index/') {
  header("Location: /project1/"); // Redirect to root without showing 'index.php'
  exit();
}

session_start(); // Start a new session or resume the existing session

// Database configuration
$host = 'localhost'; // Define the database host
$dbname = 'project1'; // Define the name of the database
$username = 'root'; // Define the username for database connection
$password = ''; // Define the password for database connection

$records = []; // Initialize an array to hold fetched records from the database

try {
    // Create a new PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the error mode to exception to handle errors properly
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare an SQL statement to select all records from the user table
    $stmt = $pdo->prepare("SELECT * FROM user");
    // Execute the prepared statement
    $stmt->execute();

    // Fetch all records as an associative array
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If a database connection error occurs, store the error message in the session
    $_SESSION['message'] = "Connection failed: " . $e->getMessage();
    // Redirect to the records page
    header("Location: records.php");
    exit(); // Terminate the script after redirection
}

// Handle form submissions for updating or deleting records
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Check if the request method is POST
    if (isset($_POST['update'])) { // Check if the update button was clicked
        // Define a regex pattern for validating phone numbers
        $phonePattern = '/^\+?[0-9\s\-\(\)]+$/'; // Example: allows digits, +, spaces, - and parentheses

        // Validate the phone number against the regex pattern
        if (!preg_match($phonePattern, $_POST['phone'])) {
            // If the phone number is invalid, store an error message in the session
            $_SESSION['message'] = "Invalid phone number format.";
        } else {
            // Prepare a SQL statement to check if the phone number exists for another user
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE phone = :phone AND id != :id");
            // Execute the statement with the phone number and the user ID
            $stmt->execute(['phone' => $_POST['phone'], 'id' => $_POST['id']]);
            // Fetch the count of existing phone numbers
            $exists = $stmt->fetchColumn();

            if ($exists > 0) { // If the phone number already exists
                $_SESSION['message'] = "This phone number is already in use."; // Store an error message
            } else {
                // Prepare a SQL statement to update the user's information
                $stmt = $pdo->prepare("UPDATE user SET firstname = :firstname, secondname = :secondname, lastname = :lastname, phone = :phone WHERE id = :id");
                // Execute the statement with the provided values
                if ($stmt->execute([
                    'firstname' => $_POST['firstname'],
                    'secondname' => $_POST['secondname'],
                    'lastname' => $_POST['lastname'],
                    'phone' => $_POST['phone'],
                    'id' => $_POST['id']
                ])) {
                    $_SESSION['message'] = "Record updated successfully!"; // Success message
                } else {
                    $_SESSION['message'] = "Error updating record."; // Error message
                }
                header("Location: records.php"); // Redirect to the records page after update
                exit(); // Terminate the script after redirection
            }
        }
    }

    if (isset($_POST['delete'])) { // Check if the delete button was clicked
        // Prepare a SQL statement to delete the user by ID
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = :id");
        // Execute the statement with the provided user ID
        if ($stmt->execute(['id' => $_POST['id']])) {
            $_SESSION['message'] = "Record deleted successfully!"; // Success message
        } else {
            $_SESSION['message'] = "Error deleting record."; // Error message
        }
        header("Location: records.php"); // Redirect to the records page after deletion
        exit(); // Terminate the script after redirection
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Character encoding for the HTML document -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design settings -->
    <title>User Records</title> <!-- Title of the HTML document -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS for styling -->
    <style>
        body {
            padding: 20px; /* Padding around the body */
        }
        table {
            margin-top: 20px; /* Margin above the table */
        }
        .modal-header, .modal-footer {
            border: none; /* Remove borders from modal header and footer */
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>User Records</h1>

        <?php if (isset($_SESSION['message'])): ?> <!-- Check if there's a message in the session -->
            <script>alert('<?= htmlspecialchars($_SESSION['message']) ?>');</script> <!-- Display the message in an alert -->
            <?php unset($_SESSION['message']); // Clear message after displaying ?>
        <?php endif; ?>

        <?php if (count($records) > 0): ?> <!-- Check if there are records to display -->
            <table class="table table-bordered table-striped"> <!-- Start a Bootstrap table -->
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Second Name</th>
                        <th>Last Name</th>
                        <th>Phone Number</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $record): ?> <!-- Loop through each record -->
                        <tr>
                            <td><?= htmlspecialchars($record['firstname']) ?></td> <!-- Display first name -->
                            <td><?= htmlspecialchars($record['secondname']) ?></td> <!-- Display second name -->
                            <td><?= htmlspecialchars($record['lastname']) ?></td> <!-- Display last name -->
                            <td><?= htmlspecialchars($record['phone']) ?></td> <!-- Display phone number -->
                            <td>
                                <!-- Button to open the edit modal -->
                                <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $record['id'] ?>" data-toggle="modal" data-target="#editModal">Edit</button>
                                <!-- Button to open the delete modal -->
                                <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $record['id'] ?>" data-toggle="modal" data-target="#deleteModal">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records found.</p> <!-- Message when no records are available -->
        <?php endif; ?>

        <a class="btn btn-primary" href="index">Back to Form</a> <!-- Button to go back to the form -->
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User</h5> <!-- Title of the modal -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <!-- Close button -->
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST"> <!-- Form for editing user details -->
                        <input type="hidden" id="editId" name="id"> <!-- Hidden field to hold user ID -->
                        <div class="form-group">
                            <label for="editFirstName">First Name</label> <!-- Label for first name -->
                            <input type="text" class="form-control" id="editFirstName" name="firstname" required> <!-- Input for first name -->
                        </div>
                        <div class="form-group">
                            <label for="editSecondName">Second Name</label> <!-- Label for second name -->
                            <input type="text" class="form-control" id="editSecondName" name="secondname" required> <!-- Input for second name -->
                        </div>
                        <div class="form-group">
                            <label for="editLastName">Last Name</label> <!-- Label for last name -->
                            <input type="text" class="form-control" id="editLastName" name="lastname" required> <!-- Input for last name -->
                        </div>
                        <div class="form-group">
                            <label for="editPhone">Phone Number</label> <!-- Label for phone number -->
                            <input type="text" class="form-control" id="editPhone" name="phone" required> <!-- Input for phone number -->
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Update</button> <!-- Submit button for updating -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5> <!-- Title of the delete modal -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <!-- Close button -->
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="deleteForm" method="POST"> <!-- Form for deleting user -->
                        <input type="hidden" id="deleteId" name="id"> <!-- Hidden field to hold user ID -->
                        <p>Are you sure you want to delete this user?</p> <!-- Confirmation message -->
                        <button type="submit" name="delete" class="btn btn-danger">Delete</button> <!-- Submit button for deletion -->
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> <!-- jQuery for modal functionality -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script> <!-- Popper.js for tooltip and popover positioning -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> <!-- Bootstrap JS for modal functionality -->

    <script>
        // Populate the edit modal with user data
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id'); // Get the user ID from the button's data attribute
            var row = $(this).closest('tr'); // Get the closest row for this button
            $('#editId').val(id); // Set the user ID in the edit modal
            $('#editFirstName').val(row.find('td:eq(0)').text()); // Set the first name in the edit modal
            $('#editSecondName').val(row.find('td:eq(1)').text()); // Set the second name in the edit modal
            $('#editLastName').val(row.find('td:eq(2)').text()); // Set the last name in the edit modal
            $('#editPhone').val(row.find('td:eq(3)').text()); // Set the phone number in the edit modal
        });

        // Populate the delete modal with user ID
        $('.delete-btn').on('click', function() {
            var id = $(this).data('id'); // Get the user ID from the button's data attribute
            $('#deleteId').val(id); // Set the user ID in the delete modal
        });
    </script>

</body>
</html>
