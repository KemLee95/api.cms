<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            [
                'category_id' => 1,
                'user_id'=> 1,
                'title' => 'Yêu Nhiều vs Yêu Bừa',
                'content' => 'Hôm vừa rồi có một bạn có inbox mình và hỏi một câu mình nghĩ là khá hay nên đã xin phép lấy làm chủ đề cho bài viết của ngày hôm nay....',
                'status' => 'unpublished',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 1,
                'user_id'=> 2,
                'title' => 'Điểm 10 văn, em là thực hay là mơ?',
                'content' => 'Dạo gần đây có vụ lùm xùm về việc một em học sinh được 10 điểm văn lại so sánh hình ảnh của “Đại gia Gatsby” trong tác phẩm cùng tên...Dạo gần đây có vụ lùm xùm về việc một em học sinh được 10 điểm văn lại so sánh hình ảnh của “Đại gia Gatsby” trong tác phẩm cùng tên...',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'user_id'=> 1,
                'title' => 'Game hóa và Văn hóa doanh nghiệp',
                'content' => 'Lựa chọn kĩ thuật Game hóa phù hợp với văn hóa doanh nghiệp',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'user_id'=> 1,
                'title' => 'Dịch Seneca (22): Hãy mạnh dạn từ bỏ những thứ không quan trọng, và tập trung vào những thứ có ý nghĩa với bản thân bạn. Hãy mong mình sống tốt, chứ không phải sống lâu!',
                'content' => 'Như đã giới thiệu trong series, Seneca thực sự là nguồn cảm hứng Stoicism của mình. Đọc Seneca không chỉ là về triết học...',
                'status' => 'published',
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
            ]
        ]);

        DB::table('permissions')->insert([
            ['name' => 'review_post'],
            ['name' => 'update_post'],
            ['name' => 'delete_post'],
            ['name' => 'restore_post'],
            ['name' => 'force_delete_post'],
        ]);

        DB::table('roles')->insert([
            ['name' => 'admin'],
            ['name' => 'user'],
        ]);

        DB::table('role_user')->insert([
                ['role_id' => 1,'user_id' => 1],
                ['role_id' => 2,'user_id' => 2]
            ]
        );

        DB::table('permission_role')->insert([
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 2, 'role_id' => 1],
            ['permission_id' => 3, 'role_id' => 1],
            ['permission_id' => 4, 'role_id' => 1],
            ['permission_id' => 5, 'role_id' => 1],
        ]);

    }
}
