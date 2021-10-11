<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use App\Models\Event;
use App\Models\Voucher;
use App\Models\VoucherUser;
use Illuminate\Http\Request;
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

  public function saveEvent(Request $req) {
    \Log::info("EventApi: save the event");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        "name" => 'required',
        "description"=> 'required',
        "percentage_decrease" => 'required',
        "maximum_quantity" => 'required',
        "expiry_date" => 'required'
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      if($req->has("event_id")) {

        return response()->json([
          "success"=> true,
          "message" => "Update the event successfully!",
          "message_title" => "Successful"
        ], 200);
      }

      DB::beginTransaction();
      $newEvent = Event::saveEvent($req);
      if($newEvent) {
        $voucher = array_map(function($per, $quan, $date) use($newEvent) {
          return array(
            "event_id" => $newEvent->id,
            "percentage_decrease"=> $per,
            "maximum_quantity" => $quan,
            "expiry_date" => $date,
            "status" => Voucher::ENABLED_STATUS,
            "unique_code"=> CommonHelpers::getUniqueCode()
          );
        }, $req->percentage_decrease, $req->maximum_quantity, $req->expiry_date);
      }

      Voucher::insert($voucher);

      DB::commit();

      return response()->json([
        "success"=> true,
        "message" => "Save the event successfully!",
        "message_title" => "Successful"
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EventApi: can't save event", ['eror message' => $e->getMessage()]);
        report($e);
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}