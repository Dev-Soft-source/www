<?php

namespace Database\Seeders;

use App\Models\SiteText;
use Illuminate\Database\Seeder;

/**
 * Seeds common site texts (footer tagline, copyright, nav items) for all languages:
 * 1=English, 6=French, 7=Arabic, 9=Spanish, 10=Tagalog, 12=Chinese,
 * 13=Hindi, 14=Urdu, 15=Russian, 16=Ukrainian.
 * Use {year} in text to be replaced by getTranslatedText(..., ['year' => date('Y')]).
 */
class SiteTextSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'footer_tagline', 'language_id' => 1, 'text' => 'Ride with Purpose. Powered by Community Values.'],
            ['slug' => 'footer_tagline', 'language_id' => 6, 'text' => 'Conduisez avec un objectif. Propulsé par les valeurs communautaires.'],
            ['slug' => 'footer_tagline', 'language_id' => 7, 'text' => 'اركب بهدف. مدعوم بقيم المجتمع.'],
            ['slug' => 'footer_tagline', 'language_id' => 9, 'text' => 'Conduce con propósito. Impulsado por los valores de la comunidad.'],
            ['slug' => 'footer_tagline', 'language_id' => 10, 'text' => 'Sumakay nang may layunin. Pinapagana ng mga halaga ng komunidad.'],
            ['slug' => 'footer_tagline', 'language_id' => 12, 'text' => '带着目的骑行。由社区价值观驱动。'],
            ['slug' => 'footer_tagline', 'language_id' => 13, 'text' => 'उद्देश्य के साथ यात्रा करें। समुदाय के मूल्यों द्वारा संचालित।'],
            ['slug' => 'footer_tagline', 'language_id' => 14, 'text' => 'مقصد کے ساتھ سواری کریں۔ معاشرتی اقدار سے چلنے والا۔'],
            ['slug' => 'footer_tagline', 'language_id' => 15, 'text' => 'Путешествуйте с целью. На ценностях сообщества.'],
            ['slug' => 'footer_tagline', 'language_id' => 16, 'text' => 'Подорожуйте з метою. На цінностях спільноти.'],

            ['slug' => 'footer_copyright', 'language_id' => 1, 'text' => '© ProximaRide {year}. All rights reserved'],
            ['slug' => 'footer_copyright', 'language_id' => 6, 'text' => '© ProximaRide {year}. Tous droits réservés.'],
            ['slug' => 'footer_copyright', 'language_id' => 7, 'text' => '© ProximaRide {year}. جميع الحقوق محفوظة.'],
            ['slug' => 'footer_copyright', 'language_id' => 9, 'text' => '© ProximaRide {year}. Todos los derechos reservados'],
            ['slug' => 'footer_copyright', 'language_id' => 10, 'text' => '© ProximaRide {year}. Lahat ng karapatan ay nakalaan.'],
            ['slug' => 'footer_copyright', 'language_id' => 12, 'text' => '© ProximaRide {year}。保留所有权利。'],
            ['slug' => 'footer_copyright', 'language_id' => 13, 'text' => '© ProximaRide {year}. सर्वाधिकार सुरक्षित।'],
            ['slug' => 'footer_copyright', 'language_id' => 14, 'text' => '© ProximaRide {year}. جملہ حقوق محفوظ ہیں۔'],
            ['slug' => 'footer_copyright', 'language_id' => 15, 'text' => '© ProximaRide {year}. Все права защищены.'],
            ['slug' => 'footer_copyright', 'language_id' => 16, 'text' => '© ProximaRide {year}. Всі права захищені.'],

            ['slug' => 'nav_coffee_on_wall', 'language_id' => 1, 'text' => 'Coffee on the Wall'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 6, 'text' => 'Café sur le mur'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 7, 'text' => 'القهوة على الحائط'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 9, 'text' => 'Café en la pared'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 10, 'text' => 'Kape sa dingding'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 12, 'text' => '墙上的咖啡'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 13, 'text' => 'दीवार पर कॉफी'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 14, 'text' => 'دیوار پر کافی'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 15, 'text' => 'Кофе на стене'],
            ['slug' => 'nav_coffee_on_wall', 'language_id' => 16, 'text' => 'Кава на стіні'],

            ['slug' => 'nav_view_all', 'language_id' => 1, 'text' => 'View All'],
            ['slug' => 'nav_view_all', 'language_id' => 6, 'text' => 'Voir tout'],
            ['slug' => 'nav_view_all', 'language_id' => 7, 'text' => 'عرض الكل'],
            ['slug' => 'nav_view_all', 'language_id' => 9, 'text' => 'Ver todo'],
            ['slug' => 'nav_view_all', 'language_id' => 10, 'text' => 'Tingnan lahat'],
            ['slug' => 'nav_view_all', 'language_id' => 12, 'text' => '查看全部'],
            ['slug' => 'nav_view_all', 'language_id' => 13, 'text' => 'सभी देखें'],
            ['slug' => 'nav_view_all', 'language_id' => 14, 'text' => 'سب دیکھیں'],
            ['slug' => 'nav_view_all', 'language_id' => 15, 'text' => 'Смотреть всё'],
            ['slug' => 'nav_view_all', 'language_id' => 16, 'text' => 'Переглянути все'],
        ];

        foreach ($rows as $row) {
            SiteText::updateOrCreate(
                [
                    'slug' => $row['slug'],
                    'language_id' => $row['language_id'],
                ],
                ['text' => $row['text']]
            );
        }
    }
}
