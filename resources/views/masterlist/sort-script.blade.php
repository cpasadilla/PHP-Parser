<!-- Sorting functionality script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Setup event listeners for the sortable headers
        document.getElementById('blHeader').addEventListener('click', function() {
            toggleSort('bl');
        });
        
        document.getElementById('shipperHeader').addEventListener('click', function() {
            toggleSort('shipper');
        });
        
        document.getElementById('consigneeHeader').addEventListener('click', function() {
            toggleSort('consignee');
        });
        
        // Initial sort by BL in ascending order
        sortTable('bl', 'asc');
    });
    
    // Variables to track sort state
    let currentSortColumn = 'bl';
    let sortDirection = 'asc';
    
    // Function to toggle sort direction and apply sort
    function toggleSort(column) {
        // Hide all sort indicators first
        document.getElementById('blSortIndicator').style.display = 'none';
        document.getElementById('shipperSortIndicator').style.display = 'none';
        document.getElementById('consigneeSortIndicator').style.display = 'none';
        
        // If clicking the same column, toggle direction
        if (currentSortColumn === column) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // If clicking a different column, default to ascending
            sortDirection = 'asc';
            currentSortColumn = column;
        }
        
        // Show the appropriate sort indicator
        const indicator = document.getElementById(`${column}SortIndicator`);
        indicator.style.display = 'inline';
        indicator.textContent = sortDirection === 'asc' ? '▲' : '▼';
        
        // Apply the sort
        sortTable(column, sortDirection);
    }
    
    // Main function to sort the table by any column
    function sortTable(column, direction = 'asc') {
        const tbody = document.querySelector('#ordersTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Determine which column index to sort by
        let columnIndex;
        switch(column) {
            case 'bl':
                columnIndex = 0; // BL is the 1st column (index 0)
                break;
            case 'shipper':
                columnIndex = 4; // SHIPPER is the 5th column (index 4)
                break;
            case 'consignee':
                columnIndex = 5; // CONSIGNEE is the 6th column (index 5)
                break;
            default:
                columnIndex = 0;
        }
        
        // Sort the rows
        const sortedRows = rows.sort((a, b) => {
            const aValue = a.cells[columnIndex].textContent.trim().toLowerCase();
            const bValue = b.cells[columnIndex].textContent.trim().toLowerCase();
            
            if (direction === 'asc') {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });
        
        // Remove existing rows
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        // Append sorted rows
        sortedRows.forEach(row => {
            tbody.appendChild(row);
        });
        
        // Recalculate totals after sorting (if that function exists)
        if (typeof calculateTotals === 'function') {
            calculateTotals();
        }
    }
</script>
