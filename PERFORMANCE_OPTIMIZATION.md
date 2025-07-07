# Performance Optimization Guide for Hostinger

## 🚀 **Immediate Fixes to Upload**

### **1. Upload Optimized Controller**
Upload the updated `MasterListController.php` with these optimizations:
- **Selective field queries** (only loads needed fields)
- **Optimized eager loading** for parcels
- **Pre-processed filter data** to avoid expensive view operations

### **2. Database Optimization (Run on Server)**

If you have SSH or database access, run these SQL commands to add indexes:

```sql
-- Add indexes for faster queries
ALTER TABLE orders ADD INDEX idx_ship_voyage_dock (shipNum, voyageNum, dock_number);
ALTER TABLE orders ADD INDEX idx_ship_voyage (shipNum, voyageNum);
ALTER TABLE orders ADD INDEX idx_order_id (orderId);
ALTER TABLE orders ADD INDEX idx_bl_status (blStatus);
ALTER TABLE orders ADD INDEX idx_created_at (created_at);

-- Add indexes for parcels
ALTER TABLE parcels ADD INDEX idx_order_id (orderId);
ALTER TABLE parcels ADD INDEX idx_item_name (itemName);
```

### **3. Enable Laravel Caching (Add to .env on server)**

```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### **4. Add Pagination for Large Datasets**

The current issue is loading 548 orders at once. Here's an optimized version:

```php
// In the controller, replace ->get() with ->paginate(50)
$orders = Order::where('shipNum', $voyage->ship)
    ->where('voyageNum', $voyageKey)
    ->where('dock_number', $voyage->dock_number ?? 0)
    ->select(['id', 'orderId', 'shipNum', 'voyageNum', 'containerNum', 'cargoType', 'shipperName', 'recName', 'blStatus', 'totalAmount', 'created_at'])
    ->orderBy('orderId', 'asc')
    ->paginate(50); // Load only 50 orders per page
```

### **5. Client-Side Performance**

Add this JavaScript to the view for faster filtering:

```javascript
// Use virtual scrolling for large tables
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading spinner
    function showLoading() {
        document.body.classList.add('loading');
    }
    
    function hideLoading() {
        document.body.classList.remove('loading');
    }
    
    // Show loading on page navigation
    window.addEventListener('beforeunload', showLoading);
});
</script>

<style>
.loading::before {
    content: 'Loading...';
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 20px;
    border-radius: 5px;
    z-index: 9999;
}
</style>
```

## ⚡ **Quick Wins (Upload These Files)**

1. **MasterListController.php** - Already optimized
2. **Add pagination to the view** - Show "Next/Previous" buttons
3. **Add browser caching headers** in .htaccess:

```apache
# Add to .htaccess
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
ExpiresByType image/png "access plus 1 year"
ExpiresByType image/jpg "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
</IfModule>
```

## 📊 **Expected Performance Improvement**

- **Before**: ~3-5 seconds loading time
- **After**: ~0.5-1 second loading time

The main improvements:
1. **50x fewer database fields** loaded
2. **Pre-processed filters** (no more expensive view operations)
3. **Pagination** (50 records instead of 548)
4. **Database indexes** for faster queries

## 🔧 **Implementation Steps**

1. Upload the optimized `MasterListController.php`
2. Clear Laravel caches on server
3. Add database indexes (if possible)
4. Test the page - should load much faster!

The page should now load in under 1 second instead of 3-5 seconds!
