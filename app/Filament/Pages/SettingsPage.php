<?php

namespace App\Filament\Pages;

use App\Models\AdminSetting;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.settings-page';

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    public function mount(): void
    {
        $emails = AdminSetting::get('contact_notification_emails');

        $this->form->fill([
            'recaptcha_site_key'          => AdminSetting::get('recaptcha_site_key', ''),
            'recaptcha_secret_key'        => AdminSetting::get('recaptcha_secret_key', ''),
            'smtp_host'                   => AdminSetting::get('smtp_host', ''),
            'smtp_port'                   => AdminSetting::get('smtp_port', '587'),
            'smtp_username'               => AdminSetting::get('smtp_username', ''),
            'smtp_password'               => AdminSetting::get('smtp_password', ''),
            'mail_from_address'           => AdminSetting::get('mail_from_address', ''),
            'mail_from_name'              => AdminSetting::get('mail_from_name', 'trouvemaalem'),
            'google_tag_manager_id'       => AdminSetting::get('google_tag_manager_id', ''),
            'contact_notification_emails' => is_array($emails) ? implode("\n", $emails) : ($emails ?? ''),
            'site_title'                  => AdminSetting::get('site_title', ''),
            'site_description'            => AdminSetting::get('site_description', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('reCAPTCHA Configuration')
                    ->description('Get keys from google.com/recaptcha/admin. Leave blank to disable reCAPTCHA validation.')
                    ->schema([
                        TextInput::make('recaptcha_site_key')
                            ->label('Site Key (public)')
                            ->placeholder('6Le...'),
                        TextInput::make('recaptcha_secret_key')
                            ->label('Secret Key (private)')
                            ->password()
                            ->revealable()
                            ->placeholder('6Le...'),
                    ])
                    ->columns(2),

                Section::make('Email Configuration (SMTP)')
                    ->description('Gmail: smtp.gmail.com:587 · SendGrid: smtp.sendgrid.net:587 · AWS SES: email-smtp.{region}.amazonaws.com:587')
                    ->schema([
                        TextInput::make('smtp_host')
                            ->label('SMTP Host')
                            ->placeholder('smtp.gmail.com'),
                        TextInput::make('smtp_port')
                            ->label('SMTP Port')
                            ->placeholder('587'),
                        TextInput::make('smtp_username')
                            ->label('SMTP Username')
                            ->placeholder('your@email.com'),
                        TextInput::make('smtp_password')
                            ->label('SMTP Password')
                            ->password()
                            ->revealable()
                            ->placeholder('app-password'),
                        TextInput::make('mail_from_address')
                            ->label('From Email Address')
                            ->email()
                            ->placeholder('noreply@trouvemaalem.com'),
                        TextInput::make('mail_from_name')
                            ->label('From Name')
                            ->placeholder('trouvemaalem'),
                    ])
                    ->columns(2),

                Section::make('Analytics & Tracking')
                    ->description('Leave blank to disable Google Tag Manager.')
                    ->schema([
                        TextInput::make('google_tag_manager_id')
                            ->label('Google Tag Manager ID')
                            ->placeholder('GTM-XXXXXX'),
                    ]),

                Section::make('Contact Form Settings')
                    ->description('Optional email addresses to notify on new contact form submissions (one per line).')
                    ->schema([
                        Textarea::make('contact_notification_emails')
                            ->label('Notification Emails')
                            ->placeholder("admin@example.com\nteam@example.com")
                            ->rows(4),
                    ]),

                Section::make('Branding (Optional)')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Site Title')
                            ->placeholder('trouvemaalem'),
                        Textarea::make('site_description')
                            ->label('Site Description')
                            ->placeholder("Connecting you with Morocco's finest artisans.")
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('settings-form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Save Settings')
                            ->submit('save'),
                        Action::make('sendTestEmail')
                            ->label('Send Test Email')
                            ->color('gray')
                            ->action('sendTestEmail'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $emailLines = array_filter(array_map('trim', explode("\n", $data['contact_notification_emails'] ?? '')));
        $emailLines = array_values(array_filter($emailLines, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));

        AdminSetting::set('recaptcha_site_key',          $data['recaptcha_site_key'] ?? '');
        AdminSetting::set('recaptcha_secret_key',        $data['recaptcha_secret_key'] ?? '');
        AdminSetting::set('smtp_host',                   $data['smtp_host'] ?? '');
        AdminSetting::set('smtp_port',                   $data['smtp_port'] ?? '587');
        AdminSetting::set('smtp_username',               $data['smtp_username'] ?? '');
        AdminSetting::set('smtp_password',               $data['smtp_password'] ?? '');
        AdminSetting::set('mail_from_address',           $data['mail_from_address'] ?? '');
        AdminSetting::set('mail_from_name',              $data['mail_from_name'] ?? 'trouvemaalem');
        AdminSetting::set('google_tag_manager_id',       $data['google_tag_manager_id'] ?? '');
        AdminSetting::set('contact_notification_emails', json_encode($emailLines));
        AdminSetting::set('site_title',                  $data['site_title'] ?? '');
        AdminSetting::set('site_description',            $data['site_description'] ?? '');

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $fromAddress = $this->data['mail_from_address'] ?? '';
        /** @var User|null $authUser */
        $authUser   = \Illuminate\Support\Facades\Auth::user();
        $adminEmail = $authUser?->email;

        if (! $fromAddress || ! $adminEmail) {
            Notification::make()
                ->title('Configure a From Address first')
                ->danger()
                ->send();

            return;
        }

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'This is a test email from the trouvemaalem admin panel.',
                function ($msg) use ($adminEmail, $fromAddress) {
                    $msg->to($adminEmail)
                        ->from($fromAddress, $this->data['mail_from_name'] ?: 'trouvemaalem')
                        ->subject('Test Email — trouvemaalem');
                }
            );

            Notification::make()
                ->title("Test email sent to {$adminEmail}")
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
