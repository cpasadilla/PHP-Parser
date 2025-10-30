# M/V Saver Star Implementation Summary

## Overview
Successfully implemented a separate ship management system for M/V Saver Star with its own masterlist and BL (Bill of Lading) creation functionality.

## Key Changes

### 1. Database
- **Migration**: Created `saver_star_ships` table with the following columns:
  - `id`: Primary key
  - `name`: Ship name (default: 'M/V Saver Star')
  - `status`: Ship status (READY, CREATE BL, STOP BL)
  - `created_at`, `updated_at`: Timestamps

### 2. Model
- **File**: `app/Models/SaverStarShip.php`
- Simple Eloquent model for managing Saver Star ships

### 3. Controller
- **File**: `app/Http/Controllers/SaverStarController.php`
- **Methods**:
  - `index()`: Display list of Saver Star ships
  - `store()`: Add new Saver Star ship
  - `update()`: Update ship status
  - `destroy()`: Delete ship
  - `showBlForm()`: Display BL creation form

### 4. Routes
- **File**: `routes/web.php`
- Added Saver Star route group with permissions:
  ```php
  Route::middleware('page.permission:saverstar')->group(function () {
      // Ships management
      Route::get('/saverstar', [SaverStarController::class, 'index'])
      Route::post('/saverstar/ships', [SaverStarController::class, 'store'])
      Route::put('/saverstar/ships/{id}', [SaverStarController::class, 'update'])
      Route::delete('/saverstar/ships/{id}', [SaverStarController::class, 'destroy'])
      
      // BL creation
      Route::get('/saverstar/bl/{id}', [SaverStarController::class, 'showBlForm'])
  ```

### 5. Views

#### Masterlist View
- **File**: `resources/views/saverstar/index.blade.php`
- Displays Saver Star ship list with:
  - Ship name column
  - Status dropdown (READY, CREATE BL, STOP BL)
  - Delete button (with permissions)
  - Link to BL creation form

#### BL Creation View
- **File**: `resources/views/saverstar/bl.blade.php`
- **Key Differences from Everwin Star BL**:
  1. **Logo**: Changed from `logo-sfx.png` to `logo-saver.jpg`
  2. **Color Theme**: Changed from `#78BF65` (green) to `#1c89ba` (blue)
  3. **Ship Display**: Shows "M/V SAVER STAR" instead of "M/V EVERWIN STAR"
  4. **Ship Selection**: Removed ship dropdown; ship is hardcoded as "SAVER"
  5. **Voyage Number**: Changed from dropdown to manual text input field
     - Users can type any voyage number
     - No automatic voyage generation
     - Required field with placeholder "Enter Voyage"

### 6. Navigation
- **File**: `resources/views/components/sidebar/content.blade.php`
- Added new sidebar dropdown menu:
  - Title: "M/V Saver Star"
  - Sublinks:
    - "Ship Status" → Manage the single ship's status
    - "Create BL" → Direct link to BL creation form
  - Permission-based visibility

## Key Design Decisions

### Single Ship System
- **No multiple ships**: Unlike Everwin Star, Saver Star has only ONE ship entry
- **No ship number**: Saver Star doesn't use numbered ships (I, II, III)
- **Simplified management**: Single status control for the entire Saver Star operations

### Direct BL Creation
- **Quick access**: "Create BL" link in sidebar goes directly to BL form
- **Automatic ship selection**: System automatically uses the single Saver Star ship
- **Status validation**: Only allows BL creation when ship status is "CREATE BL"

## Usage

### M/V Saver Star - Single Ship System
Unlike M/V Everwin Star which has multiple ships (I, II, III, etc.), **M/V Saver Star has only ONE ship**. The system is simplified to manage just this single ship.

### Managing Ship Status
1. Navigate to sidebar → "M/V Saver Star" → "Ship Status"
2. View the single M/V Saver Star ship entry
3. Change status between:
   - **READY**: Ship is ready but not accepting BL creation
   - **CREATE BL**: Users can create BL
   - **STOP BL**: BL creation is disabled

### Creating a BL for Saver Star
**Option 1: Direct from Sidebar (Recommended)**
1. Click sidebar → "M/V Saver Star" → "Create BL"
2. System automatically loads the BL form for the single Saver Star ship
3. Manually enter the **Voyage Number** (e.g., "2024-01", "V100", etc.)
4. Fill in remaining BL details
5. Submit the form

**Option 2: From Ship Status Page**
1. Navigate to "Ship Status"
2. Click the "Create BL" button (only enabled when status is "CREATE BL")
3. Follow steps 3-5 from Option 1

### Visual Differences
- **Logo**: Uses Saver Star branding (`logo-saver.jpg`)
- **Color**: Blue theme (#1c89ba) instead of green
- **Ship Display**: Shows "M/V SAVER STAR" prominently
- **Voyage Input**: Text field instead of dropdown selector

## Permissions Required
To access Saver Star features, users need:
- **Page Permission**: `saverstar`
- **Subpage Permissions**:
  - `saverstar.ships` - View and manage ships
  - `saverstar.ships.create` - Add new ships
  - `saverstar.ships.edit` - Edit ship status
  - `saverstar.ships.delete` - Delete ships
  - `saverstar.bl` - Create BL

## Files Created/Modified

### Created:
- `database/migrations/2025_10_30_115248_create_saver_star_ships_table.php`
- `app/Models/SaverStarShip.php`
- `app/Http/Controllers/SaverStarController.php`
- `resources/views/saverstar/index.blade.php`
- `resources/views/saverstar/bl.blade.php`
- `create_sample_saver_star.php` (utility script)

### Modified:
- `routes/web.php` - Added Saver Star routes
- `resources/views/components/sidebar/content.blade.php` - Added navigation menu

## Testing
- Sample ship "M/V Saver Star" created with status "CREATE BL"
- Logo file `public/images/logo-saver.jpg` verified to exist
- Routes configured with proper middleware and permissions

## Notes
- The BL form reuses the existing `pushOrder` route from CustomerController
- Voyage numbers are completely manual - no validation or auto-generation
- The ship number is internally stored as "SAVER" for database consistency
- All other BL features (customer selection, parcels, pricing, etc.) remain unchanged
