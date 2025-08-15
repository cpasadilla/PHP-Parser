# Dual Voyage Route Matching Fix

## Problem Identified
When dual voyages are open (e.g., Voyage 38 BATANES→MANILA and Voyage 39 MANILA→BATANES), the system was not correctly matching orders to the appropriate voyage based on origin-destination route.

**Example Issues:**
- Order: Origin=BATANES, Destination=MANILA → Should use Voyage 38 (BATANES→MANILA)
- Order: Origin=BASCO, Destination=ITBAYAT → Should use Voyage 36 (BASCO→ITBAYAT)

## Root Cause
The original voyage selection logic only considered ship type and origin, but didn't evaluate the complete route (origin → destination) to determine the correct directional voyage.

## Solution Implemented

### 1. Enhanced API Endpoint
- **Updated**: `getAvailableVoyagesApi()` to accept both origin and destination parameters
- **Added**: Route-aware voyage matching logic
- **Result**: API now returns voyages with route compatibility indicators

### 2. Improved Route Matching Logic
- **New Method**: `getAvailableVoyagesWithRoute()` - considers full origin→destination route
- **New Method**: `doesVoyageMatchRoute()` - validates voyage direction against route
- **Logic**: 
  - FROM MANILA → Uses OUT voyages
  - TO MANILA → Uses IN voyages
  - Auto-highlights matching routes in dropdown

### 3. Frontend Enhancements
- **Updated**: Both BL and Order forms to send origin + destination to API
- **Added**: Real-time voyage list updates when origin/destination changes
- **Added**: Visual highlighting of route-matching voyages (bold green text)
- **Added**: Auto-selection of matching route voyages

### 4. Backend Order Processing
- **Updated**: `pushOrder()` method to use specific selected voyage number
- **Enhanced**: Voyage selection priority: specific selection > route match > automatic

## Technical Implementation

### API Request Format
```
GET /api/available-voyages/{shipNum}?origin=BATANES&destination=MANILA
```

### API Response Format
```json
{
    "success": true,
    "voyages": [
        {
            "voyage_number": "38-IN",
            "label": "Voyage 38-IN (TO MANILA) - Primary",
            "matches_route": true,
            "route_priority": 1
        },
        {
            "voyage_number": "39-OUT", 
            "label": "Voyage 39-OUT (FROM MANILA) [Different Route]",
            "matches_route": false,
            "route_priority": 2
        }
    ]
}
```

### Route Matching Logic
For Ships I & II:
- **Origin=BATANES, Destination=MANILA** → Matches IN voyages (coming TO Manila)
- **Origin=MANILA, Destination=BATANES** → Matches OUT voyages (going FROM Manila)

## User Experience Improvements

### Before Fix
1. Select ship → System automatically picks voyage
2. No consideration of actual route
3. Wrong voyage assignment for dual voyage scenarios

### After Fix
1. Select ship → Shows all available voyages
2. Select origin + destination → Highlights correct route voyage
3. System auto-selects matching route if available
4. Visual feedback for route compatibility
5. Manual override still possible

## Testing Scenarios

### Test Case 1: BATANES → MANILA
- **Setup**: Dual voyages 38-IN and 39-OUT both READY
- **Input**: Origin=BATANES, Destination=MANILA
- **Expected**: Voyage 38-IN highlighted and auto-selected
- **Result**: ✅ Correctly routes to Voyage 38

### Test Case 2: BASCO → ITBAYAT  
- **Setup**: Dual voyages 36-OUT and 37-IN both READY
- **Input**: Origin=BASCO, Destination=ITBAYAT
- **Expected**: Voyage 36-OUT highlighted and auto-selected
- **Result**: ✅ Correctly routes to Voyage 36

## Files Modified
- `app/Http/Controllers/CustomerController.php` - Enhanced route matching logic
- `resources/views/customer/bl.blade.php` - Frontend voyage selection
- `resources/views/customer/order.blade.php` - Frontend voyage selection

## Debug Features Added
- Logging of route matching decisions
- Visual indicators for matching/non-matching routes
- API response includes route compatibility flags

This fix ensures that dual voyages are properly matched to their intended routes, solving the original issue where orders were being assigned to wrong voyages despite correct origin-destination selection.
