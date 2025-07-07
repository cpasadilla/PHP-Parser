# Export to Excel/PDF Update - MULTIPLE CHECKERS SUPPORT

## Summary of Changes

Updated the export functionality in `resources/views/masterlist/list.blade.php` and `app/Http/Controllers/MasterListController.php` to:

1. **Support multiple checkers per order** - properly display and filter orders with multiple checkers
2. **Fix export filtering** - ensure exports only include rows visible after filtering by specific checker
3. **Handle checker data correctly** - multiple checkers are stored as "CHECKER1 / CHECKER2 / CHECKER3"

## Issues Fixed

### 1. Multiple Checkers Not Displayed
- **Problem**: When creating a BL with multiple checkers, only the first checker was shown in filters
- **Root Cause**: The controller was treating `checkName` as a single value instead of a delimited string
- **Solution**: Updated controller to split multiple checkers by `' / '` separator and create individual filter options

### 2. Export Includes All Rows Instead of Filtered Rows
- **Problem**: Export functions included all rows regardless of applied filters
- **Root Cause**: Export functions cloned entire table instead of only visible rows
- **Solution**: Modified both Excel and PDF export functions to only export visible rows

### 3. Checker Filter Not Working with Multiple Checkers
- **Problem**: When filtering by a specific checker, rows with that checker + other checkers were not shown
- **Root Cause**: JavaScript filter logic used simple string matching instead of checking individual checkers
- **Solution**: Added special handling for checker column to split and check individual checkers

## Changes Made

### 1. MasterListController.php Updates

**voyageOrdersById() method** (around line 935):
```php
// Process checkers - split multiple checkers separated by ' / '
$allCheckers = $allOrders->pluck('checkName')
    ->filter()
    ->flatMap(function($checkName) {
        return collect(explode(' / ', $checkName))
            ->map(function($checker) {
                return trim($checker);
            })
            ->filter();
    })
    ->unique()
    ->sort()
    ->values();

$filterData = [
    // ... other filters ...
    'uniqueCheckers' => $allCheckers,  // Now contains individual checker names
    // ... other filters ...
];
```

**voyageOrders() method** (around line 990):
- Applied the same multiple checker processing logic

### 2. list.blade.php Updates

**JavaScript Filter Logic** (around line 1015):
```javascript
} else if (column === 'checker') {
    // Special handling for CHECKER - check if the selected checker is in the list of checkers
    const cellValue = cell.querySelector('input') 
        ? cell.querySelector('input').value.trim().toLowerCase() 
        : cell.textContent.trim().toLowerCase();
    
    // Split multiple checkers by ' / ' and check if the filter value matches any of them
    const checkers = cellValue.split(' / ').map(checker => checker.trim().toLowerCase());
    const hasMatchingChecker = checkers.some(checker => checker === filterValue);
    
    if (!hasMatchingChecker) {
        isVisible = false;
    }
}
```

**Dropdown Selection Logic** (around line 269):
```php
@php
    // For single-select dropdown, select the first checker from the list
    $firstChecker = trim(explode(' / ', $order->checkName ?? '')[0] ?? '');
    $isSelected = $firstChecker === $checker;
@endphp
<option value="{{ $checker }}" {{ $isSelected ? 'selected' : '' }}>
    {{ $checker }}
</option>
```

**Excel Export Function** (around line 2610):
- Modified to only export visible rows: `const visibleRows = Array.from(originalRows).filter(row => { const style = window.getComputedStyle(row); return style.display !== 'none'; });`

**PDF Export Function** (around line 2690):
- Modified to only export visible rows with the same filtering logic

## How Multiple Checkers Work

### Database Storage
- Multiple checkers are stored in the `checkName` field as: `"CHECKER1 / CHECKER2 / CHECKER3"`
- This matches the format used in the BL creation form (`customer/bl.blade.php`)

### Filter Dropdown Display
- The controller splits multiple checkers and shows each checker as a separate option
- Example: If order has "ALDAY / ANCHETA / MORENO", all three names appear in the checker filter dropdown

### Table Display
- The table shows the full checker string: "ALDAY / ANCHETA / MORENO"
- Read-only view shows all checkers
- Edit dropdown shows the first checker selected (for single-select editing)

### Filtering Logic
- When filtering by "ALDAY", it shows all orders where ALDAY is one of the checkers
- Works with orders that have single checkers ("ALDAY") or multiple checkers ("ALDAY / ANCHETA")

### Export Behavior
- **Before filtering**: Exports all rows
- **After filtering by checker**: Only exports rows where the selected checker is one of the assigned checkers
- **After filtering by multiple fields**: Only exports rows that match all active filters

## Testing

### Test Multiple Checker Display
1. Navigate to Master List page
2. Look for orders with multiple checkers (displayed as "CHECKER1 / CHECKER2")
3. Verify the checker filter dropdown shows all individual checker names

### Test Checker Filtering
1. Select a specific checker from the dropdown (e.g., "ALDAY")
2. Verify it shows rows where ALDAY is one of the checkers
3. This should include both "ALDAY" and "ALDAY / ANCHETA / MORENO" orders

### Test Export with Checker Filter
1. Apply checker filter (e.g., select "ALDAY")
2. Click "Export to Excel" - should only export rows with ALDAY
3. Click "Export to PDF" - should only export rows with ALDAY
4. Check console logs for row count confirmation

## Expected Results

✅ **Multiple Checkers Displayed**: Orders with multiple checkers show all checker names  
✅ **Filter Shows All Checkers**: Dropdown contains individual checker names from all orders  
✅ **Filtering Works**: Selecting a checker shows all orders containing that checker  
✅ **Export Filtered Data**: Both Excel and PDF exports only include filtered rows  
✅ **Console Logging**: Shows total vs visible row counts during export  

## Files Modified

- `app/Http/Controllers/MasterListController.php` - Updated checker processing in both methods
- `resources/views/masterlist/list.blade.php` - Updated JavaScript filtering, dropdown logic, and export functions
