<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckStorageLink extends Command
{
    protected $signature = 'storage:check';
    protected $description = 'Check storage link status';

    public function handle()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        $this->info('🔍 Storage Link Diagnostic');
        $this->line('');

        // Check target directory
        if (File::exists($target)) {
            $this->info("✅ Target directory exists: {$target}");
        } else {
            $this->error("❌ Target directory missing: {$target}");
            return false;
        }

        // Check link
        if (File::exists($link)) {
            $this->info("✅ Storage link exists: {$link}");
            
            if (is_link($link)) {
                $this->info("🔗 This is a symbolic link");
                $this->line("   Target: " . readlink($link));
            } else {
                $this->warn("📁 This is a directory (not a symbolic link)");
            }
        } else {
            $this->error("❌ Storage link missing: {$link}");
            $this->line("   Run: php artisan storage:link-manual");
            return false;
        }

        // List files
        $this->info("\n📂 Files in storage/app/public:");
        $files = File::files($target);
        if (empty($files)) {
            $this->line("   (No files found)");
        } else {
            foreach ($files as $file) {
                $this->line("   - " . $file->getFilename());
            }
        }

        $this->info("\n🌐 Storage URL: " . asset('storage/'));
        $this->info("✅ Storage link is working properly!");
        
        return true;
    }
}
