# Announcements Feature - Architecture & Flow Diagrams

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              Dashboard (dashboard.blade.php)             │  │
│  │  ┌────────────────────────────────────────────────────┐  │  │
│  │  │  Announcements - Freedom Wall Banner              │  │  │
│  │  │  [View All →]                                     │  │  │
│  │  └────────────────────────────────────────────────────┘  │  │
│  └──────────────────────────────────────────────────────────┘  │
│                           ↓                                      │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │        Announcements Page (announcements/index.blade.php)│  │
│  │                                                          │  │
│  │  ┌────────────────────────────────────────────────────┐ │  │
│  │  │ Post Form                                          │ │  │
│  │  │ ├─ Title (optional)                                │ │  │
│  │  │ ├─ Content (required, 5000 chars max)              │ │  │
│  │  │ └─ [Post Button]                                   │ │  │
│  │  └────────────────────────────────────────────────────┘ │  │
│  │                                                          │  │
│  │  ┌────────────────────────────────────────────────────┐ │  │
│  │  │ Announcements Feed                                 │ │  │
│  │  │ ├─ [Title]                                         │ │  │
│  │  │ ├─ by John Doe on Jan 12, 2025 10:30              │ │  │
│  │  │ ├─ Content text...                                │ │  │
│  │  │ └─ [Delete] (if author/admin)                     │ │  │
│  │  │                                                    │ │  │
│  │  │ (Auto-refreshes every 5 seconds)                  │ │  │
│  │  └────────────────────────────────────────────────────┘ │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
           ↓                                    ↓
      [AJAX POST]                        [AJAX DELETE]
           ↓                                    ↓
```

## Request/Response Flow

### Posting an Announcement

```
USER SUBMITS FORM
    ↓
JavaScript Event Listener
    ↓
Validate Form Data
    ↓
Create FormData Object
    ↓
Add CSRF Token
    ↓
AJAX POST to /announcements
    ↓
┌─────────────────────────────────────┐
│  AnnouncementController::store()     │
├─────────────────────────────────────┤
│  1. Validate input                  │
│     - title: max 255 chars          │
│     - content: required, max 5000   │
│  2. Add user_id from Auth           │
│  3. Create in database              │
│  4. Return JSON response            │
└─────────────────────────────────────┘
    ↓
Database (announcements table)
    ↓
Return Success JSON
    ↓
Frontend Notification
    ↓
Clear Form
    ↓
AJAX Reload Announcements
    ↓
Update Feed Display
```

### Displaying Announcements

```
Page Load or Auto-Refresh (5 seconds)
    ↓
JavaScript loadAnnouncements()
    ↓
AJAX GET /announcements/api/get
    ↓
┌─────────────────────────────────────┐
│ AnnouncementController::getAnnouncements() │
├─────────────────────────────────────┤
│  1. Query all announcements         │
│  2. Load user relationships         │
│  3. Order by created_at DESC        │
│  4. Format timestamps               │
│  5. Return JSON array               │
└─────────────────────────────────────┘
    ↓
Database Query
    ↓
Return JSON
[
  { id: 1, title: "...", content: "...", user_name: "...", ... },
  { id: 2, title: "...", content: "...", user_name: "...", ... }
]
    ↓
Frontend Processes JSON
    ↓
Escape HTML for XSS Prevention
    ↓
Build HTML from template
    ↓
Update DOM
    ↓
Announcement Feed Displays
```

### Deleting an Announcement

```
User Clicks Delete Button
    ↓
JavaScript deleteAnnouncement(id)
    ↓
Show Confirmation Dialog
    ↓
User Confirms
    ↓
AJAX DELETE /announcements/{id}
    ↓
┌─────────────────────────────────────┐
│ AnnouncementController::destroy()    │
├─────────────────────────────────────┤
│  1. Find announcement by ID         │
│  2. Check authorization             │
│     - Is owner? ✓ DELETE            │
│     - Is admin? ✓ DELETE            │
│     - Neither? ✗ 403 Forbidden      │
│  3. Delete from database            │
│  4. Return JSON response            │
└─────────────────────────────────────┘
    ↓
Database (DELETE query)
    ↓
Return Success JSON
    ↓
Frontend Notification
    ↓
AJAX Reload Announcements
    ↓
Update Feed Display
```

## Database Schema & Relationships

```
╔═══════════════════════════════════════╗
║           announcements               ║
╠═══════════════════════════════════════╣
║ PK  id          BIGINT UNSIGNED       ║
║ FK  user_id     BIGINT UNSIGNED ──┐   ║
║     title       VARCHAR(255)       │   ║
║     content     LONGTEXT           │   ║
║     created_at  TIMESTAMP          │   ║
║     updated_at  TIMESTAMP          │   ║
║ IX  created_at                     │   ║
╚═══════════════════════════════════════╝
                                        │
        ┌───────────────────────────────┘
        │
        ↓ ONE-TO-MANY
╔═══════════════════════════════════════╗
║            users                      ║
╠═══════════════════════════════════════╣
║ PK  id          BIGINT UNSIGNED       ║
║     name        VARCHAR(255)          ║
║     email       VARCHAR(255)          ║
║     password    VARCHAR(255)          ║
║     ...                               ║
╚═══════════════════════════════════════╝

RELATIONSHIPS:
- Announcement belongsTo User
- User hasMany Announcement
- Delete User → Delete all their Announcements (CASCADE)
```

## Code Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    ROUTES (web.php)                         │
└─────────────────────────────────────────────────────────────┘
        ↓
┌─────────────────────────────────────────────────────────────┐
│                CONTROLLER (AnnouncementController)           │
├─────────────────────────────────────────────────────────────┤
│  ├─ index()              → Display view                      │
│  ├─ store()              → Save announcement                 │
│  ├─ destroy()            → Delete announcement               │
│  └─ getAnnouncements()   → Return JSON for AJAX             │
└─────────────────────────────────────────────────────────────┘
        ↓
┌─────────────────────────────────────────────────────────────┐
│               MODEL (Announcement)                           │
├─────────────────────────────────────────────────────────────┤
│  ├─ $fillable = [...];   → Mass assignment rules             │
│  └─ user()               → Relationship to User              │
└─────────────────────────────────────────────────────────────┘
        ↓
┌─────────────────────────────────────────────────────────────┐
│              DATABASE (announcements table)                  │
├─────────────────────────────────────────────────────────────┤
│  ├─ Stores all announcements                                │
│  ├─ Indexed for fast queries                                │
│  └─ Cascades deletes                                        │
└─────────────────────────────────────────────────────────────┘
```

## Security Layer Diagram

```
REQUEST
    ↓
┌─────────────────────────────────────────────────────────────┐
│               MIDDLEWARE (auth)                              │
├─────────────────────────────────────────────────────────────┤
│  ✓ User must be logged in                                   │
│  ✗ Redirect to login if not authenticated                   │
└─────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────┐
│           CSRF PROTECTION (VerifyCsrfToken)                 │
├─────────────────────────────────────────────────────────────┤
│  ✓ Check X-CSRF-TOKEN header                                │
│  ✗ Reject request if token mismatch                         │
└─────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────┐
│            VALIDATION (Request::validate)                   │
├─────────────────────────────────────────────────────────────┤
│  ✓ title: max 255 chars, optional                           │
│  ✓ content: required, max 5000 chars                        │
│  ✗ Return 422 if validation fails                           │
└─────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────┐
│          AUTHORIZATION (isAdmin / ownership check)          │
├─────────────────────────────────────────────────────────────┤
│  ✓ For delete: Check user is owner or admin                 │
│  ✗ Return 403 if not authorized                             │
└─────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────┐
│           DATABASE (Eloquent ORM)                            │
├─────────────────────────────────────────────────────────────┤
│  ✓ Parameterized queries prevent SQL injection              │
│  ✓ Models handle escaping                                   │
└─────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────┐
│          FRONTEND XSS PROTECTION (escapeHtml)               │
├─────────────────────────────────────────────────────────────┤
│  ✓ Escape all user content before HTML insertion            │
│  ✓ Prevent malicious script execution                       │
└─────────────────────────────────────────────────────────────┘
    ↓
RESPONSE (Safe & Secure)
```

## State Management Flow

```
USER ACTION                 STATE CHANGE            UI UPDATE
─────────────              ─────────────            ─────────
User visits page    →      Load all announcements   Display feed
                    →      Start 5s timer           Auto-refresh

User posts          →      Create announcement    Clear form
                    →      Add to top of list      Show new post
                    →      Show notification      "Posted!"

Other user posts    →      5s timer fires          Load new posts
                    →      Fetch from API          Display newest

User deletes        →      Remove from DB         Remove from list
                    →      Update feed            Show notification
```

## Component Interaction Diagram

```
┌──────────────────────────────────────────────────────────────┐
│                    DASHBOARD                                 │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ Announcements Banner (integration point)               │ │
│  │ [View All] ─────┐                                      │ │
│  └────────────────┼───────────────────────────────────────┘ │
└──────────────────────┼──────────────────────────────────────┘
                       │
                       ↓
            ┌──────────────────────────┐
            │  /announcements          │
            │  (Full page view)        │
            └──────────────────────────┘
                       │
           ┌───────────┴───────────┐
           ↓                       ↓
    ┌─────────────────┐    ┌──────────────────┐
    │  Post Form      │    │  Announcements   │
    │                 │    │  Feed            │
    │ ├─ Title        │    │                  │
    │ ├─ Content      │    │ ├─ Display items │
    │ └─ [Post]       │    │ ├─ Delete btns   │
    └─────────────────┘    │ └─ Auto-refresh  │
           │               └──────────────────┘
           │ AJAX POST              ↑
           │                        │
           ├─ /announcements        │ AJAX GET
           │                        │
           └────→ DATABASE ←─ /announcements/api/get
```

## Lifecycle Diagram

```
ANNOUNCEMENT LIFECYCLE
──────────────────────

[NEW ANNOUNCEMENT]
        ↓
    Created by User
        ↓
    Stored in Database
        ↓
    Loaded in Feed (every 5s)
        ↓
    Displayed in UI
        ↓
    ┌─────────────────────┐
    │ TWO POSSIBLE ENDS   │
    └─────────────────────┘
    │                     │
    ↓                     ↓
[DELETED]            [INDEFINITE]
User clicks        Never deleted,
Delete             persists forever
    ↓
Confirmation
    ↓
Removed from DB
    ↓
Feed refreshes
    ↓
Announcement Gone
```

## Browser-to-Server Communication

```
BROWSER                           SERVER
───────────────────────────────────────────

[User Form]
    │
    ├─ JavaScript captures submit
    │
    ├─ Prevents default
    │
    ├─ Creates FormData
    │
    ├─ Adds CSRF token
    │
    ├─ Fetch API POST
    │ ────────────────────→  /announcements
    │                          │
    │                          ↓
    │                    Validation
    │                          │
    │                          ↓
    │                    Authorization
    │                          │
    │                          ↓
    │                    Create Record
    │                          │
    │                          ↓
    │       JSON Response ←────┘
    │    {success: true}
    │
    ├─ Parse response
    │
    ├─ Show notification
    │
    ├─ Clear form
    │
    └─ Trigger loadAnnouncements()
         │
         ├─ Fetch GET /announcements/api/get
         │ ────────────────────→  Server
         │                          │
         │                          ↓
         │                    Query Database
         │                          │
         │                          ↓
         │       JSON Array ←──────┘
         │
         ├─ Parse JSON
         │
         ├─ Escape HTML
         │
         ├─ Build HTML
         │
         └─ Update DOM
              ↓
         [Feed Updates]
```

---

This architecture ensures:
- ✅ **Scalability**: Database indexed for performance
- ✅ **Security**: Multiple protection layers
- ✅ **Reliability**: Persistent database storage
- ✅ **Responsiveness**: AJAX for no-reload updates
- ✅ **Maintainability**: Clear separation of concerns
