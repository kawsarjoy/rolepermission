<?php

namespace KawsarJoy\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $guarded = [];
  
	public function users()
	{
	  return $this->belongsToMany(config('permissions-config.model'));
	}

  public function permissions()
  {
    return $this->belongsToMany('KawsarJoy\RolePermission\Models\Permission');
  }

  public function scopeGetRoleByPermission($query, $name)
  {
    return $query->whereHas('permissions', function ($query) use($name) {
      
          $query->where('name', $name);
      })->get();
  }

}