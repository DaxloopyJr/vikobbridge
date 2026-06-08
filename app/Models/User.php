<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'email', 'phone_number', 'gender',
        'profile_picture', 'region', 'district', 'ward', 'village', 'street',
        'region_id', 'district_id', 'ward_id', 'village_id',
        'cell_leader_name', 'cell_leader_phone', 'lg_chairperson_name', 'lg_chairperson_phone',
        'guarantor_name', 'guarantor_phone', 'guarantor_relationship',
        'marital_status', 'spouse_name', 'spouse_phone', 'spouse_occupation',
        'dependents', 'inheritors', 'profile_completed', 'terms_accepted',
        'password', 'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dependents' => 'array',
            'inheritors' => 'array',
            'profile_completed' => 'boolean',
            'terms_accepted' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->withPivot('role_id', 'is_primary_group', 'joined_at', 'status')
            ->withTimestamps();
    }

    public function primaryGroup()
    {
        return $this->belongsToMany(Group::class, 'group_user')
            ->wherePivot('is_primary_group', true)
            ->withPivot('role_id', 'joined_at', 'status');
    }

    public function regionModel()
    {
        return $this->belongsTo(\App\Models\Location\Region::class, 'region_id');
    }

    public function districtModel()
    {
        return $this->belongsTo(\App\Models\Location\District::class, 'district_id');
    }

    public function wardModel()
    {
        return $this->belongsTo(\App\Models\Location\Ward::class, 'ward_id');
    }

    public function villageModel()
    {
        return $this->belongsTo(\App\Models\Location\Village::class, 'village_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isGroupAdmin(Group $group): bool
    {
        return $this->groups()->where('groups.id', $group->id)
            ->whereIn('group_user.role_id', function($query) {
                $query->select('id')->from('roles')
                    ->whereIn('name', ['chairperson', 'secretary', 'treasurer', 'group-admin']);
            })->exists();
    }

    public function canAccessGroup(Group $group): bool
    {
        return $this->isSuperAdmin() || $this->groups()->where('groups.id', $group->id)->exists();
    }

    /**
     * Check if user has a specific permission within their current group context.
     * This checks the role stored in the group_user pivot table.
     */
    public function hasGroupPermission(string $permission, ?int $groupId = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $groupId = $groupId ?? session('current_group_id');
        if (!$groupId) {
            return false;
        }

        $groupPivot = $this->groups()->where('groups.id', $groupId)->first();
        if (!$groupPivot || !$groupPivot->pivot || !$groupPivot->pivot->role_id) {
            return false;
        }

        $role = \Spatie\Permission\Models\Role::find($groupPivot->pivot->role_id);
        if (!$role) {
            return false;
        }

        return $role->hasPermissionTo($permission);
    }

    /**
     * Check if user has any of the given permissions within their current group context.
     */
    public function hasAnyGroupPermission(array $permissions, ?int $groupId = null): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasGroupPermission($permission, $groupId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the user's role name within a specific group.
     */
    public function groupRoleName(?int $groupId = null): ?string
    {
        $groupId = $groupId ?? session('current_group_id');
        if (!$groupId) {
            return null;
        }

        $groupPivot = $this->groups()->where('groups.id', $groupId)->first();
        if (!$groupPivot || !$groupPivot->pivot || !$groupPivot->pivot->role_id) {
            return null;
        }

        $role = \Spatie\Permission\Models\Role::find($groupPivot->pivot->role_id);
        return $role ? $role->name : null;
    }
}
