<?php
namespace App\Http\Controllers;

use App\Http\Controllers\ApiBase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\Post;

class PostsBeingEditedApi extends ApiBase {

  public function edited(Request $req) {
    \Log::info("ReaderCounterApi: track the post");
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

  public function editable(Request $req) {
    \Log::info("ReaderCounterApi: track the post");
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