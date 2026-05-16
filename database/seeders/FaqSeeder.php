<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'order'       => 1,
                'is_active'   => true,
                'question_fr' => 'Qu\'est-ce que trouvemaalem ?',
                'question_en' => 'What is trouvemaalem?',
                'question_ar' => 'ما هو موقع trouvemaalem؟',
                'answer_fr'   => 'trouvemaalem est une plateforme en ligne qui vous aide à trouver des artisans et prestataires de services locaux au Maroc. Que vous ayez besoin d\'un plombier, d\'un électricien, d\'un peintre ou de tout autre professionnel, notre annuaire vous met en relation avec des artisans qualifiés près de chez vous.',
                'answer_en'   => 'trouvemaalem is an online platform that helps you find local artisans and service providers in Morocco. Whether you need a plumber, electrician, painter, or any other professional, our directory connects you with qualified craftsmen near you.',
                'answer_ar'   => 'trouvemaalem هو منصة إلكترونية تساعدك على إيجاد الحرفيين ومقدمي الخدمات المحليين في المغرب. سواء كنت تحتاج إلى سباك أو كهربائي أو نجار أو أي محترف آخر، فإن دليلنا يضعك على تواصل مع حرفيين مؤهلين بالقرب منك.',
            ],
            [
                'order'       => 2,
                'is_active'   => true,
                'question_fr' => 'Comment rechercher un artisan près de chez moi ?',
                'question_en' => 'How do I search for an artisan near me?',
                'question_ar' => 'كيف أبحث عن حرفي بالقرب مني؟',
                'answer_fr'   => 'Utilisez la barre de recherche sur la page d\'accueil : saisissez le type de service souhaité (ex. "plombier") et votre ville. Vous pouvez aussi filtrer par catégorie, distance, note minimale ou statut vérifié. Une carte interactive affiche les artisans disponibles dans votre zone.',
                'answer_en'   => 'Use the search bar on the homepage: enter the type of service you need (e.g. "plumber") and your city. You can also filter by category, distance, minimum rating, or verified status. An interactive map displays available artisans in your area.',
                'answer_ar'   => 'استخدم شريط البحث في الصفحة الرئيسية: أدخل نوع الخدمة التي تحتاجها (مثلاً "سباك") ومدينتك. يمكنك أيضاً التصفية حسب الفئة أو المسافة أو الحد الأدنى للتقييم أو الحالة الموثقة. تعرض خريطة تفاعلية الحرفيين المتاحين في منطقتك.',
            ],
            [
                'order'       => 3,
                'is_active'   => true,
                'question_fr' => 'Les artisans listés sont-ils vérifiés ?',
                'question_en' => 'Are the listed artisans verified?',
                'question_ar' => 'هل الحرفيون المدرجون موثقون؟',
                'answer_fr'   => 'Certains artisans portent un badge "Vérifié" indiquant que leur identité et leurs compétences ont été contrôlées par notre équipe. Nous vous recommandons de privilégier ces profils, tout en consultant également les avis laissés par d\'autres clients.',
                'answer_en'   => 'Some artisans carry a "Verified" badge indicating that their identity and skills have been checked by our team. We recommend prioritizing these profiles while also consulting reviews left by other clients.',
                'answer_ar'   => 'بعض الحرفيين يحملون شارة "موثق" مما يشير إلى أن هويتهم ومهاراتهم تم التحقق منها من قبل فريقنا. نوصيك بإعطاء الأولوية لهذه الملفات الشخصية مع الاطلاع أيضاً على تقييمات العملاء الآخرين.',
            ],
            [
                'order'       => 4,
                'is_active'   => true,
                'question_fr' => 'Comment laisser un avis sur un artisan ?',
                'question_en' => 'How do I leave a review for an artisan?',
                'question_ar' => 'كيف أترك تقييماً لحرفي؟',
                'answer_fr'   => 'Rendez-vous sur la fiche de l\'artisan que vous avez sollicité, faites défiler jusqu\'à la section "Avis", puis remplissez le formulaire en indiquant votre note (1 à 5 étoiles) et votre commentaire. Tous les avis sont soumis à modération avant publication afin de garantir leur authenticité.',
                'answer_en'   => 'Visit the profile page of the artisan you hired, scroll down to the "Reviews" section, then fill in the form with your rating (1 to 5 stars) and your comment. All reviews are moderated before publication to ensure their authenticity.',
                'answer_ar'   => 'انتقل إلى صفحة ملف الحرفي الذي استعملت خدماته، مرر لأسفل حتى قسم "التقييمات"، ثم أكمل النموذج بتحديد تقييمك (من 1 إلى 5 نجوم) وتعليقك. تخضع جميع التقييمات للمراجعة قبل نشرها لضمان أصالتها.',
            ],
            [
                'order'       => 5,
                'is_active'   => true,
                'question_fr' => 'Est-ce que l\'utilisation du site est gratuite ?',
                'question_en' => 'Is using the website free?',
                'question_ar' => 'هل استخدام الموقع مجاني؟',
                'answer_fr'   => 'Oui, la consultation de l\'annuaire et la recherche d\'artisans sont entièrement gratuites pour les particuliers. Vous pouvez parcourir les profils, consulter les avis et contacter les artisans sans aucun frais.',
                'answer_en'   => 'Yes, browsing the directory and searching for artisans is completely free for individuals. You can browse profiles, read reviews, and contact artisans at no cost.',
                'answer_ar'   => 'نعم، تصفح الدليل والبحث عن الحرفيين مجاني تماماً للأفراد. يمكنك تصفح الملفات الشخصية وقراءة التقييمات والتواصل مع الحرفيين دون أي تكلفة.',
            ],
            [
                'order'       => 6,
                'is_active'   => true,
                'question_fr' => 'Comment contacter un artisan ?',
                'question_en' => 'How do I contact an artisan?',
                'question_ar' => 'كيف أتواصل مع حرفي؟',
                'answer_fr'   => 'Ouvrez la fiche de l\'artisan souhaité pour consulter ses coordonnées (numéro de téléphone, adresse). Vous pouvez le contacter directement par téléphone. Pour toute question générale, utilisez notre formulaire de contact disponible dans la section "Contact" du site.',
                'answer_en'   => 'Open the artisan\'s profile page to view their contact details (phone number, address). You can reach them directly by phone. For general inquiries, use our contact form available in the "Contact" section of the website.',
                'answer_ar'   => 'افتح صفحة الملف الشخصي للحرفي المطلوب للاطلاع على بياناته (رقم الهاتف، العنوان). يمكنك التواصل معه مباشرة عبر الهاتف. للاستفسارات العامة، استخدم نموذج الاتصال المتاح في قسم "تواصل معنا" في الموقع.',
            ],
            [
                'order'       => 7,
                'is_active'   => true,
                'question_fr' => 'Comment puis-je signaler un faux avis ou un profil incorrect ?',
                'question_en' => 'How can I report a fake review or incorrect profile?',
                'question_ar' => 'كيف يمكنني الإبلاغ عن تقييم مزيف أو ملف شخصي غير صحيح؟',
                'answer_fr'   => 'Utilisez notre formulaire de contact en décrivant le problème (nom de l\'artisan concerné, nature du signalement). Notre équipe examinera le signalement dans les plus brefs délais et prendra les mesures nécessaires pour maintenir la qualité et la fiabilité de l\'annuaire.',
                'answer_en'   => 'Use our contact form describing the issue (name of the artisan involved, nature of the report). Our team will review the report as soon as possible and take the necessary steps to maintain the quality and reliability of the directory.',
                'answer_ar'   => 'استخدم نموذج الاتصال لدينا مع وصف المشكلة (اسم الحرفي المعني، طبيعة البلاغ). سيراجع فريقنا البلاغ في أقرب وقت ممكن ويتخذ الإجراءات اللازمة للحفاظ على جودة وموثوقية الدليل.',
            ],
            [
                'order'       => 8,
                'is_active'   => true,
                'question_fr' => 'Dans quelles villes le service est-il disponible ?',
                'question_en' => 'In which cities is the service available?',
                'question_ar' => 'في أي مدن تتوفر الخدمة؟',
                'answer_fr'   => 'trouvemaalem couvre l\'ensemble du territoire marocain. Vous trouverez des artisans dans les grandes villes comme Casablanca, Rabat, Marrakech, Fès, Tanger, Agadir, ainsi que dans de nombreuses villes de taille moyenne. Notre annuaire s\'enrichit continuellement de nouveaux profils.',
                'answer_en'   => 'trouvemaalem covers the entire Moroccan territory. You will find artisans in major cities such as Casablanca, Rabat, Marrakech, Fez, Tangier, Agadir, as well as in many medium-sized cities. Our directory is continuously growing with new profiles.',
                'answer_ar'   => 'يغطي موقع trouvemaalem كامل التراب المغربي. ستجد حرفيين في المدن الكبرى مثل الدار البيضاء والرباط ومراكش وفاس وطنجة وأكادير، فضلاً عن العديد من المدن المتوسطة. يتوسع دليلنا باستمرار بإضافة ملفات شخصية جديدة.',
            ],
            [
                'order'       => 9,
                'is_active'   => true,
                'question_fr' => 'Comment puis-je inscrire mon activité sur trouvemaalem ?',
                'question_en' => 'How can I register my business on trouvemaalem?',
                'question_ar' => 'كيف يمكنني تسجيل نشاطي في trouvemaalem؟',
                'answer_fr'   => 'Si vous êtes artisan ou prestataire de services et souhaitez figurer dans notre annuaire, contactez-nous via le formulaire de contact en précisant votre métier, votre ville et vos coordonnées. Notre équipe reviendra vers vous pour compléter votre inscription et créer votre profil.',
                'answer_en'   => 'If you are an artisan or service provider and wish to be listed in our directory, contact us via the contact form specifying your trade, your city, and your contact details. Our team will get back to you to complete your registration and create your profile.',
                'answer_ar'   => 'إذا كنت حرفياً أو مقدم خدمات وتريد الظهور في دليلنا، تواصل معنا عبر نموذج الاتصال مع ذكر حرفتك ومدينتك ومعلومات التواصل الخاصة بك. سيتواصل معك فريقنا لاستكمال تسجيلك وإنشاء ملفك الشخصي.',
            ],
            [
                'order'       => 10,
                'is_active'   => true,
                'question_fr' => 'Mes informations personnelles sont-elles protégées ?',
                'question_en' => 'Is my personal information protected?',
                'question_ar' => 'هل معلوماتي الشخصية محمية؟',
                'answer_fr'   => 'Oui, nous prenons la protection de vos données très au sérieux. Les informations que vous soumettez via nos formulaires (contact, avis) sont utilisées uniquement pour le traitement de votre demande et ne sont jamais partagées avec des tiers à des fins commerciales.',
                'answer_en'   => 'Yes, we take the protection of your data very seriously. The information you submit through our forms (contact, reviews) is used solely for processing your request and is never shared with third parties for commercial purposes.',
                'answer_ar'   => 'نعم، نحن نأخذ حماية بياناتك بجدية بالغة. المعلومات التي تقدمها عبر نماذجنا (الاتصال، التقييمات) تُستخدم فقط لمعالجة طلبك ولا تتم مشاركتها أبداً مع أطراف ثالثة لأغراض تجارية.',
            ],
        ];

        foreach ($faqs as $data) {
            Faq::create($data);
        }
    }
}
