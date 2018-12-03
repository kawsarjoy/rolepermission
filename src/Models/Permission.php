<?php

namespace KawsarJoy\RolePermission;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function roles()
    {
      return $this->belongsToMany('App\Model\Role');
    }
}