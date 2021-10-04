<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as ControllerBase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Validator;

class NewPasswordController extends ControllerBase {

  public function forgotPassword(Request $req) {

    \Log::info("NewPasswordController: handle forgot password");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'email' => 'required',
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $status = Password::sendResetLink($req->only("email"));
      if($status == Password::RESET_LINK_SENT) {
        return response() -> json([
          "status" => __($status)
        ],200);
      }

      throw ValidationException::withMessages([
        'email' => [trans($status)],
      ]);

    } catch (\Exception $e) {
        \Log::error("NewPasswordController: can't handel forgot password", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }

  public function reset(Request $req) {

    \Log::info("NewPasswordController: reset password");
    try {
      $input = $req->all();
      $validator = Validator::make($input, [
        'token' => 'required',
        'email' => 'required',
        'password' => ['required', 'confirmed', RulesPassword::defaults()]
      ]);
      if($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors'=> $validator->errors()->toArray(),
        ], 401);
      }

      $status = Password::reset($req->only('email', 'password', 'password_confirmation', 'token'), function($user) use($req) {
        $user->forceFill([
          'password' => Hash::make($request->password),
          'remember_token' => Str::random(60),
        ])->save();
        $user->token()->delete();

        event(new PasswordReset($user));
      });

      if ($status == Password::PASSWORD_RESET) {
        return response() ->json([
          "success" => true,
          "message_title" => "Successful",
          'message'=> 'Password reset successfully'
        ], 200);
      }

      return response([
        "success" => false,
        'message'=> __($status),
        "message_title" => "Request failed"
      ], 500);

    } catch (\Exception $e) {
        \Log::error("NewPasswordController: can't reset password", ['eror message' => $e->getMessage()]);
        report($e);
        return response()->json([
            'success' => false,
            'message' => 'An error occurred, please contact with administrator!',
            'message_title' => "Request failed"
        ], 400 );
    }
  }
}