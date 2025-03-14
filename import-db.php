<?php
set_time_limit(6000);

// Include the database configuration file
require_once 'config.php';

// Define the correct PIN
$correctPin = "626482";

// Initialize variables
$error = "";
$message = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the PIN is submitted
    if (isset($_POST["pin"])) {
        $enteredPin = $_POST["pin"];

        // Validate the PIN
        if ($enteredPin === $correctPin) {
            // PIN is correct, allow file upload
            if (isset($_FILES["sqlFile"])) {
                $file = $_FILES["sqlFile"]["tmp_name"];
                $filename = $_FILES["sqlFile"]["name"];
                $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if ($file_ext != "sql") {
                    $error = "Invalid file type. Only SQL files are allowed.";
                } else {
                    $sqlContent = file_get_contents($file);

                    // Check connection
                    if ($conn->connect_error) {
                        $error = "Database connection failed: " . $conn->connect_error;
                    } else {
                        // Execute the SQL statements
                        $sqlStatements = explode(";", $sqlContent);
                        $success = true; // Flag to track overall success

                        foreach ($sqlStatements as $sql) {
                            $sql = trim($sql);
                            if (!empty($sql)) {
                                if ($conn->query($sql) !== TRUE) {
                                    $error = "Error executing SQL: " . $conn->error;
                                    $success = false;
                                    break; // Stop on first error
                                }
                            }
                        }
                        if ($success) {
                            $message = "SQL file imported successfully.";
                        }

                        $conn->close();
                    }
                }
            }
        } else {
            // PIN is incorrect
            $error = "Incorrect PIN. Access denied.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import SQL File</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .import-sql {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
    <h2 class="text-2xl font-semibold mb-6 text-center">Import SQL File</h2>

    <?php if (isset($message)): ?>
        <p class="text-green-600 mb-4"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p class="text-red-600 mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- PIN Input Form -->
    <form method="post" class="space-y-4">
        <div>
            <label for="pin" class="block text-sm font-medium text-gray-700">Enter PIN</label>
            <input type="password" name="pin" id="pin" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
        </div>
        <div>
            <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Submit PIN</button>
        </div>
        <?php include "./link.php"; ?>
    </form>

    <!-- File Upload Form (Hidden by Default) -->
    <?php if (isset($_POST["pin"]) && $_POST["pin"] === $correctPin): ?>
        <form method="post" enctype="multipart/form-data" class="space-y-4 mt-6">
            <div>
                <label for="sqlFile" class="block text-sm font-medium text-gray-700">Upload SQL File</label>
                <input type="file" name="sqlFile" id="sqlFile" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Import SQL File</button>
            </div>
        </form>
    <?php endif; ?>
</div>

</body>
</html>