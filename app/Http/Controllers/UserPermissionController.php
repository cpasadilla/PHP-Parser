<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use App\Models\UserPermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class UserPermissionController extends Controller
{
    public function update(Request $request)
    {
        // Verify the current user has admin privileges
        $currentUser = auth()->user();
        $userRole = $currentUser->roles ? strtoupper(trim($currentUser->roles->roles)) : '';
        $isAdmin = in_array($userRole, ['ADMIN', 'ADMINISTRATOR']);
        
        if (!$isAdmin) {
            return Redirect::route('profile.edit')->with('error', 'You do not have permission to manage user access.');
        }

        // Get the permissions data from the request
        $permissions = $request->input('permissions', []);
        
        // Begin a database transaction
        DB::beginTransaction();
        
        try {
            // Get all users
            $allUsers = User::all();
            
            // Define available operations for each module
            $modules = [
                'dashboard' => [
                    'operations' => ['access'],
                    'pages' => [
                        'ship-graphs' => ['access'],
                        'pie-charts' => ['access']
                    ]
                ],
                'customer' => ['access', 'create', 'edit', 'delete'],
                'masterlist' => [
                    'operations' => ['access', 'create', 'edit', 'delete'],
                    'pages' => [
                        'ships' => ['access', 'create', 'edit', 'delete'],
                        'voyage' => ['access', 'create', 'edit', 'delete'],
                        'container' => ['access', 'create', 'edit', 'delete'],
                        'container-details' => ['access'],
                        'list' => ['access', 'create', 'edit', 'delete'],
                        'parcel' => ['access', 'create', 'edit', 'delete'],
                        'soa' => ['access', 'create', 'delete'],
                        'customer' => ['access', 'edit', 'delete']
                    ]
                ],
                'pricelist' => ['access', 'create', 'edit', 'delete'], 
                'users' => ['access', 'create', 'edit', 'delete'],
                'history' => ['access'],
                'profile' => ['access', 'edit']
            ];
            
            // Process each user
            foreach ($allUsers as $user) {
                $userId = $user->id;
                
                // Skip admin users - they have access to everything
                $userRole = $user->roles ? strtoupper(trim($user->roles->roles)) : '';
                if (in_array($userRole, ['ADMIN', 'ADMINISTRATOR'])) {
                    continue;
                }
                
                // Initialize default permissions structure
                $userPermissions = [];
                foreach ($modules as $module => $config) {
                    $userPermissions[$module] = [];
                    
                    // Handle module with pages
                    if (is_array($config) && isset($config['operations']) && isset($config['pages'])) {
                        // Set operations for the main module
                        foreach ($config['operations'] as $operation) {
                            $userPermissions[$module][$operation] = false;
                        }
                        
                        // Set pages and their operations
                        $userPermissions[$module]['pages'] = [];
                        foreach ($config['pages'] as $page => $pageOperations) {
                            $userPermissions[$module]['pages'][$page] = [];
                            foreach ($pageOperations as $operation) {
                                $userPermissions[$module]['pages'][$page][$operation] = false;
                            }
                        }
                    } else {
                        // Handle simple module without pages
                        $operations = $config;
                        foreach ($operations as $operation) {
                            $userPermissions[$module][$operation] = false;
                        }
                    }
                }
                
                // Always enable profile access
                $userPermissions['profile']['access'] = true;
                
                // Update with submitted permissions
                if (isset($permissions[$userId])) {
                    foreach ($permissions[$userId] as $module => $moduleData) {
                        if (isset($userPermissions[$module])) {
                            // Handle pages separately if they exist
                            if (isset($moduleData['pages'])) {
                                foreach ($moduleData['pages'] as $page => $pageOperations) {
                                    if (isset($userPermissions[$module]['pages'][$page])) {
                                        foreach ($pageOperations as $operation => $value) {
                                            if (isset($userPermissions[$module]['pages'][$page][$operation])) {
                                                $userPermissions[$module]['pages'][$page][$operation] = ($value == 1);
                                            }
                                        }
                                    }
                                }
                                
                                // Remove pages from moduleData to avoid processing them in the next loop
                                unset($moduleData['pages']);
                            }
                            
                            // Process module-level operations
                            foreach ($moduleData as $operation => $value) {
                                if (isset($userPermissions[$module][$operation])) {
                                    $userPermissions[$module][$operation] = ($value == 1);
                                }
                            }
                        }
                    }
                }
                
                // Save or update the user's permissions
                UserPermission::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'pages' => json_encode($userPermissions),
                        'updated_by' => $currentUser->id
                    ]
                );
            }
            
            DB::commit();
            return Redirect::route('profile.edit')->with('status', 'permissions-updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('profile.edit')->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }

    /**
     * Delete all permissions for a user except for profile access
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        // Verify the current user has admin privileges
        $currentUser = auth()->user();
        $userRole = $currentUser->roles ? strtoupper(trim($currentUser->roles->roles)) : '';
        $isAdmin = in_array($userRole, ['ADMIN', 'ADMINISTRATOR']);
        
        if (!$isAdmin) {
            return Redirect::route('profile.edit')->with('error', 'You do not have permission to manage user access.');
        }

        // Get the user ID from the request
        $userId = $request->input('user_id');
        
        if (!$userId) {
            return Redirect::route('profile.edit')->with('error', 'No user specified for permission deletion.');
        }

        // Find the user
        $user = User::find($userId);
        if (!$user) {
            return Redirect::route('profile.edit')->with('error', 'User not found.');
        }

        // Prevent deleting admin user permissions
        $userRole = $user->roles ? strtoupper(trim($user->roles->roles)) : '';
        if (in_array($userRole, ['ADMIN', 'ADMINISTRATOR'])) {
            return Redirect::route('profile.edit')->with('error', 'Cannot delete permissions for admin users.');
        }

        try {
            // Define available operations for each module
            $modules = [
                'dashboard' => [
                    'operations' => ['access'],
                    'pages' => [
                        'ship-graphs' => ['access'],
                        'pie-charts' => ['access']
                    ]
                ],
                'customer' => ['access', 'create', 'edit', 'delete'],
                'masterlist' => [
                    'operations' => ['access', 'create', 'edit', 'delete'],
                    'pages' => [
                        'ships' => ['access', 'create', 'edit', 'delete'],
                        'voyage' => ['access', 'create', 'edit', 'delete'],
                        'container' => ['access', 'create', 'edit', 'delete'],
                        'container-details' => ['access'],
                        'list' => ['access', 'create', 'edit', 'delete'],
                        'parcel' => ['access', 'create', 'edit', 'delete'],
                        'soa' => ['access', 'create', 'delete'],
                        'customer' => ['access', 'edit', 'delete']
                    ]
                ],
                'pricelist' => ['access', 'create', 'edit', 'delete'], 
                'users' => ['access', 'create', 'edit', 'delete'],
                'history' => ['access'],
                'profile' => ['access', 'edit']
            ];
            
            // Initialize default permissions structure with everything disabled
            $defaultPermissions = [];
            foreach ($modules as $module => $config) {
                $defaultPermissions[$module] = [];
                
                // Handle module with pages
                if (is_array($config) && isset($config['operations']) && isset($config['pages'])) {
                    // Set operations for the main module
                    foreach ($config['operations'] as $operation) {
                        $defaultPermissions[$module][$operation] = false;
                    }
                    
                    // Set pages and their operations
                    $defaultPermissions[$module]['pages'] = [];
                    foreach ($config['pages'] as $page => $pageOperations) {
                        $defaultPermissions[$module]['pages'][$page] = [];
                        foreach ($pageOperations as $operation) {
                            $defaultPermissions[$module]['pages'][$page][$operation] = false;
                        }
                    }
                } else {
                    // Handle simple module without pages
                    $operations = $config;
                    foreach ($operations as $operation) {
                        $defaultPermissions[$module][$operation] = false;
                    }
                }
            }
            
            // Always enable profile access
            $defaultPermissions['profile']['access'] = true;

            // Update or create the user permissions
            UserPermission::updateOrCreate(
                ['user_id' => $userId],
                [
                    'pages' => json_encode($defaultPermissions),
                    'updated_by' => $currentUser->id
                ]
            );

            return Redirect::route('profile.edit')
                ->with('status', 'permissions-updated')
                ->with('message', 'All permissions have been removed from the user.');
                
        } catch (\Exception $e) {
            return Redirect::route('profile.edit')
                ->with('error', 'Failed to delete permissions: ' . $e->getMessage());
        }
    }
}