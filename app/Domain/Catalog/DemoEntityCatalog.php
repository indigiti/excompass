<?php
declare(strict_types=1);

namespace ExCompass\Domain\Catalog;

final class DemoEntityCatalog
{
    public function all(): array
    {
        $groups = [
            'real-estate' => [
                ['godrej-emerald-waters','Godrej Emerald Waters','Hinjawadi',92,'Strong overall residential proposition balancing access, planning, value and family usability',['2, 3 & 4 BHK','17 Acres','Family']],
                ['lodha-panache','Lodha Panache','Hinjawadi',91,'Large-format residential proposition close to the west Pune technology corridor',['2 & 3 BHK','12 Acres','End Use']],
                ['vtp-earth-one','VTP Earth One','Kharadi',90,'Township-scale proposition in an important east Pune growth corridor',['2 & 3 BHK','14 Acres','Investment']],
                ['kolte-patil-24k-manor','Kolte-Patil 24K Manor','Baner',89,'Premium apartment positioning with established west Pune access',['3 & 4 BHK','8 Acres','End Use']],
                ['nyati-elysia','Nyati Elysia','Kharadi',88,'Ready residential proposition with strong east Pune connectivity and family appeal',['2 & 3 BHK','9 Acres','Family']],
                ['majestique-marbella','Majestique Marbella','Kharadi',87,'Apartment proposition positioned around east Pune employment and lifestyle access',['2 & 3 BHK','10 Acres','Investment']],
                ['purva-atmosphere','Purva Atmosphere','Keshav Nagar',88,'Contemporary planning with access to east Pune employment hubs',['2–3 BHK','East Pune','Modern']],
                ['gera-world-of-joy','Gera World of Joy','Kharadi',87,'Family-led project concept near major commercial districts',['2–3 BHK','Kharadi','Family']],
                ['shapoorji-pallonji-joyville','Shapoorji Pallonji Joyville','Hinjawadi',86,'Scale, amenities and tech-corridor connectivity',['2–3 BHK','Township','Amenities']],
                ['villas-at-forest-trails','Forest Trails Residences','Bhugaon',85,'Low-density green setting for buyers prioritising space',['Villas','Green living','Low density']],
                ['nyati-emerald','Nyati Emerald','Baner',84,'Well-connected west Pune address with balanced family appeal',['2–3 BHK','Baner','Connectivity']],
                ['mantra-monarch','Mantra Monarch','Balewadi',83,'Urban lifestyle proposition near high-growth west Pune nodes',['2–3 BHK','Balewadi','Urban']],
                ['kasturi-eon-homes','Kasturi Eon Homes','Hinjawadi',82,'Compact contemporary residences with office-corridor proximity',['2–3 BHK','Compact','IT corridor']],
                ['goel-ganga-acropolis','Ganga Acropolis','Baner',81,'Mature neighbourhood convenience with established retail access',['2–3 BHK','Baner','Convenience']],
                ['ram-india-green-hive','Green Hive','Fursungi',80,'Value-led residential format for growing south-east Pune families',['2–3 BHK','Value','South-east Pune']],
                ['kumar-primus','Kumar Primus','Hadapsar',79,'Access-led proposition close to Magarpatta and Hadapsar',['2–3 BHK','Hadapsar','Office access']],
                ['paranjape-blue-ridge','Blue Ridge','Hinjawadi',78,'Integrated township ecosystem with offices, homes and amenities',['Township','Hinjawadi','Integrated']],
                ['vilas-javdekar-yashwin','Yashwin Encore','Wakad',77,'Practical west Pune family housing near established services',['2–3 BHK','Wakad','Family']],
                ['saarrthi-skybay','Saarrthi Skybay','Balewadi',76,'Urban high-rise living close to Balewadi High Street',['High-rise','Balewadi','Lifestyle']],
                ['kalpataru-exquisite','Kalpataru Exquisite','Wakad',75,'Premium positioning with strong western suburb connectivity',['Premium','Wakad','Connectivity']],
                ['kohinoor-courtyard-one','Kohinoor Courtyard One','Wakad',74,'Compact urban homes with access to retail and employment',['2–3 BHK','Wakad','Urban']],
                ['vision-vanalika','Vision Vanalika','Ravet',73,'Value-focused development in a fast-growing residential belt',['2–3 BHK','Ravet','Value']],
                ['ravet-central-residences','Ravet Central Residences','Ravet',72,'Entry-level family housing near expanding road infrastructure',['Affordable','Ravet','Family']],
                ['undri-terraces','Undri Terraces','Undri',71,'South Pune residential option with larger-format homes',['3 BHK','Undri','Space']],
                ['moshi-urban-greens','Moshi Urban Greens','Moshi',70,'Affordable growth-corridor housing with improving connectivity',['Affordable','Moshi','Growth corridor']],
            ],
            'hospitals' => [
                ['ruby-hall-clinic','Ruby Hall Clinic','Sassoon Road',95,'Depth of specialties and emergency capability',['Multi-speciality','Emergency','Central Pune']],
                ['jupiter-hospital','Jupiter Hospital','Baner',93,'Modern tertiary care with western Pune access',['Tertiary','Baner','Emergency']],
                ['sahyadri-super-speciality','Sahyadri Super Speciality Hospital','Deccan',91,'Broad speciality coverage with strong central access',['Multi-speciality','Deccan','Critical care']],
                ['deenanath-mangeshkar','Deenanath Mangeshkar Hospital','Erandwane',89,'Established clinical breadth with trusted citywide reach',['Multi-speciality','Erandwane','Emergency']],
                ['manipal-hospital-kharadi','Manipal Hospital Kharadi','Kharadi',87,'East Pune tertiary care close to major residential and IT hubs',['Tertiary','Kharadi','East Pune']],
            ],
            'schools' => [
                ['the-orbis-school','The Orbis School','Keshav Nagar',94,'Balanced academics, activities and parent accessibility',['CBSE','K-12','East Pune']],
                ['vidya-valley','Vidya Valley School','Sus',92,'Campus environment with established academic reputation',['ICSE','Campus','West Pune']],
                ['the-lexicon-international','The Lexicon International School','Wagholi',90,'Large-format schooling with broad co-curricular exposure',['CBSE','Wagholi','Activities']],
                ['victorious-kidss-educares','Victorious Kidss Educares','Kharadi',88,'International curriculum orientation in east Pune',['IB','International','Kharadi']],
                ['symbiosis-international-school','Symbiosis International School','Viman Nagar',86,'International-learning environment with central-east access',['IB','International','Viman Nagar']],
            ],
            'restaurants' => [
                ['malaka-spice','Malaka Spice','Koregaon Park',96,'A Pune dining institution with consistent identity',['Asian','Destination dining','Koregaon Park']],
                ['the-daily-all-day','The Daily All Day','Koregaon Park',93,'Polished all-day format with strong social appeal',['Contemporary','Brunch','Social']],
                ['shizusan-phoenix','Shizusan','Viman Nagar',91,'Pan-Asian menu with a high-energy modern setting',['Asian','Viman Nagar','Modern']],
                ['paasha','Paasha','Senapati Bapat Road',89,'Elevated rooftop dining with city views and occasion appeal',['North Indian','Rooftop','Luxury']],
                ['arthurs-theme','Arthur’s Theme','Koregaon Park',87,'European-inspired comfort dining with loyal local following',['European','Casual fine dining','Koregaon Park']],
            ],
            'hotels' => [
                ['conrad-pune','Conrad Pune','Sangamvadi',96,'Luxury stay with strong business and leisure fit',['Luxury','Business','Spa']],
                ['ritz-carlton-pune','The Ritz-Carlton Pune','Yerawada',95,'High-luxury hospitality with refined service and dining',['Luxury','Fine dining','Spa']],
                ['jw-marriott-pune','JW Marriott Pune','Senapati Bapat Road',93,'Flagship city hotel combining business, events and dining',['Luxury','Events','Business']],
                ['westin-pune','The Westin Pune Koregaon Park','Mundhwa',91,'Lifestyle luxury near Koregaon Park and business districts',['Luxury','Lifestyle','Business']],
                ['hyatt-pune','Hyatt Pune','Kalyani Nagar',88,'Reliable upscale stay with east Pune connectivity',['Upscale','Kalyani Nagar','Business']],
            ],
            'colleges' => [
                ['fergusson-college','Fergusson College','Shivajinagar',96,'Legacy, academic breadth and central-city advantage',['Arts','Science','Legacy']],
                ['coep-technological-university','COEP Technological University','Shivajinagar',95,'Engineering legacy with strong academic and industry reputation',['Engineering','Technology','Legacy']],
                ['symbiosis-college','Symbiosis College of Arts & Commerce','Senapati Bapat Road',92,'Strong urban campus ecosystem with broad student appeal',['Arts','Commerce','Urban']],
                ['mit-wpu','MIT World Peace University','Kothrud',90,'Multi-disciplinary private university with modern campus ecosystem',['University','Kothrud','Multi-disciplinary']],
                ['flame-university','FLAME University','Lavale',88,'Residential liberal education campus with premium positioning',['Liberal education','Residential','Campus']],
            ],
            'malls' => [
                ['phoenix-marketcity','Phoenix Marketcity','Viman Nagar',95,'High retail breadth with entertainment and dining',['Shopping','Entertainment','Viman Nagar']],
                ['pavillion-mall','The Pavillion','Senapati Bapat Road',92,'Premium retail and dining in a central-west catchment',['Premium retail','Dining','Central']],
                ['seasons-mall','Seasons Mall','Magarpatta',89,'Convenient east Pune retail and entertainment destination',['Shopping','Cinema','East Pune']],
                ['amanora-mall','Amanora Mall','Hadapsar',87,'Large shopping destination integrated with township catchment',['Shopping','Hadapsar','Township']],
                ['westend-mall','Westend Mall','Aundh',85,'Compact premium neighbourhood mall serving west-central Pune',['Shopping','Aundh','Neighbourhood']],
            ],
            'gyms' => [
                ['multifit-baner','MultiFit Baner','Baner',92,'Functional training and community-led fitness',['Functional','Strength','Baner']],
                ['cult-fit-kharadi','Cult.fit Kharadi','Kharadi',90,'Group training convenience for east Pune professionals',['Group classes','Kharadi','Fitness']],
                ['gold-gym-kalyani-nagar','Gold’s Gym Kalyani Nagar','Kalyani Nagar',88,'Strength and cardio-focused full-service gym format',['Strength','Cardio','Kalyani Nagar']],
                ['abs-fitness-aundh','ABS Fitness Aundh','Aundh',86,'Accessible neighbourhood fitness with broad equipment mix',['Gym','Aundh','Personal training']],
                ['nitrro-baner','Nitrro Bespoke Fitness','Baner',84,'Premium coaching-led fitness for personalised routines',['Premium','Coaching','Baner']],
            ],
            'salons' => [
                ['jawed-habib-aundh','Jawed Habib Aundh','Aundh',91,'Accessible full-service grooming proposition',['Hair','Grooming','Aundh']],
                ['enrich-koregaon-park','Enrich Salon','Koregaon Park',89,'Premium grooming in a central lifestyle neighbourhood',['Salon','Premium','Koregaon Park']],
                ['lakme-salon-baner','Lakmé Salon Baner','Baner',87,'Well-known beauty and styling format with neighbourhood access',['Beauty','Hair','Baner']],
                ['juice-salon-kalyani-nagar','Juice Salon','Kalyani Nagar',85,'Contemporary styling-focused salon experience',['Styling','Beauty','Kalyani Nagar']],
                ['bblunt-pune','BBlunt Pune','Koregaon Park',83,'Fashion-led hair and styling proposition',['Hair','Fashion','Premium']],
            ],
            'automotive' => [
                ['bavaria-motors','Bavaria Motors','Hadapsar',94,'Premium sales and service experience',['Premium','Service','BMW']],
                ['pashankar-auto','Pashankar Auto','Baner',91,'Established dealer network with strong west Pune reach',['Sales','Service','Baner']],
                ['b-u-bhandari','B U Bhandari Auto','Wakad',89,'Multi-location sales and after-sales convenience',['Sales','Service','Wakad']],
                ['sai-service','Sai Service','Deccan',87,'Long-running automotive sales and service presence',['Sales','Service','Central Pune']],
                ['garve-motors','Garve Motors','Wakad',85,'Growth-corridor dealer presence with broad customer access',['Dealer','Wakad','Service']],
            ],
            'coworking' => [
                ['wework-futura','WeWork Futura','Magarpatta',94,'Strong workspace experience and business connectivity',['Managed office','Enterprise','Magarpatta']],
                ['awfis-baner','Awfis Baner','Baner',91,'Flexible workspace for teams needing west Pune access',['Coworking','Baner','Flexible']],
                ['smartworks-marisoft','Smartworks Marisoft','Kalyani Nagar',89,'Enterprise-oriented managed offices in a major business node',['Managed office','Enterprise','Kalyani Nagar']],
                ['regus-world-trade-center','Regus World Trade Center','Kharadi',87,'Professional serviced-office format in east Pune',['Serviced office','Kharadi','Business']],
                ['91springboard-yerawada','91springboard Yerawada','Yerawada',85,'Community-led coworking for startups and small teams',['Coworking','Startups','Yerawada']],
            ],
            'localities' => [
                ['baner','Baner','West Pune',96,'Connectivity, dining, offices and residential depth',['Metro access','Schools','Dining']],
                ['kharadi','Kharadi','East Pune',94,'Major employment hub with strong rental and lifestyle demand',['IT hub','Restaurants','Housing']],
                ['koregaon-park','Koregaon Park','Central-East Pune',92,'Lifestyle-led neighbourhood with dining, nightlife and premium homes',['Dining','Luxury','Lifestyle']],
                ['aundh','Aundh','West-Central Pune',90,'Mature residential ecosystem with schools, retail and healthcare',['Schools','Retail','Residential']],
                ['viman-nagar','Viman Nagar','East Pune',88,'Airport proximity, malls, education and office connectivity',['Airport','Shopping','Offices']],
            ],
            'preschools' => [
                ['vivero-kalyani-nagar','Vivero Kalyani Nagar','Kalyani Nagar',93,'Early-learning environment with strong parent experience',['Early years','Activity-led','Kalyani Nagar']],
                ['kido-baner','Kido Baner','Baner',90,'Contemporary early-learning environment for west Pune families',['Preschool','Baner','Activity-led']],
                ['eurokids-kharadi','EuroKids Kharadi','Kharadi',88,'Accessible preschool format in a fast-growing residential catchment',['Preschool','Kharadi','Early years']],
                ['little-millennium-aundh','Little Millennium Aundh','Aundh',86,'Neighbourhood early-learning option with structured activity mix',['Preschool','Aundh','Activities']],
                ['kidzee-viman-nagar','Kidzee Viman Nagar','Viman Nagar',84,'Convenient early-years option near east Pune residential clusters',['Preschool','Viman Nagar','Early years']],
            ],
            'doctors' => [
                ['cardiology-practice','Leading Cardiology Practice','Deccan',94,'Illustrative specialist profile for cardiovascular consultation',['Cardiology','Specialist','Deccan']],
                ['orthopaedic-practice-baner','Orthopaedic Care Centre','Baner',91,'Illustrative orthopaedic profile focused on joints and sports injuries',['Orthopaedics','Sports injury','Baner']],
                ['dermatology-practice-kp','Skin & Aesthetics Clinic','Koregaon Park',89,'Illustrative dermatology profile for skin and aesthetic care',['Dermatology','Skin','Koregaon Park']],
                ['paediatric-practice-aundh','Child Health Clinic','Aundh',87,'Illustrative paediatric profile for routine and specialist child care',['Paediatrics','Children','Aundh']],
                ['ent-practice-kharadi','ENT Specialist Centre','Kharadi',85,'Illustrative ENT profile for east Pune residents',['ENT','Specialist','Kharadi']],
            ],
            'banquets' => [
                ['the-corinthians','The Corinthians','Undri',94,'Large-format event flexibility and resort setting',['Weddings','Events','Resort']],
                ['hyatt-regency-ballroom','Hyatt Regency Ballroom','Viman Nagar',92,'Premium hotel ballroom for large formal events',['Weddings','Corporate','Luxury']],
                ['jw-marriott-ballroom','JW Marriott Ballroom','Senapati Bapat Road',90,'Central luxury venue with strong event-service depth',['Luxury','Weddings','Corporate']],
                ['royal-orchid-central','Royal Orchid Central','Kalyani Nagar',88,'Convenient hotel event space for medium-format celebrations',['Banquet','Kalyani Nagar','Events']],
                ['orchid-hotel-pune','The Orchid Hotel Pune','Balewadi',86,'Large event inventory close to west Pune business districts',['Banquet','Balewadi','Weddings']],
            ],
            'cafes' => [
                ['third-wave-coffee-kp','Third Wave Coffee','Koregaon Park',92,'Reliable coffee-led work and meet-up experience',['Coffee','Work-friendly','Koregaon Park']],
                ['blue-tokai-kalyani-nagar','Blue Tokai Coffee Roasters','Kalyani Nagar',90,'Specialty coffee focus with contemporary neighbourhood appeal',['Specialty coffee','Kalyani Nagar','Work-friendly']],
                ['grey-soul-coffee','Grey Soul Coffee Roasters','Koregaon Park',88,'Roastery-led experience for serious coffee drinkers',['Coffee','Roastery','Koregaon Park']],
                ['cafe-peter-aundh','Cafe Peter','Aundh',86,'Casual all-day café suited to quick meetings and comfort food',['Cafe','Aundh','Casual']],
                ['zen-cafe-koregaon-park','Zen Café','Koregaon Park',84,'Quiet café format for conversations and slow afternoons',['Cafe','Quiet','Koregaon Park']],
            ],
            'weekend' => [
                ['mulshi','Mulshi','Pune district',96,'Scenic drive, monsoon appeal and easy weekend access',['Nature','Drive','Monsoon']],
                ['lonavala','Lonavala','Pune district',94,'Classic hill-station escape with broad stay and food options',['Hill station','Drive','Family']],
                ['lavasa','Lavasa','Mulshi taluka',90,'Lakeside roads and architecture-led day-trip appeal',['Lake','Drive','Day trip']],
                ['tamhini-ghat','Tamhini Ghat','Pune district',88,'Monsoon drives, waterfalls and dramatic Western Ghats scenery',['Nature','Monsoon','Drive']],
                ['bhimashankar','Bhimashankar','Pune district',86,'Temple, forest and trekking appeal for a longer day trip',['Temple','Trek','Nature']],
            ],
        ];

        $entities = [];
        foreach ($groups as $vertical => $items) {
            foreach ($items as $item) {
                [$slug,$name,$location,$score,$highlight,$tags] = $item;
                $entities[] = [
                    'vertical' => $vertical,
                    'slug' => $slug,
                    'name' => $name,
                    'location' => $location,
                    'score' => $score,
                    'highlight' => $highlight,
                    'tags' => $tags,
                    'status' => 'published',
                    'demo' => true,
                ];
            }
        }

        return $entities;
    }
}
