<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\PostRidePageSetting;
use App\Models\PostRidePageSettingDetail;
use Illuminate\Database\Seeder;

class PriceErrorParagraphsSeeder extends Seeder
{
    /**
     * Multi-language values for price error modal (paragraph 2 uses :max_per_seat placeholder).
     * Keys match language abbreviation from languages table.
     */
    protected function translations(): array
    {
        return [
            'en' => [
                'price_error_paragraph_1' => 'To comply with Canadian and Quebec carpooling regulations, the total amount collected for a trip cannot exceed the official 2026 reimbursement rate of $0.72/km.',
                'price_error_paragraph_2' => 'The maximum allowed for this trip is $:max_per_seat per seat.',
                'price_error_paragraph_3' => 'This limit is mandatory to ensure your ride is classified as a non-commercial carpool, protecting your insurance coverage and maintaining the cost-sharing status of your contributions.',
                'price_error_heading' => 'Price Limit Exceeded',
                'price_error_adjust_btn_label' => 'Adjust Price',
            ],
            'fr' => [
                'price_error_paragraph_1' => 'Pour respecter les règlements de covoiturage canadiens et québécois, le montant total perçu pour un trajet ne peut pas dépasser le taux de remboursement officiel 2026 de 0,72 $/km.',
                'price_error_paragraph_2' => 'Le maximum autorisé pour ce trajet est de :max_per_seat $ par place.',
                'price_error_paragraph_3' => 'Cette limite est obligatoire pour que votre trajet soit classé comme covoiturage non commercial, protégeant votre couverture d\'assurance et le statut de partage des coûts de vos contributions.',
                'price_error_heading' => 'Limite de prix dépassée',
                'price_error_adjust_btn_label' => 'Ajuster le prix',
            ],
            'ar' => [
                'price_error_paragraph_1' => 'للامتثال للوائح مشاركة السيارات في كندا وكيبيك، لا يمكن أن يتجاوز المبلغ الإجمالي المحصل لرحلة معدل الاسترداد الرسمي لعام 2026 البالغ 0.72 دولار/كم.',
                'price_error_paragraph_2' => 'الحد الأقصى المسموح به لهذه الرحلة هو :max_per_seat دولار للمقعد الواحد.',
                'price_error_paragraph_3' => 'هذا الحد إلزامي لضمان تصنيف رحلتك كمشاركة سيارات غير تجارية، وحماية تغطية التأمين الخاصة بك والحفاظ على وضع تقاسم التكاليف لمساهماتك.',
                'price_error_heading' => 'تجاوز حد السعر',
                'price_error_adjust_btn_label' => 'تعديل السعر',
            ],
            'es' => [
                'price_error_paragraph_1' => 'Para cumplir con las regulaciones de coche compartido de Canadá y Quebec, el monto total cobrado por un viaje no puede exceder la tasa de reembolso oficial 2026 de 0,72 $/km.',
                'price_error_paragraph_2' => 'El máximo permitido para este viaje es $:max_per_seat por asiento.',
                'price_error_paragraph_3' => 'Este límite es obligatorio para que su viaje se clasifique como coche compartido no comercial, protegiendo su cobertura de seguro y el estado de uso compartido de costos de sus contribuciones.',
                'price_error_heading' => 'Límite de precio excedido',
                'price_error_adjust_btn_label' => 'Ajustar precio',
            ],
            'tl' => [
                'price_error_paragraph_1' => 'Upang sumunod sa mga regulasyon ng carpooling ng Canada at Quebec, ang kabuuang halagang kinolekta para sa isang biyahe ay hindi maaaring lumampas sa opisyal na 2026 reimbursement rate na $0.72/km.',
                'price_error_paragraph_2' => 'Ang pinakamataas na pinapayagan para sa biyaheng ito ay $:max_per_seat bawat upuan.',
                'price_error_paragraph_3' => 'Ang limitasyong ito ay mandatory upang masiguro na ang iyong biyahe ay maiuri bilang non-commercial carpool, na pinoprotektahan ang iyong insurance coverage at ang cost-sharing status ng iyong mga kontribusyon.',
                'price_error_heading' => 'Na-exceed ang limitasyon ng presyo',
                'price_error_adjust_btn_label' => 'Iayos ang presyo',
            ],
            'zh' => [
                'price_error_paragraph_1' => '为遵守加拿大和魁北克拼车规定，单次行程收取的总金额不得超过2026年官方报销标准每公里0.72加元。',
                'price_error_paragraph_2' => '本次行程每个座位允许的最高金额为:max_per_seat加元。',
                'price_error_paragraph_3' => '此限制为确保您的行程被列为非商业拼车所必需，以保护您的保险保障并维持您分摊费用的成本共享身份。',
                'price_error_heading' => '超出价格限制',
                'price_error_adjust_btn_label' => '调整价格',
            ],
            'hi' => [
                'price_error_paragraph_1' => 'कनाडा और क्यूबेक कारपूलिंग नियमों का पालन करने के लिए, यात्रा के लिए एकत्र की गई कुल राशि आधिकारिक 2026 प्रतिपूर्ति दर $0.72/km से अधिक नहीं हो सकती।',
                'price_error_paragraph_2' => 'इस यात्रा के लिए अधिकतम अनुमत प्रति सीट $:max_per_seat है।',
                'price_error_paragraph_3' => 'यह सीमा अनिवार्य है ताकि आपकी सवारी को गैर-वाणिज्यिक कारपूल के रूप में वर्गीकृत किया जा सके, आपके बीमा कवरेज की रक्षा और आपके योगदान की लागत-साझाकरण स्थिति बनाए रखी जा सके।',
                'price_error_heading' => 'कीमत की सीमा पार',
                'price_error_adjust_btn_label' => 'कीमत समायोजित करें',
            ],
            'ur' => [
                'price_error_paragraph_1' => 'کینیڈا اور کیوبیک کارپولنگ کے ضوابط کی تعمیل کے لیے، سفر کے لیے وصول کی جانے والی کل رقم سرکاری 2026 ریئمبرسمنٹ ریٹ $0.72/km سے زیادہ نہیں ہو سکتی۔',
                'price_error_paragraph_2' => 'اس سفر کے لیے فی سیٹ زیادہ سے زیادہ اجازت شدہ رقم $:max_per_seat ہے۔',
                'price_error_paragraph_3' => 'یہ حد لازمی ہے تاکہ آپ کی سواری کو غیر تجارتی کارپول کے طور پر درجہ بندی کیا جا سکے، آپ کے انشورنس کوریج کی حفاظت اور آپ کے حصے کی لاگت شیئرنگ کی حیثیت برقرار رہے۔',
                'price_error_heading' => 'قیمت کی حد سے تجاوز',
                'price_error_adjust_btn_label' => 'قیمت ایڈجسٹ کریں',
            ],
            'ru' => [
                'price_error_paragraph_1' => 'В соответствии с правилами совместных поездок Канады и Квебека общая сумма, взимаемая за поездку, не может превышать официальную ставку возмещения 2026 года в размере 0,72 $/км.',
                'price_error_paragraph_2' => 'Максимально допустимая сумма для этой поездки составляет $:max_per_seat за место.',
                'price_error_paragraph_3' => 'Это ограничение обязательно для того, чтобы ваша поездка была классифицирована как непредпринимательский карпулинг, защищая ваше страховое покрытие и статус совместного использования расходов.',
                'price_error_heading' => 'Превышен лимит цены',
                'price_error_adjust_btn_label' => 'Изменить цену',
            ],
            'uk' => [
                'price_error_paragraph_1' => 'Для дотримання правил спільних поїздок Канади та Квебеку загальна сума, що стягується за поїздку, не може перевищувати офіційну ставку відшкодування 2026 року 0,72 $/км.',
                'price_error_paragraph_2' => 'Максимально дозволена сума для цієї поїздки становить $:max_per_seat за місце.',
                'price_error_paragraph_3' => 'Це обмеження обов\'язкове для того, щоб ваша поїздка класифікувалася як непідприємницький карпулінг, захищаючи ваше страхове покриття та статус спільного поділу витрат.',
                'price_error_heading' => 'Ліміт ціни перевищено',
                'price_error_adjust_btn_label' => 'Скоригувати ціну',
            ],
        ];
    }

    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $setting = PostRidePageSetting::first();
        if (!$setting) {
            $this->command->warn('PostRidePageSetting not found. Run post ride page setup first.');
            return;
        }

        $translations = $this->translations();
        $languages = Language::orderBy('id')->get();

        foreach ($languages as $language) {
            $abbr = $language->abbreviation ?? '';
            $texts = $translations[$abbr] ?? $translations['en'];

            $updated = PostRidePageSettingDetail::where('post_ride_page_setting_id', $setting->id)
                ->where('language_id', $language->id)
                ->update([
                    'price_error_paragraph_1' => $texts['price_error_paragraph_1'],
                    'price_error_paragraph_2' => $texts['price_error_paragraph_2'],
                    'price_error_paragraph_3' => $texts['price_error_paragraph_3'],
                    'price_error_heading' => $texts['price_error_heading'],
                    'price_error_adjust_btn_label' => $texts['price_error_adjust_btn_label'],
                ]);

            if ($updated) {
                $this->command->info("Updated price error paragraphs for: {$language->name} ({$abbr})");
            } else {
                $this->command->warn("No detail row for language: {$language->name} ({$abbr}) – create it in admin first.");
            }
        }

        $this->command->info('Price error paragraphs seeder finished.');
    }
}
