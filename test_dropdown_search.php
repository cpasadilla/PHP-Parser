<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Customer Dropdown Search</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold mb-6 text-center">Customer Search Test</h1>
        
        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
            <input 
                type="text" 
                id="customer_name" 
                autocomplete="off" 
                class="w-full px-3 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                placeholder="Start typing customer name..."
            >
            <div id="customerDropdown" class="absolute z-50 w-full bg-white border border-gray-300 rounded-lg shadow-lg mt-1 hidden max-h-48 overflow-y-auto">
                <!-- Customer suggestions will appear here -->
            </div>
            <input type="hidden" id="customer_id">
        </div>
        
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h3 class="font-medium text-gray-700 mb-2">Selected Customer:</h3>
            <p><strong>Name:</strong> <span id="selectedName">None</span></p>
            <p><strong>ID:</strong> <span id="selectedId">None</span></p>
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Try searching for:</strong></p>
            <ul class="list-disc list-inside mt-2">
                <li>"ABC" - for ABC Trading Corporation</li>
                <li>"Maria" - for various Maria customers</li>
                <li>"Ship" - for shipping companies</li>
                <li>"Cruz" - for customers with Cruz surname</li>
                <li>"Trading" - for trading companies</li>
            </ul>
        </div>
    </div>

    <script>
        function setupCustomerSearch() {
            const input = document.getElementById('customer_name');
            const dropdown = document.getElementById('customerDropdown');
            const idField = document.getElementById('customer_id');
            const selectedName = document.getElementById('selectedName');
            const selectedId = document.getElementById('selectedId');
            let searchTimeout;
            
            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Clear previous timeout
                clearTimeout(searchTimeout);
                
                // Clear results if query is empty
                if (query.length === 0) {
                    dropdown.innerHTML = '';
                    dropdown.classList.add('hidden');
                    idField.value = '';
                    updateSelectedDisplay('', '');
                    return;
                }
                
                // Search immediately for single characters, with slight delay for multiple characters
                const delay = query.length === 1 ? 0 : 300;
                
                searchTimeout = setTimeout(() => {
                    // Simulate the Laravel route - you can replace this with actual fetch to your Laravel app
                    fetch(`http://localhost/SFX-1/public/accounting/search-customers?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        
                        if (data.length === 0) {
                            dropdown.innerHTML = '<div class="px-4 py-2 text-gray-500">No customers found</div>';
                            dropdown.classList.remove('hidden');
                            return;
                        }
                        
                        // Sort results by relevance (exact matches first, then partial matches)
                        data.sort((a, b) => {
                            const aName = a.name.toLowerCase();
                            const bName = b.name.toLowerCase();
                            const queryLower = query.toLowerCase();
                            
                            // Exact match at start gets highest priority
                            const aStartsWithQuery = aName.startsWith(queryLower);
                            const bStartsWithQuery = bName.startsWith(queryLower);
                            
                            if (aStartsWithQuery && !bStartsWithQuery) return -1;
                            if (!aStartsWithQuery && bStartsWithQuery) return 1;
                            
                            // Then by word boundaries (whole word matches)
                            const aWordMatch = aName.includes(' ' + queryLower) || aName.startsWith(queryLower);
                            const bWordMatch = bName.includes(' ' + queryLower) || bName.startsWith(queryLower);
                            
                            if (aWordMatch && !bWordMatch) return -1;
                            if (!aWordMatch && bWordMatch) return 1;
                            
                            // Finally by alphabetical order
                            return aName.localeCompare(bName);
                        });
                        
                        // Limit results to top 10 for better performance
                        data.slice(0, 10).forEach(customer => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 cursor-pointer hover:bg-gray-100 border-b border-gray-200 last:border-b-0';
                            div.textContent = customer.name;
                            div.dataset.id = customer.id;
                            div.dataset.name = customer.name;
                            
                            // Highlight matching text
                            const nameText = customer.name;
                            const regex = new RegExp(`(${query})`, 'gi');
                            div.innerHTML = nameText.replace(regex, '<span class="bg-yellow-200">$1</span>');
                            
                            div.addEventListener('click', function() {
                                input.value = customer.name;
                                idField.value = customer.id;
                                updateSelectedDisplay(customer.name, customer.id);
                                dropdown.classList.add('hidden');
                            });
                            
                            dropdown.appendChild(div);
                        });
                        
                        // Show the dropdown
                        dropdown.classList.remove('hidden');
                        
                        // Add a "showing X results" indicator if there are more than 10
                        if (data.length > 10) {
                            const moreDiv = document.createElement('div');
                            moreDiv.className = 'px-4 py-2 text-xs text-gray-500 bg-gray-50';
                            moreDiv.textContent = `Showing 10 of ${data.length} results`;
                            dropdown.appendChild(moreDiv);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching customers:', error);
                        dropdown.innerHTML = '<div class="px-4 py-2 text-red-500">Error loading customers</div>';
                        dropdown.classList.remove('hidden');
                    });
                }, delay);
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Hide dropdown when input loses focus (but allow clicking on dropdown items)
            input.addEventListener('blur', function(e) {
                setTimeout(() => {
                    if (!dropdown.contains(document.activeElement)) {
                        dropdown.classList.add('hidden');
                    }
                }, 200);
            });
            
            // Show dropdown when input gets focus and has value
            input.addEventListener('focus', function() {
                if (this.value.trim() && dropdown.children.length > 0) {
                    dropdown.classList.remove('hidden');
                }
            });
            
            // Clear selection when input is manually cleared
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' || e.key === 'Delete') {
                    idField.value = '';
                    updateSelectedDisplay('', '');
                }
            });
            
            function updateSelectedDisplay(name, id) {
                selectedName.textContent = name || 'None';
                selectedId.textContent = id || 'None';
            }
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', setupCustomerSearch);
    </script>
</body>
</html>
