<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlacesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Philippine Places Data
        $philippinePlaces = [
            [
                'name' => 'Banaue Rice Terraces',
                'description' => 'Often called the \'Eighth Wonder of the World,\' these 2,000-year-old terraces were carved into the mountains by the Ifugao people.',
                'location' => 'Banaue, Ifugao, Philippines',
                'latitude' => '16.9333',
                'longitude' => '121.1333',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'Ancient hand-carved terraces of breathtaking beauty.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Chocolate Hills',
                'description' => 'A geological formation of over 1,200 symmetrical hills that turn brown during the dry season.',
                'location' => 'Bohol, Philippines',
                'latitude' => '9.8250',
                'longitude' => '124.1500',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'Nature\'s sweetest wonder of Bohol.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mayon Volcano',
                'description' => 'An active volcano famous for its perfectly symmetrical cone shape.',
                'location' => 'Albay, Bicol, Philippines',
                'latitude' => '13.2578',
                'longitude' => '123.6854',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'The world\'s most perfect volcanic cone.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Intramuros',
                'description' => 'The historic walled city of Manila, built during the Spanish colonial period.',
                'location' => 'Manila, Philippines',
                'latitude' => '14.5891',
                'longitude' => '120.9730',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'Step back in time inside Manila\'s walled city.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'San Agustin Church',
                'description' => 'A UNESCO World Heritage site and the oldest stone church in the Philippines, built in 1607.',
                'location' => 'Intramuros, Manila, Philippines',
                'latitude' => '14.5896',
                'longitude' => '120.9737',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A timeless symbol of Spanish-era faith.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Fort Santiago',
                'description' => 'A 16th-century citadel and historic site where Dr. José Rizal was imprisoned.',
                'location' => 'Intramuros, Manila, Philippines',
                'latitude' => '14.5899',
                'longitude' => '120.9733',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A fortress rich with Philippine history.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'National Museum of Fine Arts',
                'description' => 'A museum housing classical Filipino art, including works of Juan Luna and Félix Resurrección Hidalgo.',
                'location' => 'Padre Burgos Avenue, Manila, Philippines',
                'latitude' => '14.5833',
                'longitude' => '120.9790',
                'country' => 'Philippines',
                'type' => 'museum',
                'caption' => 'Home of the Philippines greatest masterpieces.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Puerto Princesa Underground River',
                'description' => 'A UNESCO World Heritage site and one of the New 7 Wonders of Nature, featuring a navigable underground river.',
                'location' => 'Puerto Princesa, Palawan, Philippines',
                'latitude' => '10.2030',
                'longitude' => '118.9260',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A subterranean wonder of Palawan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Rizal Park (Luneta)',
                'description' => 'A historic urban park dedicated to national hero Dr. José Rizal.',
                'location' => 'Ermita, Manila, Philippines',
                'latitude' => '14.5820',
                'longitude' => '120.9796',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A landmark of freedom and remembrance.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Calle Crisologo',
                'description' => 'A preserved cobblestone street showcasing Spanish colonial-era houses.',
                'location' => 'Vigan, Ilocos Sur, Philippines',
                'latitude' => '17.5700',
                'longitude' => '120.3867',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'Walk through the charm of old Vigan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Malacañang Palace Museum',
                'description' => 'A museum within the presidential palace showcasing Philippine political history.',
                'location' => 'San Miguel, Manila, Philippines',
                'latitude' => '14.5995',
                'longitude' => '120.9822',
                'country' => 'Philippines',
                'type' => 'museum',
                'caption' => 'Explore the heritage of Philippine leadership.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Taal Volcano',
                'description' => 'An active volcano within a lake, famous for its picturesque view.',
                'location' => 'Batangas, Philippines',
                'latitude' => '13.0022',
                'longitude' => '120.9936',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A volcano within a lake within a volcano.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Magellan\'s Cross',
                'description' => 'A Christian cross planted by Ferdinand Magellan upon arriving in Cebu in 1521.',
                'location' => 'Cebu City, Philippines',
                'latitude' => '10.2920',
                'longitude' => '123.9059',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'The cradle of Christianity in the Philippines.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Apo Reef Natural Park',
                'description' => 'The second-largest contiguous coral reef system in the world, rich in marine biodiversity.',
                'location' => 'Occidental Mindoro, Philippines',
                'latitude' => '12.7500',
                'longitude' => '120.6500',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A diver\'s paradise of corals and marine life.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mind Museum',
                'description' => 'A modern science museum offering interactive exhibits for all ages.',
                'location' => 'Bonifacio Global City, Taguig, Philippines',
                'latitude' => '14.5547',
                'longitude' => '121.0450',
                'country' => 'Philippines',
                'type' => 'museum',
                'caption' => 'Where science comes alive in fun ways.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Enchanted Kingdom',
                'description' => 'The country\'s premier theme park offering rides, attractions, and entertainment.',
                'location' => 'Santa Rosa, Laguna, Philippines',
                'latitude' => '14.3122',
                'longitude' => '121.1161',
                'country' => 'Philippines',
                'type' => 'other',
                'caption' => 'A magical place for family adventures.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Coron',
                'description' => 'A tropical paradise with limestone cliffs, turquoise lagoons, and WWII shipwreck diving spots.',
                'location' => 'Palawan, Philippines',
                'latitude' => '12.2000',
                'longitude' => '120.2000',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A dreamy escape into crystal-clear waters.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Hundred Islands National Park',
                'description' => 'A protected area featuring 124 islands with white-sand beaches and caves.',
                'location' => 'Alaminos, Pangasinan, Philippines',
                'latitude' => '16.1760',
                'longitude' => '119.9700',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'Explore countless islands of beauty.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Camiguin White Island',
                'description' => 'A pristine sandbar offering panoramic views of Mount Hibok-Hibok.',
                'location' => 'Camiguin, Philippines',
                'latitude' => '9.2500',
                'longitude' => '124.7000',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'A white sandbar in the middle of paradise.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Paoay Church',
                'description' => 'A UNESCO World Heritage site and one of the best examples of Baroque architecture in the Philippines.',
                'location' => 'Paoay, Ilocos Norte, Philippines',
                'latitude' => '18.1167',
                'longitude' => '120.6000',
                'country' => 'Philippines',
                'type' => 'heritage',
                'caption' => 'Majestic Baroque beauty of Ilocos.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert places and get their IDs for photos
        foreach ($philippinePlaces as $index => $placeData) {
            $placeId = DB::table('places')->insertGetId($placeData);
            
            // Get filename from JSON data
            $jsonData = [
                'banaue.png', 'chocolatehills.png', 'mayon.png', 'intramuros.png', 'sanagustin.png',
                'fortsantiago.png', 'fineartsmuseum.png', 'puertoprincesa.png', 'rizalpark.png', 'callecrisologo.png',
                'malacanang.png', 'taal.png', 'magellanscross.png', 'aporeef.png', 'mindmuseum.png',
                'enchantedkingdom.png', 'coron.png', 'hundredisland.png', 'camiguinisland.png', 'paoaychurch.png'
            ];
            
            // Insert corresponding photo
            DB::table('places_photo')->insert([
                'place_id' => $placeId,
                'caption' => $placeData['caption'],
                'description' => $placeData['description'],
                'filename' => $jsonData[$index],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info('Philippine places and photos seeded successfully!');
        $this->command->info('Seeded ' . count($philippinePlaces) . ' places with their corresponding photos.');
    }
}
