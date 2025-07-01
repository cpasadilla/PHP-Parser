# Delete BL History Feature with Restore Functionality

## Overview
A new tab has been added to the History page that tracks all deleted Bill of Lading (BL) orders with full restore capabilities. This provides an audit trail for order deletions and allows restoration of accidentally deleted orders.

## Features
- **Delete BL History Tab**: Shows all deleted orders with detailed information
- **Filter by User**: Filter deleted orders by who deleted them
- **Filter by Status**: Filter by deleted-only or restored-only orders
- **Restore Functionality**: Ability to restore deleted orders with all their data
- **Comprehensive Information**: Each deleted order record includes:
  - BL Number
  - Ship Name
  - Voyage Number
  - Shipper Name (from order data)
  - Consignee Name (from order data)
  - Total Amount (from order data)
  - Deleted By (user who deleted it)
  - Deleted At (timestamp)
  - Status (Deleted/Restored)
  - Action Button (Restore/View Restored)

## Implementation Details

### Database Changes
- **New Table**: `order_delete_logs` - stores deletion records
- **Migration**: `2025_06_18_144117_create_order_delete_logs_table.php`
- **Migration**: `2025_06_18_145047_add_restore_fields_to_order_delete_logs_table.php`
- **New Fields**:
  - `order_data` (JSON) - Complete order data for restore
  - `parcels_data` (JSON) - Associated parcels data for restore
  - `restored_at` (timestamp) - When order was restored
  - `restored_by` (string) - Who restored the order
  - `restored_order_id` (bigint) - New order ID after restore

### Model Changes
- **New Model**: `OrderDeleteLog` - handles delete log records
- **Updated**: `MasterListController@destroyOrder` - now logs deletions with complete data
- **New Method**: `MasterListController@restoreOrder` - handles order restoration

### View Changes
- **Updated**: `resources/views/history/index.blade.php` - added new tab with restore functionality
- **Updated**: `HistoryController@index` - added delete logs data with filtering

### Bug Fixes
- **Fixed Data Display**: Now correctly shows shipper name, consignee name, and total amount from order data
- **Improved Data Storage**: Stores complete order and parcels data for accurate restoration
- **Fixed Field Detection**: Enhanced logic to handle empty/null fields and trim whitespace
- **Robust Data Retrieval**: Added fallback logic to look up customer data if name fields are empty
- **Fixed Restore Error**: Resolved TypeError in restore functionality with proper null checking and error handling
- **Enhanced Error Handling**: Added comprehensive validation and error messages for restore operations

### Features
1. **Automatic Logging**: When an order is deleted, a complete record is automatically created in the delete log
2. **User Tracking**: Records which user performed the deletion and restoration
3. **Data Preservation**: Preserves complete order and parcels information
4. **Complete Restoration**: Restores orders with all associated parcels and data
5. **Filtering**: Users can filter by who performed the deletion and restoration status
6. **Pagination**: Results are paginated for better performance
7. **Status Tracking**: Shows whether orders are deleted or restored
8. **Audit Trail**: Complete audit trail of deletions and restorations

## Usage

### Viewing Delete History
1. Navigate to the History page
2. Click on the "Delete BL History" tab
3. Use the filter dropdowns to filter by specific users or status if needed
4. View the complete audit trail of deleted orders

### Restoring Deleted Orders
1. In the Delete BL History tab, find the order you want to restore
2. Click the "🔄 Restore" button for orders that haven't been restored yet
3. Confirm the restoration in the dialog
4. The system will create a new order with all the original data and parcels
5. The status will change to "✓ Restored" with timestamp and user information
6. A "👁️ View Restored" button will appear to view the restored order

## Security & Data Integrity
- Only users with appropriate permissions can access the history page and restore orders
- All deletions are automatically logged (cannot be bypassed)
- Deletion logs cannot be deleted (audit integrity)
- Complete order data is preserved for accurate restoration
- Restoration creates new orders (doesn't reuse old IDs for integrity)
- Restoration tracking prevents double-restoration

## Technical Notes
- Restored orders get new database IDs but maintain all original data
- Associated parcels are also restored with proper linking
- Original timestamps are preserved in the stored data
- Restoration timestamps track when the restore action occurred
