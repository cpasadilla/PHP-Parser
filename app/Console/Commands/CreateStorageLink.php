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
            $this->error('The "public/storage" directory already exists.');
            return;
        }

        try {
            // Create the directory structure manually
            if (!File::exists($link)) {
                File::makeDirectory($link, 0755, true);
            }

            // Copy files instead of creating symlink
            $files = File::allFiles($target);
            foreach ($files as $file) {
                $relativePath = str_replace($target, '', $file->getPathname());
                $destinationPath = $link . $relativePath;
                
                $destinationDir = dirname($destinationPath);
                if (!File::exists($destinationDir)) {
                    File::makeDirectory($destinationDir, 0755, true);
                }
                
                File::copy($file->getPathname(), $destinationPath);
            }

            $this->info('Storage link created successfully using file copy method.');
        } catch (\Exception $e) {
            $this->error('Failed to create storage link: ' . $e->getMessage());
        }
    }
}
