<?php

namespace Database\Seeders;

use App\Models\Lga;
use Illuminate\Database\Seeder;

class LgaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lgas = [
            // Zone A
            [
                'name' => 'Agaie',
                'code' => 'AGE',
                'headquarters' => 'Agaie',
                'zone' => 'Zone A',
                'latitude' => 9.0167,
                'longitude' => 6.3333,
                'population_estimate' => 132907,
                'area_km2' => 1903.0,
                'description' => 'Agricultural LGA known for rice and sugarcane production'
            ],
            [
                'name' => 'Bida',
                'code' => 'BDA',
                'headquarters' => 'Bida',
                'zone' => 'Zone A',
                'latitude' => 9.0833,
                'longitude' => 6.0167,
                'population_estimate' => 188181,
                'area_km2' => 1180.0,
                'description' => 'Historic emirate city and commercial center'
            ],
            [
                'name' => 'Edati',
                'code' => 'EDT',
                'headquarters' => 'Edati',
                'zone' => 'Zone A',
                'latitude' => 9.0667,
                'longitude' => 6.1000,
                'population_estimate' => 88867,
                'area_km2' => 1408.0,
                'description' => 'Agricultural area with focus on rice production'
            ],
            [
                'name' => 'Gbako',
                'code' => 'GBK',
                'headquarters' => 'Bida',
                'zone' => 'Zone A',
                'latitude' => 9.0500,
                'longitude' => 6.4167,
                'population_estimate' => 147572,
                'area_km2' => 1473.0,
                'description' => 'Agricultural LGA with diverse crop production'
            ],
            [
                'name' => 'Katcha',
                'code' => 'KTC',
                'headquarters' => 'Katcha',
                'zone' => 'Zone A',
                'latitude' => 8.7333,
                'longitude' => 6.2000,
                'population_estimate' => 135008,
                'area_km2' => 2710.0,
                'description' => 'River Niger border LGA with fishing and farming'
            ],
            [
                'name' => 'Lapai',
                'code' => 'LPI',
                'headquarters' => 'Lapai',
                'zone' => 'Zone A',
                'latitude' => 9.0333,
                'longitude' => 6.5667,
                'population_estimate' => 110127,
                'area_km2' => 3051.0,
                'description' => 'Educational center with Ibrahim Badamasi Babangida University'
            ],
            [
                'name' => 'Lavun',
                'code' => 'LVN',
                'headquarters' => 'Kutigi',
                'zone' => 'Zone A',
                'latitude' => 9.2000,
                'longitude' => 5.7667,
                'population_estimate' => 120077,
                'area_km2' => 1667.0,
                'description' => 'Agricultural LGA with rice and yam cultivation'
            ],

            // Zone B
            [
                'name' => 'Agwara',
                'code' => 'AGW',
                'headquarters' => 'Agwara',
                'zone' => 'Zone B',
                'latitude' => 11.0167,
                'longitude' => 4.0833,
                'population_estimate' => 58506,
                'area_km2' => 2595.0,
                'description' => 'Border LGA with Benin Republic, known for cross-border trade'
            ],
            [
                'name' => 'Borgu',
                'code' => 'BRG',
                'headquarters' => 'New Bussa',
                'zone' => 'Zone B',
                'latitude' => 10.1833,
                'longitude' => 4.5167,
                'population_estimate' => 131016,
                'area_km2' => 17662.0,
                'description' => 'Largest LGA by area, home to Kainji National Park'
            ],
            [
                'name' => 'Mariga',
                'code' => 'MRG',
                'headquarters' => 'Bangi',
                'zone' => 'Zone B',
                'latitude' => 10.4167,
                'longitude' => 5.9167,
                'population_estimate' => 335216,
                'area_km2' => 7365.0,
                'description' => 'Large agricultural LGA with cattle rearing'
            ],
            [
                'name' => 'Mashegu',
                'code' => 'MSH',
                'headquarters' => 'Mashegu',
                'zone' => 'Zone B',
                'latitude' => 10.0000,
                'longitude' => 5.7667,
                'population_estimate' => 171421,
                'area_km2' => 4949.0,
                'description' => 'Agricultural area with focus on grain production'
            ],
            [
                'name' => 'Wushishi',
                'code' => 'WSH',
                'headquarters' => 'Wushishi',
                'zone' => 'Zone B',
                'latitude' => 9.7000,
                'longitude' => 5.9667,
                'population_estimate' => 103474,
                'area_km2' => 2187.0,
                'description' => 'Agricultural LGA with mining activities'
            ],

            // Zone C
            [
                'name' => 'Bosso',
                'code' => 'BSS',
                'headquarters' => 'Maikunkele',
                'zone' => 'Zone C',
                'latitude' => 9.6000,
                'longitude' => 6.5167,
                'population_estimate' => 147359,
                'area_km2' => 1592.0,
                'description' => 'Educational hub with Federal University of Technology'
            ],
            [
                'name' => 'Chanchaga',
                'code' => 'CHG',
                'headquarters' => 'Minna',
                'zone' => 'Zone C',
                'latitude' => 9.6333,
                'longitude' => 6.5833,
                'population_estimate' => 201429,
                'area_km2' => 1669.0,
                'description' => 'Urban LGA encompassing parts of Minna metropolis'
            ],
            [
                'name' => 'Gurara',
                'code' => 'GRR',
                'headquarters' => 'Gawu Babangida',
                'zone' => 'Zone C',
                'latitude' => 9.2833,
                'longitude' => 6.8500,
                'population_estimate' => 85670,
                'area_km2' => 1235.0,
                'description' => 'Agricultural LGA with Gurara River'
            ],
            [
                'name' => 'Kontagora',
                'code' => 'KTG',
                'headquarters' => 'Kontagora',
                'zone' => 'Zone B',
                'latitude' => 10.4000,
                'longitude' => 5.4667,
                'population_estimate' => 149603,
                'area_km2' => 3718.0,
                'description' => 'Historic emirate and commercial center'
            ],
            [
                'name' => 'Magama',
                'code' => 'MGM',
                'headquarters' => 'Nasko',
                'zone' => 'Zone B',
                'latitude' => 10.9167,
                'longitude' => 5.0833,
                'population_estimate' => 118379,
                'area_km2' => 3356.0,
                'description' => 'Border LGA with agricultural activities'
            ],
            [
                'name' => 'Mariga',
                'code' => 'MRG2',
                'headquarters' => 'Bangi',
                'zone' => 'Zone B',
                'latitude' => 10.4167,
                'longitude' => 5.9167,
                'population_estimate' => 335216,
                'area_km2' => 7365.0,
                'description' => 'Large agricultural LGA with cattle rearing'
            ],
            [
                'name' => 'Minna',
                'code' => 'MNN',
                'headquarters' => 'Minna',
                'zone' => 'Zone C',
                'latitude' => 9.6167,
                'longitude' => 6.5500,
                'population_estimate' => 304113,
                'area_km2' => 76.0,
                'description' => 'State capital and administrative center'
            ],
            [
                'name' => 'Mokwa',
                'code' => 'MKW',
                'headquarters' => 'Mokwa',
                'zone' => 'Zone A',
                'latitude' => 9.2833,
                'longitude' => 5.0500,
                'population_estimate' => 188671,
                'area_km2' => 4323.0,
                'description' => 'Agricultural and commercial center'
            ],
            [
                'name' => 'Muya',
                'code' => 'MYA',
                'headquarters' => 'Sarkin Pawa',
                'zone' => 'Zone B',
                'latitude' => 9.8833,
                'longitude' => 6.2000,
                'population_estimate' => 75927,
                'area_km2' => 1804.0,
                'description' => 'Agricultural LGA with mining potential'
            ],
            [
                'name' => 'Paikoro',
                'code' => 'PKR',
                'headquarters' => 'Paiko',
                'zone' => 'Zone C',
                'latitude' => 9.3167,
                'longitude' => 6.7333,
                'population_estimate' => 131367,
                'area_km2' => 1746.0,
                'description' => 'Agricultural area with proximity to FCT'
            ],
            [
                'name' => 'Rafi',
                'code' => 'RAF',
                'headquarters' => 'Kagara',
                'zone' => 'Zone B',
                'latitude' => 10.3333,
                'longitude' => 6.1667,
                'population_estimate' => 246644,
                'area_km2' => 4670.0,
                'description' => 'Large LGA with agricultural and mining activities'
            ],
            [
                'name' => 'Rijau',
                'code' => 'RJU',
                'headquarters' => 'Rijau',
                'zone' => 'Zone B',
                'latitude' => 11.1333,
                'longitude' => 5.2833,
                'population_estimate' => 165912,
                'area_km2' => 4768.0,
                'description' => 'Northern border LGA with agricultural focus'
            ],
            [
                'name' => 'Shiroro',
                'code' => 'SHR',
                'headquarters' => 'Kuta',
                'zone' => 'Zone C',
                'latitude' => 9.9500,
                'longitude' => 6.8333,
                'population_estimate' => 206188,
                'area_km2' => 3940.0,
                'description' => 'Home to Shiroro Dam and hydroelectric power station'
            ],
            [
                'name' => 'Suleja',
                'code' => 'SLJ',
                'headquarters' => 'Suleja',
                'zone' => 'Zone C',
                'latitude' => 9.1833,
                'longitude' => 7.1833,
                'population_estimate' => 216578,
                'area_km2' => 436.0,
                'description' => 'Industrial and commercial center near FCT'
            ],
            [
                'name' => 'Tafa',
                'code' => 'TFA',
                'headquarters' => 'Tafa',
                'zone' => 'Zone C',
                'latitude' => 9.3167,
                'longitude' => 7.3667,
                'population_estimate' => 117181,
                'area_km2' => 2161.0,
                'description' => 'Border LGA with FCT, residential and agricultural'
            ]
        ];

        foreach ($lgas as $lgaData) {
            Lga::updateOrCreate(
                ['code' => $lgaData['code']],
                $lgaData
            );
        }
    }
}
