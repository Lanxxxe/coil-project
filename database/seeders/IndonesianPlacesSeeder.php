<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Places;
use App\Models\PlacesPhoto;

class IndonesianPlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $indonesianPlaces = [
            [
                'name' => 'Borobudur Temple',
                'description' => 'The world\'s largest Buddhist temple, built in the 9th century and a UNESCO World Heritage site.',
                'latitude' => '-7.607958',
                'longitude' => '110.203824',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Magelang, Central Java, Indonesia',
                'caption' => 'A majestic symbol of Buddhist heritage.',
                'photos' => [
                    [
                        'caption' => 'Ancient Buddhist temple complex',
                        'description' => 'UNESCO World Heritage Buddhist temple',
                        'filename' => 'borobudur.png'
                    ]
                ]
            ],
            [
                'name' => 'Prambanan Temple',
                'description' => 'A 9th-century Hindu temple compound dedicated to the Trimurti: Brahma, Vishnu, and Shiva.',
                'latitude' => '-7.752016',
                'longitude' => '110.491456',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Sleman, Yogyakarta, Indonesia',
                'caption' => 'An iconic masterpiece of Hindu architecture.',
                'photos' => [
                    [
                        'caption' => 'Hindu temple complex',
                        'description' => 'Ancient Hindu temple dedicated to Trimurti',
                        'filename' => 'prambanan.png'
                    ]
                ]
            ],
            [
                'name' => 'Bali Tanah Lot Temple',
                'description' => 'A stunning rock formation with a sea temple perched on top, famous for sunset views.',
                'latitude' => '-8.621229',
                'longitude' => '115.086885',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Tabanan, Bali, Indonesia',
                'caption' => 'A spiritual temple with breathtaking sunsets.',
                'photos' => [
                    [
                        'caption' => 'Sea temple on rock formation',
                        'description' => 'Temple perched on rock with sunset views',
                        'filename' => 'tanahlot.png'
                    ]
                ]
            ],
            [
                'name' => 'Mount Bromo',
                'description' => 'An active volcano known for its dramatic landscapes and sunrise views.',
                'latitude' => '-7.942069',
                'longitude' => '112.952977',
                'type' => 'landmark',
                'country' => 'Indonesia',
                'location' => 'East Java, Indonesia',
                'caption' => 'A mystical sunrise above the volcano.',
                'photos' => [
                    [
                        'caption' => 'Active volcano landscape',
                        'description' => 'Dramatic volcanic landscape with sunrise views',
                        'filename' => 'mountbromo.png'
                    ]
                ]
            ],
            [
                'name' => 'Komodo National Park',
                'description' => 'Home of the famous Komodo dragons, with islands and stunning marine biodiversity.',
                'latitude' => '-8.589191',
                'longitude' => '119.462412',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'East Nusa Tenggara, Indonesia',
                'caption' => 'A land of dragons and untouched beauty.',
                'photos' => [
                    [
                        'caption' => 'Komodo dragon habitat',
                        'description' => 'National park home to Komodo dragons',
                        'filename' => 'komodopark.png'
                    ]
                ]
            ],
            [
                'name' => 'Ubud Monkey Forest',
                'description' => 'A sacred forest sanctuary inhabited by hundreds of monkeys, with temples and lush greenery.',
                'latitude' => '-8.519012',
                'longitude' => '115.262951',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Ubud, Bali, Indonesia',
                'caption' => 'A peaceful jungle where monkeys roam free.',
                'photos' => [
                    [
                        'caption' => 'Sacred monkey forest sanctuary',
                        'description' => 'Forest sanctuary with temples and monkeys',
                        'filename' => 'ubudforest.png'
                    ]
                ]
            ],
            [
                'name' => 'Taman Mini Indonesia Indah',
                'description' => 'A cultural park showcasing Indonesia\'s diverse heritage and architecture.',
                'latitude' => '-6.2925',
                'longitude' => '106.8956',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Jakarta, Indonesia',
                'caption' => 'Discover the rich culture of Indonesia in one place.',
                'photos' => [
                    [
                        'caption' => 'Cultural heritage park',
                        'description' => 'Park showcasing Indonesian heritage and architecture',
                        'filename' => 'tamanindah.png'
                    ]
                ]
            ],
            [
                'name' => 'Istiqlal Mosque',
                'description' => 'The largest mosque in Southeast Asia, symbolizing Indonesian independence.',
                'latitude' => '-6.1705',
                'longitude' => '106.8307',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Jakarta, Indonesia',
                'caption' => 'A grand mosque of unity and faith.',
                'photos' => [
                    [
                        'caption' => 'Largest mosque in Southeast Asia',
                        'description' => 'Grand mosque symbolizing Indonesian independence',
                        'filename' => 'istiqlalmosque.png'
                    ]
                ]
            ],
            [
                'name' => 'Borobudur Museum',
                'description' => 'A museum displaying archaeological artifacts from the Borobudur temple area.',
                'latitude' => '-7.6075',
                'longitude' => '110.2033',
                'type' => 'museum',
                'country' => 'Indonesia',
                'location' => 'Magelang, Central Java, Indonesia',
                'caption' => 'Unveiling the stories behind Borobudur.',
                'photos' => [
                    [
                        'caption' => 'Archaeological museum',
                        'description' => 'Museum with Borobudur temple artifacts',
                        'filename' => 'borobudurmuseum.png'
                    ]
                ]
            ],
            [
                'name' => 'Jakarta National Monument (Monas)',
                'description' => 'A 132-meter monument symbolizing Indonesia\'s struggle for independence.',
                'latitude' => '-6.1754',
                'longitude' => '106.8272',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Jakarta, Indonesia',
                'caption' => 'Standing tall as Indonesia\'s freedom symbol.',
                'photos' => [
                    [
                        'caption' => 'National independence monument',
                        'description' => '132-meter monument symbolizing independence',
                        'filename' => 'monas.png'
                    ]
                ]
            ],
            [
                'name' => 'Wayang Museum',
                'description' => 'A museum dedicated to Indonesia\'s traditional shadow puppetry art.',
                'latitude' => '-6.1744',
                'longitude' => '106.8227',
                'type' => 'museum',
                'country' => 'Indonesia',
                'location' => 'Jakarta, Indonesia',
                'caption' => 'Experience the magic of wayang puppetry.',
                'photos' => [
                    [
                        'caption' => 'Traditional shadow puppetry museum',
                        'description' => 'Museum showcasing Indonesian wayang art',
                        'filename' => 'wayangmuseum.png'
                    ]
                ]
            ],
            [
                'name' => 'Bali Uluwatu Temple',
                'description' => 'A cliff-top temple overlooking the Indian Ocean, famous for Kecak dance performances.',
                'latitude' => '-8.8291',
                'longitude' => '115.0880',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Bali, Indonesia',
                'caption' => 'Sacred cliffs meet cultural performances.',
                'photos' => [
                    [
                        'caption' => 'Cliff-top temple overlooking ocean',
                        'description' => 'Temple on cliffs with Kecak dance performances',
                        'filename' => 'uluwatutemple.png'
                    ]
                ]
            ],
            [
                'name' => 'Raja Ampat Islands',
                'description' => 'An archipelago with some of the richest marine biodiversity in the world.',
                'latitude' => '-0.2333',
                'longitude' => '130.6167',
                'type' => 'landmark',
                'country' => 'Indonesia',
                'location' => 'West Papua, Indonesia',
                'caption' => 'A diver\'s paradise with crystal-clear waters.',
                'photos' => [
                    [
                        'caption' => 'Marine biodiversity paradise',
                        'description' => 'Archipelago with rich marine biodiversity',
                        'filename' => 'rajaampat.png'
                    ]
                ]
            ],
            [
                'name' => 'Taman Sari Water Castle',
                'description' => 'A former royal garden and bathing complex of the Sultanate of Yogyakarta.',
                'latitude' => '-7.7900',
                'longitude' => '110.3630',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Yogyakarta, Indonesia',
                'caption' => 'Echoes of royal luxury and history.',
                'photos' => [
                    [
                        'caption' => 'Royal water castle complex',
                        'description' => 'Former royal garden and bathing complex',
                        'filename' => 'tamansari.png'
                    ]
                ]
            ],
            [
                'name' => 'Bandung Geological Museum',
                'description' => 'A museum showcasing fossils, minerals, and geological history of Indonesia.',
                'latitude' => '-6.9147',
                'longitude' => '107.6098',
                'type' => 'museum',
                'country' => 'Indonesia',
                'location' => 'Bandung, West Java, Indonesia',
                'caption' => 'Explore Indonesia\'s ancient earth story.',
                'photos' => [
                    [
                        'caption' => 'Geological history museum',
                        'description' => 'Museum with fossils and minerals of Indonesia',
                        'filename' => 'bandungmuseum.png'
                    ]
                ]
            ],
            [
                'name' => 'Toba Lake',
                'description' => 'The largest volcanic lake in the world, with Samosir Island at its center.',
                'latitude' => '2.7150',
                'longitude' => '98.8820',
                'type' => 'landmark',
                'country' => 'Indonesia',
                'location' => 'North Sumatra, Indonesia',
                'caption' => 'A serene volcanic lake with rich Batak culture.',
                'photos' => [
                    [
                        'caption' => 'Largest volcanic lake in the world',
                        'description' => 'Volcanic lake with Samosir Island and Batak culture',
                        'filename' => 'tobalake.png'
                    ]
                ]
            ],
            [
                'name' => 'Maimun Palace',
                'description' => 'A royal palace of the Deli Sultanate with a blend of Malay, Mughal, and Italian architecture.',
                'latitude' => '3.5952',
                'longitude' => '98.6722',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Medan, North Sumatra, Indonesia',
                'caption' => 'A regal landmark of Medan\'s heritage.',
                'photos' => [
                    [
                        'caption' => 'Royal palace with diverse architecture',
                        'description' => 'Palace with Malay, Mughal, and Italian architecture',
                        'filename' => 'maimunpalace.png'
                    ]
                ]
            ],
            [
                'name' => 'Makassar Fort Rotterdam',
                'description' => 'A 17th-century Dutch fort and cultural heritage site in Makassar.',
                'latitude' => '-5.1342',
                'longitude' => '119.5000',
                'type' => 'heritage',
                'country' => 'Indonesia',
                'location' => 'Makassar, South Sulawesi, Indonesia',
                'caption' => 'A historic fortress by the sea.',
                'photos' => [
                    [
                        'caption' => '17th-century Dutch fort',
                        'description' => 'Historic Dutch fort and cultural heritage site',
                        'filename' => 'fortrotterdam.png'
                    ]
                ]
            ],
            [
                'name' => 'Bali Neka Art Museum',
                'description' => 'A museum housing traditional and modern Balinese art collections.',
                'latitude' => '-8.5069',
                'longitude' => '115.2623',
                'type' => 'museum',
                'country' => 'Indonesia',
                'location' => 'Ubud, Bali, Indonesia',
                'caption' => 'Celebrating the beauty of Balinese art.',
                'photos' => [
                    [
                        'caption' => 'Traditional and modern Balinese art',
                        'description' => 'Museum with traditional and modern Balinese art',
                        'filename' => 'balinekamuseum.png'
                    ]
                ]
            ],
            [
                'name' => 'Gili Islands',
                'description' => 'A trio of small islands known for white-sand beaches and coral reefs.',
                'latitude' => '-8.3500',
                'longitude' => '116.0333',
                'type' => 'landmark',
                'country' => 'Indonesia',
                'location' => 'Lombok, West Nusa Tenggara, Indonesia',
                'caption' => 'A tropical escape with crystal waters.',
                'photos' => [
                    [
                        'caption' => 'Trio of tropical islands',
                        'description' => 'Small islands with white-sand beaches and coral reefs',
                        'filename' => 'giliislands.png'
                    ]
                ]
            ],
        ];

        foreach ($indonesianPlaces as $placeData) {
            // Extract photos data before creating the place
            $photos = $placeData['photos'];
            unset($placeData['photos']);

            // Create the place
            $place = Places::create($placeData);

            // Create associated photos
            foreach ($photos as $photoData) {
                $photoData['place_id'] = $place->place_id;
                PlacesPhoto::create($photoData);
            }
        }

        $this->command->info('Indonesian places data seeded successfully!');
    }
}