<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->enum('type', ['string', 'boolean', 'json'])->default('string');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $defaults = [
            ['key' => 'recaptcha_site_key',             'value' => null,            'type' => 'string',  'description' => 'reCAPTCHA v3 site key (public)'],
            ['key' => 'recaptcha_secret_key',            'value' => null,            'type' => 'string',  'description' => 'reCAPTCHA v3 secret key (private)'],
            ['key' => 'smtp_host',                       'value' => null,            'type' => 'string',  'description' => 'SMTP server hostname'],
            ['key' => 'smtp_port',                       'value' => '587',           'type' => 'string',  'description' => 'SMTP server port (usually 587 or 465)'],
            ['key' => 'smtp_username',                   'value' => null,            'type' => 'string',  'description' => 'SMTP username / email'],
            ['key' => 'smtp_password',                   'value' => null,            'type' => 'string',  'description' => 'SMTP password or app password'],
            ['key' => 'mail_from_address',               'value' => null,            'type' => 'string',  'description' => 'From email address for outgoing mail'],
            ['key' => 'mail_from_name',                  'value' => 'trouvemaalem', 'type' => 'string',  'description' => 'From name for outgoing mail'],
            ['key' => 'google_tag_manager_id',           'value' => null,            'type' => 'string',  'description' => 'Google Tag Manager container ID (e.g. GTM-XXXXXX)'],
            ['key' => 'contact_notification_emails',     'value' => null,            'type' => 'json',    'description' => 'Emails to notify on new contact form submissions (JSON array)'],
            ['key' => 'site_title',                      'value' => null,            'type' => 'string',  'description' => 'Optional override for site title'],
            ['key' => 'site_description',                'value' => null,            'type' => 'string',  'description' => 'Optional override for site description'],
        ];

        foreach ($defaults as $setting) {
            \DB::table('admin_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
