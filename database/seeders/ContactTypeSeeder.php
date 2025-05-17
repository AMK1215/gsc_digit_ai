<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Email', 'image' => 'email.png'],
            ['name' => 'Phone', 'image' => 'phone.png'],
            ['name' => 'Telegram', 'image' => 'telegram.png'],
            ['name' => 'Facebook', 'image' => 'facebook.png'],
            ['name' => 'Instagram', 'image' => 'instagram.png'],
            ['name' => 'Twitter', 'image' => 'twitter.png'],
            ['name' => 'LinkedIn', 'image' => 'linkedin.png'],
            ['name' => 'WhatsApp', 'image' => 'whatsapp.png'],
            ['name' => 'Skype', 'image' => 'skype.png'],
            ['name' => 'Viber', 'image' => 'viber.png'],
            ['name' => 'Snapchat', 'image' => 'snapchat.png'],
            ['name' => 'TikTok', 'image' => 'tiktok.png'],
            ['name' => 'Line', 'image' => 'line.png'],
            ['name' => 'Discord', 'image' => 'discord.png'],
        ];
        foreach ($types as $type) {
            \App\Models\ContactType::create($type);
        }
    }
}
