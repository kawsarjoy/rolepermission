<?php 
    namespace KawsarJoy\RolePermission;

    trait Permissible 
    {
        public function roles()
        {
          return $this->belongsToMany('KawsarJoy\RolePermission\Models\Role', config('permissions-config.table-prefix').'role_user');
        }
    
        public function hasRole($roles)
        {
          if(!config('permissions-config.rolepermission-enable'))
            return true;
          if (!is_array($roles)) $roles = [$roles];
            
          if ($this->roles()->whereIn('name', $roles)->first()) 
              return true;

          return false;
        }
    
        public function permissions()
        {
          $permissions = [];
    
          foreach ($this->roles as $key => $role) {

            $permissions[] = $role->permissions;
          }
    
          return collect($permissions)->flatten();
        }
    
        /**
         * Get all of the permissions for the user.
         */
        public function hasPermission($permissions)
        {
          if(!config('permissions-config.rolepermission-enable'))
            return true;
          if(!is_array($permissions)) $permissions = [$permissions];
    
          return (boolean) $this->permissions()->whereIn('name', $permissions)->first();
        }
    
    
        public function scopeGetUserByRole($query, $name)
        {
          return $query->whereHas(config('permissions-config.table-prefix').'roles', function ($query) use($name) {
            
                $query->where('name', $name);
            })->get();
        }
    }