<?php

namespace Database\Seeders;

use App\Models\Artisan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $users = User::where('email', '!=', 'admin@trouvemaalem.com')->get();

        $reviews = [
            'hassan-bennani' => [
                ['rating' => 5, 'comment' => 'Excellent travail, très ponctuel et professionnel. Je recommande vivement Hassan pour tout problème de plomberie.'],
                ['rating' => 5, 'comment' => 'A réparé une fuite complexe en moins d\'une heure. Tarif raisonnable et travail soigné.'],
                ['rating' => 4, 'comment' => 'Bon plombier, a bien résolu notre problème de canalisation bouchée. Très réactif.'],
            ],
            'karim-tazi' => [
                ['rating' => 5, 'comment' => 'Installation du tableau électrique impeccable. Karim est très méthodique et explique bien son travail.'],
                ['rating' => 4, 'comment' => 'Electricien compétent, a installé nos prises et spots sans problème. Propre et efficace.'],
                ['rating' => 5, 'comment' => 'Intervention rapide pour une panne urgente. Très professionnel, je recommande.'],
            ],
            'rachid-amrani' => [
                ['rating' => 5, 'comment' => 'Des meubles sur mesure d\'une qualité exceptionnelle. Le bois est magnifique et la finition parfaite.'],
                ['rating' => 5, 'comment' => 'Rachid a réalisé nos portes en cèdre sculpté. Un vrai artiste, travail d\'une grande précision.'],
                ['rating' => 4, 'comment' => 'Très satisfait de notre bibliothèque sur mesure. Délai respecté et prix correct.'],
                ['rating' => 5, 'comment' => 'Maître charpentier exceptionnel. La table en thuya qu\'il a fabriquée est une vraie œuvre d\'art.'],
            ],
            'fatima-zahra' => [
                ['rating' => 5, 'comment' => 'Les babouches en cuir sont absolument magnifiques, artisanat authentique de très haute qualité.'],
                ['rating' => 5, 'comment' => 'Sac en cuir acheté pour un cadeau, la qualité est irréprochable. Fatima est une vraie artisane.'],
                ['rating' => 4, 'comment' => 'Très beau travail sur les produits en cuir. Livraison rapide et emballage soigné.'],
            ],
            'abdelkader-roudani' => [
                ['rating' => 5, 'comment' => 'Magnifiques poteries, les couleurs sont vives et la cuisson parfaite. Un savoir-faire ancestral remarquable.'],
                ['rating' => 4, 'comment' => 'Très beau tagine, cuit parfaitement. Abdelkader est passionné par son métier, ça se voit.'],
            ],
            'youssef-alami' => [
                ['rating' => 5, 'comment' => 'Les céramiques de Youssef sont d\'une beauté exceptionnelle. Motifs andalous authentiques, couleurs magnifiques.'],
                ['rating' => 5, 'comment' => 'Commande de vaisselle pour un restaurant, tout est parfait. Qualité professionnelle irréprochable.'],
                ['rating' => 4, 'comment' => 'Très satisfait de mon service de thé en céramique. Belle présentation et emballage protecteur.'],
            ],
            'amine-cherkaoui' => [
                ['rating' => 5, 'comment' => 'Refait l\'installation électrique complète de l\'appartement. Travail propre et aux normes. Très sérieux.'],
                ['rating' => 4, 'comment' => 'Intervention rapide pour l\'installation de climatiseurs. Efficace et professionnel.'],
            ],
            'samir-bouzid' => [
                ['rating' => 5, 'comment' => 'A installé notre nouvelle salle de bain complète. Travail impeccable, délai respecté. Très recommandé.'],
                ['rating' => 4, 'comment' => 'Bon plombier, a réglé une fuite sous l\'évier rapidement. Prix honnête.'],
            ],
        ];

        foreach ($reviews as $slug => $artisanReviews) {
            $artisan = Artisan::where('slug', $slug)->first();
            if (! $artisan) {
                continue;
            }

            foreach ($artisanReviews as $index => $data) {
                Review::create([
                    'artisan_id' => $artisan->id,
                    'user_id'    => $users[$index % $users->count()]->id,
                    'rating'     => $data['rating'],
                    'comment'    => $data['comment'],
                ]);
            }
        }
    }
}
