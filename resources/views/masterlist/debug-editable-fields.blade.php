<!-- Debug Script for Cargo Status and Remark Textareas -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cargo Status Textarea Handling
        const cargoStatusTextareas = document.querySelectorAll('.cargo-status-textarea');
        console.log('Found cargo status textareas:', cargoStatusTextareas.length);
        
        function updateCargoStatus(textarea) {
            const orderId = textarea.getAttribute('data-order-id');
            const newValue = textarea.value;
            
            console.log('Updating cargo status:', { orderId, newValue });
            textarea.classList.add('saving');
            
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token not found');
                alert('CSRF token not found. Cannot save changes.');
                textarea.classList.remove('saving');
                return;
            }
            
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: 'cargoType',
                    value: newValue
                })
            })
            .then(response => {
                console.log('Cargo status response status:', response.status);
                return response.text().then(text => {
                    try {
                        return text ? JSON.parse(text) : {};
                    } catch (e) {
                        console.error('Error parsing JSON:', e, text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                console.log('Cargo status response data:', data);
                if (data.success) {
                    console.log('Cargo status updated successfully');
                    setTimeout(() => {
                        textarea.classList.remove('saving');
                    }, 500);
                } else {
                    console.error('Failed to update cargo status:', data.message);
                    textarea.classList.remove('saving');
                    alert('Failed to save cargo status: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                textarea.classList.remove('saving');
                alert('Error saving cargo status. Please check the console for details.');
            });
        }
        
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }
        
        const debouncedUpdate = debounce(updateCargoStatus, 500);
        
        cargoStatusTextareas.forEach(textarea => {
            textarea.addEventListener('change', function() {
                updateCargoStatus(this);
            });
            
            textarea.addEventListener('blur', function() {
                updateCargoStatus(this);
            });
            
            textarea.addEventListener('input', function() {
                debouncedUpdate(this);
            });
            
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    updateCargoStatus(this);
                }
            });
        });
        
        // Remark Textarea Handling
        const remarkTextareas = document.querySelectorAll('.remark-textarea');
        console.log('Found remark textareas:', remarkTextareas.length);
        
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            const newHeight = Math.max(40, textarea.scrollHeight + 4);
            textarea.style.height = newHeight + 'px';
        }
        
        function updateRemark(textarea) {
            const orderId = textarea.getAttribute('data-order-id');
            const newValue = textarea.value;
            
            console.log('Updating remark:', { orderId, newValue });
            textarea.classList.add('saving');
            
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token not found');
                alert('CSRF token not found. Cannot save changes.');
                textarea.classList.remove('saving');
                return;
            }
            
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: 'remark',
                    value: newValue
                })
            })
            .then(response => {
                console.log('Remark response status:', response.status);
                return response.text().then(text => {
                    try {
                        return text ? JSON.parse(text) : {};
                    } catch (e) {
                        console.error('Error parsing JSON:', e, text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                console.log('Remark response data:', data);
                if (data.success) {
                    console.log('Remark updated successfully');
                    setTimeout(() => {
                        textarea.classList.remove('saving');
                    }, 500);
                } else {
                    console.error('Failed to update remark:', data.message);
                    textarea.classList.remove('saving');
                    alert('Failed to save remark: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                textarea.classList.remove('saving');
                alert('Error saving remark. Please check the console for details.');
            });
        }
        
        const debouncedRemarkUpdate = debounce(updateRemark, 500);
        
        remarkTextareas.forEach(textarea => {
            autoResize(textarea);
            
            textarea.addEventListener('change', function() {
                updateRemark(this);
            });
            
            textarea.addEventListener('blur', function() {
                updateRemark(this);
            });
            
            textarea.addEventListener('input', function() {
                autoResize(this);
                debouncedRemarkUpdate(this);
            });
            
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    updateRemark(this);
                }
            });
              window.addEventListener('resize', function() {
                autoResize(textarea);
            });
        });
        
        // Container Textarea Handling
        const containerTextareas = document.querySelectorAll('.container-textarea');
        console.log('Found container textareas:', containerTextareas.length);
        
        function updateContainer(textarea) {
            const orderId = textarea.getAttribute('data-order-id');
            const newValue = textarea.value;
            
            console.log('Updating container:', { orderId, newValue });
            textarea.classList.add('saving');
            
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token not found');
                alert('CSRF token not found. Cannot save changes.');
                textarea.classList.remove('saving');
                return;
            }
            
            fetch(`/update-order-field/${orderId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token.getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: 'containerNum',
                    value: newValue
                })
            })
            .then(response => {
                console.log('Container response status:', response.status);
                return response.text().then(text => {
                    try {
                        return text ? JSON.parse(text) : {};
                    } catch (e) {
                        console.error('Error parsing JSON:', e, text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                console.log('Container response data:', data);
                if (data.success) {
                    console.log('Container updated successfully');
                    setTimeout(() => {
                        textarea.classList.remove('saving');
                    }, 500);
                } else {
                    console.error('Failed to update container:', data.message);
                    textarea.classList.remove('saving');
                    alert('Failed to save container: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('AJAX error:', error);
                textarea.classList.remove('saving');
                alert('Error saving container. Please check the console for details.');
            });
        }
        
        const debouncedContainerUpdate = debounce(updateContainer, 500);
        
        containerTextareas.forEach(textarea => {
            // Initial resize based on content
            autoResize(textarea);
            
            textarea.addEventListener('change', function() {
                updateContainer(this);
            });
            
            textarea.addEventListener('blur', function() {
                updateContainer(this);
            });
            
            textarea.addEventListener('input', function() {
                autoResize(this);
                debouncedContainerUpdate(this);
            });
            
            textarea.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    updateContainer(this);
                }
            });
            
            window.addEventListener('resize', function() {
                autoResize(textarea);
            });
        });
    });
</script>
