<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use App\Models\Event;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUser;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;

class EventApi extends ApiBase {

  public function getEventList(Request $req) {
    \Log::info("EventApi: get the event detail");
    try {
      $input = $req->all();
      $data = Event::getEventList($req);

      return response()->json([
        "success"=> true,
        "data" => $data
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EventApi: can't get the event detail", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getEventDetail(Request $req) {
    \Log::info("EventApi: get the event list");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'event_id' => 'required|numeric'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $event = Event::getEventDetail($req->event_id);
      if(!$event){
        return response()->json([
          "success" => false,
          "message" => "The event does not exist",
          "message_title" => "Request failed"
        ], 400);
      }

      return response()->json([
        "success"=> true,
        "event" => $event
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EventApi: can't get the event list", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getVoucherPartial(Request $req) {
    \Log::info("EventApi: get the voucher for an partial");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'event_id' => 'required|numeric'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $data = Voucher::getVoucherPartial($req);

      return response()->json([
        "success"=> true,
        "data" => $data
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EventApi: can't the voucher for an partial", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getVoucherDetail(Request $req) {
    \Log::info("EventApi: get the voucher detail");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'voucher_id' => 'required|numeric'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $voucher = Voucher::getVoucherDetail($req);

      return response()->json([
        "success"=> true,
        "voucher" => $voucher
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EventApi: can't the voucher detail", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function getVoucherUsers(Request $req) {
    \Log::info("EventApi: get the voucher-users");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'voucher_id' => 'required|numeric'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $data = VoucherUser::getVoucherUsers($req);

      return response()->json([
        "success"=> true,
        "data" => $data
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EventApi: can't the voucher-user", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function countEnabledEvents(Request $req) {
    \Log::info("EventApi: get the voucher-users");
    try {
      $input = $req->all();

      $total = Event::lockForUpdate()->whereNull("deleted_at")->where("status", Event::ENABLED_STATUS)->count();

      return response()->json([
        "success"=> true,
        "total" => $total
      ], 200);

    } catch (\Exception $e) {
      \Log::error("EventApi: can't the voucher-user", ['eror message' => $e->getMessage()]);
      report($e);
      return response()->json([
          'success' => false,
          'message' => 'An error occurred, please contact with administrator!',
          'message_title' => "Request failed"
      ], 400 );
    }
  }

  public function getEventPartial(Request $req) {
    \Log::info("EventApi: get the voucher-users");
    try {
      $input = $req->all();
      $data = Event::getEventPartial($req);
      return response()->json([
        "success"=> true,
        "data" => $data
      ], 200);

    } catch (\Exception $e) {
      \Log::error("EventApi: can't the voucher-user", ['eror message' => $e->getMessage()]);
      report($e);
      return response()->json([
          'success' => false,
          'message' => 'An error occurred, please contact with administrator!',
          'message_title' => "Request failed"
      ], 400 );
    }
  }

  public function getVoucherList(Request $req) {
    \Log::info("EventApi: get the voucher-users");
    try {
      $input = $req->all();
      $data = Voucher::getVoucherList($req);
      return response()->json([
        "success"=> true,
        "data" => $data
      ], 200);

    } catch (\Exception $e) {
      \Log::error("EventApi: can't the voucher-user", ['eror message' => $e->getMessage()]);
      report($e);
      return response()->json([
          'success' => false,
          'message' => 'An error occurred, please contact with administrator!',
          'message_title' => "Request failed"
      ], 400 );
    }
  }

  public function getVoucherForUser(Request $req) {
    \Log::info("EventApi: get the voucher-users");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'voucher_id' => 'required|numeric'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }
      
      $voucher = Voucher::whereNull("deleted_at")
      ->where("status", Voucher::ENABLED_STATUS)
      ->where("id", $req->voucher_id)
      ->whereHas("event", function($sql){
        $sql->whereNull("deleted_at");
        $sql->where("status", Event::ENABLED_STATUS);
      })
      ->lockForUpdate()
      ->get();

      if(!count($voucher)) {
        return response()->json([
          "success" => false,
          "message" => "The post can not access",
          "message_title" => "Request failed"
        ], 406);
      }

      $event = Event::whereNull("deleted_at")
      ->where("status", Event::ENABLED_STATUS)
      ->whereHas("vouchers", function($sql) use($req) {
        $sql->whereNull("deleted_at");
        $sql->where("id", $req->voucher_id);
      })
      ->select("name")->first();

      $checkExist = VoucherUser::whereNull("deleted_at")
      ->where("voucher_id", $req->voucher_id)
      ->where("user_id", Auth::id())
      ->get();

      if(count($checkExist)) {
        return response() -> json([
          "success" => false,
          "message" => "You can only get 1 and only 1 voucher types in " . strtoupper($event->name),
          "message_title" => "Request failed"
        ], 406);
      }

      $available = Voucher::lockForUpdate()->whereNull("deleted_at")
      ->where("id",$req->voucher_id)->pluck("available_quantity")->first();
      if($available == 0) {
        return response()->json([
          "success" => false,
          "message" => "The available total value of the voucher is to be used. There is no more available voucher.",
          "message_title" => "Request failed"
        ], 406);
      }
      $newVoucherUser = array(
        "voucher_id" => $req->voucher_id,
        "user_id" => Auth::id(),
        "status" => VoucherUser::ENABLED_STATUS,
        "created_at" => now(),
        "updated_at" => now()
      );

      DB::transaction(function () use($req, $newVoucherUser) {

        Voucher::sharedLock()->whereNull("deleted_at")->find($req->voucher_id)->decrement("available_quantity", 1);
        DB::table('voucher_user')->sharedLock()->upsert($newVoucherUser, [
          "voucher_id", "user_id", "status"
        ]);
      });

      return response()->json([
        "success"=> true,
        "message"=> "You have been gotten the voucher",
        "message_title" => "Successful"
      ], 200);

    } catch (\Exception $e) {
      \Log::error("EventApi: can't the voucher-user", ['eror message' => $e->getMessage()]);
      report($e);
      return response()->json([
          'success' => false,
          'message' => 'An error occurred, please contact with administrator!',
          'message_title' => "Request failed"
      ], 400 );
    }
  }

  public function saveEvent(Request $req) {
    \Log::info("EventApi: save the event");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        "name" => 'required',
        "description"=> 'required',
        "status" => 'required',
        "percentage_decrease" => 'required',
        "maximum_quantity" => 'required',
        "expiry_date" => 'required',
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      if($req->has("event_id")) {
        $event = Event::where("deleted_at", null)->find($req->event_id);
        if(!$event) {
          return response()->json([
            "success" => false,
            "message" => "The event does not exist",
            "message_title" => "Request failed"
          ], 400);
        }

        if($req->status != Event::ENABLED_STATUS) {
          $check = Event::where([
            ["deleted_at" ,"=", null],
            ["id", "=", $req->event_id]
          ])
          ->whereHas("vouchers", function($sql){
            $sql->where("deleted_at", null);
            $sql->whereHas("users");
          })->get();
          if(count($check)) {
            return response()->json([
              "success" => false,
              "message" => "The event have been usabled, Can't change the status of the event",
              "message_title" => "Request failed"
            ], 400);
          }
        }
        
        $oldVoucher = Voucher::whereNull("deleted_at")
        ->where("event_id", $req->event_id)->pluck("id");
        
        $voucherId = $req->has("voucher_id") ? $req->voucher_id : [];
        $updateEvent = array(
          "name" => $req->name,
          "description"=>$req->description,
          "status"=> $req->status
        );
        
        DB::beginTransaction();
        for($idx = 0; $idx < count($oldVoucher); $idx ++){
          $id = $oldVoucher[$idx];
          $voucherItem = Voucher::lockForUpdate()->whereNull("deleted_at")->find($id);
          $countUser = VoucherUser::lockForUpdate()->whereNull("deleted_at")
          ->where("voucher_id", $id)->count();
          if(!in_array($id, $voucherId)) {
            if($countUser) {
              return response()->json([
                "success" => false,
                "message" => "The voucher has been used, can delete the voucher",
                "message_title" => "Reuqest failed"
              ]);
            } else {
              // Delete
              Voucher::whereNull("deleted_at")->find($id)->delete();
            }
          } else {
            // Update
            $position = array_search($id, $voucherId);
            if(($req->percentage_decrease[$position] != $voucherItem->percentage_decrease) && $countUser) {
              DB::rollBack();
              return response()->json([
                "success" => false,
                "message" => "The voucher has been used, can change the percentage decrease of the voucher",
                "message_title" => "Reuqest failed"
              ]);
            }

            if(($req->maximum_quantity[$position] <= ($voucherItem->maximum_quantity - $voucherItem->available_quantity)) && $countUser) {
              DB::rollBack();
              return response()->json([
                "success" => false,
                "message" => "The voucher has been used, can reduce the quanlity of the voucher",
                "message_title" => "Reuqest failed"
              ]);
            }

            if(date($req->expiry_date[$position]) < now()) {
              DB::rollBack();
              return response() -> json([
                "success" => false,
                "message" => "The expiry date is invalid",
                "message_title" => "Reuqest failed"
              ], 400);
            }
            
            $update = array(
              "percentage_decrease" => $req->percentage_decrease[$position],
              "maximum_quantity" => $req->maximum_quantity[$position],
              "available_quantity"=> $req->maximum_quantity[$position] - $countUser,
              "expiry_date" => $req->expiry_date[$position],
            );

            $voucherItem->update($update);
          }
        }

        if(count($req->percentage_decrease) > count($voucherId)) {

          $position = count($voucherId);
          $per = array_slice($req->percentage_decrease, $position);
          $max = array_slice($req->maximum_quantity, $position);
          $expiry = array_slice($req->expiry_date, $position);
          $new = array_map(function($perParam, $maxPram, $expiryPram) use($req) {
            return array(
              "event_id" => $req->event_id,
              "percentage_decrease" => $perParam,
              "maximum_quantity" => $maxPram,
              "available_quantity" => $maxPram,
              "expiry_date" => $expiryPram,
              "status" => Voucher::ENABLED_STATUS,
              "unique_code"=> CommonHelpers::getUniqueCode(),
              "created_at" => now(),
              "updated_at" => now(),
            );
          }, $per, $max, $expiry);

          Voucher::insert($new);
        }

        Event::where([
          ["deleted_at" ,"=", null],
          ["id", "=", $req->event_id]
        ])->update($updateEvent);

        DB::commit();

        return response()->json([
          "success"=> true,
          "message" => "Update the event successfully!",
          "message_title" => "Successful"
        ], 200);
      }

      DB::beginTransaction();

      $newEvent = array(
        "name" =>$req->name,
        "description"=> $req->description,
        "status" => $req->status
      );
      $newEventlSql = Event::create($newEvent);
      
      $newVoucher = array_map(function($per, $quan, $date) use($newEventlSql) {
        return array(
          "event_id" => $newEventlSql->id,
          "percentage_decrease"=> $per,
          "maximum_quantity" => $quan,
          "available_quantity" => $quan,
          "expiry_date" => $date,
          "status" => Voucher::ENABLED_STATUS,
          "unique_code"=> CommonHelpers::getUniqueCode(),
          "created_at" => now(),
          "updated_at" => now(),
        );
      }, $req->percentage_decrease, $req->maximum_quantity, $req->expiry_date);
      
      Voucher::insert($newVoucher);

      DB::commit();

      return response()->json([
        "success"=> true,
        "message" => "Save the event successfully!",
        "message_title" => "Successful"
      ], 200);

    } catch (\Exception $e) {
      DB::rollBack();
      \Log::error("EventApi: can't save event", ['eror message' => $e->getMessage()]);
      report($e);
      return response()->json([
          'success' => false,
          'message' => 'An error occurred, please contact with administrator!',
          'message_title' => "Request failed"
      ], 400);
    }
  }
}