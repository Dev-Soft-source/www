<?php

namespace Database\Seeders;

use App\Models\MenuDetail;
use Illuminate\Database\Seeder;

/**
 * Seeds footer menu details (menu_id 2–5) for all languages from languages table:
 * 1=English, 6=French, 7=Arabic, 9=Spanish, 10=Tagalog, 12=Chinese,
 * 13=Hindi, 14=Urdu, 15=Russian, 16=Ukrainian.
 */
class MenuDetailFooterSeeder extends Seeder
{
    public function run(): void
    {
        $languages = $this->getLanguageData();

        foreach ($languages as $languageId => $rows) {
            foreach ($rows as $row) {
                $sectionTitle = $row['section_title'];
                $menuItems = $row['menu_items'];
                unset($row['section_title'], $row['menu_items']);
                MenuDetail::updateOrCreate(
                    [
                        'menu_id' => $row['menu_id'],
                        'language_id' => $languageId,
                    ],
                    ['section_title' => $sectionTitle, 'menu_items' => $menuItems]
                );
            }
        }
    }

    /** @return array<int, list<array{menu_id: int, section_title: string, menu_items: array}>> */
    private function getLanguageData(): array
    {
        return [
            1 => $this->getEnglishRows(),
            6 => $this->getFrenchRows(),
            7 => $this->getArabicRows(),
            9 => $this->getSpanishRows(),
            10 => $this->getTagalogRows(),
            12 => $this->getChineseRows(),
            13 => $this->getHindiRows(),
            14 => $this->getUrduRows(),
            15 => $this->getRussianRows(),
            16 => $this->getUkrainianRows(),
        ];
    }

    private function getEnglishRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'Useful links', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'My profile'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'My rides'],
                ['id' => 3, 'link' => 'signup', 'name' => 'Sign up'],
                ['id' => 4, 'link' => 'login', 'name' => 'Log in'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'Post a ride'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'Search a ride'],
            ]],
            ['menu_id' => 3, 'section_title' => 'How it works', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'For drivers'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'For passengers'],
                ['id' => 9, 'link' => 'students', 'name' => 'For students'],
            ]],
            ['menu_id' => 4, 'section_title' => 'Contact us', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'Contact us / Support'],
                ['id' => 11, 'link' => 'news', 'name' => 'Media'],
            ]],
            ['menu_id' => 5, 'section_title' => 'Terms', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Terms and conditions'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'Terms of use'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Privacy policy'],
            ]],
        ];
    }

    private function getFrenchRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'Liens utiles', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'Mon profil'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'Mes trajets'],
                ['id' => 3, 'link' => 'signup', 'name' => "S'inscrire"],
                ['id' => 4, 'link' => 'login', 'name' => 'Se connecter'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'Publier un trajet'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'Rechercher un trajet'],
            ]],
            ['menu_id' => 3, 'section_title' => 'Comment ça marche', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'Pour les conducteurs'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'Pour les passagers'],
                ['id' => 9, 'link' => 'students', 'name' => 'Pour les étudiants'],
            ]],
            ['menu_id' => 4, 'section_title' => 'Nous contacter', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'Contactez-nous / Support'],
                ['id' => 11, 'link' => 'news', 'name' => 'Médias'],
            ]],
            ['menu_id' => 5, 'section_title' => 'Conditions', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Termes et conditions'],
                ['id' => 13, 'link' => 'terms_use', 'name' => "Conditions d'utilisation"],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Politique de confidentialité'],
            ]],
        ];
    }

    private function getArabicRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'روابط مفيدة', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'ملفي الشخصي'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'رحلاتي'],
                ['id' => 3, 'link' => 'signup', 'name' => 'التسجيل'],
                ['id' => 4, 'link' => 'login', 'name' => 'تسجيل الدخول'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'نشر رحلة'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'البحث عن رحلة'],
            ]],
            ['menu_id' => 3, 'section_title' => 'كيف يعمل', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'للسائقين'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'للركاب'],
                ['id' => 9, 'link' => 'students', 'name' => 'للطلاب'],
            ]],
            ['menu_id' => 4, 'section_title' => 'اتصل بنا', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'اتصل بنا / الدعم'],
                ['id' => 11, 'link' => 'news', 'name' => 'وسائل الإعلام'],
            ]],
            ['menu_id' => 5, 'section_title' => 'الشروط', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'الشروط والأحكام'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'شروط الاستخدام'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'سياسة الخصوصية'],
            ]],
        ];
    }

    private function getSpanishRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'Enlaces útiles', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'Mi perfil'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'Mis viajes'],
                ['id' => 3, 'link' => 'signup', 'name' => 'Registrarse'],
                ['id' => 4, 'link' => 'login', 'name' => 'Iniciar sesión'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'Publicar un viaje'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'Buscar un viaje'],
            ]],
            ['menu_id' => 3, 'section_title' => 'Cómo funciona', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'Para conductores'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'Para pasajeros'],
                ['id' => 9, 'link' => 'students', 'name' => 'Para estudiantes'],
            ]],
            ['menu_id' => 4, 'section_title' => 'Contáctenos', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'Contáctenos / Soporte'],
                ['id' => 11, 'link' => 'news', 'name' => 'Medios'],
            ]],
            ['menu_id' => 5, 'section_title' => 'Términos', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Términos y condiciones'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'Términos de uso'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Política de privacidad'],
            ]],
        ];
    }

    private function getTagalogRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'Mga kapaki-pakinabang na link', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'Aking profile'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'Aking mga biyahe'],
                ['id' => 3, 'link' => 'signup', 'name' => 'Mag-sign up'],
                ['id' => 4, 'link' => 'login', 'name' => 'Mag-log in'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'Mag-post ng biyahe'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'Maghanap ng biyahe'],
            ]],
            ['menu_id' => 3, 'section_title' => 'Paano ito gumagana', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'Para sa mga driver'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'Para sa mga pasahero'],
                ['id' => 9, 'link' => 'students', 'name' => 'Para sa mga estudyante'],
            ]],
            ['menu_id' => 4, 'section_title' => 'Makipag-ugnayan sa amin', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'Makipag-ugnayan sa amin / Suporta'],
                ['id' => 11, 'link' => 'news', 'name' => 'Media'],
            ]],
            ['menu_id' => 5, 'section_title' => 'Mga tuntunin', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Mga tuntunin at kundisyon'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'Mga tuntunin ng paggamit'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Patakaran sa privacy'],
            ]],
        ];
    }

    private function getChineseRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => '实用链接', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => '我的资料'],
                ['id' => 2, 'link' => 'my_rides', 'name' => '我的行程'],
                ['id' => 3, 'link' => 'signup', 'name' => '注册'],
                ['id' => 4, 'link' => 'login', 'name' => '登录'],
                ['id' => 5, 'link' => 'post_ride', 'name' => '发布行程'],
                ['id' => 6, 'link' => 'search_ride', 'name' => '搜索行程'],
            ]],
            ['menu_id' => 3, 'section_title' => '如何运作', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => '司机须知'],
                ['id' => 8, 'link' => 'passengers', 'name' => '乘客须知'],
                ['id' => 9, 'link' => 'students', 'name' => '学生须知'],
            ]],
            ['menu_id' => 4, 'section_title' => '联系我们', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => '联系我们/支持'],
                ['id' => 11, 'link' => 'news', 'name' => '媒体'],
            ]],
            ['menu_id' => 5, 'section_title' => '条款', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => '条款和条件'],
                ['id' => 13, 'link' => 'terms_use', 'name' => '使用条款'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => '隐私政策'],
            ]],
        ];
    }

    private function getHindiRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'उपयोगी लिंक', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'मेरा प्रोफाइल'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'मेरी सवारी'],
                ['id' => 3, 'link' => 'signup', 'name' => 'साइन अप करें'],
                ['id' => 4, 'link' => 'login', 'name' => 'लॉग इन करें'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'सवारी पोस्ट करें'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'सवारी खोजें'],
            ]],
            ['menu_id' => 3, 'section_title' => 'यह कैसे काम करता है', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'ड्राइवरों के लिए'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'यात्रियों के लिए'],
                ['id' => 9, 'link' => 'students', 'name' => 'छात्रों के लिए'],
            ]],
            ['menu_id' => 4, 'section_title' => 'संपर्क करें', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'संपर्क करें / सहायता'],
                ['id' => 11, 'link' => 'news', 'name' => 'मीडिया'],
            ]],
            ['menu_id' => 5, 'section_title' => 'नियम और शर्तें', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'नियम और शर्तें'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'उपयोग की शर्तें'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'गोपनीयता नीति'],
            ]],
        ];
    }

    private function getUrduRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'مفید لنکس', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'میرا پروفائل'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'میری سواریاں'],
                ['id' => 3, 'link' => 'signup', 'name' => 'سائن اپ'],
                ['id' => 4, 'link' => 'login', 'name' => 'لاگ ان'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'سواری پوسٹ کریں'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'سواری تلاش کریں'],
            ]],
            ['menu_id' => 3, 'section_title' => 'یہ کیسے کام کرتا ہے', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'ڈرائیوروں کے لیے'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'مسافروں کے لیے'],
                ['id' => 9, 'link' => 'students', 'name' => 'طالب علموں کے لیے'],
            ]],
            ['menu_id' => 4, 'section_title' => 'ہم سے رابطہ کریں', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'ہم سے رابطہ کریں / سپورٹ'],
                ['id' => 11, 'link' => 'news', 'name' => 'میڈیا'],
            ]],
            ['menu_id' => 5, 'section_title' => 'شرائط', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'شرائط و ضوابط'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'استعمال کی شرائط'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'رازداری کی پالیسی'],
            ]],
        ];
    }

    private function getRussianRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'Полезные ссылки', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'Мой профиль'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'Мои поездки'],
                ['id' => 3, 'link' => 'signup', 'name' => 'Регистрация'],
                ['id' => 4, 'link' => 'login', 'name' => 'Вход'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'Опубликовать поездку'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'Найти поездку'],
            ]],
            ['menu_id' => 3, 'section_title' => 'Как это работает', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'Для водителей'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'Для пассажиров'],
                ['id' => 9, 'link' => 'students', 'name' => 'Для студентов'],
            ]],
            ['menu_id' => 4, 'section_title' => 'Связаться с нами', 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => 'Связаться с нами / Поддержка'],
                ['id' => 11, 'link' => 'news', 'name' => 'СМИ'],
            ]],
            ['menu_id' => 5, 'section_title' => 'Условия', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Условия и положения'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'Условия использования'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Политика конфиденциальности'],
            ]],
        ];
    }

    private function getUkrainianRows(): array
    {
        return [
            ['menu_id' => 2, 'section_title' => 'Корисні посилання', 'menu_items' => [
                ['id' => 1, 'link' => 'profile', 'name' => 'Мій профіль'],
                ['id' => 2, 'link' => 'my_rides', 'name' => 'Мої поїздки'],
                ['id' => 3, 'link' => 'signup', 'name' => 'Реєстрація'],
                ['id' => 4, 'link' => 'login', 'name' => 'Вхід'],
                ['id' => 5, 'link' => 'post_ride', 'name' => 'Опублікувати поїздку'],
                ['id' => 6, 'link' => 'search_ride', 'name' => 'Знайти поїздку'],
            ]],
            ['menu_id' => 3, 'section_title' => 'Як це працює', 'menu_items' => [
                ['id' => 7, 'link' => 'drivers', 'name' => 'Для водіїв'],
                ['id' => 8, 'link' => 'passengers', 'name' => 'Для пасажирів'],
                ['id' => 9, 'link' => 'students', 'name' => 'Для студентів'],
            ]],
            ['menu_id' => 4, 'section_title' => "Зв'язатися з нами", 'menu_items' => [
                ['id' => 10, 'link' => 'contact_us', 'name' => "Зв'язатися з нами / Підтримка"],
                ['id' => 11, 'link' => 'news', 'name' => 'Медіа'],
            ]],
            ['menu_id' => 5, 'section_title' => 'Умови', 'menu_items' => [
                ['id' => 12, 'link' => 'terms_conditions', 'name' => 'Умови та положення'],
                ['id' => 13, 'link' => 'terms_use', 'name' => 'Умови використання'],
                ['id' => 14, 'link' => 'privacy_policy', 'name' => 'Політика конфіденційності'],
            ]],
        ];
    }
}
