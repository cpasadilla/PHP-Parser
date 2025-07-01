// Initialize wharfage calculation functionality
document.addEventListener("DOMContentLoaded", function() {
    // Reference to important elements
    const freightInput = document.getElementById("freight");
    const valueInput = document.getElementById("value");
    const wharfageInput = document.getElementById("wharfage");
    const containerInput = document.getElementById("container_no");
    const shipInput = document.getElementById("ship_no");  // Using ship_no instead of ship
    const voyageInput = document.getElementById("voyage_no"); // Look for the voyage_no input
    
    // We'll use a default voyage since it's not available directly in the form
    // In a real-world scenario, this would be dynamically fetched or stored elsewhere
    let currentVoyage = '';
    
    // Get current voyage from the input if it exists
    if (voyageInput && voyageInput.value) {
        currentVoyage = voyageInput.value;
    } else if (window.currentVoyage) {
        currentVoyage = window.currentVoyage;
    }
    
    // Flag to track if wharfage should be skipped due to subsequent container use
    let skipWharfage = false;
    
    // Helper function to format currency values
    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }
    
    // Function to check if container is a subsequent use
    function checkContainerUsage() {
        // Only check if all required fields are filled
        if (!containerInput || !containerInput.value || !shipInput) {
            return;
        }
        
        const container = containerInput.value.trim();
        const ship = shipInput.value;
        
        if (container && ship && currentVoyage) {
            // Create a form data object
            const formData = new FormData();
            formData.append('container', container);
            formData.append('ship', ship);
            formData.append('voyage', currentVoyage);
            
            // Send AJAX request to check container usage
            fetch('/check-container-usage', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                skipWharfage = data.skipWharfage;
                calculateWharfage();
            })
            .catch(error => {
                console.error('Error checking container usage:', error);
                skipWharfage = false;
                calculateWharfage();
            });
        } else {
            // If any required field is missing, just calculate normally
            calculateWharfage();
        }
    }
    
    // Function to calculate wharfage
    function calculateWharfage() {
        // Get current values
        const freight = parseFloat(freightInput.value.replace(/,/g, '')) || 0;
        const value = parseFloat(valueInput.value.replace(/,/g, '')) || 0;
        
        // Skip wharfage calculation if both value and freight are 0
        if (value <= 0 && freight <= 0) {
            wharfageInput.value = "0.00";
            return;
        }
        
        // Skip wharfage calculation if this is a subsequent use of a reserved container
        if (skipWharfage) {
            wharfageInput.value = "0.00";
            return;
        }
          // Check if the cart contains only GROCERIES
        let onlyGroceries = true;
        let hasGM019orGM020 = false;
        
        // Access the cart from the global scope
        const cartItems = window.cart || [];
        
        // Skip category check if cart is empty
        if (cartItems.length === 0) {
            // Default to non-groceries formula if cart is empty
            onlyGroceries = false;
        } else {
            // Check each item in cart
            for (let i = 0; i < cartItems.length; i++) {
                // Check if item is not GROCERIES
                if (cartItems[i].category !== 'GROCERIES') {
                    onlyGroceries = false;
                    break;
                }
                
                // Check if the item is GM-019 or GM-020
                // Look for both item_code and itemCode properties that might be used
                const itemCode = cartItems[i].item_code || cartItems[i].itemCode || '';
                if (itemCode === 'GM-019' || itemCode === 'GM-020') {
                    hasGM019orGM020 = true;
                }
            }
        }
          // Calculate wharfage based on parcel category
        let wharfage = 0;
        if (onlyGroceries || hasGM019orGM020) {
            wharfage = freight / 800 * 23; // For GROCERIES only or when contains GM-019/GM-020
        } else {
            wharfage = freight / 1200 * 23; // For other items
        }
        
        // Wharfage rules:
        // If both value and freight are zero, wharfage is zero (already handled above)
        if (freight === 0) {
            wharfage = 11.20;
        } else if (wharfage > 0 && wharfage < 11.20) {
            wharfage = 11.20;
        }
        
        // Format and set the wharfage value
        wharfageInput.value = formatCurrency(wharfage);
    }
    
    // Add event listeners to recalculate wharfage when freight or value changes
    if (freightInput) {
        freightInput.addEventListener("change", calculateWharfage);
        freightInput.addEventListener("input", calculateWharfage);
    }
    
    if (valueInput) {
        valueInput.addEventListener("change", calculateWharfage);
        valueInput.addEventListener("input", calculateWharfage);
    }
    
    // Add event listener for container number changes to check for reserved containers
    if (containerInput) {
        containerInput.addEventListener("change", checkContainerUsage);
        containerInput.addEventListener("blur", checkContainerUsage);
    }
    
    // Add event listeners for ship changes
    if (shipInput) {
        shipInput.addEventListener("change", function() {
            // When ship changes, we should recalculate the wharfage
            checkContainerUsage();
        });
    }
    
    // Calculate initial wharfage when the page loads
    if (freightInput && valueInput && wharfageInput) {
        // First check container usage, which will then trigger wharfage calculation
        if (containerInput && containerInput.value && shipInput && shipInput.value) {
            checkContainerUsage();
        } else {
            calculateWharfage();
        }
    }
      // Add event listener for cart updates
    document.addEventListener("cartUpdated", calculateWharfage);
    
    // Add event listener for voyage updates
    document.addEventListener("voyageUpdated", function(event) {
        if (event.detail) {
            currentVoyage = event.detail;
            checkContainerUsage();
        }
    });
});
