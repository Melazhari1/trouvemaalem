<?php

namespace Database\Seeders;

use App\Models\Artisan;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin user (Filament BO access)
        \App\Models\User::create([
            'name'     => 'Admin',
            'email'    => 'admin@trouvemaalem.com',
            'password' => 'admin123',
        ]);

        // Regular users for reviews
        foreach ([
            ['name' => 'Mehdi Alaoui',   'email' => 'mehdi@example.com',  'password' => 'password'],
            ['name' => 'Sara Bensouda',  'email' => 'sara@example.com',   'password' => 'password'],
            ['name' => 'Khalid Ouahabi', 'email' => 'khalid@example.com', 'password' => 'password'],
            ['name' => 'Lina Fassi',     'email' => 'lina@example.com',   'password' => 'password'],
            ['name' => 'Aymane Naciri',  'email' => 'aymane@example.com', 'password' => 'password'],
        ] as $userData) {
            \App\Models\User::create($userData);
        }

        // ── Categories ───────────────────────────────────────────────────────────
        $plumbing = Category::create([
            'slug'  => 'plumbing',
            'image' => 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?q=80&w=2070&auto=format&fit=crop',
            'name_en' => 'Plumbing',
            'name_fr' => 'Plomberie',
            'name_ar' => 'السباكة',
            'description_en' => 'Professional plumbing installation and repair services.',
            'description_fr' => 'Services professionnels d\'installation et de réparation de plomberie.',
            'description_ar' => 'خدمات السباكة الاحترافية للتركيب والإصلاح.',
        ]);

        $electricity = Category::create([
            'slug'  => 'electricity',
            'image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=2070&auto=format&fit=crop',
            'name_en' => 'Electricity',
            'name_fr' => 'Électricité',
            'name_ar' => 'الكهرباء',
            'description_en' => 'Certified electricians for all your wiring and electrical needs.',
            'description_fr' => 'Électriciens certifiés pour tous vos besoins en câblage et en installations électriques.',
            'description_ar' => 'كهربائيون معتمدون لجميع احتياجاتك من الأسلاك والتركيبات الكهربائية.',
        ]);

        $pottery = Category::create([
            'slug'  => 'pottery',
            'image' => 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?q=80&w=2070&auto=format&fit=crop',
            'name_en' => 'Pottery',
            'name_fr' => 'Poterie',
            'name_ar' => 'الفخار',
            'description_en' => 'Traditional Moroccan pottery and ceramics.',
            'description_fr' => 'Poterie et céramiques marocaines traditionnelles.',
            'description_ar' => 'الفخار والخزف المغربي التقليدي.',
        ]);

        $leather = Category::create([
            'slug'  => 'leatherwork',
            'image' => 'https://images.unsplash.com/photo-1549420067-154050e5015b?q=80&w=2070&auto=format&fit=crop',
            'name_en' => 'Leatherwork',
            'name_fr' => 'Maroquinerie',
            'name_ar' => 'الجلد',
            'description_en' => 'Handcrafted Moroccan leather goods.',
            'description_fr' => 'Articles en cuir marocain faits à la main.',
            'description_ar' => 'منتجات جلدية مغربية مصنوعة يدوياً.',
        ]);

        $painting = Category::create([
            'slug'  => 'painting',
            'image' => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?q=80&w=2070&auto=format&fit=crop',
            'name_en' => 'Painting',
            'name_fr' => 'Peinture',
            'name_ar' => 'الدهان',
            'description_en' => 'Interior and exterior painting professionals.',
            'description_fr' => 'Professionnels de la peinture intérieure et extérieure.',
            'description_ar' => 'محترفون في الدهان الداخلي والخارجي.',
        ]);

        $carpentry = Category::create([
            'slug'  => 'carpentry',
            'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80&w=2070&auto=format&fit=crop',
            'name_en' => 'Carpentry',
            'name_fr' => 'Menuiserie',
            'name_ar' => 'النجارة',
            'description_en' => 'Custom woodworking and furniture craftsmanship.',
            'description_fr' => 'Travail du bois sur mesure et artisanat de meubles.',
            'description_ar' => 'نجارة مخصصة وصناعة الأثاث الحرفية.',
        ]);

        // ── Artisans ─────────────────────────────────────────────────────────────

        // Casablanca
        Artisan::create([
            'category_id' => $plumbing->id,
            'slug'        => 'hassan-bennani',
            'city'        => 'Casablanca',
            'lat'         => 33.5731,
            'lng'         => -7.5898,
            'image'       => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?q=80&w=2069&auto=format&fit=crop',
            'rating'      => 4.8,
            'phone'       => '+212 600 123 456',
            'is_verified' => true,
            'name_en'     => 'Hassan Bennani',
            'name_fr'     => 'Hassan Bennani',
            'name_ar'     => 'حسن بناني',
            'bio_en'      => 'Expert plumber with 15 years of experience in residential and commercial plumbing. Specializes in modern bathroom installations and water heater repairs.',
            'bio_fr'      => 'Plombier expert avec 15 ans d\'expérience en plomberie résidentielle et commerciale. Spécialisé dans les installations de salles de bain modernes et la réparation de chauffe-eau.',
            'bio_ar'      => 'سباك خبير يتمتع بـ 15 عاماً من الخبرة في السباكة السكنية والتجارية. متخصص في تركيبات الحمامات الحديثة وإصلاح سخانات المياه.',
            'location_en' => 'Casablanca, Morocco',
            'location_fr' => 'Casablanca, Maroc',
            'location_ar' => 'الدار البيضاء، المغرب',
        ]);

        Artisan::create([
            'category_id' => $electricity->id,
            'slug'        => 'karim-tazi',
            'city'        => 'Casablanca',
            'lat'         => 33.5890,
            'lng'         => -7.6113,
            'image'       => 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?q=80&w=2069&auto=format&fit=crop',
            'rating'      => 4.5,
            'phone'       => '+212 600 234 567',
            'name_en'     => 'Karim Tazi',
            'name_fr'     => 'Karim Tazi',
            'name_ar'     => 'كريم التازي',
            'bio_en'      => 'Certified electrician for residential and industrial projects. Expert in solar panel installation and smart home wiring.',
            'bio_fr'      => 'Électricien certifié pour les projets résidentiels et industriels. Expert en installation de panneaux solaires et en câblage domotique.',
            'bio_ar'      => 'كهربائي معتمد للمشاريع السكنية والصناعية. خبير في تركيب الألواح الشمسية وتمديدات المنازل الذكية.',
            'location_en' => 'Casablanca, Morocco',
            'location_fr' => 'Casablanca, Maroc',
            'location_ar' => 'الدار البيضاء، المغرب',
        ]);

        Artisan::create([
            'category_id' => $painting->id,
            'slug'        => 'omar-el-fassi',
            'city'        => 'Casablanca',
            'lat'         => 33.5650,
            'lng'         => -7.6200,
            'image'       => 'https://images.unsplash.com/photo-1558618666-fcd25c85f82e?q=80&w=2032&auto=format&fit=crop',
            'rating'      => 4.3,
            'phone'       => '+212 600 345 678',
            'name_en'     => 'Omar El Fassi',
            'name_fr'     => 'Omar El Fassi',
            'name_ar'     => 'عمر الفاسي',
            'bio_en'      => 'Professional painter specializing in decorative finishes and traditional Moroccan zellige-inspired wall designs.',
            'bio_fr'      => 'Peintre professionnel spécialisé dans les finitions décoratives et les designs muraux inspirés du zellige marocain traditionnel.',
            'bio_ar'      => 'دهان محترف متخصص في التشطيبات الزخرفية وتصاميم الجدران المستوحاة من الزليج المغربي التقليدي.',
            'location_en' => 'Casablanca, Morocco',
            'location_fr' => 'Casablanca, Maroc',
            'location_ar' => 'الدار البيضاء، المغرب',
        ]);

        // Rabat
        Artisan::create([
            'category_id' => $electricity->id,
            'slug'        => 'amine-cherkaoui',
            'city'        => 'Rabat',
            'lat'         => 34.0209,
            'lng'         => -6.8416,
            'image'       => 'https://images.unsplash.com/photo-1605152276897-4f618f831968?q=80&w=2070&auto=format&fit=crop',
            'rating'      => 4.7,
            'phone'       => '+212 600 456 789',
            'name_en'     => 'Amine Cherkaoui',
            'name_fr'     => 'Amine Cherkaoui',
            'name_ar'     => 'أمين الشرقاوي',
            'bio_en'      => 'Experienced electrician based in Rabat. Specializes in apartment rewiring, circuit breaker installation, and emergency electrical repairs.',
            'bio_fr'      => 'Électricien expérimenté basé à Rabat. Spécialisé dans la réfection électrique des appartements, l\'installation de disjoncteurs et les réparations électriques d\'urgence.',
            'bio_ar'      => 'كهربائي متمرس مقيم في الرباط. متخصص في إعادة التمديد الكهربائي للشقق وتركيب قواطع الدائرة وإصلاحات الكهرباء الطارئة.',
            'location_en' => 'Rabat, Morocco',
            'location_fr' => 'Rabat, Maroc',
            'location_ar' => 'الرباط، المغرب',
        ]);

        Artisan::create([
            'category_id' => $carpentry->id,
            'slug'        => 'rachid-amrani',
            'city'        => 'Rabat',
            'lat'         => 34.0133,
            'lng'         => -6.8326,
            'image'       => 'https://images.unsplash.com/photo-1601058272524-0611e132d3c8?q=80&w=2070&auto=format&fit=crop',
            'rating'      => 4.9,
            'phone'       => '+212 600 567 890',
            'is_verified' => true,
            'name_en'     => 'Rachid Amrani',
            'name_fr'     => 'Rachid Amrani',
            'name_ar'     => 'رشيد أمراني',
            'bio_en'      => 'Master carpenter crafting bespoke furniture and traditional Moroccan doors. Over 20 years of woodworking excellence.',
            'bio_fr'      => 'Maître menuisier créant des meubles sur mesure et des portes marocaines traditionnelles. Plus de 20 ans d\'excellence en travail du bois.',
            'bio_ar'      => 'نجار ماهر يصنع الأثاث المخصص والأبواب المغربية التقليدية. أكثر من 20 عاماً من التميز في النجارة.',
            'location_en' => 'Rabat, Morocco',
            'location_fr' => 'Rabat, Maroc',
            'location_ar' => 'الرباط، المغرب',
        ]);

        // Marrakech
        Artisan::create([
            'category_id' => $pottery->id,
            'slug'        => 'abdelkader-roudani',
            'city'        => 'Marrakech',
            'lat'         => 31.6295,
            'lng'         => -7.9811,
            'image'       => 'https://images.unsplash.com/photo-1516086704603-9bb6ffea1f48?q=80&w=2070&auto=format&fit=crop',
            'rating'      => 4.6,
            'phone'       => '+212 600 678 901',
            'name_en'     => 'Abdelkader Roudani',
            'name_fr'     => 'Abdelkader Roudani',
            'name_ar'     => 'عبد القادر الرودانی',
            'bio_en'      => 'Traditional potter from Marrakech, creating vibrant tagine pots and ornamental ceramics using ancestral techniques.',
            'bio_fr'      => 'Potier traditionnel de Marrakech, créant des tajines colorés et des céramiques ornementales selon des techniques ancestrales.',
            'bio_ar'      => 'فخراني تقليدي من مراكش، يصنع أواني الطاجين الملونة والخزف الزخرفي باستخدام تقنيات موروثة.',
            'location_en' => 'Marrakech, Morocco',
            'location_fr' => 'Marrakech, Maroc',
            'location_ar' => 'مراكش، المغرب',
        ]);

        Artisan::create([
            'category_id' => $plumbing->id,
            'slug'        => 'noureddine-bahja',
            'city'        => 'Marrakech',
            'lat'         => 31.6340,
            'lng'         => -7.9956,
            'image'       => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?q=80&w=2070&auto=format&fit=crop',
            'rating'      => 4.2,
            'phone'       => '+212 600 789 012',
            'name_en'     => 'Noureddine Bahja',
            'name_fr'     => 'Noureddine Bahja',
            'name_ar'     => 'نور الدين بهجة',
            'bio_en'      => 'Reliable plumber in Marrakech medina area. Expert in traditional riad plumbing systems and modern bathroom renovations.',
            'bio_fr'      => 'Plombier fiable dans la médina de Marrakech. Expert en systèmes de plomberie traditionnels des riads et en rénovations de salles de bain modernes.',
            'bio_ar'      => 'سباك موثوق في منطقة مدينة مراكش. خبير في أنظمة السباكة التقليدية للرياض وتجديد الحمامات الحديثة.',
            'location_en' => 'Marrakech, Morocco',
            'location_fr' => 'Marrakech, Maroc',
            'location_ar' => 'مراكش، المغرب',
        ]);

        // Fès
        Artisan::create([
            'category_id' => $leather->id,
            'slug'        => 'fatima-zahra',
            'city'        => 'Fes',
            'lat'         => 34.0331,
            'lng'         => -5.0003,
            'image'       => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=2070&auto=format&fit=crop',
            'rating'      => 4.8,
            'phone'       => '+212 600 890 123',
            'is_verified' => true,
            'name_en'     => 'Fatima Zahra',
            'name_fr'     => 'Fatima Zahra',
            'name_ar'     => 'فاطمة الزهراء',
            'bio_en'      => 'Experienced leather artisan in the old Medina of Fès, crafting authentic babouches and bags using traditional tanning methods.',
            'bio_fr'      => 'Artisane cuiriste expérimentée dans la vieille Médina de Fès, confectionnant des babouches authentiques et des sacs selon les méthodes de tannage traditionnelles.',
            'bio_ar'      => 'حرفية جلود متمرسة في المدينة القديمة بفاس، تصنع البلاغ والحقائب الأصيلة باستخدام طرق الدباغة التقليدية.',
            'location_en' => 'Fès, Morocco',
            'location_fr' => 'Fès, Maroc',
            'location_ar' => 'فاس، المغرب',
        ]);

        Artisan::create([
            'category_id' => $carpentry->id,
            'slug'        => 'mouad-idrissi',
            'city'        => 'Fes',
            'lat'         => 34.0372,
            'lng'         => -4.9998,
            'image'       => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?q=80&w=2058&auto=format&fit=crop',
            'rating'      => 4.4,
            'phone'       => '+212 600 901 234',
            'name_en'     => 'Mouad Idrissi',
            'name_fr'     => 'Mouad Idrissi',
            'name_ar'     => 'معاذ الإدريسي',
            'bio_en'      => 'Fès-based carpenter specializing in traditional carved cedar wood panels, mashrabiya screens, and custom furniture.',
            'bio_fr'      => 'Menuisier basé à Fès, spécialisé dans les panneaux en cèdre sculpté traditionnel, les claustras mashrabiya et le mobilier sur mesure.',
            'bio_ar'      => 'نجار مقيم في فاس متخصص في الألواح الخشبية من خشب الأرز المنحوت التقليدي والمشربيات والأثاث المخصص.',
            'location_en' => 'Fès, Morocco',
            'location_fr' => 'Fès, Maroc',
            'location_ar' => 'فاس، المغرب',
        ]);

        // Tanger
        Artisan::create([
            'category_id' => $painting->id,
            'slug'        => 'yassine-lahlou',
            'city'        => 'Tangier',
            'lat'         => 35.7595,
            'lng'         => -5.8340,
            'image'       => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?q=80&w=2032&auto=format&fit=crop',
            'rating'      => 4.1,
            'phone'       => '+212 600 012 345',
            'name_en'     => 'Yassine Lahlou',
            'name_fr'     => 'Yassine Lahlou',
            'name_ar'     => 'ياسين لحلو',
            'bio_en'      => 'Creative painter in Tangier offering interior design painting, waterproofing, and Mediterranean-style wall finishes.',
            'bio_fr'      => 'Peintre créatif à Tanger proposant de la peinture de design d\'intérieur, l\'imperméabilisation et des finitions murales de style méditerranéen.',
            'bio_ar'      => 'دهان مبدع في طنجة يقدم خدمات الدهان الداخلي وعزل المياه والتشطيبات الجدارية بالطراز المتوسطي.',
            'location_en' => 'Tangier, Morocco',
            'location_fr' => 'Tanger, Maroc',
            'location_ar' => 'طنجة، المغرب',
        ]);

        Artisan::create([
            'category_id' => $plumbing->id,
            'slug'        => 'samir-bouzid',
            'city'        => 'Tangier',
            'lat'         => 35.7672,
            'lng'         => -5.8000,
            'image'       => 'https://images.unsplash.com/photo-1607472586893-edb57bdc0e39?q=80&w=2074&auto=format&fit=crop',
            'rating'      => 4.6,
            'phone'       => '+212 600 111 222',
            'name_en'     => 'Samir Bouzid',
            'name_fr'     => 'Samir Bouzid',
            'name_ar'     => 'سمير بوزيد',
            'bio_en'      => 'Tangier-based plumber with expertise in pipe fitting, drain cleaning, and complete bathroom installations.',
            'bio_fr'      => 'Plombier basé à Tanger avec une expertise en raccordement de tuyauterie, débouchage et installations de salles de bain complètes.',
            'bio_ar'      => 'سباك مقيم في طنجة يتمتع بخبرة في تركيب الأنابيب وتنظيف المصارف وتركيب الحمامات الكاملة.',
            'location_en' => 'Tangier, Morocco',
            'location_fr' => 'Tanger, Maroc',
            'location_ar' => 'طنجة، المغرب',
        ]);

        // Safi
        Artisan::create([
            'category_id' => $pottery->id,
            'slug'        => 'youssef-alami',
            'city'        => 'Safi',
            'lat'         => 32.2994,
            'lng'         => -9.2372,
            'image'       => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?q=80&w=2070&auto=format&fit=crop',
            'rating'      => 4.9,
            'phone'       => '+212 600 333 444',
            'is_verified' => true,
            'name_en'     => 'Youssef Alami',
            'name_fr'     => 'Youssef Alami',
            'name_ar'     => 'يوسف العلامي',
            'bio_en'      => 'Master potter from Safi, specializing in vibrant glazed ceramics with traditional Andalusian patterns.',
            'bio_fr'      => 'Maître potier de Safi, spécialisé dans les céramiques émaillées aux couleurs vives avec des motifs andalous traditionnels.',
            'bio_ar'      => 'فخراني ماهر من آسفي متخصص في الخزف المطلي الزاهي بالزخارف الأندلسية التقليدية.',
            'location_en' => 'Safi, Morocco',
            'location_fr' => 'Safi, Maroc',
            'location_ar' => 'آسفي، المغرب',
        ]);

        $this->call([
            FaqSeeder::class,
            PostSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
