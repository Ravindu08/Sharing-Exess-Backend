<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sample Food Listings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            margin: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            border: none;
            cursor: pointer;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .result {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .success {
            border-left: 4px solid #4CAF50;
        }
        .error {
            border-left: 4px solid #f44336;
        }
        .sample-list {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .sample-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍽️ Add Sample Food Listings</h1>
        
        <div class="sample-list">
            <h3>📦 Sample Listings to be Added:</h3>
            <div class="sample-item">🥖 Fresh Bread - 20 loaves (Colombo 03)</div>
            <div class="sample-item">🍚 Rice Bags - 5 kg bags (Nugegoda)</div>
            <div class="sample-item">🥫 Canned Vegetables - 15 cans (Dehiwala)</div>
            <div class="sample-item">🍎 Fresh Fruits - 10 kg mixed (Mount Lavinia)</div>
            <div class="sample-item">🥛 Milk Powder - 8 packets (Battaramulla)</div>
            <div class="sample-item">🛢️ Cooking Oil - 5 liters (Maharagama)</div>
            <div class="sample-item">🍯 Sugar - 3 kg (Kohuwala)</div>
            <div class="sample-item">☕ Tea Bags - 100 bags (Moratuwa)</div>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <button onclick="addSamples()" class="button">➕ Add Sample Listings</button>
            <a href="test_listings.php" class="button" target="_blank">👁️ View Current Listings</a>
        </div>

        <div id="result" class="result" style="display: none;"></div>
    </div>

    <script>
        function addSamples() {
            const resultDiv = document.getElementById('result');
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<p>⏳ Adding sample listings...</p>';
            
            fetch('add_sample_listings.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.className = 'result success';
                        resultDiv.innerHTML = `
                            <h3>✅ Success!</h3>
                            <p>${data.message}</p>
                            <p><strong>Added:</strong> ${data.added_count} listings</p>
                            <p><strong>Errors:</strong> ${data.error_count}</p>
                        `;
                    } else {
                        resultDiv.className = 'result error';
                        resultDiv.innerHTML = `
                            <h3>❌ Error</h3>
                            <p>${data.message || 'Failed to add sample listings'}</p>
                        `;
                    }
                })
                .catch(error => {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <h3>❌ Network Error</h3>
                        <p>Failed to connect to the server. Please check:</p>
                        <ul>
                            <li>XAMPP Apache server is running</li>
                            <li>Database connection is working</li>
                            <li>PHP is properly configured</li>
                        </ul>
                        <p>Error: ${error.message}</p>
                    `;
                });
        }
    </script>
</body>
</html> 