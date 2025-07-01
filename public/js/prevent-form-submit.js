// Add this script to prevent form submission when pressing Enter in customer selection fields
document.addEventListener("DOMContentLoaded", function() {
    // Target the main form
    const form = document.getElementById("myForm");

    // Add event listener to the form to prevent default submission on Enter
    form.addEventListener("keydown", function(event) {
        // Check if the Enter key was pressed
        if (event.key === "Enter") {
            // Get the active element (the field being interacted with)
            const activeElement = document.activeElement;
            
            // If the active element is an input field (but not a submit button)
            if (activeElement.tagName === "INPUT" && 
                activeElement.type !== "submit" && 
                activeElement.type !== "button") {
                
                // Prevent default form submission
                event.preventDefault();
                
                // If there's a datalist associated with this input, select the first option
                if (activeElement.hasAttribute("list")) {
                    const listId = activeElement.getAttribute("list");
                    const datalist = document.getElementById(listId);
                    
                    if (datalist && datalist.options.length > 0) {
                        // Get options that match the current input value
                        const value = activeElement.value.toLowerCase();
                        const matchingOptions = Array.from(datalist.options)
                            .filter(option => option.value.toLowerCase().includes(value));
                            
                        // If there are matching options, select the first one
                        if (matchingOptions.length > 0) {
                            activeElement.value = matchingOptions[0].value;
                            // Trigger change event to update related fields
                            activeElement.dispatchEvent(new Event('change'));
                        }
                    }
                }
            }
        }
    });

    // Handle the submit button specifically
    const submitButton = document.getElementById("submitOrder");
    if (submitButton) {
        // Only allow form submission when the submit button is clicked
        submitButton.addEventListener("click", function(event) {
            // This is the only legitimate way to submit the form
            // We don't need to do anything special here, as this is allowed
        });
    }
});
