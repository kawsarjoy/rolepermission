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
      return $this->belongsToMany('App\Model\Permission');
    }
}