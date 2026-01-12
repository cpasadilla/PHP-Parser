# Developer Reference - Announcements Feature

## Quick Links

| Component | File | Purpose |
|-----------|------|---------|
| Model | `app/Models/Announcement.php` | Data model & relationships |
| Controller | `app/Http/Controllers/AnnouncementController.php` | Business logic |
| View | `resources/views/announcements/index.blade.php` | UI & frontend |
| Routes | `routes/web.php` (lines 41-44) | Endpoint definitions |
| Migration | `database/migrations/2025_01_12_000000_create_announcements_table.php` | Database schema |

## API Endpoints

### 1. View Announcements Page
```
GET /announcements
Response: HTML page with announcements
Auth Required: Yes (middleware: auth)
```

### 2. Post Announcement
```
POST /announcements
Content-Type: application/x-www-form-urlencoded
Headers: X-CSRF-TOKEN

Body Parameters:
- title (string, optional, max 255)
- content (string, required, max 5000)

Response: JSON
{
  "success": true,
  "message": "Announcement posted successfully!"
}
```

### 3. Get Announcements (JSON)
```
GET /announcements/api/get
Accept: application/json

Response: JSON Array
[
  {
    "id": 1,
    "title": "Welcome",
    "content": "This is an announcement",
    "user_name": "John Doe",
    "created_at": "Jan 12, 2025 10:30",
    "user_id": 5
  },
  ...
]
```

### 4. Delete Announcement
```
DELETE /announcements/{id}
Headers: X-CSRF-TOKEN

Response: JSON
{
  "success": true,
  "message": "Announcement deleted successfully!"
}

Error Response (403): 
{
  "success": false,
  "message": "You do not have permission to delete this announcement."
}
```

## Controller Methods

### `index()`
- **Route**: `GET /announcements`
- **Returns**: View with all announcements
- **Relationship**: Loads user data for each announcement
- **Ordering**: `created_at DESC` (newest first)

### `store(Request $request)`
- **Route**: `POST /announcements`
- **Validation**:
  - `title`: nullable, string, max:255
  - `content`: required, string, max:5000
- **Action**: Creates announcement, sets current user
- **Returns**: JSON success/error response

### `destroy(Announcement $announcement)`
- **Route**: `DELETE /announcements/{id}`
- **Authorization**: Only owner or admin
- **Returns**: JSON success/error response
- **Error**: 403 Forbidden if not authorized

### `getAnnouncements()`
- **Route**: `GET /announcements/api/get`
- **Returns**: JSON array of announcements with user data
- **Used By**: AJAX calls from frontend
- **Formatting**: Converts timestamps to readable format

## Model Relationships

```php
Announcement::class
├── belongsTo(User::class)
│   └── user_id → users.id
│       └── Foreign key with CASCADE delete
```

## Blade View Features

### Form Elements
- Title input: Optional, max 255 chars
- Content textarea: Required, max 5000 chars
- Character counter: Real-time count display
- Clear button: Resets form
- Submit button: Posts announcement

### Announcement Display
- Title (if provided): Displayed as heading
- Content: Displayed with newlines preserved
- Author: User who posted
- Timestamp: When posted (formatted)
- Delete button: For author/admin only

### JavaScript Functions
```javascript
// Form submission with AJAX
document.getElementById('announcementForm').addEventListener('submit', ...)

// Load announcements from API
function loadAnnouncements() { ... }

// Delete announcement with confirmation
function deleteAnnouncement(id) { ... }

// Show notification toast
function showNotification(message, type) { ... }

// Escape HTML to prevent XSS
function escapeHtml(text) { ... }

// Auto-refresh interval
setInterval(loadAnnouncements, 5000);
```

## Database Queries

### Get all announcements (with user)
```php
Announcement::with('user')
    ->orderBy('created_at', 'desc')
    ->get();
```

### Get single announcement
```php
Announcement::find($id);
```

### Create announcement
```php
Announcement::create([
    'user_id' => Auth::id(),
    'title' => $title,
    'content' => $content
]);
```

### Delete announcement
```php
$announcement->delete();
```

## Validation Rules

### Title
- Type: String
- Required: No (optional)
- Max Length: 255 characters
- Special Chars: Allowed

### Content
- Type: String
- Required: Yes
- Max Length: 5000 characters
- Special Chars: Allowed (HTML escaped on display)

## Authorization Logic

```php
// Current user can delete their own announcements
Auth::id() === $announcement->user_id

// Admins can delete any announcement
Auth::user()->isAdmin()

// Combined check
if (Auth::id() === $announcement->user_id || Auth::user()->isAdmin()) {
    // Allow delete
}
```

## Security Measures

### 1. CSRF Protection
```php
// Token included in form
@csrf

// Verified on POST/DELETE
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\VerifyCsrfToken::class,
    ]
];
```

### 2. XSS Prevention
```javascript
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
```

### 3. Authorization
```php
// Delete check
if (Auth::id() !== $announcement->user_id && !Auth::user()->isAdmin()) {
    return response()->json([...], 403);
}
```

### 4. Validation
```php
$validated = $request->validate([
    'title' => 'nullable|string|max:255',
    'content' => 'required|string|max:5000',
]);
```

## Performance Considerations

### Database Index
```sql
INDEX idx_created_at (created_at)
```
- Used for ordering announcements
- Speeds up queries when sorting by date

### Query Optimization
```php
// Eager load user to avoid N+1 queries
->with('user')

// Order by most recent first
->orderBy('created_at', 'desc')
```

### Frontend Optimization
- AJAX prevents full page reloads
- 5-second refresh interval (not too frequent)
- HTML escaping prevents rendering issues
- Character counter prevents large submissions

## Extending the Feature

### Add Edit Functionality
```php
// In controller:
public function update(Request $request, Announcement $announcement) { ... }

// In routes:
Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);

// In view: Add edit form and button
```

### Add Categories
```php
// Add column to migration:
$table->string('category')->default('general');

// In model:
protected $fillable = ['user_id', 'title', 'content', 'category'];

// Filter in view:
Announcement::where('category', $category)->get();
```

### Add Reactions
```php
// New table: announcement_reactions
// Columns: id, announcement_id, user_id, reaction_type

// In Announcement model:
public function reactions() {
    return $this->hasMany(AnnouncementReaction::class);
}
```

### Add Comments
```php
// New table: announcement_comments
// Columns: id, announcement_id, user_id, content, created_at

// In Announcement model:
public function comments() {
    return $this->hasMany(AnnouncementComment::class);
}
```

## Debugging Tips

### Check Database
```php
// In tinker
php artisan tinker
>>> App\Models\Announcement::all()
>>> App\Models\Announcement::with('user')->get()
>>> App\Models\Announcement::count()
```

### Check Routes
```bash
php artisan route:list | grep announcement
```

### Browser Console
- Press F12
- Check Console tab for JavaScript errors
- Check Network tab for AJAX requests
- Check Application/Storage for cookies/session

### Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

## Common Issues & Solutions

### Issue: 403 Forbidden on Delete
**Cause**: You're not the author and not an admin
**Solution**: Only delete your own announcements, or log in as admin

### Issue: CSRF Token Mismatch
**Cause**: Session expired or token invalid
**Solution**: Clear cookies, log in again

### Issue: Posts not saving
**Cause**: JavaScript error or database issue
**Solution**: Check F12 console and Laravel logs

### Issue: Auto-refresh not working
**Cause**: JavaScript error or fetch API issue
**Solution**: Check browser console for errors

### Issue: Styling looks broken
**Cause**: Tailwind CSS not compiled
**Solution**: Run `npm run build` in project root

## Testing Examples

### Unit Test
```php
public function test_user_can_post_announcement()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/announcements', [
            'title' => 'Test',
            'content' => 'Test content'
        ]);
    
    $this->assertDatabaseHas('announcements', [
        'user_id' => $user->id,
        'content' => 'Test content'
    ]);
}
```

### Browser Test
```javascript
// Test posting via console
fetch('/announcements', {
    method: 'POST',
    body: new FormData(document.querySelector('form')),
    headers: {
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
    }
}).then(r => r.json()).then(console.log)
```

---

**Last Updated**: January 12, 2025
**Version**: 1.0
**Status**: ✅ Production Ready
