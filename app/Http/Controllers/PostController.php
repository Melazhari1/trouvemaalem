<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_published', true)->orderBy('created_at', 'desc')->get();
        return Inertia::render('Blog/Index', ['posts' => $posts]);
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)->where('is_published', true)->firstOrFail();
        return Inertia::render('Blog/Show', [
            'post'   => $post,
            'schema' => $this->getSchema($post),
        ]);
    }

    private function getSchema(Post $post): array
    {
        $baseUrl = config('app.url');
        $locale  = app()->getLocale();
        $pageUrl = "{$baseUrl}/{$locale}/blog/{$post->slug}";

        $articleNode = [
            '@type'         => 'BlogPosting',
            '@id'           => "{$pageUrl}#article",
            'headline'      => $post->title,
            'description'   => $post->excerpt,
            'datePublished' => $post->created_at->toIso8601String(),
            'dateModified'  => $post->updated_at->toIso8601String(),
            'url'           => $pageUrl,
            'inLanguage'    => $locale,
            'author'        => ['@id' => "{$baseUrl}/#organization"],
            'publisher'     => ['@id' => "{$baseUrl}/#organization"],
            'isPartOf'      => ['@id' => "{$baseUrl}/#website"],
            'breadcrumb'    => [
                '@type'           => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => "{$baseUrl}/{$locale}/"],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog',    'item' => "{$baseUrl}/{$locale}/blog"],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => $pageUrl],
                ],
            ],
        ];

        if ($post->image) {
            $imageUrl = str_starts_with($post->image, 'http')
                ? $post->image
                : Storage::disk('public')->url($post->image);
            $articleNode['image'] = ['@type' => 'ImageObject', 'url' => $imageUrl];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [$articleNode],
        ];
    }
}
