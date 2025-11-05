# Gate Pass Quick Reference Guide

## Quick Links
- View All Gate Passes: `/gatepass`
- Create Gate Pass: Click "+ New" in masterlist GATE PASS column
- View Summary: Click "Summary" link in masterlist or gate pass view

## Key Points

### ✓ Gate Pass Numbers
- **Manually entered** (not auto-generated)
- Must be **unique**
- Should match physical gate pass document number

### ✓ Multiple Gate Passes per BL
- ✅ **YES** - You can create multiple gate passes for one BL
- Common for partial releases (e.g., customer picks up items in batches)

### ✓ What Gets Tracked
- Gate Pass Number
- Release Date
- Checker Name & Receiver Name
- Items Released (description, quantity, unit)
- Checker Notes (plate number, etc.)
- Who created it (saved in database, not visible in masterlist)

### ✓ Visual Indicators
- **Masterlist**: GATE PASS column shows all gate pass numbers
- **BL View**: Red "RELEASED" stamp appears when gate passes exist
- **Summary**: Shows complete release status with color coding

### ✓ Item Tracking
- **Total Quantity**: How much was in the BL originally
- **Released Quantity**: How much is being released in THIS gate pass
- **Remaining Quantity**: Auto-calculated (Total - Released)

## Common Tasks

### Create a Gate Pass
1. Go to Masterlist
2. Find the BL row
3. Click "+ New" in GATE PASS column
4. Fill in gate pass number and date
5. Enter checker and receiver names
6. Adjust quantities for items being released
7. Add notes if needed
8. Click "Create Gate Pass"

### View Release Status
1. From Masterlist: Click "Summary" in GATE PASS column
2. See what's released vs. what remains
3. View all gate passes for that BL

### Print Gate Pass
1. Click gate pass number to open
2. Click "PRINT" button
3. Disable headers/footers in print dialog for clean output

### Edit Gate Pass
1. Click gate pass number to open
2. Click "EDIT" button
3. Make changes
4. Click "Update Gate Pass"

### Example Workflow

**Scenario**: Customer wants to pick up items from BL#005

1. **Initial BL Content**:
   - 1200 sks rice 25kg
   - 100 bxs grocery

2. **First Pickup** (Gate Pass #001):
   - Released: 1000 sks rice, 20 bxs grocery
   - Notes: "Plate# ABC-123"
   - Remaining: 200 sks rice, 80 bxs grocery

3. **Second Pickup** (Gate Pass #002):
   - Released: 200 sks rice, 30 bxs grocery
   - Notes: "Plate# XYZ-789"
   - Remaining: 0 sks rice, 50 bxs grocery

4. **Summary Shows**:
   - Rice: 1200 total → 1200 released → 0 remaining ✓ Fully Released
   - Grocery: 100 total → 50 released → 50 remaining ⚠ Partially Released

## Status Colors

### In Summary Report:
- 🟢 **Green** = Fully Released (no items remaining)
- 🟠 **Orange** = Partially Released (some items remaining)
- 🔴 **Red** = Not Released (no items released yet)

## Important Notes

⚠️ **Gate Pass Number Must Be Unique**
- System will reject duplicate gate pass numbers
- Make sure to enter the correct number from physical document

⚠️ **Released Quantity Cannot Exceed Total**
- System validates that you don't release more than available
- If you need to release more, check if total quantity is correct

⚠️ **Permissions Required**
- You need edit permission on masterlist to create/edit gate passes
- View-only users can see gate passes but cannot create/edit them

✓ **Automatic Tracking**
- System remembers who created each gate pass
- This information is stored for audit purposes
- Creator name not shown in masterlist (like OR/AR tracking)

✓ **BL Stamp**
- Red "RELEASED" stamp automatically appears on BL when first gate pass is created
- Shows date of first gate pass
- Remains visible even if gate pass is deleted (if others exist)

## Tips

💡 **Use Checker Notes Effectively**
- Record vehicle plate numbers
- Note any damaged items
- Add special handling instructions
- Document weather conditions if relevant

💡 **Create Summary Before New Gate Pass**
- Check the summary report before creating a new gate pass
- Verify remaining quantities
- Ensure you don't duplicate releases

💡 **Print for Records**
- Print gate pass immediately after creation
- File with physical documents
- Summary report can be printed for monthly records

## Troubleshooting

### "Gate pass number already exists"
→ Choose a different number or check if it was already entered

### Items not showing in create form
→ Make sure the BL has parcels/items added to it

### Cannot see "+ New" button
→ Check if you have edit permissions on masterlist

### RELEASED stamp not showing
→ Refresh the page after creating gate pass

### Wrong quantities entered
→ Use "Edit" function to correct before printing

## Contact
For technical issues or questions about the gate pass system, contact your system administrator.

---
*Last Updated: November 4, 2025*
