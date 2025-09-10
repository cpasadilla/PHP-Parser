# Crew Management System Implementation Summary

## Overview
A comprehensive crew management system has been implemented for SFX-1, allowing users to manage crew records, documents, and leave applications across multiple ships (MV EVERWIN STAR I-V), office staff, and laborers.

## Database Structure

### Tables Created
1. **crews** - Main crew member information
2. **crew_documents** - Document management with expiry tracking
3. **crew_leaves** - Leave credit allocation by year
4. **leave_applications** - Leave application requests and approvals

### Key Features in Database
- Soft deletes for crew records
- Ship assignment tracking
- Document status management (pending, verified, rejected, expired)
- Leave credit tracking with automatic calculations
- File storage paths for documents

## Models Created

### 1. Crew Model (`app/Models/Crew.php`)
- Manages crew member information
- Relationships with Ship, CrewDocument, CrewLeave, LeaveApplication
- Calculated attributes for leave credits
- Scopes for filtering (by ship, department, active status)

### 2. CrewDocument Model (`app/Models/CrewDocument.php`)
- Document management with expiry tracking
- Status management (pending/verified/rejected/expired)
- Document type constants for consistent data
- Automatic expiry checking with scopes

### 3. CrewLeave Model (`app/Models/CrewLeave.php`)
- Leave credit allocation by type and year
- Different leave types (vacation, sick, emergency, etc.)

### 4. LeaveApplication Model (`app/Models/LeaveApplication.php`)
- Leave application workflow
- Approval/rejection tracking
- File attachment support for supporting documents

## Controllers Implemented

### 1. CrewController
- Full CRUD operations for crew members
- Ship transfer functionality
- Filtering and search capabilities
- Automatic leave credit allocation on creation

### 2. CrewDocumentController
- Document upload and management
- Verification workflow
- Expiry tracking and notifications
- File download functionality

### 3. LeaveApplicationController
- Leave application submission
- Approval/rejection workflow
- Sick leave form upload with auto-approval
- Credit validation before approval

## Views Created

### Crew Management
- **index.blade.php** - Crew list with filtering and transfer
- **create.blade.php** - Add new crew member form
- **edit.blade.php** - Edit crew member form
- **show.blade.php** - Comprehensive crew details view

### Document Management
- **index.blade.php** - Document list with expiry warnings
- **create.blade.php** - Document upload form

### Leave Applications
- **index.blade.php** - Application list with approval actions
- **create.blade.php** - New application form with credit validation
- **upload-sick-leave.blade.php** - Sick leave form upload

## Key Features Implemented

### 1. Crew Management
- ✅ Store crew records for ship crew, office staff, and laborers
- ✅ Ship assignment tracking (MV EVERWIN STAR I-V)
- ✅ Personal and employment information management
- ✅ Emergency contact information
- ✅ Certificate and document number tracking
- ✅ Transfer crew between ships functionality

### 2. Document Management
- ✅ File upload for crew documents
- ✅ Document type categorization (seaman book, passport, visa, certificates, etc.)
- ✅ Expiry date tracking with notifications
- ✅ Admin verification workflow (pending/verified/rejected)
- ✅ Document download functionality
- ✅ Automatic status updates for expired documents

### 3. Leave Management
- ✅ Leave credit allocation and tracking
- ✅ Multiple leave types (vacation, sick, emergency, etc.)
- ✅ Leave application workflow
- ✅ Admin approval/rejection with reasons
- ✅ Credit validation before approval
- ✅ Sick leave form upload with auto-approval
- ✅ Supporting document attachments

### 4. Notifications & Alerts
- ✅ Expiring document warnings (30-day advance notice)
- ✅ Expired document notifications
- ✅ Leave credit insufficient warnings
- ✅ Dashboard notifications for document expiry

### 5. Reporting & Views
- ✅ Crew list by ship assignment
- ✅ Document expiry reports
- ✅ Leave credit summaries
- ✅ Application status tracking

## Sidebar Navigation Added
New "Crew Management" dropdown with:
- Crew List
- Document Management
- Expiring Documents
- Leave Applications
- Upload Sick Leave Form

## Routes Implemented
- `/crew/*` - Crew CRUD operations
- `/crew-documents/*` - Document management
- `/leave-applications/*` - Leave application management
- `/upload-sick-leave` - Sick leave form upload

## File Storage
- Documents stored in `storage/app/public/crew-documents/`
- Leave documents in `storage/app/public/leave-documents/`
- Public access via symlink

## Automation Features
- **Document Expiry Checking**: Command `crew:check-expiring-documents`
- **Auto Leave Credit Allocation**: When creating new crew members
- **Status Updates**: Automatic expired status for documents
- **Credit Calculations**: Real-time available credits calculation

## Permission System Integration
- Uses existing permission system
- Three main permissions: `crew`, `crew-documents`, `leave-applications`
- Admin users have full access to all features

## Sample Data
- 5 sample crew members created via seeder
- Includes different departments and ship assignments
- Default leave credits allocated (15 vacation, 7 sick days)

## Next Steps for Production
1. **Email Notifications**: Add email alerts for expiring documents
2. **Calendar Integration**: Leave calendar view
3. **Reporting Dashboard**: Enhanced analytics and reports
4. **Mobile Responsive**: Optimize for mobile devices
5. **Bulk Operations**: Mass document uploads, bulk transfers
6. **Advanced Filters**: More detailed search and filter options
7. **Audit Trail**: Track all changes to crew records

## Usage Instructions
1. **Adding Crew**: Use "Add New Crew Member" button in Crew List
2. **Document Upload**: Go to Document Management → Upload Document
3. **Leave Application**: Create new application or upload sick leave form
4. **Ship Transfer**: Use Transfer button in crew list or crew details
5. **Document Verification**: Pending documents can be verified by authorized users
6. **Leave Approval**: Admins can approve/reject applications from the list

The system is fully functional and ready for use with comprehensive crew management capabilities across all requested features.
