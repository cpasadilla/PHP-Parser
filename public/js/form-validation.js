// Form validation function
function validateForm() {
    // Required fields to check
    const requiredFields = [
        { id: "ship_no", name: "Ship" },
        { id: "origin", name: "Origin" },
        { id: "destination", name: "Destination" },
        { id: "shipper_name", name: "Shipper Name" },
        { id: "consignee_name", name: "Consignee Name" }
    ];
    
    // Check if all required fields are filled
    for (const field of requiredFields) {
        const element = document.getElementById(field.id);
        if (!element || !element.value.trim()) {
            alert(`Please fill in the ${field.name} field before submitting.`);
            if (element) {
                element.focus();
            }
            return false;
        }
    }
    
    // Check if at least one item is in the cart
    if (cart.length === 0) {
        alert("Please add at least one item to the cart before submitting.");
        return false;
    }
    
    // Additional validation can be added here if needed
    
    return true;
}
