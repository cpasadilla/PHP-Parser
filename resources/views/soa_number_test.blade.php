<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SOA Number Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"] { 
            width: 100%; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-size: 16px;
        }
        .status { 
            margin-top: 10px; 
            padding: 10px; 
            border-radius: 4px; 
            display: none;
        }
        .status.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .debug { 
            margin-top: 20px; 
            padding: 15px; 
            background-color: #f8f9fa; 
            border: 1px solid #e9ecef; 
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SOA Number Test Page</h1>
        
        <div class="form-group">
            <label for="soaNumberInput">SOA Number:</label>
            <input type="text" id="soaNumberInput" placeholder="Enter SOA No." value="">
            <div id="saveStatus" class="status"></div>
        </div>

        <div class="form-group">
            <label>Test Order ID:</label>
            <input type="text" id="testOrderId" value="73733" readonly>
        </div>

        <div class="form-group">
            <button onclick="testSave()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Test Manual Save</button>
        </div>

        <div id="debugOutput" class="debug">
Debug output will appear here...
        </div>
    </div>

    <script>
        // Debug function to log everything
        function debugLog(message) {
            const debugDiv = document.getElementById('debugOutput');
            const timestamp = new Date().toLocaleTimeString();
            debugDiv.textContent += `[${timestamp}] ${message}\n`;
            console.log(`[SOA Debug] ${message}`);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('DOM Content Loaded');
            
            const soaInput = document.getElementById('soaNumberInput');
            if (soaInput) {
                debugLog('SOA input element found');
                
                // Add event listeners
                soaInput.addEventListener('input', function() {
                    debugLog(`Input changed to: "${this.value}"`);
                    saveSoaNumber();
                });
                
                soaInput.addEventListener('blur', function() {
                    debugLog(`Input lost focus, value: "${this.value}"`);
                });
                
                soaInput.addEventListener('keyup', function() {
                    debugLog(`Key up, value: "${this.value}"`);
                });
                
            } else {
                debugLog('ERROR: SOA input element not found!');
            }
        });

        let saveTimeout;
        
        function saveSoaNumber() {
            const soaInput = document.getElementById('soaNumberInput');
            const orderIdInput = document.getElementById('testOrderId');
            const statusDiv = document.getElementById('saveStatus');
            
            if (!soaInput || !orderIdInput) {
                debugLog('ERROR: Input elements not found');
                return;
            }
            
            const soaNumber = soaInput.value.trim();
            const orderId = orderIdInput.value.trim();
            
            debugLog(`saveSoaNumber called with SOA: "${soaNumber}", Order ID: "${orderId}"`);
            
            // Clear previous timeout
            if (saveTimeout) {
                clearTimeout(saveTimeout);
                debugLog('Cleared previous timeout');
            }
            
            // Set new timeout
            saveTimeout = setTimeout(() => {
                debugLog('Timeout executed, making AJAX request...');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    debugLog('ERROR: CSRF token not found');
                    showStatus('error', 'CSRF token not found');
                    return;
                }
                
                const requestData = {
                    soa_number: soaNumber,
                    order_id: orderId,
                    _token: csrfToken.getAttribute('content')
                };
                
                debugLog(`Request data: ${JSON.stringify(requestData)}`);
                
                fetch('/test-update-soa-number', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                    },
                    body: JSON.stringify(requestData)
                })
                .then(response => {
                    debugLog(`Response status: ${response.status}`);
                    debugLog(`Response ok: ${response.ok}`);
                    return response.json();
                })
                .then(data => {
                    debugLog(`Response data: ${JSON.stringify(data)}`);
                    
                    if (data.success) {
                        showStatus('success', 'SOA number saved successfully!');
                        debugLog('SOA number saved successfully');
                    } else {
                        showStatus('error', 'Failed to save SOA number: ' + (data.message || 'Unknown error'));
                        debugLog('Save failed: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    debugLog(`Fetch error: ${error.message}`);
                    showStatus('error', 'Network error: ' + error.message);
                });
                
            }, 1000);
            
            debugLog('Timeout set for 1 second');
        }
        
        function showStatus(type, message) {
            const statusDiv = document.getElementById('saveStatus');
            statusDiv.className = `status ${type}`;
            statusDiv.textContent = message;
            statusDiv.style.display = 'block';
            
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 3000);
        }
        
        function testSave() {
            debugLog('Manual test save button clicked');
            saveSoaNumber();
        }
    </script>
</body>
</html>
