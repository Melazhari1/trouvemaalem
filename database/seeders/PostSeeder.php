<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'slug'       => 'moroccan-decoration-trends-2026',
                'image'      => 'https://images.unsplash.com/photo-1542017282-12461ba0cd58?q=80&w=2070&auto=format&fit=crop',
                'is_published' => true,

                'title_en'   => 'Top 5 Moroccan Decoration Trends for 2026',
                'excerpt_en' => 'Discover the latest interior design trends inspired by Moroccan craftsmanship to transform your home.',
                'content_en' => '<p>Moroccan craftsmanship continues to inspire the world.</p><h2>1. Revisited Zellige</h2><p>Zellige tiles are more popular than ever, now with a modern touch.</p><h2>2. Earthy Colours</h2><p>Terracotta, ochre and sand tones dominate this year\'s palettes.</p><h2>3. Sculpted Cedar Furniture</h2><p>Hand-carved cedar wood makes a grand return in minimalist interiors.</p><h2>4. Sustainable Berber Rugs</h2><p>The focus is on natural materials and vegetable dyes.</p><h2>5. Brass Light Fixtures</h2><p>Pierced brass pendants create enchanting shadow play in living spaces.</p>',

                'title_fr'   => 'Les 5 Tendances de Décoration Marocaine en 2026',
                'excerpt_fr' => 'Découvrez les dernières tendances en matière de design intérieur inspiré de l\'artisanat marocain pour transformer votre maison.',
                'content_fr' => '<p>L\'artisanat marocain continue d\'inspirer le monde entier.</p><h2>1. Le Zellige revisité</h2><p>Les carreaux de zellige sont plus populaires que jamais, mais avec une touche de modernité.</p><h2>2. Couleurs terreuses</h2><p>Les tons de terracotta, ocre et sable dominent les palettes de cette année.</p><h2>3. Mobilier en bois sculpté</h2><p>Le bois de cèdre sculpté à la main fait son grand retour dans les intérieurs minimalistes.</p><h2>4. Tapis berbères durables</h2><p>L\'accent est mis sur les matériaux naturels et les teintures végétales.</p><h2>5. Luminaires en laiton</h2><p>Des suspensions ajourées créent des jeux d\'ombres enchanteurs dans les espaces de vie.</p>',

                'title_ar'   => 'أبرز 5 اتجاهات في الديكور المغربي لعام 2026',
                'excerpt_ar' => 'اكتشف أحدث اتجاهات التصميم الداخلي المستوحاة من الحرف المغربية لتحويل منزلك.',
                'content_ar' => '<p>يواصل الحرف المغربي إلهام العالم أجمع.</p><h2>1. الزليج المُعاد اكتشافه</h2><p>بلاطات الزليج أكثر شعبية من أي وقت مضى، لكن بلمسة عصرية.</p><h2>2. الألوان الترابية</h2><p>درجات التراكوتا والأوكر والرمل تهيمن على لوحات الألوان هذا العام.</p><h2>3. الأثاث المنحوت من خشب الأرز</h2><p>يعود خشب الأرز المنحوت يدوياً بقوة إلى الديكور الداخلي البسيط.</p><h2>4. السجاد البربري المستدام</h2><p>التركيز على المواد الطبيعية والأصباغ النباتية.</p><h2>5. تجهيزات الإضاءة النحاسية</h2><p>المعلقات النحاسية المثقوبة تخلق لعبة ظلال ساحرة في أماكن المعيشة.</p>',
            ],
            [
                'slug'       => 'how-to-choose-the-right-artisan',
                'image'      => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?q=80&w=2070&auto=format&fit=crop',
                'is_published' => true,

                'title_en'   => 'How to Choose the Right Artisan for Your Renovation',
                'excerpt_en' => 'Finding the right professional is crucial to your project\'s success. Here are our tips for making the best choice.',
                'content_en' => '<p>Renovating your home is a major project. Choosing the right artisan is the most important step.</p><h2>Check References</h2><p>Always ask to see examples of previous work and read reviews from past clients.</p><h2>Request Multiple Quotes</h2><p>Don\'t stop at the first quote. Compare prices, but also timelines and proposed materials.</p><h2>Communication is Key</h2><p>A good artisan must listen to your needs and clearly explain each step of the project.</p>',

                'title_fr'   => 'Comment Choisir le Bon Artisan pour vos Rénovations ?',
                'excerpt_fr' => 'Trouver le bon professionnel est crucial pour la réussite de vos projets. Voici nos conseils pour faire le meilleur choix.',
                'content_fr' => '<p>Rénover sa maison est un grand projet. Le choix de l\'artisan est l\'étape la plus importante.</p><h2>Vérifier les références</h2><p>Demandez toujours à voir des exemples de travaux précédents et lisez les avis des anciens clients.</p><h2>Demander plusieurs devis</h2><p>Ne vous arrêtez pas au premier devis. Comparez les prix, mais aussi les délais et les matériaux proposés.</p><h2>La communication est clé</h2><p>Un bon artisan doit être à l\'écoute de vos besoins et capable de vous expliquer clairement les différentes étapes du projet.</p>',

                'title_ar'   => 'كيف تختار الحرفي المناسب لتجديد منزلك؟',
                'excerpt_ar' => 'إيجاد المحترف المناسب أمر بالغ الأهمية لنجاح مشروعك. إليك نصائحنا لاتخاذ أفضل قرار.',
                'content_ar' => '<p>تجديد منزلك مشروع كبير. واختيار الحرفي هو أهم خطوة في هذه العملية.</p><h2>التحقق من المراجع</h2><p>اطلب دائماً الاطلاع على أمثلة من الأعمال السابقة وقراءة تقييمات العملاء السابقين.</p><h2>طلب عدة عروض أسعار</h2><p>لا تتوقف عند العرض الأول. قارن الأسعار وكذلك المواعيد والمواد المقترحة.</p><h2>التواصل هو الأساس</h2><p>يجب أن يستمع الحرفي الجيد لاحتياجاتك ويشرح لك بوضوح كل خطوة من خطوات المشروع.</p>',
            ],
            [
                'slug'       => 'art-of-leather-in-fes',
                'image'      => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=2070&auto=format&fit=crop',
                'is_published' => true,

                'title_en'   => 'The Art of Leatherwork in Fès',
                'excerpt_en' => 'Dive into the fascinating history of the Chouara tanneries and discover the process behind Moroccan leather.',
                'content_en' => '<p>The tanneries of Fès are world-famous, and for good reason: they use methods unchanged since the 11th century.</p><h2>An Ancestral Process</h2><p>The leather is first cleaned, then softened, and finally dyed using natural pigments such as poppy (red), indigo (blue) or henna (orange).</p><h2>High-Quality Products</h2><p>Whether for babouches, bags or poufs, Moroccan leather is renowned for its suppleness and durability. Buying Moroccan leather is buying a piece of history.</p>',

                'title_fr'   => 'L\'Art du Travail du Cuir à Fès',
                'excerpt_fr' => 'Plongez dans l\'histoire fascinante des tanneries de Chouara et découvrez le processus de création du cuir marocain.',
                'content_fr' => '<p>Les tanneries de Fès sont mondialement connues et pour cause, elles utilisent des méthodes qui n\'ont pas changé depuis le 11ème siècle.</p><h2>Un processus ancestral</h2><p>Le cuir est d\'abord nettoyé, puis assoupli et enfin teint à l\'aide de pigments naturels comme le coquelicot (pour le rouge), l\'indigo (pour le bleu) ou le henné (pour l\'orange).</p><h2>Des produits de haute qualité</h2><p>Que ce soit pour des babouches, des sacs ou des poufs, le cuir marocain est réputé pour sa souplesse et sa durabilité.</p>',

                'title_ar'   => 'فن صناعة الجلد في فاس',
                'excerpt_ar' => 'انغمس في التاريخ الرائع لمدابغ الشوارة واكتشف طريقة صنع الجلد المغربي.',
                'content_ar' => '<p>مدابغ فاس مشهورة عالمياً، وذلك لأسباب وجيهة؛ فهي تستخدم أساليب لم تتغير منذ القرن الحادي عشر.</p><h2>عملية موروثة عن الأجداد</h2><p>يُنظَّف الجلد أولاً، ثم يُلين، وأخيراً يُصبغ باستخدام أصباغ طبيعية كالخشخاش (للأحمر) والنيلة (للأزرق) والحناء (للبرتقالي).</p><h2>منتجات عالية الجودة</h2><p>سواء للبلاغ أو الحقائب أو المقاعد، يشتهر الجلد المغربي بمرونته ومتانته. شراء منتج من الجلد المغربي هو اقتناء قطعة من التاريخ.</p>',
            ],
        ];

        foreach ($posts as $data) {
            Post::create($data);
        }
    }
}
