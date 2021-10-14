<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutgoingEmailTracking extends ModelBase {
  
  use HasFactory;

  public $connection = 'mysql';
  public $table = 'outgoing_email_tracking';

  const PENDING_STATUS = 'pending';
  const SENDING_STATUS = 'sending';
  const DONE_STATUS = 'done';
  const ERROR_STATUS = 'error';

  protected $fillable = [
    'emmail',
    'status',
  ];

  public function user() {
    return $this->hasOne(User::class, 'email', 'email');
  }
}