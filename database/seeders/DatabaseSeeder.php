<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ComparisonCriteriaSeeder::class,
            CatalogSeeder::class,
        ]);

        // حساب مشرف للتطوير — غيّر كلمة المرور قبل أي عرض أو نشر
        if (! User::where('email', 'admin@example.com')->exists()) {
            $admin = new User();
            $admin->forceFill([
                'name' => 'المشرف',
                'email' => 'admin@example.com',
                'password' => 'password', // يُشفَّر تلقائيًا عبر cast من نوع hashed في موديل User
                'is_admin' => true,
            ])->save();

            $this->command->info('تم إنشاء حساب المشرف: admin@example.com / password');
        }
    }
}
