# Storage Link Fix for Hostinger

Since exec() is disabled on Hostinger, we need to create the storage link manually.

## Manual Steps:

1. In File Manager, go to public_html/
2. Create folder: storage/
3. Inside storage/, create folder: app/
4. Inside app/, create folder: public/

## File Structure Should Look Like:
public_html/
├── storage/
│   └── app/
│       └── public/
└── storage/ (your Laravel storage folder)
    └── app/
        └── public/

## Alternative: Copy files instead of symlink
Copy all files from: public_html/storage/app/public/
To: public_html/storage/app/public/

This creates a direct file copy instead of a symbolic link.
