<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'trouvemaalem') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Google Tag Manager -->
        @php $gtmId = \App\Models\AdminSetting::get('google_tag_manager_id', ''); @endphp
        @if($gtmId)
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
        @endif

        <!-- reCAPTCHA v3 -->
        @if(!empty($page['props']['recaptchaSiteKey']))
        <script src="https://www.google.com/recaptcha/api.js?render={{ $page['props']['recaptchaSiteKey'] }}" async defer></script>
        @endif

        <!-- Scripts -->
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        <!-- Structured Data (server-side for crawlers / validators) -->
        @if(!empty($page['props']['schema']))
        <script type="application/ld+json">{!! json_encode($page['props']['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
        @endif
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-50">
        <!-- Google Tag Manager (noscript) -->
        @if($gtmId)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        @endif

        @inertia
    </body>
</html>
