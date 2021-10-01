<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder {

  public function run() {

    DB::table('post_status')->insert([
    [
        'post_id' => 1,
        'name' => 'published',
        "created_at" => now(),
        "updated_at" => now(),
    ],
    [
        'post_id' => 2,
        'name' => 'unpublished',
        "created_at" => now(),
        "updated_at" => now(),
    ],
    [
        'post_id' => 3,
        'name' => 'published',
        "created_at" => now(),
        "updated_at" => now(),
    ],
    [
        'post_id' => 4,
        'name' => 'published',
        "created_at" => now(),
        "updated_at" => now(),
    ],
    [
        'post_id' => 5,
        'name' => 'published',
        "created_at" => now(),
        "updated_at" => now(),
    ],
    [
        'post_id' => 6,
        'name' => 'published',
        "created_at" => now(),
        "updated_at" => now(),
    ],
    ]);

    DB::table('posts')->insert([
        [
            'category_id' => 1,
            'user_id'=> 6,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'category_id' => 2,
            'user_id'=> 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'category_id' => 3,
            'user_id'=> 3,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'category_id' => 4,
            'user_id'=> 4,
            'created_at' => now(),
            'updated_at' => now(),
        ],

        [
            'category_id' => 5,
            'user_id'=> 1,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'category_id' => 6,
            'user_id'=> 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    ]);
    
    DB::table('post_detail')->insert([
      [
          'post_id' => 1,
          'category_id' => 1,
          'title' => 'Yêu Nhiều vs Yêu Bừa',
          'content' => 'Hôm vừa rồi có một bạn có inbox mình và hỏi một câu mình nghĩ là khá hay nên đã xin phép lấy làm chủ đề cho bài viết của ngày hôm nay....',
          'created_at' => now(),
          'updated_at' => now(),
      ],
      [
          'post_id' => 2,
          'category_id' => 1,
          'title' => 'Điểm 10 văn, em là thực hay là mơ?',
          'content' => 'Dạo gần đây có vụ lùm xùm về việc một em học sinh được 10 điểm văn lại so sánh hình ảnh của “Đại gia Gatsby” trong tác phẩm cùng tên...Dạo gần đây có vụ lùm xùm về việc một em học sinh được 10 điểm văn lại so sánh hình ảnh của “Đại gia Gatsby” trong tác phẩm cùng tên...',
          'created_at' => now(),
          'updated_at' => now(),
      ],
      [
          'post_id' => 3,
          'category_id' => 2,
          'title' => 'Game hóa và Văn hóa doanh nghiệp',
          'content' => 'Lựa chọn kĩ thuật Game hóa phù hợp với văn hóa doanh nghiệp',
          'created_at' => now(),
          'updated_at' => now(),
      ],
      [
          'post_id' => 4,
          'category_id' => 3,
          'title' => 'Hãy mạnh dạn từ bỏ những thứ không quan trọng, và tập trung vào những thứ có ý nghĩa với bản thân bạn. Hãy mong mình sống tốt, chứ không phải sống lâu!',
          'content' => 'Như đã giới thiệu trong series, Seneca thực sự là nguồn cảm hứng Stoicism của mình. Đọc Seneca không chỉ là về triết học...',
          'created_at' => now(),
          'updated_at' => now(),
      ],
      [
          'post_id' => 5,
          'category_id' => 1,
          'title' => 'Chúng ta có phải một thế hệ lười yêu?',
          'content' => 'Tình yêu là câu chuyện hai người gặp nhau, nảy sinh tình cảm và cố gắng cho nhau những tình cảm chân thật nhất. Gặp được nhau là cái duyên, nhưng ở bên nhau được hay không đó lại là cả một quá trình.',
          'created_at' => now(),
          'updated_at' => now(),
      ],
      [
          'post_id' => 6,
          'category_id' => 4,
          'title' => '[Vật Lý Cơ Bản] Einstein vs Free Will',
          'content' => 'Quà nghỉ lễ của các bạn đây. Sorry vì bây giờ mình mới post tại mình well... cũng phải nghỉ lễ chứ =)) đây là 1 liều thuốc hại não...',
          'created_at' => now(),
          'updated_at' => now(),
      ]
    ]);

    DB::table('categories')->insert([
        [
            'name'=> 'Quan điểm - Tranh luận',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name'=> 'KHOA HỌC - CÔNG NGHỆ',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name'=> 'Truyền Cảm Hứng',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name'=> 'SCIENCE2VN',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
  }
}