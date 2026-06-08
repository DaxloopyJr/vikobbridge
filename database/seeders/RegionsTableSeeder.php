<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('regions')->delete();

        \DB::table('regions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'region' => 'Arusha',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            1 => 
            array (
                'id' => 2,
                'region' => 'Dar es Salaam',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            2 => 
            array (
                'id' => 3,
                'region' => 'Dodoma',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            3 => 
            array (
                'id' => 4,
                'region' => 'Geita',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            4 => 
            array (
                'id' => 5,
                'region' => 'Iringa',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            5 => 
            array (
                'id' => 6,
                'region' => 'Kagera',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            6 => 
            array (
                'id' => 7,
                'region' => 'Kaskazini Pemba',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            7 => 
            array (
                'id' => 8,
                'region' => 'Kaskazini Unguja',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            8 => 
            array (
                'id' => 9,
                'region' => 'Katavi',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            9 => 
            array (
                'id' => 10,
                'region' => 'Kigoma',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            10 => 
            array (
                'id' => 11,
                'region' => 'Kilimanjaro',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            11 => 
            array (
                'id' => 12,
                'region' => 'Kusini Pemba',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            12 => 
            array (
                'id' => 13,
                'region' => 'Kusini Unguja',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            13 => 
            array (
                'id' => 14,
                'region' => 'Lindi',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            14 => 
            array (
                'id' => 15,
                'region' => 'Manyara',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            15 => 
            array (
                'id' => 16,
                'region' => 'Mara',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            16 => 
            array (
                'id' => 17,
                'region' => 'Mbeya',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            17 => 
            array (
                'id' => 18,
                'region' => 'Mjini Magharibi',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            18 => 
            array (
                'id' => 19,
                'region' => 'Morogoro',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            19 => 
            array (
                'id' => 20,
                'region' => 'Mtwara',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            20 => 
            array (
                'id' => 21,
                'region' => 'Mwanza',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            21 => 
            array (
                'id' => 22,
                'region' => 'Njombe',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            22 => 
            array (
                'id' => 23,
                'region' => 'Pwani',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            23 => 
            array (
                'id' => 24,
                'region' => 'Rukwa',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            24 => 
            array (
                'id' => 25,
                'region' => 'Ruvuma',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            25 => 
            array (
                'id' => 26,
                'region' => 'Shinyanga',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            26 => 
            array (
                'id' => 27,
                'region' => 'Simiyu',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            27 => 
            array (
                'id' => 28,
                'region' => 'Singida',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            28 => 
            array (
                'id' => 29,
                'region' => 'Songwe',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            29 => 
            array (
                'id' => 30,
                'region' => 'Tabora',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            30 => 
            array (
                'id' => 31,
                'region' => 'Tanga',
                'zone_id' => 1,
                'code' => NULL,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));
        
        
    }
}