<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Collection;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();
        return Inertia::render('Faq', [
            'faqs'   => $faqs,
            'schema' => $this->getSchema($faqs),
        ]);
    }

    private function getSchema(Collection $faqs): array
    {
        $baseUrl = config('app.url');
        $locale  = app()->getLocale();
        $pageUrl = "{$baseUrl}/{$locale}/faq";

        return [
            '@context' => 'https://schema.org',
            '@graph'   => [
                [
                    '@type'       => 'FAQPage',
                    '@id'         => "{$pageUrl}#webpage",
                    'url'         => $pageUrl,
                    'name'        => 'FAQ — Questions Fréquentes | TrouveMaalem',
                    'description' => "Trouvez les réponses à vos questions sur TrouveMaalem, la plateforme d'artisans au Maroc.",
                    'inLanguage'  => $locale,
                    'isPartOf'    => ['@id' => "{$baseUrl}/#website"],
                    'breadcrumb'  => [
                        '@type'           => 'BreadcrumbList',
                        'itemListElement' => [
                            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => "{$baseUrl}/{$locale}/"],
                            ['@type' => 'ListItem', 'position' => 2, 'name' => 'FAQ',     'item' => $pageUrl],
                        ],
                    ],
                    'mainEntity' => $faqs->map(fn ($f) => [
                        '@type' => 'Question',
                        'name'  => $f->question,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => $f->answer,
                        ],
                    ])->toArray(),
                ],
            ],
        ];
    }
}
