<?php

namespace KawsarJoy\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $guarded = [];

  public function getTable()
  {
      return config('permissions-config.table-prefix').'roles';
  }
  
	public function users()
	{
	  return $this->belongsToMany(config('permissions-config.model'));
	}

  public function permissions()
  {
    return $this->belongsToMany('KawsarJoy\RolePermission\Models\Permission', config('permissions-config.table-prefix').'permission_'.config('permissions-config.table-prefix').'role');
  }

  public function scopeGetRoleByPermission($query, $name)
  {
    return $query->whereHas(config('permissions-config.table-prefix').'permissions', function ($query) use($name) {
      
          $query->where('name', $name);
      })->get();
  }

}