<?php
namespace App\Models;

use App\Models\ModelBase;
use Illuminate\Support\Facades\Auth;

class Voucher extends ModelBase{
  public $connection = 'mysql';
  public $table = 'vouchers';

  const DISABLED_STATUS = 'disabled';
  const ENABLED_STATUS = 'enabled';

  protected $fillable = [
    'event_id', 
    'percentage_decrease', 
    'maximum_quantity', 
    'available_quantity', 
    'expiry_date', 
    'status', 
    'unique_code'
  ];
  
  protected $casts = [
    "expiry_date" => 'datetime'
  ];

  public function event() {
    return $this->belongsTo(Event::class, 'event_id', 'id');
  }

  public function users() {
    return $this->belongsToMany(User::class, 'voucher_user', 'voucher_id', 'user_id')
    ->where("voucher_user.deleted_at", null);
  }

  public function voucher_user() {
    return $this->hasMany(VoucherUser::class, "id", "user_id");
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
        "available_quantity",
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
      "vouchers.maximum_quantity",
      "vouchers.expiry_date",
      "vouchers.status",
      "vouchers.unique_code",
      "vouchers.created_at"
    );

    return $voucherSql->lockForUpdate()->find($req->voucher_id);
  }

  public static function getVoucherList($req, $paginate = 5) {

    if($req->has('paginate')) {
      $paginate = $req->paginate;
    }

    $sql = Voucher::whereNull("vouchers.deleted_at")
    ->where("vouchers.status", Voucher::ENABLED_STATUS)
    ->whereHas("event", function($sql){
      $sql->whereNull("events.deleted_at");
      $sql->where("events.status", Event::ENABLED_STATUS);
    })
    ->whereHas("users", function($sql) {
      $sql->where("users.id", Auth::id());
      $sql->whereHas("voucher_user");
    })
    ->leftJoin("events", function($join){
      $join->whereNull("events.deleted_at");
      $join->on("events.id", "vouchers.event_id");
    })
    ->select(
      "events.name as event_name",
      "vouchers.id",
      "vouchers.event_id",
      "vouchers.percentage_decrease",
      "vouchers.maximum_quantity",
      "vouchers.available_quantity",
      "vouchers.expiry_date"
    );
    
    if($req->has("voucher_id")){
      $sql->where("id", $req->voucher_id);
    }
    return $sql->lockForUpdate()->paginate($paginate);
  }
}