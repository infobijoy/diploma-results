<?php
include "./config.php";

$results = [];
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = trim($_POST["roll"]);

    if (!empty($roll)) {
        $stmt = $conn->prepare("SELECT id, roll, data FROM students WHERE roll = ?");
        $stmt->bind_param("s", $roll);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mergedData = []; // Array to store merged data for the roll
            $firstId = null; // Store the first ID for the roll

            while ($row = $result->fetch_assoc()) {
                $data = json_decode($row['data'], true);

                if (isset($data['ref_sub'])) {
                    unset($data['ref_sub']);
                }

                if (empty($mergedData)) { // First row for this roll
                    $mergedData = $data;
                    $firstId = $row['id'];
                } else {
                    if (is_array($mergedData) && is_array($data)) {
                        $mergedData = array_merge($mergedData, $data);
                    }
                }
            }

            $results[] = [
                'id' => $firstId,
                'roll' => $roll,
                'data' => $mergedData
            ];
        } else {
            $errorMsg = "No result found for Roll: $roll.";
        }
        $stmt->close();
    } else {
        $errorMsg = "Please enter a valid roll number.";
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode([
    'results' => $results,
    'errorMsg' => $errorMsg
]);
exit();
?>