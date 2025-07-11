<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fName',
        'lName',
        'email',
        'password',
        'role',
        'location',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(){
        return $this->hasOne(Roles::class, 'user_id');
    }

    public function permissions(){
        return $this->hasOne(UserPermission::class, 'user_id');
    }
    
    /**
     * Check if user has permission to access a specific page
     * 
     * @param string $page
     * @return bool
     */
    public function hasPagePermission(string $page): bool
    {
        // Admin always has access
        if ($this->roles && in_array(strtoupper(trim($this->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) {
            return true;
        }
        
        // Everyone has access to profile
        if ($page === 'profile') {
            return true;
        }
        
        // Get permission data
        $permissionsData = $this->getPermissionsData();
        
        // Check for access to this page
        return isset($permissionsData[$page]['access']) && $permissionsData[$page]['access'] === true;
    }
    
    /**
     * Check if user has permission for a specific operation on a page
     * 
     * @param string $page
     * @param string $operation
     * @return bool
     */
    public function hasPermission(string $page, string $operation): bool
    {
        // Admin always has all permissions
        if ($this->roles && in_array(strtoupper(trim($this->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) {
            return true;
        }
        
        // Everyone can access their profile
        if ($page === 'profile' && $operation === 'access') {
            return true;
        }
        
        // Get permission data
        $permissionsData = $this->getPermissionsData();
        
        // Check for specific permission
        return isset($permissionsData[$page][$operation]) && $permissionsData[$page][$operation] === true;
    }
    
    /**
     * Check if user has permission for a specific subpage of a module
     * 
     * @param string $module
     * @param string $subpage
     * @param string $operation
     * @return bool
     */
    public function hasSubpagePermission(string $module, string $subpage, string $operation = 'access'): bool
    {
        // Admin always has all permissions
        if ($this->roles && in_array(strtoupper(trim($this->roles->roles)), ['ADMIN', 'ADMINISTRATOR'])) {
            return true;
        }
        
        // Everyone can access their profile
        if ($module === 'profile') {
            return true;
        }
        
        // Get permission data
        $permissionsData = $this->getPermissionsData();
        
        // Check for access to this subpage
        return isset($permissionsData[$module]['pages'][$subpage][$operation]) && 
               $permissionsData[$module]['pages'][$subpage][$operation] === true;
    }
    
    /**
     * Get decoded permissions data
     * 
     * @return array
     */
    public function getPermissionsData(): array
    {
        if (!$this->permissions || !$this->permissions->pages) {
            return [];
        }
        
        return is_string($this->permissions->pages) 
            ? json_decode($this->permissions->pages, true) 
            : $this->permissions->pages;
    }
}