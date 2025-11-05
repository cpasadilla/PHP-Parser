# Gate Pass Implementation Documentation

## Overview
The Gate Pass feature has been successfully implemented in the SFX-1 system. This feature allows the tracking and management of cargo releases from containers, providing comprehensive documentation of what items have been released and what remains unreleased.

## Key Features

### 1. Gate Pass Creation
- Staff/Admin can create gate passes from the masterlist by clicking the "+ New" button in the GATE PASS column
- Each gate pass is associated with a specific BL# and Container#
- Gate Pass numbers are manually entered (not auto-generated) to match physical gate pass documents
- Multiple gate passes can be created for a single BL (partial releases)

### 2. Gate Pass Information
Each gate pass records:
- **Gate Pass Number**: Manually entered to match physical document
- **BL Number**: Automatically pulled from the order
- **Container Number**: Automatically pulled from the order
- **Shipper Name**: Automatically pulled from the order
- **Consignee Name**: Automatically pulled from the order
- **Release Date**: Date when items were released
- **Checker Name**: Name of the checker/staff who processed the release
- **Receiver Name**: Name of the person who received the items
- **Checker Notes**: Additional notes (e.g., vehicle plate number, special instructions)

### 3. Items Released
For each gate pass, you can specify:
- **Item Description**: Name of the item (e.g., "rice 25kg", "grocery")
- **Total Quantity**: Total quantity of this item in the BL
- **Unit**: Unit of measurement (sks, bxs, pcs, etc.)
- **Released Quantity**: Quantity being released in this gate pass
- **Remaining Quantity**: Automatically calculated (Total - Released)

### 4. Release Summary Report
- Accessible from both the masterlist and gate pass details
- Shows complete cargo status for a BL/Container combination
- Displays:
  - All items in the BL
  - Total quantities for each item
  - Total released quantities (across all gate passes)
  - Remaining unreleased quantities
  - Status indicators (Fully Released, Partially Released, Not Released)
- Lists all gate passes created for that BL with dates and items released

### 5. Visual Indicators
- **Masterlist Integration**: 
  - New "GATE PASS" column shows all gate pass numbers for each BL
  - Links to view individual gate passes
  - "Summary" link to view complete release status
  - "+ New" button to create additional gate passes
  
- **BL View Stamp**:
  - When a BL has gate passes, a red "RELEASED" stamp appears on the Bill of Lading
  - Shows the date of the first gate pass
  - Positioned at top-right of the BL for visibility

## Database Structure

### Tables Created

#### 1. `gate_passes` table
```
- id (primary key)
- gate_pass_no (unique, manually entered)
- order_id (foreign key to orders table - the BL)
- container_number
- shipper_name
- consignee_name
- checker_notes (text)
- checker_name
- checker_signature (for future signature implementation)
- receiver_name
- receiver_signature (for future signature implementation)
- release_date (date)
- created_by (foreign key to users table)
- created_by_name (name of creator)
- timestamps
```

#### 2. `gate_pass_items` table
```
- id (primary key)
- gate_pass_id (foreign key to gate_passes table)
- item_description
- total_quantity (decimal)
- unit (string)
- released_quantity (decimal)
- remaining_quantity (decimal, calculated)
- timestamps
```

## Routes

All gate pass routes are under the `/gatepass` prefix:

```
GET  /gatepass                          - List all gate passes
GET  /gatepass/create?order_id={id}     - Create new gate pass form
POST /gatepass                          - Store new gate pass
GET  /gatepass/{id}                     - View/Print gate pass
GET  /gatepass/{id}/edit                - Edit gate pass
PUT  /gatepass/{id}                     - Update gate pass
DELETE /gatepass/{id}                   - Delete gate pass
GET  /gatepass/summary/report?order_id={id} - View release summary
GET  /gatepass/api/release-summary/{orderId} - API endpoint for summary data
```

## Files Created/Modified

### New Files Created

#### Models:
- `app/Models/GatePass.php`
- `app/Models/GatePassItem.php`

#### Controller:
- `app/Http/Controllers/GatePassController.php`

#### Migrations:
- `database/migrations/2025_11_04_000000_create_gate_passes_table.php`
- `database/migrations/2025_11_04_000001_create_gate_pass_items_table.php`

#### Views:
- `resources/views/gatepass/index.blade.php` - List of all gate passes
- `resources/views/gatepass/create.blade.php` - Create new gate pass
- `resources/views/gatepass/edit.blade.php` - Edit existing gate pass
- `resources/views/gatepass/show.blade.php` - View/Print gate pass
- `resources/views/gatepass/summary.blade.php` - Release summary report

### Files Modified

#### Models:
- `app/Models/Order.php` - Added gatePasses relationship and helper methods

#### Controllers:
- `app/Http/Controllers/MasterListController.php` - Added gate passes eager loading in list() and viewBl() methods

#### Routes:
- `routes/web.php` - Added gate pass routes and GatePassController import

#### Views:
- `resources/views/masterlist/list.blade.php` - Added GATE PASS column with links
- `resources/views/masterlist/view-bl.blade.php` - Added RELEASED stamp when gate passes exist

## Usage Workflow

### Creating a Gate Pass

1. **From Masterlist**:
   - Navigate to the masterlist
   - Find the BL for which you want to create a gate pass
   - Click the "+ New" button in the GATE PASS column
   - This redirects to the gate pass creation form with the BL information pre-loaded

2. **Fill in Gate Pass Details**:
   - Enter the Gate Pass Number (must be unique)
   - Set the Release Date
   - Enter Checker Name and Receiver Name
   - Add any notes in the Checker Notes field (e.g., vehicle plate number)

3. **Specify Items Released**:
   - Items from the BL's parcels are automatically loaded
   - For each item, enter the quantity being released
   - The system automatically calculates remaining quantity
   - You can add additional items using the "+ Add Item" button
   - Remove items with the "Remove" button if needed

4. **Submit**:
   - Click "Create Gate Pass"
   - You'll be redirected to the printable gate pass view

### Viewing Gate Passes

1. **From Masterlist**:
   - Gate pass numbers are displayed in the GATE PASS column
   - Click any gate pass number to view/print it
   - Click "Summary" to see the complete release status

2. **From Gate Pass List**:
   - Navigate to `/gatepass` to see all gate passes
   - Use filters to search by gate pass number, BL number, or container number
   - Click "View" to see a specific gate pass
   - Click "Summary" to see the release status for that BL

### Editing/Deleting Gate Passes

1. **Edit**:
   - From the gate pass view, click "EDIT"
   - Modify any information (gate pass number, dates, checker/receiver names, items)
   - Click "Update Gate Pass"

2. **Delete**:
   - From the edit page, click "Delete Gate Pass"
   - Confirm the deletion
   - The gate pass and all its items will be permanently removed

### Viewing Release Summary

1. **Access Summary**:
   - From masterlist: Click "Summary" link in the GATE PASS column
   - From gate pass view: Click "VIEW SUMMARY" button
   - From gate pass list: Click "Summary" button for any gate pass

2. **Summary Report Shows**:
   - BL and Container information
   - Complete item breakdown with total, released, and remaining quantities
   - Status for each item (Fully Released, Partially Released, Not Released)
   - History of all gate passes for that BL
   - Overall status banner indicating if all cargo is released

## Security & Permissions

- Gate pass creation/editing respects the same permission structure as the masterlist
- Only users with edit permissions on the masterlist can create/edit gate passes
- The system tracks who created each gate pass (created_by and created_by_name)
- This information is stored but not displayed in the masterlist (similar to OR/AR tracking)

## Printing

All gate pass views (show and summary) are print-optimized:
- Clean layout suitable for physical documents
- Header with company logo and information
- Professional formatting
- Print button included for easy printing
- Reminder to disable headers/footers in print settings for clean output

## Example Scenario

**Scenario**: Shipper has BL#005 with 1200 sks of rice 25kg and 100 bxs of grocery in Container SFX1-005. Consignee makes partial releases.

### First Release:
1. Create Gate Pass #001
2. Released: 1000 sks rice 25kg, 20 bxs grocery
3. Remaining: 200 sks rice 25kg, 80 bxs grocery
4. BL now shows "RELEASED" stamp with Gate Pass #001 link

### Second Release:
1. Create Gate Pass #002
2. Released: 200 sks rice 25kg, 50 bxs grocery
3. Remaining: 0 sks rice 25kg, 30 bxs grocery
4. BL shows both Gate Pass #001 and #002 links

### Summary Report Shows:
- Rice 25kg: Total 1200, Released 1200, Remaining 0 (Fully Released)
- Grocery: Total 100, Released 70, Remaining 30 (Partially Released)
- Overall status: Cargo Partially Released - Items Remaining

## Future Enhancements (Optional)

1. **Digital Signatures**: Currently, signature fields are placeholders. Can be enhanced to support digital signature capture
2. **Automated Notifications**: Send notifications when all items are fully released
3. **QR Codes**: Generate QR codes on gate passes for quick scanning
4. **Mobile Interface**: Optimize for mobile devices for on-site gate pass creation
5. **Batch Gate Pass Creation**: Create multiple gate passes at once
6. **Export Functionality**: Export gate pass list and summaries to Excel/PDF

## Troubleshooting

### Issue: Gate pass not showing in masterlist
- **Solution**: Ensure the browser cache is cleared and page is refreshed. The masterlist should automatically load gate passes with the orders.

### Issue: Cannot create gate pass
- **Solution**: Check that:
  - User has edit permissions on masterlist
  - Gate pass number is unique (not already used)
  - All required fields are filled
  - Released quantity doesn't exceed total quantity

### Issue: RELEASED stamp not showing on BL
- **Solution**: The stamp only appears when at least one gate pass exists for the BL. Refresh the page after creating a gate pass.

## Support & Maintenance

- Database migrations are already run and tables are created
- All models have proper relationships defined
- Routes are registered and protected with authentication middleware
- Views follow the same design patterns as existing pages in the system

For any issues or questions, refer to the code comments in:
- `app/Http/Controllers/GatePassController.php` - Main business logic
- `app/Models/GatePass.php` - Data model definitions
- `resources/views/gatepass/*.blade.php` - View implementations
