<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;

use App\Models\Post;
use App\Models\ReaderCounter;
use App\Models\ReaderCounterDetail;


class ReaderCounterApi extends ApiBase {

  public function tracking(Request $req) {
    \Log::info("ReaderCounterApi: save the new post");
    try {

      $input = $req->all();
      $validator = Validator::make($input, [
        'post_id' => 'required',
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }
      $post = Post::where("deleted_at", null)->find($req->post_id);
      if(empty($post)) {
        return response() -> json([
          "success"=> false,
          "message_title" => "Request failed",
          "message" => "Can not get data, Please contact with administrator!",
          $post
        ]);
      }

      $readCounter = ReaderCounter::saveReaderCounter($post->id);
      ReaderCounterDetail::saveReaderCounterDetail($readCounter->id);
      
      return response() -> json([
        "success"=> true,
        "message_title" => "Successful",
        "message" => "Tracking the post successfully!",
      ]);


    } catch (\Exception $e) {
        \Log::error("ReaderCounterApi: can't track the post", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}