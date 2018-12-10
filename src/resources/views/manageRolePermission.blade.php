<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1z">

        <title>KawsarJoy-rolepermission</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 150vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="container-fluid">

                @if($errors->count())
                    <ul>
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                @endif

                <div class="row">
                    <div class="col-sm">
                    
                        <form method="POST" action="{{ route('saveRole') }}">
                            @csrf
                            <div class="form-group">
                                <label for="roleName">Role Name</label>
                                <input type="text" name="name" class="form-control" id="roleName" aria-describedby="roleNameHelp" placeholder="Enter role name" autocomplete="off" required>
                                <small id="roleNameHelp" class="form-text text-muted">Use small letters and hyphen only</small>
                            </div>
                            <div class="form-group">
                                <label for="roleName">Role Description</label>
                                <textarea name="description" id="roleDescription" cols="5" rows="5" class="form-control" id="roleDescription" aria-describedby="roleDescriptionHelp" placeholder="Enter role description" required></textarea>
                                <small id="roleDescriptionHelp" class="form-text text-muted">Briefly describe this role</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="col-sm">
                    
                        <form method="POST" action="{{ route('savePermission') }}">
                            @csrf
                            <div class="form-group">
                                <label for="permissionName">Permission Name</label>
                                <input type="text" name="name" class="form-control" id="permissionName" aria-describedby="permissionNameHelp" placeholder="Enter permission name" autocomplete="off" required>
                                <small id="permissionNameHelp" class="form-text text-muted">Use small letters and hyphen only</small>
                            </div>
                            <div class="form-group">
                                <label for="permissionName">Permission Description</label>
                                <textarea name="description" id="permissionDescription" cols="5" rows="5" class="form-control" id="permissionDescription" aria-describedby="permissionDescriptionHelp" placeholder="Enter permission description" required></textarea>
                                <small id="permissionDescriptionHelp" class="form-text text-muted">Briefly describe this permission</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-sm">
                    
                        <form method="POST" action="{{ route('addRole') }}">
                            @csrf
                            <div class="form-group">
                                <label for="user">Select User</label>
                                <select name="user" id="user" class="form-control" required>
                                    @foreach($users as $user)

                                        <option value="<?php echo $user[config('permissions-config.primary-key')] ;?>">{{ $user[config('permissions-config.name')] }}</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="roles">Select Roles</label>
                                <select name="roles[]" id="roles" class="form-control" multiple required>
                                    @foreach($roles as $role)

                                        <option value="{{ $role->id }}">{{ $role->name }}</option>

                                    @endforeach
                                </select>

                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                    <div class="col-sm">
                    
                        <form method="POST" action="{{ route('addPermission') }}">
                            @csrf
                            <div class="form-group">
                                <label for="role">Select Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    @foreach($roles as $role)

                                        <option value="{{ $role->id }}">{{ $role->name }}</option>

                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="permissions">Select Permissions</label>
                                <select name="permissions[]" id="permissions" class="form-control" multiple required>
                                    @foreach($permissions as $permission)

                                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>

                                    @endforeach
                                </select>

                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>



            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        
    </body>
</html>
