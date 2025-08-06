<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\locations;
use App\Models\order;

class UpdateLocationsCase extends Command
{
    protected $signature = 'locations:update-case';
    protected $description = 'Update all location values to uppercase';

    public function handle()
    {
        // Update users table
        $this->info('Updating users table...');
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                if ($user->location) {
                    $user->location = strtoupper($user->getRawOriginal('location'));
                    $user->save();
                }
            }
        });

        // Update locations table
        $this->info('Updating locations table...');
        locations::chunk(100, function ($locations) {
            foreach ($locations as $location) {
                if ($location->location) {
                    $location->location = strtoupper($location->getRawOriginal('location'));
                }
                if ($location->name) {
                    $location->name = strtoupper($location->getRawOriginal('name'));
                }
                $location->save();
            }
        });

        // Update orders table
        $this->info('Updating orders table...');
        \App\Models\order::chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $updated = false;
                
                if ($order->origin) {
                    $order->origin = strtoupper($order->getRawOriginal('origin'));
                    $updated = true;
                }
                
                if ($order->destination) {
                    $order->destination = strtoupper($order->getRawOriginal('destination'));
                    $updated = true;
                }
                
                if ($order->updated_location) {
                    $order->updated_location = strtoupper($order->getRawOriginal('updated_location'));
                    $updated = true;
                }
                
                if ($updated) {
                    $order->save();
                }
            }
        });

        $this->info('All locations have been updated to uppercase.');
    }
}
