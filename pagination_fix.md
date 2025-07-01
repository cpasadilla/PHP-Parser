# Division by Zero Error Fix

## Issue
The parcel management page was throwing a `DivisionByZeroError` when using the "All" pagination option, specifically when there were no results to display.

## Root Cause
The error occurred in the `LengthAwarePaginator` constructor when we passed `$parcels->count()` as the `perPage` parameter. When the collection was empty, this became 0, causing Laravel's pagination logic to attempt division by zero.

## Fix Applied

### 1. Added Import Statement
```php
use Illuminate\Pagination\LengthAwarePaginator;
```

### 2. Updated Pagination Logic
```php
// Before (problematic)
$parcels = new \Illuminate\Pagination\LengthAwarePaginator(
    $parcels,
    $parcels->count(),
    $parcels->count(), // This could be 0, causing division by zero
    1,
    [...]
);

// After (fixed)
$count = $parcels->count();
$parcels = new LengthAwarePaginator(
    $parcels,
    $count,
    max(1, $count), // Prevent division by zero - minimum value is 1
    1,
    [...]
);
```

## Testing Scenarios

✅ **Empty Results + "All" Option**: No longer throws division by zero error
✅ **Empty Results + Regular Pagination**: Works correctly  
✅ **Results + "All" Option**: Displays all items correctly
✅ **Results + Regular Pagination**: Works as expected

## Technical Details

The `max(1, $count)` ensures that:
- If `$count` is 0 (no results), we use 1 as the perPage value
- If `$count` is greater than 0, we use the actual count
- This prevents division by zero while maintaining proper pagination behavior

The fix maintains the original functionality while handling the edge case of empty result sets safely.
