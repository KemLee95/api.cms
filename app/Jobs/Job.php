<?php
namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobBase extends ShouldQueue {
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public function handle() {
    
    $this->before();
    try {

      $this->run();
      $this->whenSucceeded();

    } catch (Exception $exception) {

      $this->whenFailed();
    } finally {

      $this->after();
    }
  }
  
  public function run() {

  }

  public function before() {

  }

  public function after() {

  }

  public function whenSucceeded() {

  }

  public function whenFailed() {
      
  }
}