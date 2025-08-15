# Dual Voyage Implementation Plan - ✅ COMPLETE

## Overview
Implementation plan to allow 2 different voyages to be open simultaneously on the same ship for order/BL creation.

## Current System Analysis
- Currently, only one voyage per ship (per direction for Ships I & II) can be in "READY" status
- Order creation automatically selects the highest numbered "READY" voyage
- Ship status controls voyage creation but doesn't support multiple active voyages

## Implementation Strategy - ✅ ALL COMPLETED

### 1. Database Changes - ✅ COMPLETE
- ✅ Add `is_primary` boolean field to voyages table to distinguish primary vs secondary voyages
- ✅ Add `voyage_group` field to group related voyages together
- **Migration**: `database/migrations/2025_08_14_155052_add_dual_voyage_fields_to_voyages_table.php`

### 2. Controller Changes - ✅ COMPLETE
- ✅ Modify MasterListController::update() to handle "DUAL VOYAGE" status
- ✅ Update CustomerController voyage selection logic (shipOne, shipThree methods)
- ✅ Add voyage selection API endpoint (/api/available-voyages/{shipNum})
- ✅ Add voyage selection UI components

### 3. UI Changes - ✅ COMPLETE
- ✅ Add voyage selection dropdown in order creation forms (bl.blade.php, order.blade.php)
- ✅ Update masterlist voyage display to show multiple active voyages with primary/secondary badges
- ✅ Add controls to manage dual voyage mode in masterlist interface

### 4. Business Logic - ✅ COMPLETE
- ✅ Allow maximum 2 READY voyages per ship (per direction for Ships I & II)
- ✅ Implement voyage priority system (primary vs secondary)
- ✅ Add validation to prevent more than 2 active voyages
- ✅ Dynamic voyage selection in frontend based on available voyages

## Implementation Steps

### Step 1: Database Migration
```sql
ALTER TABLE voyages ADD COLUMN is_primary BOOLEAN DEFAULT TRUE;
ALTER TABLE voyages ADD COLUMN voyage_group VARCHAR(50) NULL;
```

### Step 2: Update Ship Status Options
Add "DUAL VOYAGE" status option alongside existing statuses:
- READY
- CREATE BL  
- STOP BL
- NEW VOYAGE
- **DUAL VOYAGE** (NEW)
- DRY DOCK
- NEW DOCK

### Step 3: Modify Voyage Creation Logic
When ship status is set to "DUAL VOYAGE":
- Create a second voyage with same number but different group
- Both voyages should be in READY status
- Mark one as primary, one as secondary

### Step 4: Update Order Creation Process
- Show voyage selection dropdown when multiple READY voyages exist
- Allow user to choose which voyage to assign the order to
- Default to primary voyage if no selection made

### Step 5: UI Components
- Voyage selection dropdown in order forms
- Visual indicators for primary/secondary voyages
- Management interface for dual voyage control

## Files to Modify

1. **Database Migration**
   - Create new migration file for voyages table

2. **Controllers**
   - `app/Http/Controllers/MasterListController.php`
   - `app/Http/Controllers/CustomerController.php`

3. **Views**
   - `resources/views/masterlist/index.blade.php` (ship status options)
   - `resources/views/customer/order.blade.php` (voyage selection)
   - `resources/views/customer/bl.blade.php` (voyage selection)
   - `resources/views/masterlist/voyage_new.blade.php` (display dual voyages)

4. **Models**
   - `app/Models/voyage.php` (add new fields)

## Expected Behavior

### Normal Mode (Current)
- Single voyage per ship in READY status
- Orders automatically assigned to that voyage

### Dual Voyage Mode (New)
- Two voyages per ship in READY status
- User can select which voyage to use for new orders
- Each voyage maintains independent BL numbering
- Both voyages can accept new orders simultaneously

## Benefits
- Increased flexibility for operations
- Better load distribution
- Parallel processing of different cargo types
- Enhanced scheduling capabilities
