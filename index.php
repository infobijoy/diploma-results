<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Student Result</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .check-result {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

<!-- Search Form Card -->
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg mb-8">
    <h2 class="text-2xl font-semibold mb-6 text-center">Search Student Result</h2>
    <form id="searchForm" method="POST" class="space-y-4">
        <div>
            <label for="roll" class="block text-sm font-medium text-gray-700">Enter Roll Number:</label>
            <input type="number" id="roll" name="roll" required class="mt-1 block w-full rounded-md p-2 border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Roll Number">
        </div>
        <div>
            <button type="submit" id="searchButton" class="w-full py-2 px-4 bg-indigo-600 hover:bg-indigo-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <span id="buttonText">Search</span>
                <span id="loadingSpinner" class="hidden">
                    <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </div>
    </form>

    <div id="resultContainer" class="mt-6 space-y-4 hidden">
        <!-- Results will be dynamically inserted here -->
    </div>

    <div id="errorContainer" class="text-red-600 mt-4 hidden">
        <!-- Error messages will be dynamically inserted here -->
    </div>
</div>

<!-- Owner Information Card -->
<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
    <footer class="text-center text-sm text-gray-500">
        <p>Owner Information:</p>
        <p>
            <i class="fab fa-facebook-square text-indigo-600 mr-1"></i>
            <a href="https://www.facebook.com/FreelancerBijoyChandraDas" class="text-indigo-600 hover:underline" target="_blank">Programmer Bijoy</a>
        </p>
        <p>
            <i class="fab fa-whatsapp text-green-600 mr-1"></i>
            <a href="https://wa.me/+8801634846245" class="text-indigo-600 hover:underline" target="_blank">+8801634846245</a>
        </p>
        <p>
            <i class="fas fa-phone text-blue-600 mr-1"></i>
            <a href="tel:+8801634846245" class="text-indigo-600 hover:underline">+8801634846245</a>
        </p>
        <p class="mt-4">
            Thank you for using our service! If you have any questions or feedback, please don't hesitate to reach out. We appreciate your support.
        </p>
        <p class="mt-2 mb-4">
            For custom web development, design, or any other digital solutions, feel free to contact me. Let's build something amazing together!
        </p>
        <?php include "./link.php"; ?>
    </footer>
</div>

<script>
document.getElementById('searchForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const roll = document.getElementById('roll').value;
    const buttonText = document.getElementById('buttonText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const resultContainer = document.getElementById('resultContainer');
    const errorContainer = document.getElementById('errorContainer');

    // Show loading spinner
    buttonText.classList.add('hidden');
    loadingSpinner.classList.remove('hidden');

    // Clear previous results and errors
    resultContainer.innerHTML = '';
    resultContainer.classList.add('hidden');
    errorContainer.innerHTML = '';
    errorContainer.classList.add('hidden');

    // Send AJAX request to check-results.php
    fetch('check-results.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `roll=${encodeURIComponent(roll)}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.errorMsg) {
            errorContainer.innerHTML = data.errorMsg;
            errorContainer.classList.remove('hidden');
        } else if (data.results.length > 0) {
            data.results.forEach(result => {
                const resultCard = document.createElement('div');
                resultCard.className = 'bg-gray-50 p-4 rounded-md shadow-sm';
                resultCard.innerHTML = `
                    <h3 class="text-lg font-semibold mb-2">Result Roll: ${result.roll}</h3>
                    <ul class="list-disc list-inside space-y-1">
                        ${Object.entries(result.data).map(([key, value]) => `
                            <li>
                                <strong class="capitalize">${key}:</strong>
                                ${Array.isArray(value) ? value.join(', ') : value}
                            </li>
                        `).join('')}
                    </ul>
                `;
                resultContainer.appendChild(resultCard);
            });
            resultContainer.classList.remove('hidden');
        }
    })
    .catch(error => {
        errorContainer.innerHTML = 'An error occurred while processing your request.';
        errorContainer.classList.remove('hidden');
    })
    .finally(() => {
        // Hide loading spinner
        buttonText.classList.remove('hidden');
        loadingSpinner.classList.add('hidden');
    });
});
</script>

</body>
</html>