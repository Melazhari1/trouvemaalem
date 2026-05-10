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
                'question_en' => 'How do I find a worker?',
                'question_fr' => 'Comment trouver un artisan ?',
                'question_ar' => 'كيف أجد حرفياً؟',
                'answer_en'   => 'You can use the search bar or browse categories on the home page.',
                'answer_fr'   => 'Vous pouvez utiliser la barre de recherche ou parcourir les catégories sur la page d\'accueil.',
                'answer_ar'   => 'يمكنك استخدام شريط البحث أو تصفح الفئات في الصفحة الرئيسية.',
            ],
            [
                'order'       => 2,
                'question_en' => 'Is the service free?',
                'question_fr' => 'Le service est-il gratuit ?',
                'question_ar' => 'هل الخدمة مجانية؟',
                'answer_en'   => 'Yes, using the platform to find workers is completely free.',
                'answer_fr'   => 'Oui, l\'utilisation de la plateforme pour trouver des artisans est totalement gratuite.',
                'answer_ar'   => 'نعم، استخدام المنصة للعثور على حرفيين مجاني تماماً.',
            ],
            [
                'order'       => 3,
                'question_en' => 'How are workers verified?',
                'question_fr' => 'Comment les artisans sont-ils vérifiés ?',
                'question_ar' => 'كيف يتم التحقق من الحرفيين؟',
                'answer_en'   => 'We manually review each worker profile before approval.',
                'answer_fr'   => 'Nous examinons manuellement chaque profil d\'artisan avant approbation.',
                'answer_ar'   => 'نقوم بمراجعة كل ملف حرفي يدوياً قبل الموافقة عليه.',
            ],
            [
                'order'       => 4,
                'question_en' => 'Can I leave a review?',
                'question_fr' => 'Puis-je laisser un avis ?',
                'question_ar' => 'هل يمكنني ترك تقييم؟',
                'answer_en'   => 'Yes, after a job is completed you can rate and review the worker.',
                'answer_fr'   => 'Oui, une fois le travail terminé, vous pouvez noter et évaluer l\'artisan.',
                'answer_ar'   => 'نعم، بعد اكتمال العمل يمكنك تقييم الحرفي ومراجعته.',
            ],
            [
                'order'       => 5,
                'question_en' => 'What if I have a dispute?',
                'question_fr' => 'Que faire en cas de litige ?',
                'question_ar' => 'ماذا أفعل في حالة نزاع؟',
                'answer_en'   => 'You can contact our support team to help resolve any issues.',
                'answer_fr'   => 'Vous pouvez contacter notre équipe d\'assistance pour résoudre tout problème.',
                'answer_ar'   => 'يمكنك التواصل مع فريق الدعم لدينا للمساعدة في حل أي مشكلة.',
            ],
            [
                'order'       => 6,
                'question_en' => 'How do I contact a worker?',
                'question_fr' => 'Comment contacter un artisan ?',
                'question_ar' => 'كيف أتواصل مع حرفي؟',
                'answer_en'   => 'You can find their contact information on their profile page.',
                'answer_fr'   => 'Vous trouverez leurs coordonnées sur leur page de profil.',
                'answer_ar'   => 'يمكنك العثور على معلومات الاتصال الخاصة بهم في صفحة ملفهم الشخصي.',
            ],
            [
                'order'       => 7,
                'question_en' => 'Do workers provide guarantees?',
                'question_fr' => 'Les artisans offrent-ils des garanties ?',
                'question_ar' => 'هل يقدم الحرفيون ضمانات؟',
                'answer_en'   => 'Guarantees depend on the individual worker. Please discuss this before starting the job.',
                'answer_fr'   => 'Les garanties dépendent de l\'artisan. Veuillez en discuter avant de commencer le travail.',
                'answer_ar'   => 'تعتمد الضمانات على الحرفي نفسه. يرجى مناقشة ذلك قبل البدء في العمل.',
            ],
            [
                'order'       => 8,
                'question_en' => 'Can I become a worker?',
                'question_fr' => 'Puis-je m\'inscrire en tant qu\'artisan ?',
                'question_ar' => 'هل يمكنني التسجيل كحرفي؟',
                'answer_en'   => 'Yes, you can register as a worker from the top menu.',
                'answer_fr'   => 'Oui, vous pouvez vous inscrire en tant qu\'artisan depuis le menu principal.',
                'answer_ar'   => 'نعم، يمكنك التسجيل كحرفي من القائمة العلوية.',
            ],
            [
                'order'       => 9,
                'question_en' => 'How do I change my location?',
                'question_fr' => 'Comment modifier ma localisation ?',
                'question_ar' => 'كيف أغير موقعي؟',
                'answer_en'   => 'Use the location button on the home page to update your city.',
                'answer_fr'   => 'Utilisez le bouton de localisation sur la page d\'accueil pour mettre à jour votre ville.',
                'answer_ar'   => 'استخدم زر الموقع في الصفحة الرئيسية لتحديث مدينتك.',
            ],
            [
                'order'       => 10,
                'question_en' => 'Are prices fixed?',
                'question_fr' => 'Les prix sont-ils fixes ?',
                'question_ar' => 'هل الأسعار ثابتة؟',
                'answer_en'   => 'Prices are negotiated directly with the worker based on the job requirements.',
                'answer_fr'   => 'Les prix sont négociés directement avec l\'artisan selon les exigences du travail.',
                'answer_ar'   => 'تُحدد الأسعار بالتفاوض المباشر مع الحرفي بناءً على متطلبات العمل.',
            ],
            [
                'order'       => 11,
                'question_en' => 'What payment methods are accepted?',
                'question_fr' => 'Quels modes de paiement sont acceptés ?',
                'question_ar' => 'ما طرق الدفع المقبولة؟',
                'answer_en'   => 'Payment methods are agreed upon between you and the worker, usually cash or bank transfer.',
                'answer_fr'   => 'Les modalités de paiement sont convenues entre vous et l\'artisan, généralement espèces ou virement bancaire.',
                'answer_ar'   => 'تُحدد طرق الدفع بالاتفاق بينك وبين الحرفي، وعادةً ما تكون نقداً أو تحويلاً بنكياً.',
            ],
            [
                'order'       => 12,
                'question_en' => 'Do you operate nationwide?',
                'question_fr' => 'Opérez-vous à l\'échelle nationale ?',
                'question_ar' => 'هل تعملون على المستوى الوطني؟',
                'answer_en'   => 'We currently operate in major cities across Morocco.',
                'answer_fr'   => 'Nous opérons actuellement dans les principales villes du Maroc.',
                'answer_ar'   => 'نعمل حالياً في المدن الرئيسية في جميع أنحاء المغرب.',
            ],
            [
                'order'       => 13,
                'question_en' => 'Can I save my favourite workers?',
                'question_fr' => 'Puis-je enregistrer mes artisans favoris ?',
                'question_ar' => 'هل يمكنني حفظ الحرفيين المفضلين؟',
                'answer_en'   => 'This feature is coming soon!',
                'answer_fr'   => 'Cette fonctionnalité arrive bientôt !',
                'answer_ar'   => 'هذه الميزة قادمة قريباً!',
            ],
            [
                'order'       => 14,
                'question_en' => 'Is there a mobile app?',
                'question_fr' => 'Y a-t-il une application mobile ?',
                'question_ar' => 'هل يوجد تطبيق للهاتف؟',
                'answer_en'   => 'The website is fully optimised for mobile. A native app is in development.',
                'answer_fr'   => 'Le site est entièrement optimisé pour mobile. Une application native est en cours de développement.',
                'answer_ar'   => 'الموقع محسّن بالكامل للهاتف المحمول. تطبيق أصلي قيد التطوير.',
            ],
            [
                'order'       => 15,
                'question_en' => 'How can I delete my account?',
                'question_fr' => 'Comment supprimer mon compte ?',
                'question_ar' => 'كيف يمكنني حذف حسابي؟',
                'answer_en'   => 'You can delete your account from your profile settings page.',
                'answer_fr'   => 'Vous pouvez supprimer votre compte depuis la page des paramètres de votre profil.',
                'answer_ar'   => 'يمكنك حذف حسابك من صفحة إعدادات ملفك الشخصي.',
            ],
        ];

        foreach ($faqs as $data) {
            Faq::create($data);
        }
    }
}
