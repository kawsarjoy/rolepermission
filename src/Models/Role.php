<?php

namespace KawsarJoy\RolePermission\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	public function users()
	{
	  return $this->belongsToMany('App\User');
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