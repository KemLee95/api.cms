<?php
namespace App\Models;

use App\Models\ModelBase;

class Voucher extends ModelBase{
  public $connection = 'mysql';
  public $table = 'vouchers';

  const DISABLED_STATUS = 'disabled';
  const ENABLED_STATUS = 'enabled';

  public function event() {
    return $this->belongsTo(Event::class, 'event_id', 'id');
  }

  public function users() {
    return $this->belongsToMany(User::class, 'voucher_user', 'voucher_id', 'user_id')
    ->where("voucher_user.deleted_at", null);
  }

  public static function getVoucherPartial($req, $paninate = 1) {
    if($req->has('paginate')) {
      $paninate = $req->paginate;
    }

    $voucherSql = Voucher::where("deleted_at", null)
    ->whereHas("event", function($sql) use($req) {
      $sql->where("events.deleted_at", null);
      $sql->where("events.id", $req->event_id);
    })
    ->select(
        "id", 
        "event_id", 
        "percentage_decrease", 
        "maximum_quantity", 
        "expiry_date", "status", 
        "unique_code"
    )
    ->with("users", function($sql){
      $sql->select("users.id", "users.user_name", "users.name", "users.email");
    });

    return $voucherSql->paginate($paninate);
  }

  public static function getVoucherDetail($req, $paninate = 1) {
    if($req->has('paginate')) {
      $paninate = $req->paginate;
    }
    $voucherSql = Voucher::where("vouchers.deleted_at", null)
    ->leftJoin("events", function($join){
      $join->on("events.id", "vouchers.event_id");
    })
    ->select(
      "vouchers.id",
      "vouchers.event_id",
      "events.name as event_name",
      "vouchers.percentage_decrease",
      "vouchers.expiry_date",
      "vouchers.status",
      "vouchers.unique_code",
      "vouchers.created_at"
    );

    return $voucherSql->find($req->voucher_id);
  }
}