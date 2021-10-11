<?php
namespace App\Models;

class Event extends ModelBase {

  public $connection = 'mysql';
  public $table = 'events';
  public $timestap = true;

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
      'events.created_at',
    )
    ->addSelect(\DB::raw("(SELECT COUNT(*) FROM vouchers WHERE vouchers.event_id = events.id AND vouchers.deleted_at IS NULL) as voucher_total"));
    return $eventSql->find($id);
  }

  public static function saveEvent($req) {
    $sql = new Event();
    $sql->name = $req->name;
    $sql->description = $req->description;
    $sql->save();

    return $sql;
  }
}