<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Plumbers',
                    'fr' => 'Plombiers',
                    'ar' => 'السباكون',
                ],
                'description' => [
                    'en' => 'Professional plumbing services for residential and commercial needs',
                    'fr' => 'Services de plomberie professionnels pour les besoins résidentiels et commerciaux',
                    'ar' => 'خدمات السباكة المهنية للاحتياجات السكنية والتجارية',
                ],
            ],
            [
                'name' => [
                    'en' => 'Electricians',
                    'fr' => 'Électriciens',
                    'ar' => 'الكهربائيون',
                ],
                'description' => [
                    'en' => 'Expert electrical installation, repair, and maintenance services',
                    'fr' => 'Services professionnels d\'installation, réparation et maintenance électrique',
                    'ar' => 'خدمات تركيب وإصلاح والصيانة الكهربائية المتخصصة',
                ],
            ],
            [
                'name' => [
                    'en' => 'Carpenters',
                    'fr' => 'Menuisiers',
                    'ar' => 'النجارون',
                ],
                'description' => [
                    'en' => 'Custom woodworking, furniture making, and carpentry services',
                    'fr' => 'Services de travail du bois, fabrication de meubles et menuiserie',
                    'ar' => 'خدمات أعمال الخشب والأثاث والنجارة المخصصة',
                ],
            ],
            [
                'name' => [
                    'en' => 'Masons',
                    'fr' => 'Maçons',
                    'ar' => 'البناؤون',
                ],
                'description' => [
                    'en' => 'Building construction, masonry, and structural work services',
                    'fr' => 'Services de construction de bâtiments, maçonnerie et travaux de structure',
                    'ar' => 'خدمات البناء والبناء الحجري والأعمال الهندسية',
                ],
            ],
            [
                'name' => [
                    'en' => 'Painters',
                    'fr' => 'Peintres',
                    'ar' => 'الرسامون',
                ],
                'description' => [
                    'en' => 'Interior and exterior painting, decorative finishes, and surface coating',
                    'fr' => 'Peinture intérieure et extérieure, finitions décoratives et revêtement de surface',
                    'ar' => 'الطلاء الداخلي والخارجي والتشطيبات الزخرفية وطلاء الأسطح',
                ],
            ],
            [
                'name' => [
                    'en' => 'Mechanics',
                    'fr' => 'Mécaniciens',
                    'ar' => 'الميكانيكيون',
                ],
                'description' => [
                    'en' => 'Vehicle maintenance, repair, and diagnostics services',
                    'fr' => 'Services d\'entretien, réparation et diagnostic automobile',
                    'ar' => 'خدمات صيانة وإصلاح وتشخيص المركبات',
                ],
            ],
            [
                'name' => [
                    'en' => 'Hairdressers',
                    'fr' => 'Coiffeurs',
                    'ar' => 'الحلاقون',
                ],
                'description' => [
                    'en' => 'Hair cutting, styling, coloring, and grooming services',
                    'fr' => 'Services de coupe de cheveux, coiffage, coloration et toilettage',
                    'ar' => 'خدمات قص الشعر والتصفيف والتلوين والعناية الشخصية',
                ],
            ],
            [
                'name' => [
                    'en' => 'Tailors',
                    'fr' => 'Tailleurs',
                    'ar' => 'الخياطون',
                ],
                'description' => [
                    'en' => 'Custom tailoring, clothing repair, and alterations',
                    'fr' => 'Confection sur mesure, réparation de vêtements et alterations',
                    'ar' => 'الخياطة المخصصة وإصلاح الملابس والتعديلات',
                ],
            ],
            [
                'name' => [
                    'en' => 'Welders',
                    'fr' => 'Soudeurs',
                    'ar' => 'اللحامون',
                ],
                'description' => [
                    'en' => 'Metal welding, fabrication, and structural welding services',
                    'fr' => 'Services de soudure des métaux, fabrication et soudure structurelle',
                    'ar' => 'خدمات اللحام المعدني والتصنيع واللحام الهندسي',
                ],
            ],
            [
                'name' => [
                    'en' => 'Gardeners',
                    'fr' => 'Jardiniers',
                    'ar' => 'البستانيون',
                ],
                'description' => [
                    'en' => 'Garden design, landscaping, plant care, and outdoor maintenance',
                    'fr' => 'Conception de jardins, aménagement paysager, entretien des plantes et espaces verts',
                    'ar' => 'تصميم الحدائق والعناية بالنباتات والعناية بالمساحات الخضراء',
                ],
            ],
            [
                'name' => [
                    'en' => 'House cleaners',
                    'fr' => 'Nettoyeurs',
                    'ar' => 'عمال النظافة',
                ],
                'description' => [
                    'en' => 'Professional house cleaning, deep cleaning, and maintenance services',
                    'fr' => 'Services de nettoyage de maison professionnels, nettoyage profond et entretien',
                    'ar' => 'خدمات تنظيف المنزل المهنية والتنظيف العميق والصيانة',
                ],
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
