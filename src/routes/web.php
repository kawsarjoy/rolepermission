<?php


    Route::group(['namespace' => 'KawsarJoy\RolePermission\Http\Controllers', 
                  'middleware' => ['web', 'auth']], function(){

        Route::get('test', function(){

            return KawsarJoy\RolePermission\Models\Role::find(1)->users;
        });

        Route::get('manage-role-permission', 'ManageRolePermissionController@index')->name('manageRolePermission');

        Route::post('save-role', 'ManageRolePermissionController@saveRole')->name('saveRole');
        Route::post('save-permission', 'ManageRolePermissionController@savePermission')->name('savePermission');
        Route::post('add-role', 'ManageRolePermissionController@addRole')->name('addRole');
        Route::post('add-permission', 'ManageRolePermissionController@addPermission')->name('addPermission');
    });
