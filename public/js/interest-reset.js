// Clear localStorage entries for interest activation
function clearInterestActivation() {
    // Get all localStorage keys
    const keys = [];
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('interest_start_')) {
            keys.push(key);
        }
    }

    // Remove all interest activation entries
    keys.forEach(key => {
        localStorage.removeItem(key);
    });

    alert('Interest activation status has been cleared. The button should now be visible.');
    
    // Refresh the page to show the interest button
    location.reload();
}
