<?php
declare(strict_types=1);
namespace ExCompass\Domain\Catalog;

final class EntityRepository {
    public function all(): array {
        return [
            ['vertical'=>'real-estate','slug'=>'godrej-emerald-waters','name'=>'Godrej Emerald Waters','location'=>'Pimpri-Chinchwad','score'=>91,'highlight'=>'Balanced connectivity, planning and developer track record','tags'=>['2–4 BHK','RERA','Family']],
            ['vertical'=>'real-estate','slug'=>'vtp-bellissimo','name'=>'VTP Bellissimo','location'=>'Hinjawadi','score'=>89,'highlight'=>'Strong west-Pune location and amenity proposition','tags'=>['2–3 BHK','IT corridor']],
            ['vertical'=>'hospitals','slug'=>'ruby-hall-clinic','name'=>'Ruby Hall Clinic','location'=>'Sassoon Road','score'=>93,'highlight'=>'Depth of specialties and emergency capability','tags'=>['Multi-speciality','Emergency']],
            ['vertical'=>'hospitals','slug'=>'jupiter-hospital','name'=>'Jupiter Hospital','location'=>'Baner','score'=>90,'highlight'=>'Modern tertiary care with western Pune access','tags'=>['Tertiary','Baner']],
            ['vertical'=>'schools','slug'=>'the-orbis-school','name'=>'The Orbis School','location'=>'Keshav Nagar','score'=>92,'highlight'=>'Balanced academics, activities and parent accessibility','tags'=>['CBSE','K-12']],
            ['vertical'=>'schools','slug'=>'vidya-valley','name'=>'Vidya Valley School','location'=>'Sus','score'=>90,'highlight'=>'Campus environment with established academic reputation','tags'=>['ICSE','Campus']],
            ['vertical'=>'restaurants','slug'=>'malaka-spice','name'=>'Malaka Spice','location'=>'Koregaon Park','score'=>94,'highlight'=>'A Pune dining institution with consistent identity','tags'=>['Asian','Destination dining']],
            ['vertical'=>'hotels','slug'=>'conrad-pune','name'=>'Conrad Pune','location'=>'Sangamvadi','score'=>94,'highlight'=>'Luxury stay with strong business and leisure fit','tags'=>['Luxury','Business']],
            ['vertical'=>'colleges','slug'=>'fergusson-college','name'=>'Fergusson College','location'=>'Shivajinagar','score'=>95,'highlight'=>'Legacy, academic breadth and central-city advantage','tags'=>['Arts','Science']],
            ['vertical'=>'malls','slug'=>'phoenix-marketcity','name'=>'Phoenix Marketcity','location'=>'Viman Nagar','score'=>92,'highlight'=>'High retail breadth with entertainment and dining','tags'=>['Shopping','Entertainment']],
            ['vertical'=>'gyms','slug'=>'multifit-baner','name'=>'MultiFit Baner','location'=>'Baner','score'=>89,'highlight'=>'Functional training and community-led fitness','tags'=>['Functional','Strength']],
            ['vertical'=>'salons','slug'=>'jawed-habib-aundh','name'=>'Jawed Habib Aundh','location'=>'Aundh','score'=>87,'highlight'=>'Accessible full-service grooming proposition','tags'=>['Hair','Grooming']],
            ['vertical'=>'automotive','slug'=>'bavaria-motors','name'=>'Bavaria Motors','location'=>'Hadapsar','score'=>90,'highlight'=>'Premium sales and service experience','tags'=>['Premium','Service']],
            ['vertical'=>'coworking','slug'=>'wework-futura','name'=>'WeWork Futura','location'=>'Magarpatta','score'=>91,'highlight'=>'Strong workspace experience and business connectivity','tags'=>['Managed office','Enterprise']],
            ['vertical'=>'localities','slug'=>'baner','name'=>'Baner','location'=>'West Pune','score'=>93,'highlight'=>'Connectivity, dining, offices and residential depth','tags'=>['Metro access','Schools','Dining']],
            ['vertical'=>'preschools','slug'=>'vivero-kalyani-nagar','name'=>'Vivero Kalyani Nagar','location'=>'Kalyani Nagar','score'=>91,'highlight'=>'Early-learning environment with strong parent experience','tags'=>['Early years','Activity-led']],
            ['vertical'=>'doctors','slug'=>'cardiology-practice','name'=>'Leading Cardiology Practice','location'=>'Deccan','score'=>92,'highlight'=>'Illustrative specialist profile pending editorial verification','tags'=>['Cardiology','Specialist']],
            ['vertical'=>'banquets','slug'=>'the-corinthians','name'=>'The Corinthians','location'=>'Undri','score'=>90,'highlight'=>'Large-format event flexibility and resort setting','tags'=>['Weddings','Events']],
            ['vertical'=>'cafes','slug'=>'third-wave-coffee-kp','name'=>'Third Wave Coffee','location'=>'Koregaon Park','score'=>88,'highlight'=>'Reliable coffee-led work and meet-up experience','tags'=>['Coffee','Work-friendly']],
            ['vertical'=>'weekend','slug'=>'mulshi','name'=>'Mulshi','location'=>'Pune district','score'=>94,'highlight'=>'Scenic drive, monsoon appeal and easy weekend access','tags'=>['Nature','Drive']],
        ];
    }
    public function forVertical(string $vertical): array {
        return array_values(array_filter($this->all(),fn(array $e):bool=>$e['vertical']===$vertical));
    }
    public function find(string $vertical,string $slug): ?array {
        foreach($this->all() as $e) if($e['vertical']===$vertical&&$e['slug']===$slug) return $e;
        return null;
    }
}
