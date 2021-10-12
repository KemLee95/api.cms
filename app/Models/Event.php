<?php
namespace App\Models;

use Illuminate\Support\Facades\Auth;

class Event extends ModelBase {

  public $connection = 'mysql';
  public $table = 'events';
  public $timestap = true;
  
  const DISABLED_STATUS = 'disabled';
  const ENABLED_STATUS = 'enabled';

  protected $fillable = ['name', 'description', 'status'];

  protected $datas = [
    "creatd_at", "updated_at", "deleted_at"
  ];

  public function vouchers() {
    return $this->hasMany(Voucher::class, 'event_id', 'id')->where("deleted_at", null);
  }

  public static function getEventList($req, $paginate = 10) {
    if($req->has("paginate")) {
      $paginate = $req->paginate;
    }

    $eventSql = Event::where("deleted_at", null);

    return $eventSql->paginate($paginate);
  }

  public static function getEventDetail($id) {
    $eventSql = Event::where("events.deleted_at", null)
    ->select(
      "events.id",
      "events.name",
      "events.description",
      "events.status",
      'events.created_at',
    )
    ->addSelect(\DB::raw("(SELECT COUNT(*) FROM vouchers WHERE vouchers.event_id = events.id AND vouchers.deleted_at IS NULL) as voucher_total"));
    return $eventSql->find($id);
  }

  public static function getEventPartial($req, $paginate = 1) {
    if($req->has('paginate')) {
      $paginate = $req->paginate;
    }

    $sql = Event::whereNull("events.deleted_at")->where("events.status", Event::ENABLED_STATUS)
    ->select("events.id", "events.name")
    ->with("vouchers", function($sql){
      $sql->where("vouchers.status", Voucher::ENABLED_STATUS);
      $sql->with("users", function($sql){
        $sql->select("users.id");
        $sql->where("users.id", Auth::id());
      });
      $sql->select(
        "vouchers.id",
        "vouchers.event_id",
        "vouchers.percentage_decrease",
        "vouchers.maximum_quantity",
        "vouchers.available_quantity",
        "vouchers.status",
        "vouchers.unique_code",
      );
    });

    return $sql->paginate($paginate);
  }
}