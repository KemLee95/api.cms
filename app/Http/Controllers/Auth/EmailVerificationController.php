<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as ControllerBase;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class EmailVerificationController extends ControllerBase {

  public function sendVerificationEmail(Request $req) {

    \Log::info("EmailVerificationController: send the verification email");
    try {
      if(Auth::user()->hasVerifiedEmail()){
        return response() -> json([
          "success" => true,
          "message" => "Already Verified!",
          "message_title" => "Successful!"
        ], 200);
      }

      Auth::user()->sendEmailVerificationNotification();

      return response() -> json([
        "success" => true,
        "message" => "Finised sent the varification email, please check your email",
        "message_title" => "Successful!"
      ], 200);

    } catch (\Exception $e) {
        \Log::error("EmailVerificationController: can't send the verification email", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function verify($id, $hash, Request $req) {
    
    \Log::info("EmailVerificationController: verify");
    try {
      $user = User::where("deleted_at", null)->find($id);

      if (! hash_equals((string) $hash,
        sha1($user->getEmailForVerification()))) {

        return response() ->json([
          "success" => false,
          'message' => 'An error occurred, please contact with administrator!',
          'message_title' => "Request failed"
        ]);
      }

      if($user->hasVerifiedEmail()){
        return view("success")->with("message", "Already Verified!");
      }

      if($user->markEmailAsVerified()) {
        event(new Verified(Auth::user()));
      }

      return view("success")->with("message", "Verification successful!");

    } catch (\Exception $e) {
        \Log::error("EmailVerificationController: can't verify", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}