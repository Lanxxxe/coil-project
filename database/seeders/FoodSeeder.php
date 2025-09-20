<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Philippine Foods Data
        $philippineFoods = [
            [
                'name' => 'Adobo',
                'description' => 'A savory stew of chicken or pork marinated in soy sauce, vinegar, garlic, and spices.',
                'place_of_origin' => 'Nationwide (Philippines)',
                'category' => 'Main Dish',
                'caption' => 'The unofficial national dish of the Philippines.',
                'price' => 185.00, // Average of 120-250
                'filename' => 'adobo.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sinigang',
                'description' => 'A sour tamarind-based soup with pork, shrimp, or fish and mixed vegetables.',
                'place_of_origin' => 'Nationwide (Philippines)',
                'category' => 'Soup',
                'caption' => 'A comforting bowl of sour and savory flavors.',
                'price' => 215.00, // Average of 150-280
                'filename' => 'sinigang.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Lechon',
                'description' => 'A whole roasted pig with crispy skin and tender meat, often served at feasts.',
                'place_of_origin' => 'Cebu',
                'category' => 'Main Dish',
                'caption' => 'Crispy, juicy, and a true fiesta star.',
                'price' => 425.00, // Average of 350-500 per kilo
                'filename' => 'lechon.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Kare-Kare',
                'description' => 'A peanut-based stew with oxtail, tripe, and vegetables, served with bagoong (shrimp paste).',
                'place_of_origin' => 'Pampanga',
                'category' => 'Main Dish',
                'caption' => 'A rich peanut stew best with shrimp paste.',
                'price' => 275.00, // Average of 200-350
                'filename' => 'karekare.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Laing',
                'description' => 'Taro leaves cooked in coconut milk and chili peppers.',
                'place_of_origin' => 'Bicol Region',
                'category' => 'Main Dish',
                'caption' => 'Spicy coconut goodness from Bicol.',
                'price' => 140.00, // Average of 100-180
                'filename' => 'laing.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pancit Canton',
                'description' => 'Stir-fried egg noodles with vegetables, soy sauce, and meat or seafood.',
                'place_of_origin' => 'Nationwide (Chinese-Filipino Influence)',
                'category' => 'Noodles',
                'caption' => 'A noodle dish for every celebration.',
                'price' => 170.00, // Average of 120-220
                'filename' => 'pancitcanton.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Halo-Halo',
                'description' => 'A shaved ice dessert with sweetened fruits, jellies, leche flan, and purple yam.',
                'place_of_origin' => 'Nationwide (Philippines)',
                'category' => 'Dessert',
                'caption' => 'A colorful and refreshing summer treat.',
                'price' => 140.00, // Average of 100-180
                'filename' => 'halohalo.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bibingka',
                'description' => 'A rice cake baked in banana leaves, topped with salted egg and cheese.',
                'place_of_origin' => 'Nationwide (Christmas Tradition)',
                'category' => 'Dessert',
                'caption' => 'A warm holiday rice cake favorite.',
                'price' => 85.00, // Average of 50-120
                'filename' => 'bibingka.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Putobumbong',
                'description' => 'A purple sticky rice delicacy steamed in bamboo tubes, topped with coconut and sugar.',
                'place_of_origin' => 'Nationwide (Christmas Tradition)',
                'category' => 'Dessert',
                'caption' => 'Christmas wouldn\'t be complete without this.',
                'price' => 70.00, // Average of 40-100
                'filename' => 'putobumbong.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Sisig',
                'description' => 'A sizzling dish made of chopped pig\'s head, liver, and spices, often served with egg.',
                'place_of_origin' => 'Pampanga',
                'category' => 'Main Dish',
                'caption' => 'A sizzling Pampanga delicacy.',
                'price' => 200.00, // Average of 150-250
                'filename' => 'sisig.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bulalo',
                'description' => 'A beef marrow stew with corn, cabbage, and potatoes in a clear broth.',
                'place_of_origin' => 'Batangas',
                'category' => 'Soup',
                'caption' => 'A hearty beef bone marrow soup.',
                'price' => 325.00, // Average of 250-400
                'filename' => 'bulalo.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Batchoy',
                'description' => 'A noodle soup with pork, liver, chicharrón, and egg in a savory broth.',
                'place_of_origin' => 'Iloilo',
                'category' => 'Noodles',
                'caption' => 'Iloilo\'s iconic noodle comfort food.',
                'price' => 115.00, // Average of 80-150
                'filename' => 'batchoy.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Inasal',
                'description' => 'Grilled chicken marinated in calamansi, vinegar, and annatto oil.',
                'place_of_origin' => 'Bacolod',
                'category' => 'Main Dish',
                'caption' => 'Juicy grilled chicken Bacolod-style.',
                'price' => 160.00, // Average of 120-200
                'filename' => 'inasal.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Dinuguan',
                'description' => 'A savory stew made from pork offal simmered in pig\'s blood, vinegar, and spices.',
                'place_of_origin' => 'Nationwide (Philippines)',
                'category' => 'Main Dish',
                'caption' => 'Rich and bold flavors in a dark stew.',
                'price' => 140.00, // Average of 100-180
                'filename' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Taho',
                'description' => 'A warm snack made of soft tofu, arnibal (caramelized sugar syrup), and tapioca pearls.',
                'place_of_origin' => 'Nationwide (Philippines)',
                'category' => 'Snack',
                'caption' => 'A sweet and silky morning favorite.',
                'price' => 35.00, // Average of 20-50
                'filename' => 'taho.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Turon',
                'description' => 'A deep-fried spring roll filled with banana and jackfruit, coated in caramelized sugar.',
                'place_of_origin' => 'Nationwide (Philippines)',
                'category' => 'Snack/Dessert',
                'caption' => 'Crispy, sweet banana fritter rolls.',
                'price' => 27.50, // Average of 15-40
                'filename' => 'turon.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Chicharrón',
                'description' => 'Crispy deep-fried pork rinds, often served as pulutan (beer snack).',
                'place_of_origin' => 'Nationwide (Spanish Influence)',
                'category' => 'Snack',
                'caption' => 'Crunchy pork snack perfect with drinks.',
                'price' => 100.00, // Average of 50-150
                'filename' => 'chicharron.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Lumpiang Shanghai',
                'description' => 'Deep-fried spring rolls filled with ground pork and vegetables.',
                'place_of_origin' => 'Nationwide (Chinese-Filipino Influence)',
                'category' => 'Snack/Appetizer',
                'caption' => 'Crispy spring rolls loved at parties.',
                'price' => 150.00, // Average of 100-200 per serving
                'filename' => 'lumpiang-shanghai.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pinakbet',
                'description' => 'A vegetable stew made with bagoong (fermented shrimp paste) and mixed vegetables.',
                'place_of_origin' => 'Ilocos Region',
                'category' => 'Main Dish',
                'caption' => 'Ilocano\'s healthy vegetable dish.',
                'price' => 160.00, // Average of 120-200
                'filename' => 'pinakbet.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Longganisa',
                'description' => 'Filipino-style sausages with regional variations, often garlicky or sweet.',
                'place_of_origin' => 'Vigan & Lucban (various regions)',
                'category' => 'Breakfast Dish',
                'caption' => 'Savory sausages with local flair.',
                'price' => 140.00, // Average of 100-180
                'filename' => 'longganisa.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert the data into the food table
        DB::table('food')->insert($philippineFoods);

        $this->command->info('Philippine foods seeded successfully!');
    }
}
