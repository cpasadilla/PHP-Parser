<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrewDocument;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CheckExpiringDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crew:check-expiring-documents {--days=30 : Number of days to check ahead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expiring crew documents and log notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        
        // Get expiring documents
        $expiringDocuments = CrewDocument::expiringSoon($days)->with(['crew', 'uploadedBy'])->get();
        $expiredDocuments = CrewDocument::expired()->with(['crew', 'uploadedBy'])->get();

        if ($expiringDocuments->count() > 0) {
            $this->info("Found {$expiringDocuments->count()} documents expiring within {$days} days:");
            
            foreach ($expiringDocuments as $document) {
                $daysUntilExpiry = $document->expiry_date->diffInDays(now());
                $message = "- {$document->crew->full_name} ({$document->crew->employee_id}): {$document->document_type_name} expires in {$daysUntilExpiry} days ({$document->expiry_date->format('M d, Y')})";
                $this->warn($message);
                
                // Log for system tracking
                Log::warning('Document expiring soon', [
                    'crew_id' => $document->crew_id,
                    'crew_name' => $document->crew->full_name,
                    'employee_id' => $document->crew->employee_id,
                    'document_type' => $document->document_type,
                    'document_name' => $document->document_name,
                    'expiry_date' => $document->expiry_date->format('Y-m-d'),
                    'days_until_expiry' => $daysUntilExpiry
                ]);
            }
        }

        if ($expiredDocuments->count() > 0) {
            $this->error("Found {$expiredDocuments->count()} expired documents:");
            
            foreach ($expiredDocuments as $document) {
                $daysExpired = now()->diffInDays($document->expiry_date);
                $message = "- {$document->crew->full_name} ({$document->crew->employee_id}): {$document->document_type_name} expired {$daysExpired} days ago ({$document->expiry_date->format('M d, Y')})";
                $this->error($message);
                
                // Log for system tracking
                Log::error('Document expired', [
                    'crew_id' => $document->crew_id,
                    'crew_name' => $document->crew->full_name,
                    'employee_id' => $document->crew->employee_id,
                    'document_type' => $document->document_type,
                    'document_name' => $document->document_name,
                    'expiry_date' => $document->expiry_date->format('Y-m-d'),
                    'days_expired' => $daysExpired
                ]);

                // Auto-update status to expired
                $document->update(['status' => 'expired']);
            }
        }

        if ($expiringDocuments->count() === 0 && $expiredDocuments->count() === 0) {
            $this->info('No expiring or expired documents found.');
        }

        // Summary
        $this->line('');
        $this->info('Summary:');
        $this->line("Expiring documents: {$expiringDocuments->count()}");
        $this->line("Expired documents: {$expiredDocuments->count()}");
        
        return Command::SUCCESS;
    }
}
