<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as ControllerBase;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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
        "message" => "Finised sent the varification email!",
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

  public function verify(EmailVerificationRequest $req) {
    
    \Log::info("EmailVerificationController: verifyl");
    try {
      if(Auth::user()->hasVerifiedEmail()){
        return response() -> json([
          "success" => true,
          "message" => "Already Verified!",
          "message_title" => "Successful!"
        ], 200);
      }
      if(Auth::user()->markEmailAsVerified()) {
        event(new Verified(Auth::user()));
      }

      return response() -> json([
        "success" => true,
        "message" => "Finised varified for the email",
        "message_title" => "Successful!"
      ], 200);

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