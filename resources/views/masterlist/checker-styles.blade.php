<style>
/* Custom styles for the multiple select checker dropdown */
#checkerName {
    overflow: auto;
    min-height: 20px;
    height: auto !important;
    cursor: pointer;
}

#checkerName option {
    padding: 2px;
}

/* Style for the checker select on printout */
@media print {
    #checkerName {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        border: none !important;
        overflow: visible;
        height: auto !important;
        min-height: 0 !important;
    }
    
    #checkerName option:not(:checked) {
        display: none;
    }
}
</style>
