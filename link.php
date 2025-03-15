<?php

$isAdmin = false; // Default to false

if (isset($_GET['user']) && $_GET['user'] === 'admin') {
    $isAdmin = true;
}

if ($isAdmin) {
    echo '<div class="flex justify-between">
    <div class="check-result">
        <a href="./" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Check Result</a>
    </div>
    <div class="pdf-to-sql">
        <a href="./pdf-to-sql.php" class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">PDF to SQL</a>
    </div>
    <div class="import-sql">
        <a href="./import-db.php" class="w-full py-2 px-4 bg-red-600 hover:bg-red-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">Import SQL</a>
    </div>
</div>';
} else {
    // Optional: Display different content or nothing for non-admin users.
    // echo '<div class="check-result">
    //     <a href="./" class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Check Result</a>
    // </div>';
}

?>