<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasPermissions;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Auth;

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
        return $this->belongsToMany(Role::class, "role_user", "user_id", "role_id")
        ->where("role_user.deleted_at", null);
    }

    public function user_login() {
        return $this->hasMany(UserLogin::class, "user_id", "id");
    }

    public function sendPasswordResetNotification($token) {
        $url = env('CLIENT_URL') .'auth/' .'reset-password?token=' . $token;
        $this->notify(new ResetPasswordNotification($url));
    }

    public function sendEmailVerificationNotification(){
        $this->notify(new VerifyEmail());
    }

    public static function getUserList($req, $paginate = 10) {
        if($req->has("paginate")) {
            $paginate = $req->paginate;
        }
        $users = User::where("users.deleted_at", null)
        ->with("roles", function($sql) {
            $sql->select("roles.id", "roles.name");
            $sql->with("permissions", function($sql) {
                $sql->select("permissions.id", "permissions.name");
            });
        })
        ->leftJoin("user_status", function($join){
            $join->where("user_status.deleted_at", null);
            $join->on("user_status.user_id", "users.id");
        })
        ->select(
            "users.id",
            "users.name",
            "users.user_name",
            "users.email",
            "users.created_at",
            "users.updated_at",
            "users.email_verified_at",
            "user_status.state as state"
        );
        return $users->paginate($paginate);
    }

    public static function getUserInfo($userId) {
        $users = User::where("users.deleted_at", null)
        ->where("users.id", $userId)
        ->with("roles", function($sql) {
            $sql->select("roles.id", "roles.name");
            $sql->with("permissions", function($sql) {
                $sql->select("permissions.id", "permissions.name");
            });
        })
        ->select(
            "users.id",
            "users.name",
            "users.user_name",
            "users.email",
            "users.created_at",
            "users.updated_at",
            "users.email_verified_at",
        );
        return $users->first();
    }

    public static function getInactiveUser() {

        $inactiveUsers = User::where("users.deleted_at", null)
        ->whereDoesntHave("roles", function($sql){
            $sql->whereIn("roles.id", ['1']);
        })
        ->whereNotNull("users.email_verified_at")
        ->whereDoesntHave('user_login', function($sql){
          $sql->where("created_at", ">", Carbon::yesterday());
        }, ">=", 1)
        ->select(
            "users.id",
            "users.name as user_name",
            "users.email",
        );
        
        return $inactiveUsers->get();
    }
    
    public static function getVerifiedUserListByRoleId($roleId = 2) {

        $admins = User::where("users.deleted_at", null)
        ->whereNotNull("users.email_verified_at")
        ->whereHas("roles", function($sql) use($roleId) {
            $sql->where("roles.id", $roleId);
        })
        ->select(
            "users.id as user_id",
            "users.name as user_name",
            "users.email"
        );

        return $admins->get();
    }
}
