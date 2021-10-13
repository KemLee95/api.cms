<?php
namespace App\Models;

class VoucherUser extends ModelBase {

  public $connection = 'mysql';
  public $table = 'voucher_user';
  public $timestap = true;

  const DISABLED_STATUS = 'disabled';
  const ENABLED_STATUS = 'enabled';

  protected $fillable = ["voucher_id", "user_id", "status"];

  public function users(){
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public static function getVoucherUsers($req, $paginate = 10) {
    if($req->has("paginate")){
      $paginate = $req->paginate;
    }
    $sql = VoucherUser::where("voucher_user.deleted_at", null)
    ->leftJoin("users", function($join){
      $join->where("users.deleted_at", null);
      $join->on("users.id", "voucher_user.user_id");
    })
    ->select(
      "voucher_user.id",
      "voucher_user.user_id",
      "voucher_user.created_at",
      "voucher_user.status",
      "users.user_name as user_name",
      "users.email"
    );

    if($req->has("voucher_id")){
      $sql->where("voucher_user.voucher_id", $req->voucher_id);
    }
    if($req->has("user_id")) {
      $sql->where("voucher_user.user_id", $req->user_id);
    }
    
    return $sql->lockForUpdate()->paginate($paginate);
  }
}