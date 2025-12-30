<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed FAQs
        $faqs = [
            [
                'question_ar' => 'كيف يمكنني البحث عن عقار؟',
                'question_en' => 'How can I search for a property?',
                'answer_ar' => 'يمكنك استخدام محرك البحث المتقدم في الموقع لتصفية العقارات حسب المدينة، النوع، السعر، والمساحة. كما يمكنك التواصل مع فريقنا للحصول على مساعدة شخصية.',
                'answer_en' => 'You can use our advanced search engine to filter properties by city, type, price, and area. You can also contact our team for personalized assistance.',
            ],
            [
                'question_ar' => 'ما هي الأوراق المطلوبة لشراء عقار؟',
                'question_en' => 'What documents are required to purchase a property?',
                'answer_ar' => 'تحتاج إلى بطاقة الهوية الوطنية، إثبات الدخل، وشهادة عدم الممانعة من جهة العمل إن وجدت. سيقوم فريقنا القانوني بإرشادك خلال كامل العملية.',
                'answer_en' => 'You need a national ID, proof of income, and a no-objection certificate from your employer if applicable. Our legal team will guide you through the entire process.',
            ],
            [
                'question_ar' => 'هل تقدمون خدمات التمويل العقاري؟',
                'question_en' => 'Do you offer mortgage financing services?',
                'answer_ar' => 'نعم، نتعاون مع أفضل البنوك والمؤسسات المالية لتوفير حلول تمويلية مرنة تناسب احتياجاتك.',
                'answer_en' => 'Yes, we partner with leading banks and financial institutions to provide flexible financing solutions tailored to your needs.',
            ],
            [
                'question_ar' => 'كم تستغرق عملية شراء العقار؟',
                'question_en' => 'How long does the property purchase process take?',
                'answer_ar' => 'عادةً ما تستغرق العملية من 2 إلى 4 أسابيع، اعتماداً على نوع العقار وإجراءات التمويل.',
                'answer_en' => 'The process typically takes 2 to 4 weeks, depending on the property type and financing procedures.',
            ],
            [
                'question_ar' => 'هل يمكنني زيارة العقار قبل الشراء؟',
                'question_en' => 'Can I visit the property before purchasing?',
                'answer_ar' => 'بالتأكيد! نوفر جولات ميدانية مجانية لجميع العقارات المعروضة. يمكنك حجز موعد من خلال الموقع أو التواصل معنا مباشرة.',
                'answer_en' => 'Absolutely! We provide free site visits for all listed properties. You can book an appointment through our website or contact us directly.',
            ],
            [
                'question_ar' => 'ما هي رسوم الخدمة؟',
                'question_en' => 'What are the service fees?',
                'answer_ar' => 'رسوم الخدمة تختلف حسب نوع العقار وقيمته. سيتم توضيح جميع الرسوم بشفافية قبل إتمام أي صفقة.',
                'answer_en' => 'Service fees vary depending on the property type and value. All fees will be transparently disclosed before completing any transaction.',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        // Seed Services
        $services = [
            [
                'title_ar' => 'البحث عن العقارات',
                'title_en' => 'Property Search',
                'content_ar' => 'نساعدك في العثور على العقار المثالي الذي يناسب احتياجاتك وميزانيتك من خلال قاعدة بيانات شاملة ومحدثة باستمرار.',
                'content_en' => 'We help you find the perfect property that matches your needs and budget through a comprehensive and constantly updated database.',
                'icon' => null,
            ],
            [
                'title_ar' => 'التقييم العقاري',
                'title_en' => 'Property Valuation',
                'content_ar' => 'نقدم خدمات تقييم عقاري دقيقة ومعتمدة لمساعدتك في اتخاذ قرارات استثمارية صحيحة.',
                'content_en' => 'We provide accurate and certified property valuation services to help you make informed investment decisions.',
                'icon' => null,
            ],
            [
                'title_ar' => 'الاستشارات القانونية',
                'title_en' => 'Legal Consultation',
                'content_ar' => 'فريقنا القانوني المتخصص يوفر لك الاستشارات والإرشادات اللازمة لضمان سلامة المعاملات العقارية.',
                'content_en' => 'Our specialized legal team provides you with the necessary consultations and guidance to ensure safe real estate transactions.',
                'icon' => null,
            ],
            [
                'title_ar' => 'إدارة الممتلكات',
                'title_en' => 'Property Management',
                'content_ar' => 'نوفر خدمات إدارة شاملة لممتلكاتك العقارية، من الصيانة إلى التأجير وتحصيل الإيجارات.',
                'content_en' => 'We provide comprehensive management services for your real estate properties, from maintenance to leasing and rent collection.',
                'icon' => null,
            ],
            [
                'title_ar' => 'التسويق العقاري',
                'title_en' => 'Real Estate Marketing',
                'content_ar' => 'نستخدم أحدث استراتيجيات التسويق الرقمي لضمان وصول عقارك إلى أكبر عدد من المشترين المحتملين.',
                'content_en' => 'We use the latest digital marketing strategies to ensure your property reaches the maximum number of potential buyers.',
                'icon' => null,
            ],
            [
                'title_ar' => 'التمويل العقاري',
                'title_en' => 'Mortgage Financing',
                'content_ar' => 'نساعدك في الحصول على أفضل عروض التمويل العقاري من خلال شراكاتنا مع البنوك الرائدة.',
                'content_en' => 'We help you get the best mortgage financing offers through our partnerships with leading banks.',
                'icon' => null,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Seed Pages
        $pages = [
            [
                'slug' => 'about-us',
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'content_ar' => 'نحن شركة رائدة في مجال العقارات، نقدم خدمات متكاملة للبحث عن العقارات وشرائها وبيعها. مع أكثر من 15 عاماً من الخبرة في السوق المصري، نفخر بتقديم أفضل الحلول العقارية لعملائنا. فريقنا المتخصص يعمل على مدار الساعة لضمان رضاكم الكامل وتحقيق أهدافكم الاستثمارية.',
                'content_en' => 'We are a leading real estate company, offering comprehensive services for property search, buying, and selling. With over 15 years of experience in the Egyptian market, we pride ourselves on providing the best real estate solutions to our clients. Our specialized team works around the clock to ensure your complete satisfaction and achieve your investment goals.',
                'team_members' => null,
            ],
            [
                'slug' => 'contact-us',
                'title_ar' => 'اتصل بنا',
                'title_en' => 'Contact Us',
                'content_ar' => 'نحن هنا لخدمتك! يمكنك التواصل معنا عبر الهاتف، البريد الإلكتروني، أو زيارة مكتبنا الرئيسي. فريق خدمة العملاء لدينا جاهز للإجابة على جميع استفساراتك ومساعدتك في العثور على العقار المثالي.',
                'content_en' => 'We are here to serve you! You can contact us by phone, email, or visit our main office. Our customer service team is ready to answer all your inquiries and help you find the perfect property.',
                'team_members' => null,
            ],
            [
                'slug' => 'our-team',
                'title_ar' => 'فريقنا',
                'title_en' => 'Our Team',
                'content_ar' => 'فريقنا يتكون من نخبة من المتخصصين في مجال العقارات، الاستشارات القانونية، والتسويق. نعمل معاً لتقديم أفضل تجربة عقارية لعملائنا.',
                'content_en' => 'Our team consists of elite specialists in real estate, legal consulting, and marketing. We work together to provide the best real estate experience for our clients.',
                'team_members' => json_encode([
                    [
                        'name' => 'أحمد محمد',
                        'position' => 'المدير التنفيذي',
                        'photo' => null,
                    ],
                    [
                        'name' => 'سارة علي',
                        'position' => 'مديرة المبيعات',
                        'photo' => null,
                    ],
                    [
                        'name' => 'محمود حسن',
                        'position' => 'مستشار قانوني',
                        'photo' => null,
                    ],
                ]),
            ],
            [
                'slug' => 'privacy-policy',
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'content_ar' => 'نحن نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية. جميع المعلومات التي تقدمها لنا يتم التعامل معها بسرية تامة ولا يتم مشاركتها مع أي جهات خارجية دون موافقتك الصريحة.',
                'content_en' => 'We respect your privacy and are committed to protecting your personal data. All information you provide us is handled with complete confidentiality and is not shared with any third parties without your explicit consent.',
                'team_members' => null,
            ],
            [
                'slug' => 'terms-and-conditions',
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms and Conditions',
                'content_ar' => 'باستخدامك لموقعنا وخدماتنا، فإنك توافق على الالتزام بالشروط والأحكام التالية. يرجى قراءتها بعناية قبل استخدام أي من خدماتنا.',
                'content_en' => 'By using our website and services, you agree to comply with the following terms and conditions. Please read them carefully before using any of our services.',
                'team_members' => null,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
