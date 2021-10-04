<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasPermissions;

use App\Models\Post;
use App\Models\Role;
use App\Models\UserLogin;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'user_name',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $connection = 'mysql';
    protected $table = 'users';


    public static function getTableName() {
        return with(new static)->getTable();
    }

    public function post() {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }

    public function roles() {
        return $this->belongsToMany(Role::class, "role_user", "role_id", "user_id");
    }

    public function user_login() {
        return $this->hasMany(UserLogin::class, "user_id", "id");
    }

    public static function getUserList($req) {
        $users = User::where("users.deleted_at", null)
        ->with("roles", function($sql) {
            // $sql->where("deleted_at, null");
            $sql->select("id", "name");
            $sql->with("permissions", function($sql) {
                // $sql->where("deleted_at, null");
                $sql->select("id", "name");
            });
        })
        ->select(
            "users.id",
            "users.name",
            "users.user_name",
            "users.email",
            "users.created_at",
            "users.updated_at",
        );
        return $users->get();
    }

    public static function getInactiveUser() {

        $inactiveUsers = User::where("users.deleted_at", null)
        ->whereDoesntHave('user_login', function($sql){
          $sql->where("created_at", ">", Carbon::yesterday());
        }, ">=", 1);
        
        return $inactiveUsers->get();
    }
}
