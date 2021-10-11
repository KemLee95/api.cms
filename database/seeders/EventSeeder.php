<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder {
  public function run() {
    DB::table('events')->insert([
      [
        "name" => "siêu sale 10.10",
        "description"=> "Chương trình đem đến hàng triệu voucher miễn phí vận chuyển, mua sắm hàng hiệu từ 100.000 đồng, cơ hội trúng xe hơi cùng nhiều ưu đãi từ các thương hiệu hàng đầu.",
        "created_at" => now(),
        "updated_at" => now(),
      ]
    ]);

    DB::table('vouchers')->insert([
      [
        "event_id" => 1,
        "status" => "enabled",
        "percentage_decrease" => 10,
        "maximum_quantity" => 50,
        "expiry_date" => now()->addDay(30),
        "unique_code"=> strval(uniqid()),
        "created_at" => now(),
        "updated_at" => now()
      ],
      [
        "event_id" => 1,
        "status" => "enabled",
        "percentage_decrease" => 25,
        "maximum_quantity" => 20,
        "expiry_date" => now()->addDay(30),
        "unique_code"=> strval(uniqid()),
        "created_at" => now(),
        "updated_at" => now()
      ], 
      [
        "event_id" => 1,
        "status" => "enabled",
        "percentage_decrease" => 45,
        "maximum_quantity" => 10,
        "expiry_date" => now()->addDay(30),
        "unique_code"=> strval(uniqid()),
        "created_at" => now(),
        "updated_at" => now()
      ],
    ]);

    DB::table('voucher_user')->insert([
      [
        'voucher_id' => 1,
        'user_id' => 14,
        "status" => 'enabled',
        "created_at" => now(),
        "updated_at" => now()
      ],
      [
        'voucher_id' => 2,
        'user_id' => 18,
        "status" => 'enabled',
        "created_at" => now(),
        "updated_at" => now()
      ],
      [
        'voucher_id' => 3,
        'user_id' => 17,
        "status" => 'enabled',
        "created_at" => now(),
        "updated_at" => now()
      ],
    ]);
  }
}