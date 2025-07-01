<!-- Script for Cargo Status Textarea -->
<script>    document.addEventListener('DOMContentLoaded', function () {
        const cargoStatusTextareas = document.querySelectorAll('.cargo-status-textarea');
        console.log('Found cargo status textareas:', cargoStatusTextareas.length);
        
        // Function to send update to server
        function updateCargoStatus(textarea) {
            const orderId = textarea.getAttribute('data-order-id');
            const newValue = textarea.value;
            
            console.log('Updating cargo status:', { orderId, newValue });
            
            // Add visual indication that saving is in progress
            textarea.classList.add('saving');
            
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },                body: JSON.stringify({
                    field: 'cargoType', // This should match the actual database column name
                    value: newValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Cargo status updated successfully');
                    // Remove the saving indicator after a short delay
                    setTimeout(() => {
                        textarea.classList.remove('saving');
                    }, 500);
                } else {
                    console.error('Failed to update cargo status:', data.message);
                    textarea.classList.remove('saving');
                    alert('Failed to save cargo status information. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                textarea.classList.remove('saving');
                alert('Error saving cargo status information. Please check your connection and try again.');
            });
        }
        
        // Debounce function to limit API calls
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        
        // Create debounced update function
        const debouncedUpdate = debounce(updateCargoStatus, 500);
        
        cargoStatusTextareas.forEach(textarea => {
            // Handle change event (when user clicks away)
            textarea.addEventListener('change', function() {
                updateCargoStatus(this);
            });
            
            // Handle blur event (when focus leaves the textarea)
            textarea.addEventListener('blur', function() {
                updateCargoStatus(this);
            });
            
            // Handle input event with debounce (for auto-save while typing)
            textarea.addEventListener('input', function() {
                debouncedUpdate(this);
            });
            
            // Handle keydown event for Enter key
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); // Prevent adding a new line
                    updateCargoStatus(this);
                }
            });
        });
    });
</script>

<!-- Script for Remark Textarea -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const remarkTextareas = document.querySelectorAll('.remark-textarea');
        console.log('Found remark textareas:', remarkTextareas.length);
        
        // Function to auto-resize textarea based on content
        function autoResize(textarea) {
            // Reset height to allow proper calculation
            textarea.style.height = 'auto';
            
            // Set new height based on scroll height plus a small buffer
            const newHeight = Math.max(40, textarea.scrollHeight + 4);
            textarea.style.height = newHeight + 'px';
        }
        
        // Function to send update to server
        function updateRemark(textarea) {
            const orderId = textarea.getAttribute('data-order-id');
            const newValue = textarea.value;
            
            console.log('Updating remark:', { orderId, newValue });
            
            // Add visual indication that saving is in progress
            textarea.classList.add('saving');
            
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    field: 'remark',
                    value: newValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Remark updated successfully');
                    // Remove the saving indicator after a short delay
                    setTimeout(() => {
                        textarea.classList.remove('saving');
                    }, 500);
                } else {
                    console.error('Failed to update remark:', data.message);
                    textarea.classList.remove('saving');
                    alert('Failed to save remark information. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                textarea.classList.remove('saving');
                alert('Error saving remark information. Please check your connection and try again.');
            });
        }
        
        // Debounce function to limit API calls
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        
        // Create debounced update function
        const debouncedUpdate = debounce(updateRemark, 500);        
        remarkTextareas.forEach(textarea => {
            // Initial resize based on content
            autoResize(textarea);
            
            // Handle change event (when user clicks away)
            textarea.addEventListener('change', function() {
                updateRemark(this);
            });
            
            // Handle blur event (when focus leaves the textarea)
            textarea.addEventListener('blur', function() {
                updateRemark(this);
            });
            
            // Handle input event with debounce (for auto-save while typing)
            // Also handle auto-resizing when input changes
            textarea.addEventListener('input', function() {
                autoResize(this);
                debouncedUpdate(this);
            });
            
            // Handle keydown event for Enter key
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault(); // Prevent adding a new line
                    updateRemark(this);
                }
            });
            
            // Handle window resize event
            window.addEventListener('resize', function() {
                autoResize(textarea);
            });
        });
    });
</script>
