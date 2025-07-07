<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateStorageLink extends Command
{
    protected $signature = 'storage:link-manual';
    protected $description = 'Create storage link without using exec()';

    public function handle()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (File::exists($link)) {
            $this->error('Storage link already exists!');
            return false;
        }

        if (!File::exists($target)) {
            $this->error('Target directory does not exist!');
            return false;
        }

        try {
            // Try to create symbolic link
            if (symlink($target, $link)) {
                $this->info('Storage link created successfully!');
                return true;
            }
        } catch (\Exception $e) {
            $this->warn('Symlink failed, trying to copy files instead...');
        }

        // Fallback: Copy files
        try {
            File::copyDirectory($target, $link);
            $this->info('Storage files copied successfully!');
            return true;
        } catch (\Exception $e) {
            $this->error('Failed to create storage link: ' . $e->getMessage());
            return false;
        }
    }
}
