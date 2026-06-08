<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DistrictsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('districts')->delete();
        
        \DB::table('districts')->insert(array (
            0 => 
            array (
                'id' => 1,
                'district' => 'Halmashauri ya Jiji la Arusha',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            1 => 
            array (
                'id' => 2,
                'district' => 'Halmashauri ya Wilaya ya Arusha',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            2 => 
            array (
                'id' => 3,
                'district' => 'Halmashauri ya Wilaya ya Karatu',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            3 => 
            array (
                'id' => 4,
                'district' => 'Halmashauri ya Wilaya ya Longido',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            4 => 
            array (
                'id' => 5,
                'district' => 'Halmashauri ya Wilaya ya Meru',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            5 => 
            array (
                'id' => 6,
                'district' => 'Halmashauri ya Wilaya ya Monduli',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            6 => 
            array (
                'id' => 7,
                'district' => 'Halmashauri ya Wilaya ya Ngorongoro',
                'region_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            7 => 
            array (
                'id' => 9,
                'district' => 'Halmashauri ya Jiji la Dar es Salaam',
                'region_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            8 => 
            array (
                'id' => 10,
                'district' => 'Halmashauri ya Manispaa ya Kigamboni',
                'region_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            9 => 
            array (
                'id' => 11,
                'district' => 'Halmashauri ya Manispaa ya Kinondoni',
                'region_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            10 => 
            array (
                'id' => 12,
                'district' => 'Halmashauri ya Manispaa ya Temeke',
                'region_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            11 => 
            array (
                'id' => 13,
                'district' => 'Halmashauri ya Manispaa ya Ubungo',
                'region_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            12 => 
            array (
                'id' => 14,
                'district' => 'Halmashauri ya Jiji la Dodoma',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            13 => 
            array (
                'id' => 15,
                'district' => 'Halmashauri ya Mji wa Kondoa',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            14 => 
            array (
                'id' => 16,
                'district' => 'Halmashauri ya Wilaya ya Bahi',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            15 => 
            array (
                'id' => 17,
                'district' => 'Halmashauri ya Wilaya ya Chamwino',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            16 => 
            array (
                'id' => 18,
                'district' => 'Halmashauri ya Wilaya ya Chemba',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            17 => 
            array (
                'id' => 19,
                'district' => 'Halmashauri ya Wilaya ya Kondoa',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            18 => 
            array (
                'id' => 20,
                'district' => 'Halmashauri ya Wilaya ya Kongwa',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            19 => 
            array (
                'id' => 21,
                'district' => 'Halmashauri ya Wilaya ya Mpwapwa',
                'region_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            20 => 
            array (
                'id' => 22,
                'district' => 'Halmashauri ya Mji Geita',
                'region_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            21 => 
            array (
                'id' => 23,
                'district' => 'Halmashauri ya Wilaya ya Bukombe',
                'region_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            22 => 
            array (
                'id' => 24,
                'district' => 'Halmashauri ya Wilaya ya Chato',
                'region_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            23 => 
            array (
                'id' => 25,
                'district' => 'Halmashauri ya Wilaya ya Geita',
                'region_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            24 => 
            array (
                'id' => 26,
                'district' => 'Halmashauri ya Wilaya ya Nyang\'hwale',
                'region_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            25 => 
            array (
                'id' => 27,
                'district' => 'Halmashuari ya Wilaya ya Mbogwe',
                'region_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            26 => 
            array (
                'id' => 28,
                'district' => 'Halmashauri ya Manispaa ya Iringa',
                'region_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            27 => 
            array (
                'id' => 29,
                'district' => 'Halmashauri ya Mji wa Mafinga',
                'region_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            28 => 
            array (
                'id' => 30,
                'district' => 'Halmashauri ya Wilaya ya Iringa',
                'region_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            29 => 
            array (
                'id' => 31,
                'district' => 'Halmashauri ya Wilaya ya Iringa Vijijini',
                'region_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            30 => 
            array (
                'id' => 32,
                'district' => 'Halmashauri ya Wilaya ya Kilolo',
                'region_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            31 => 
            array (
                'id' => 33,
                'district' => 'Halmashauri ya Wilaya ya Mufindi',
                'region_id' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            32 => 
            array (
                'id' => 34,
                'district' => 'Halmashauri ya Manispaa ya Bukoba',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            33 => 
            array (
                'id' => 35,
                'district' => 'Halmashauri ya Wilaya ya Biharamulo',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            34 => 
            array (
                'id' => 36,
                'district' => 'Halmashauri ya Wilaya ya Bukoba',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            35 => 
            array (
                'id' => 37,
                'district' => 'Halmashauri ya Wilaya ya Karagwe',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            36 => 
            array (
                'id' => 38,
                'district' => 'Halmashauri ya Wilaya ya Kyerwa',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            37 => 
            array (
                'id' => 39,
                'district' => 'Halmashauri ya Wilaya ya Missenyi',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            38 => 
            array (
                'id' => 40,
                'district' => 'Halmashauri ya Wilaya ya Muleba',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            39 => 
            array (
                'id' => 41,
                'district' => 'Halmashauri ya Wilaya ya Ngara',
                'region_id' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            40 => 
            array (
                'id' => 42,
                'district' => 'Baraza la Mji Wilaya ya Wete',
                'region_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            41 => 
            array (
                'id' => 43,
                'district' => 'Halmashauri ya Wilaya Micheweni',
                'region_id' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            42 => 
            array (
                'id' => 44,
                'district' => 'Baraza la Mji Kaskazini A',
                'region_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            43 => 
            array (
                'id' => 45,
                'district' => 'Baraza la Mji Kaskazini B',
                'region_id' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            44 => 
            array (
                'id' => 46,
                'district' => 'Halmashauri ya Manispaa ya Mpanda',
                'region_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            45 => 
            array (
                'id' => 47,
                'district' => 'Halmashauri ya Wilaya ya Mlele',
                'region_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            46 => 
            array (
                'id' => 48,
                'district' => 'Halmashauri ya Wilaya ya Mpimbwe',
                'region_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            47 => 
            array (
                'id' => 49,
                'district' => 'Halmashauri ya Wilaya ya Nsimbo',
                'region_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            48 => 
            array (
                'id' => 50,
                'district' => 'Halmashauri ya Wilaya ya Tanganyika',
                'region_id' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            49 => 
            array (
                'id' => 51,
                'district' => 'Halmashauri ya Manispaa Kigoma Ujiji',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            50 => 
            array (
                'id' => 52,
                'district' => 'Halmashauri ya Mji wa Kasulu',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            51 => 
            array (
                'id' => 53,
                'district' => 'Halmashauri ya Wilaya ya Buhigwe',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            52 => 
            array (
                'id' => 54,
                'district' => 'Halmashauri ya Wilaya ya Kakonko',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            53 => 
            array (
                'id' => 55,
                'district' => 'Halmashauri ya Wilaya ya Kasulu',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            54 => 
            array (
                'id' => 56,
                'district' => 'Halmashauri ya Wilaya ya Kibondo',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            55 => 
            array (
                'id' => 57,
                'district' => 'Halmashauri ya Wilaya ya Kigoma',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            56 => 
            array (
                'id' => 58,
                'district' => 'Halmashauri ya Wilaya ya Uvinza',
                'region_id' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            57 => 
            array (
                'id' => 59,
                'district' => 'Halmashauri ya Manispaa ya Moshi',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            58 => 
            array (
                'id' => 60,
                'district' => 'Halmashauri ya Wilaya ya Hai',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            59 => 
            array (
                'id' => 61,
                'district' => 'Halmashauri ya Wilaya ya Moshi',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            60 => 
            array (
                'id' => 62,
                'district' => 'Halmashauri ya Wilaya ya Mwanga',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            61 => 
            array (
                'id' => 63,
                'district' => 'Halmashauri ya Wilaya ya Rombo',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            62 => 
            array (
                'id' => 64,
                'district' => 'Halmashauri ya Wilaya ya Same',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            63 => 
            array (
                'id' => 65,
                'district' => 'Halmashauri ya Wilaya ya Siha',
                'region_id' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            64 => 
            array (
                'id' => 66,
                'district' => 'Baraza la Mji Chake Chake',
                'region_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            65 => 
            array (
                'id' => 67,
                'district' => 'Baraza la Mji Mkoani',
                'region_id' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            66 => 
            array (
                'id' => 68,
                'district' => 'Baraza la Mji Kati',
                'region_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            67 => 
            array (
                'id' => 69,
                'district' => 'Halmashauri ya Wilaya ya Kusini',
                'region_id' => 13,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            68 => 
            array (
                'id' => 70,
                'district' => 'Halmashauri ya Manispaa ya Lindi',
                'region_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            69 => 
            array (
                'id' => 71,
                'district' => 'Halmashauri ya Wilaya ya Kilwa',
                'region_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            70 => 
            array (
                'id' => 72,
                'district' => 'Halmashauri ya Wilaya ya Liwale',
                'region_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            71 => 
            array (
                'id' => 73,
                'district' => 'Halmashauri ya Wilaya ya Mtama',
                'region_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            72 => 
            array (
                'id' => 74,
                'district' => 'Halmashauri ya Wilaya ya Nachingwea',
                'region_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            73 => 
            array (
                'id' => 75,
                'district' => 'Halmashauri ya Wilaya ya Ruangwa',
                'region_id' => 14,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            74 => 
            array (
                'id' => 76,
                'district' => 'Halmashauri ya Mji wa Babati',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            75 => 
            array (
                'id' => 77,
                'district' => 'Halmashauri ya Mji wa Mbulu',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            76 => 
            array (
                'id' => 78,
                'district' => 'Halmashauri ya Wilaya ya Babati',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            77 => 
            array (
                'id' => 79,
                'district' => 'Halmashauri ya Wilaya ya Hanang',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            78 => 
            array (
                'id' => 80,
                'district' => 'Halmashauri ya Wilaya ya Kiteto',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            79 => 
            array (
                'id' => 81,
                'district' => 'Halmashauri ya Wilaya ya Mbulu',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            80 => 
            array (
                'id' => 82,
                'district' => 'Halmashauri ya Wilaya ya Simanjiro',
                'region_id' => 15,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            81 => 
            array (
                'id' => 83,
                'district' => 'Halmashauri ya Manispaa ya Musoma',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            82 => 
            array (
                'id' => 84,
                'district' => 'Halmashauri ya Mji wa Bunda',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            83 => 
            array (
                'id' => 85,
                'district' => 'Halmashauri ya Mji wa Tarime',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            84 => 
            array (
                'id' => 86,
                'district' => 'Halmashauri ya Wilaya ya Bunda',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            85 => 
            array (
                'id' => 87,
                'district' => 'Halmashauri ya Wilaya ya Butiama',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            86 => 
            array (
                'id' => 88,
                'district' => 'Halmashauri ya Wilaya ya Musoma',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            87 => 
            array (
                'id' => 89,
                'district' => 'Halmashauri ya Wilaya ya Rorya',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            88 => 
            array (
                'id' => 90,
                'district' => 'Halmashauri ya Wilaya ya Serengeti',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            89 => 
            array (
                'id' => 91,
                'district' => 'Halmashauri ya Wilaya ya Tarime',
                'region_id' => 16,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            90 => 
            array (
                'id' => 92,
                'district' => 'Halmashauri ya Jiji la Mbeya',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            91 => 
            array (
                'id' => 93,
                'district' => 'Halmashauri ya Wilaya ya Busokelo',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            92 => 
            array (
                'id' => 94,
                'district' => 'Halmashauri ya Wilaya ya Chunya',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            93 => 
            array (
                'id' => 95,
                'district' => 'Halmashauri ya Wilaya ya Kyela',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            94 => 
            array (
                'id' => 96,
                'district' => 'Halmashauri ya Wilaya ya Mbarali',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            95 => 
            array (
                'id' => 97,
                'district' => 'Halmashauri ya Wilaya ya Mbeya',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            96 => 
            array (
                'id' => 98,
                'district' => 'Halmashauri ya Wilaya ya Rungwe',
                'region_id' => 17,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            97 => 
            array (
                'id' => 99,
                'district' => 'Baraza la Manispaa Magharibi A',
                'region_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            98 => 
            array (
                'id' => 100,
                'district' => 'Baraza la Manispaa Magharibi B',
                'region_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            99 => 
            array (
                'id' => 101,
                'district' => 'Baraza la Manispaa Mjini',
                'region_id' => 18,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            100 => 
            array (
                'id' => 102,
                'district' => 'Halmashauri ya Manispaa ya Morogoro',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            101 => 
            array (
                'id' => 103,
                'district' => 'Halmashauri ya Mji Ifakara',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            102 => 
            array (
                'id' => 104,
                'district' => 'Halmashauri ya Wilaya ya Gairo',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            103 => 
            array (
                'id' => 105,
                'district' => 'Halmashauri ya Wilaya ya Kilosa',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            104 => 
            array (
                'id' => 106,
                'district' => 'Halmashauri ya Wilaya ya Malinyi',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            105 => 
            array (
                'id' => 107,
                'district' => 'Halmashauri ya Wilaya ya Mlimba',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            106 => 
            array (
                'id' => 108,
                'district' => 'Halmashauri ya Wilaya ya Morogoro',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            107 => 
            array (
                'id' => 109,
                'district' => 'Halmashauri ya Wilaya ya Mvomero',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            108 => 
            array (
                'id' => 110,
                'district' => 'Halmashauri ya Wilaya ya Ulanga',
                'region_id' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            109 => 
            array (
                'id' => 111,
                'district' => 'Halmashauri ya Manispaa ya Mtwara Mikind',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            110 => 
            array (
                'id' => 112,
                'district' => 'Halmashauri ya Mji wa Masasi',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            111 => 
            array (
                'id' => 113,
                'district' => 'Halmashauri ya Mji wa Nanyamba',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            112 => 
            array (
                'id' => 114,
                'district' => 'Halmashauri ya Mji wa Newala',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            113 => 
            array (
                'id' => 115,
                'district' => 'Halmashauri ya Wilaya Nanyumbu',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            114 => 
            array (
                'id' => 116,
                'district' => 'Halmashauri ya Wilaya ya Masasi',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            115 => 
            array (
                'id' => 117,
                'district' => 'Halmashauri ya Wilaya ya Mtwara',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            116 => 
            array (
                'id' => 118,
                'district' => 'Halmashauri ya Wilaya ya Newala',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            117 => 
            array (
                'id' => 119,
                'district' => 'Halmashauri ya Wilaya ya Tandahimba',
                'region_id' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            118 => 
            array (
                'id' => 120,
                'district' => 'Halmashauri  ya Wilaya ya Ukerewe',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            119 => 
            array (
                'id' => 121,
                'district' => 'Halmashauri ya Jiji la Mwanza',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            120 => 
            array (
                'id' => 122,
                'district' => 'Halmashauri ya Manispaa ya Ilemela',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            121 => 
            array (
                'id' => 123,
                'district' => 'Halmashauri ya Wilaya ya Buchosa',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            122 => 
            array (
                'id' => 124,
                'district' => 'Halmashauri ya Wilaya ya Kwimba',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            123 => 
            array (
                'id' => 125,
                'district' => 'Halmashauri ya Wilaya ya Magu',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            124 => 
            array (
                'id' => 126,
                'district' => 'Halmashauri ya Wilaya ya Misungwi',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            125 => 
            array (
                'id' => 127,
                'district' => 'Halmashauri ya Wilaya ya Sengerema',
                'region_id' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            126 => 
            array (
                'id' => 128,
                'district' => 'Halmashauri ya Mji wa Makambako',
                'region_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            127 => 
            array (
                'id' => 129,
                'district' => 'Halmashauri ya Mji wa Njombe',
                'region_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            128 => 
            array (
                'id' => 130,
                'district' => 'Halmashauri ya Wilaya ya Ludewa',
                'region_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            129 => 
            array (
                'id' => 131,
                'district' => 'Halmashauri ya Wilaya ya Makete',
                'region_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            130 => 
            array (
                'id' => 132,
                'district' => 'Halmashauri ya Wilaya ya Njombe',
                'region_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            131 => 
            array (
                'id' => 133,
                'district' => 'Halmashauri ya Wilaya ya Wanging\'ombe',
                'region_id' => 22,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            132 => 
            array (
                'id' => 134,
                'district' => 'Halmashauri ya Mji wa Kibaha',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            133 => 
            array (
                'id' => 135,
                'district' => 'Halmashauri ya Wilaya ya  Mkuranga',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            134 => 
            array (
                'id' => 136,
                'district' => 'Halmashauri ya Wilaya ya Bagamoyo',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            135 => 
            array (
                'id' => 137,
                'district' => 'Halmashauri ya Wilaya ya Chalinze',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            136 => 
            array (
                'id' => 138,
                'district' => 'Halmashauri ya Wilaya ya Kibaha',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            137 => 
            array (
                'id' => 139,
                'district' => 'Halmashauri ya Wilaya ya Kisarawe',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            138 => 
            array (
                'id' => 140,
                'district' => 'Halmashauri ya Wilaya ya Mafia',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            139 => 
            array (
                'id' => 141,
                'district' => 'Halmashauri ya Wilaya ya Rufiji',
                'region_id' => 23,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            140 => 
            array (
                'id' => 142,
                'district' => 'Halmashauri ya Manispaa ya Sumbawanga',
                'region_id' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            141 => 
            array (
                'id' => 143,
                'district' => 'Halmashauri ya Wilaya ya Kalambo',
                'region_id' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            142 => 
            array (
                'id' => 144,
                'district' => 'Halmashauri ya Wilaya ya Nkasi',
                'region_id' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            143 => 
            array (
                'id' => 145,
                'district' => 'Halmashauri ya Wilaya ya Sumbawanga',
                'region_id' => 24,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            144 => 
            array (
                'id' => 146,
                'district' => 'Halmashauri ya Manispaa ya Songea',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            145 => 
            array (
                'id' => 147,
                'district' => 'Halmashauri ya Mji wa Mbinga',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            146 => 
            array (
                'id' => 148,
                'district' => 'Halmashauri ya Wilaya ya Madaba',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            147 => 
            array (
                'id' => 149,
                'district' => 'Halmashauri ya Wilaya ya Mbinga',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            148 => 
            array (
                'id' => 150,
                'district' => 'Halmashauri ya Wilaya ya Namtumbo',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            149 => 
            array (
                'id' => 151,
                'district' => 'Halmashauri ya Wilaya ya Nyasa',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            150 => 
            array (
                'id' => 152,
                'district' => 'Halmashauri ya Wilaya ya Songea',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            151 => 
            array (
                'id' => 153,
                'district' => 'Halmashauri ya Wilaya ya Tunduru',
                'region_id' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            152 => 
            array (
                'id' => 154,
                'district' => 'Halmashauri ya Manispaa ya Kahama',
                'region_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            153 => 
            array (
                'id' => 155,
                'district' => 'Halmashauri ya Manispaa ya Shinyanga',
                'region_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            154 => 
            array (
                'id' => 156,
                'district' => 'Halmashauri ya Wilaya ya Kishapu',
                'region_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            155 => 
            array (
                'id' => 157,
                'district' => 'Halmashauri ya Wilaya ya Msalala',
                'region_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            156 => 
            array (
                'id' => 158,
                'district' => 'Halmashauri ya Wilaya ya Shinyanga',
                'region_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            157 => 
            array (
                'id' => 159,
                'district' => 'Halmashauri ya Wilaya ya Ushetu',
                'region_id' => 26,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            158 => 
            array (
                'id' => 160,
                'district' => 'Halmashauri ya Mji wa Bariadi',
                'region_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            159 => 
            array (
                'id' => 161,
                'district' => 'Halmashauri ya Wilaya ya Bariadi',
                'region_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            160 => 
            array (
                'id' => 162,
                'district' => 'Halmashauri ya Wilaya ya Busega',
                'region_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            161 => 
            array (
                'id' => 163,
                'district' => 'Halmashauri ya Wilaya ya Itilima',
                'region_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            162 => 
            array (
                'id' => 164,
                'district' => 'Halmashauri ya Wilaya ya Maswa',
                'region_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            163 => 
            array (
                'id' => 165,
                'district' => 'Halmashauri ya Wilaya ya Meatu',
                'region_id' => 27,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            164 => 
            array (
                'id' => 166,
                'district' => 'Halmashauri ya Manispaa ya Singida',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            165 => 
            array (
                'id' => 167,
                'district' => 'Halmashauri ya Wilaya ya Ikungi',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            166 => 
            array (
                'id' => 168,
                'district' => 'Halmashauri ya Wilaya ya Iramba',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            167 => 
            array (
                'id' => 169,
                'district' => 'Halmashauri ya Wilaya ya Itigi',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            168 => 
            array (
                'id' => 170,
                'district' => 'Halmashauri ya Wilaya ya Manyoni',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            169 => 
            array (
                'id' => 171,
                'district' => 'Halmashauri ya Wilaya ya Mkalama',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            170 => 
            array (
                'id' => 172,
                'district' => 'Halmashauri ya Wilaya ya Singida',
                'region_id' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            171 => 
            array (
                'id' => 173,
                'district' => 'Halmashauri ya Mji wa Tunduma',
                'region_id' => 29,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            172 => 
            array (
                'id' => 174,
                'district' => 'Halmashauri ya Wilaya ya Ileje',
                'region_id' => 29,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            173 => 
            array (
                'id' => 175,
                'district' => 'Halmashauri ya Wilaya ya Mbozi',
                'region_id' => 29,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            174 => 
            array (
                'id' => 176,
                'district' => 'Halmashauri ya Wilaya ya Momba',
                'region_id' => 29,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            175 => 
            array (
                'id' => 177,
                'district' => 'Halmashauri ya Wilaya ya Songwe',
                'region_id' => 29,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            176 => 
            array (
                'id' => 178,
                'district' => 'Halmashauri ya Manispaa ya Tabora',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            177 => 
            array (
                'id' => 179,
                'district' => 'Halmashauri ya Mji wa Nzega',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            178 => 
            array (
                'id' => 180,
                'district' => 'Halmashauri ya Wilaya ya Igunga',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            179 => 
            array (
                'id' => 181,
                'district' => 'Halmashauri ya Wilaya ya Kaliua',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            180 => 
            array (
                'id' => 182,
                'district' => 'Halmashauri ya Wilaya ya Nzega',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            181 => 
            array (
                'id' => 183,
                'district' => 'Halmashauri ya Wilaya ya Sikonge',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            182 => 
            array (
                'id' => 184,
                'district' => 'Halmashauri ya Wilaya ya Urambo',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            183 => 
            array (
                'id' => 185,
                'district' => 'Halmashauri ya Wilaya ya Uyui',
                'region_id' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            184 => 
            array (
                'id' => 186,
                'district' => 'Halmashauri ya Jiji la Tanga',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            185 => 
            array (
                'id' => 187,
                'district' => 'Halmashauri ya Mji wa Handeni',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            186 => 
            array (
                'id' => 188,
                'district' => 'Halmashauri ya Mji wa Korogwe',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            187 => 
            array (
                'id' => 189,
                'district' => 'Halmashauri ya Wilaya ya Bumbuli',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            188 => 
            array (
                'id' => 190,
                'district' => 'Halmashauri ya Wilaya ya Handeni',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            189 => 
            array (
                'id' => 191,
                'district' => 'Halmashauri ya Wilaya ya Kilindi',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            190 => 
            array (
                'id' => 192,
                'district' => 'Halmashauri ya Wilaya ya Korogwe',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            191 => 
            array (
                'id' => 193,
                'district' => 'Halmashauri ya Wilaya ya Lushoto',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            192 => 
            array (
                'id' => 194,
                'district' => 'Halmashauri ya Wilaya ya Mkinga',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            193 => 
            array (
                'id' => 195,
                'district' => 'Halmashauri ya Wilaya ya Muheza',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
            194 => 
            array (
                'id' => 196,
                'district' => 'Halmashauri ya Wilaya ya Pangani',
                'region_id' => 31,
                'created_at' => now(),
                'updated_at' => now(),
            ),
        ));

        
    }
}