<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // 头像假数据
        $avatars = [
            'https://iocaffcdn.phphub.org/uploads/avatars/17854_1541253700.jpg!/both/200x200',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/NDnzMutoxX.png?imageView2/1/w/200/h/200',
        ];

        $names = [
            'Everan', 'admin'
        ];

        $phones = [
            '18818801234', '18818808888'
        ];

        $remark = [
            '超级管理员', '管理员'
        ];

        $identify = [1, 2];

        // 生成数据集合
        $users = factory(User::class)
            ->times(2)
            ->make()
            ->each(function ($user, $index)
            use ($avatars, $names, $phones, $remark, $identify) {
                $user->avatar = $avatars[$index];
                $user->name = $names[$index];
                $user->phone = $phones[$index];
                $user->remark = $remark[$index];
                $user->identify = $identify[$index];
            });

        $user_array = $users->makeVisible(['password'])->toArray();

        User::query()->insert($user_array);
    }
}
