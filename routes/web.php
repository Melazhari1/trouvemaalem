<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Admin\LocaleController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;

// Admin locale switcher (outside locale-prefix group, auth checked by Filament)
Route::get('/admin/locale/{locale}', [LocaleController::class, 'switch'])
    ->where('locale', 'en|fr|ar')
    ->name('admin.locale.switch');

// Root redirect to default locale
Route::get('/', function () {
    return redirect('/fr');
});

// Sitemap index + per-locale sitemaps
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-{locale}.xml', [SitemapController::class, 'locale'])
    ->where('locale', 'fr|en|ar')
    ->name('sitemap.locale');

// Group all localized routes
Route::prefix('{locale}')->where(['locale' => 'en|fr|ar'])->group(function () {
    
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');

    Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/artisan/{slug}', [ArtisanController::class, 'show'])->name('artisan.show');
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/{service}-in-{city}', [SearchController::class, 'localSearch'])->name('search.local');
    Route::get('/api/map-data', [ArtisanController::class, 'mapData'])->name('api.map-data');
    Route::post('/api/contact/submit', [ContactFormController::class, 'submit'])->middleware('throttle:5,60')->name('api.contact.submit');
    Route::post('/api/artisans/{id}/reviews/submit', [ReviewController::class, 'submit'])->middleware('throttle:10,60')->name('api.review.submit');

});

// Fallback to automatically redirect old non-localized URLs (e.g. /categories/plumbing) to the default locale (/en/categories/plumbing)
Route::fallback(function () {
    $path = request()->path();
    return redirect('/en/' . $path);
});
