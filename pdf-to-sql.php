<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF to JSON to SQL</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .pdf-to-sql {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
    <h2 class="text-2xl font-semibold mb-6 text-center">Upload PDF and Convert to SQL</h2>
    <div class="mb-4">
        <input type="file" id="fileInput" accept="application/pdf" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
    </div>
    <p id="status" class="text-sm text-gray-600 mb-4">Status: Waiting for file upload...</p>
    <button id="downloadBtn" style="display: none;" class="w-full py-2 px-4 bg-green-600 hover:bg-green-700 rounded-md text-white font-semibold focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">Download SQL File</button>
    <script>
    document.getElementById("fileInput").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (!file) return;

        document.getElementById("status").textContent = "Processing PDF...";
        const reader = new FileReader();
        reader.onload = function() {
            const typedArray = new Uint8Array(reader.result);
            processPDF(typedArray);
        };
        reader.readAsArrayBuffer(file);
    });

    async function processPDF(data) {
        const pdf = await pdfjsLib.getDocument({ data }).promise;
        let fullText = "";

        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const textContent = await page.getTextContent();
            const pageText = textContent.items.map(item => item.str).join(" ");
            fullText += pageText + "\n";
        }

        document.getElementById("status").textContent = "Extracting and formatting data...";
        const jsonData = parseTextToJSON(fullText);

        generateSQLFile(jsonData);
        document.getElementById("status").textContent = "Processing complete!";
    }

    function parseTextToJSON(text) {
        const data = [];

        // Format 1 (GPA Data)
        const format1Regex = /(\d{6}) \((.*?)\)/g;
        text.replace(format1Regex, (_, id, details) => {
            const gpaData = Object.fromEntries(details.match(/gpa\d: [\d.]+/g)?.map(g => g.split(": ").map(v => v.trim())) || []);
            data.push({ roll: id, data: JSON.stringify(gpaData) });
        });

        // Format 2 (GPA + Reference Subjects)
        const format2Regex = /(\d{6}) \{(.*?)\}/g;
        text.replace(format2Regex, (_, id, details) => {
            const gpaData = {};
            const refSubjects = [];

            details.split(",").forEach(item => {
                item = item.trim();
                if (item.startsWith("gpa")) {
                    let [key, value] = item.split(": ");
                    gpaData[key] = value;
                } else if (item.includes("ref_sub")) {
                    const subjects = item.match(/\d{5}\(T\)/g)?.map(s => s.trim()) || [];
                    refSubjects.push(...subjects);
                }
            });

            gpaData["ref_sub"] = refSubjects;
            data.push({ roll: id, data: JSON.stringify(gpaData) });
        });

        // Format 3 (Only Subject Codes, ensuring (T) is included)
        const format3Regex = /(\d{6}) \{ (.*?) \}/g;
        text.replace(format3Regex, (_, id, details) => {
            const subjects = details.match(/\d{5}\(T\)/g)?.map(s => s.trim()) || [];
            data.push({ roll: id, data: JSON.stringify({ "subjects": subjects }) });
        });

        // Format 4 (CGPA and GPA)
        const format4Regex = /(\d{6}) cgpa: ([\d.]+)(.*?)\)/g;
        text.replace(format4Regex, (_, id, cgpa, details) => {
            const gpaData = Object.fromEntries(details.match(/gpa\d: [\d.]+/g)?.map(g => g.split(": ").map(v => v.trim())) || []);
            gpaData["cgpa"] = cgpa;
            data.push({ roll: id, data: JSON.stringify(gpaData) });
        });

        return data;
    }

    function generateSQLFile(jsonData) {
        let sqlContent = `
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll VARCHAR(10),
    data TEXT
);\n\n`;

        jsonData.forEach(row => {
            sqlContent += `
INSERT INTO students (roll, data) VALUES ('${row.roll}', '${row.data.replace(/'/g, "''")}');\n`;
        });

        // Create a downloadable SQL file
        const blob = new Blob([sqlContent], { type: "text/sql" });
        const url = URL.createObjectURL(blob);
        const downloadBtn = document.getElementById("downloadBtn");

        downloadBtn.style.display = "block";
        downloadBtn.onclick = function () {
            const a = document.createElement("a");
            a.href = url;
            a.download = "data.sql";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        };
    }
</script>
    <?php include "./link.php"; ?>
</div>

</body>
</html>