<?php

namespace KawsarJoy\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = [];

    public function getTable()
    {
        return config('permissions-config.table-prefix').'permissions';
    }

    public function roles()
    {
      return $this->belongsToMany('KawsarJoy\RolePermission\Models\Permission', config('permissions-config.table-prefix').'permission_'.config('permissions-config.table-prefix').'role');
    }

    public function users()
    {
      $users = [];

      foreach ($this->roles as $key => $role) {

        $users[] = $role->users;
      }

      return collect($users)->flatten();
    }

    public function scopeGetPermissionByRole($query, $name)
    {
      return $query->whereHas('roles', function ($query) use($name) {
        
            $query->where('name', $name);
        })->get();
    }
}