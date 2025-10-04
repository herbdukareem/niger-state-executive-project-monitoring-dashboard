<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Lga;
use App\Models\Ward;

class NigerStateLgaWardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ward::truncate();
        Lga::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Truncated existing LGA and Ward data');

        // Niger State LGA and Ward data
        $lgasData = $this->getNigerStateLGAs();

        foreach ($lgasData as $lgaData) {
            $this->command->info("Creating LGA: {$lgaData['name']}");
            
            // Create LGA
            $lga = Lga::create([
                'name' => $lgaData['name'],
                'code' => $lgaData['code'],
                'headquarters' => $lgaData['headquarters'],
                'zone' => $this->determineZone($lgaData['name']),
                'latitude' => $lgaData['coordinates']['latitude'],
                'longitude' => $lgaData['coordinates']['longitude'],
                'population_estimate' => $lgaData['population_estimate'],
                'area_km2' => $lgaData['area_km2'],
                'description' => "Local Government Area in Niger State with headquarters at {$lgaData['headquarters']}"
            ]);

            // Create Wards for this LGA
            foreach ($lgaData['wards'] as $wardData) {
                Ward::create([
                    'lga_id' => $lga->id,
                    'name' => $wardData['name'],
                    'code' => $wardData['code'],
                    'latitude' => $this->generateWardCoordinates($lgaData['coordinates']['latitude']),
                    'longitude' => $this->generateWardCoordinates($lgaData['coordinates']['longitude']),
                    'population_estimate' => $this->estimateWardPopulation($lgaData['population_estimate'], count($lgaData['wards'])),
                    'description' => "Ward in {$lgaData['name']} Local Government Area"
                ]);
            }

            $this->command->info("Created {$lga->name} LGA with " . count($lgaData['wards']) . " wards");
        }

        $this->command->info('Niger State LGA and Ward seeding completed successfully!');
        $this->command->info('Total LGAs created: ' . Lga::count());
        $this->command->info('Total Wards created: ' . Ward::count());
    }

    /**
     * Get Niger State LGAs data
     */
    private function getNigerStateLGAs(): array
    {
        return array_merge(
            $this->getFirstBatchLGAs(),
            $this->getSecondBatchLGAs(),
            $this->getThirdBatchLGAs(),
            $this->getFourthBatchLGAs()
        );
    }

    /**
     * Get first batch of LGAs
     */
    private function getFirstBatchLGAs(): array
    {
        return [
            [
                'name' => 'Agaie',
                'code' => 'AGA',
                'headquarters' => 'Agaie',
                'area_km2' => 1903,
                'population_estimate' => 132312,
                'coordinates' => ['latitude' => 9.0167, 'longitude' => 6.1833],
                'wards' => [
                    ['name' => 'Agaie', 'code' => 'AGA01'],
                    ['name' => 'Baro', 'code' => 'AGA02'],
                    ['name' => 'Ekossa', 'code' => 'AGA03'],
                    ['name' => 'Etsugaie', 'code' => 'AGA04'],
                    ['name' => 'Katcha', 'code' => 'AGA05'],
                    ['name' => 'Lapai', 'code' => 'AGA06'],
                    ['name' => 'Magaji', 'code' => 'AGA07'],
                    ['name' => 'Tagagi', 'code' => 'AGA08'],
                    ['name' => 'Tswata', 'code' => 'AGA09'],
                    ['name' => 'Yuna', 'code' => 'AGA10']
                ]
            ],
            [
                'name' => 'Agwara',
                'code' => 'AGW',
                'headquarters' => 'Agwara',
                'area_km2' => 2492,
                'population_estimate' => 58579,
                'coordinates' => ['latitude' => 11.0167, 'longitude' => 4.3833],
                'wards' => [
                    ['name' => 'Agwara', 'code' => 'AGW01'],
                    ['name' => 'Gallah', 'code' => 'AGW02'],
                    ['name' => 'Kokani', 'code' => 'AGW03'],
                    ['name' => 'Mago', 'code' => 'AGW04'],
                    ['name' => 'Papiri', 'code' => 'AGW05'],
                    ['name' => 'Rofia', 'code' => 'AGW06'],
                    ['name' => 'Suteku', 'code' => 'AGW07'],
                    ['name' => 'Swashi', 'code' => 'AGW08'],
                    ['name' => 'Takuma', 'code' => 'AGW09'],
                    ['name' => 'Yanigi', 'code' => 'AGW10']
                ]
            ],
            [
                'name' => 'Bida',
                'code' => 'BID',
                'headquarters' => 'Bida',
                'area_km2' => 1180,
                'population_estimate' => 188181,
                'coordinates' => ['latitude' => 9.0833, 'longitude' => 6.0167],
                'wards' => [
                    ['name' => 'Bariki', 'code' => 'BID01'],
                    ['name' => 'Bida I', 'code' => 'BID02'],
                    ['name' => 'Bida II', 'code' => 'BID03'],
                    ['name' => 'Bida III', 'code' => 'BID04'],
                    ['name' => 'Dokodza', 'code' => 'BID05'],
                    ['name' => 'Kyari', 'code' => 'BID06'],
                    ['name' => 'Landzun', 'code' => 'BID07'],
                    ['name' => 'Masaba I', 'code' => 'BID08'],
                    ['name' => 'Masaba II', 'code' => 'BID09'],
                    ['name' => 'Mayaki Ndajiya', 'code' => 'BID10'],
                    ['name' => 'Nassarafu', 'code' => 'BID11']
                ]
            ],
            [
                'name' => 'Borgu',
                'code' => 'BOR',
                'headquarters' => 'New Bussa',
                'area_km2' => 17321,
                'population_estimate' => 131555,
                'coordinates' => ['latitude' => 10.2000, 'longitude' => 4.5000],
                'wards' => [
                    ['name' => 'Babanna', 'code' => 'BOR01'],
                    ['name' => 'Dugga', 'code' => 'BOR02'],
                    ['name' => 'Kaoje', 'code' => 'BOR03'],
                    ['name' => 'Konkoso', 'code' => 'BOR04'],
                    ['name' => 'Malale', 'code' => 'BOR05'],
                    ['name' => 'New Bussa', 'code' => 'BOR06'],
                    ['name' => 'Shagunu', 'code' => 'BOR07'],
                    ['name' => 'Wawa', 'code' => 'BOR08'],
                    ['name' => 'Yelwa', 'code' => 'BOR09'],
                    ['name' => 'Zugurma', 'code' => 'BOR10']
                ]
            ],
            [
                'name' => 'Bosso',
                'code' => 'BOS',
                'headquarters' => 'Maikunkele',
                'area_km2' => 1592,
                'population_estimate' => 147359,
                'coordinates' => ['latitude' => 9.6167, 'longitude' => 6.5500],
                'wards' => [
                    ['name' => 'Beji', 'code' => 'BOS01'],
                    ['name' => 'Bosso', 'code' => 'BOS02'],
                    ['name' => 'Central', 'code' => 'BOS03'],
                    ['name' => 'Garatu', 'code' => 'BOS04'],
                    ['name' => 'Kodo', 'code' => 'BOS05'],
                    ['name' => 'Maikunkele', 'code' => 'BOS06'],
                    ['name' => 'Maitumbi', 'code' => 'BOS07'],
                    ['name' => 'Shaba', 'code' => 'BOS08'],
                    ['name' => 'Tudun Fulani', 'code' => 'BOS09'],
                    ['name' => 'Yankee', 'code' => 'BOS10']
                ]
            ],
            [
                'name' => 'Chanchaga',
                'code' => 'CHA',
                'headquarters' => 'Minna',
                'area_km2' => 1669,
                'population_estimate' => 201429,
                'coordinates' => ['latitude' => 9.6167, 'longitude' => 6.5500],
                'wards' => [
                    ['name' => 'Barkin Sale', 'code' => 'CHA01'],
                    ['name' => 'Chanchaga', 'code' => 'CHA02'],
                    ['name' => 'Dutsen Kura Gwari', 'code' => 'CHA03'],
                    ['name' => 'Dutsen Kura Hausa', 'code' => 'CHA04'],
                    ['name' => 'Gwari', 'code' => 'CHA05'],
                    ['name' => 'Kpakungu', 'code' => 'CHA06'],
                    ['name' => 'Limawa', 'code' => 'CHA07'],
                    ['name' => 'Maitumbi', 'code' => 'CHA08'],
                    ['name' => 'Minna Centre', 'code' => 'CHA09'],
                    ['name' => 'Nasarawa', 'code' => 'CHA10'],
                    ['name' => 'Sauka Kahuta', 'code' => 'CHA11']
                ]
            ],
            [
                'name' => 'Edati',
                'code' => 'EDA',
                'headquarters' => 'Enagi',
                'area_km2' => 1188,
                'population_estimate' => 88867,
                'coordinates' => ['latitude' => 9.1500, 'longitude' => 6.2000],
                'wards' => [
                    ['name' => 'Batati', 'code' => 'EDA01'],
                    ['name' => 'Doko', 'code' => 'EDA02'],
                    ['name' => 'Enagi', 'code' => 'EDA03'],
                    ['name' => 'Etsugba', 'code' => 'EDA04'],
                    ['name' => 'Gadayi', 'code' => 'EDA05'],
                    ['name' => 'Lemu', 'code' => 'EDA06'],
                    ['name' => 'Sakaba', 'code' => 'EDA07'],
                    ['name' => 'Shabamaliki', 'code' => 'EDA08'],
                    ['name' => 'Tsohon Kabula', 'code' => 'EDA09'],
                    ['name' => 'Wamba', 'code' => 'EDA10']
                ]
            ],
            [
                'name' => 'Gbako',
                'code' => 'GBA',
                'headquarters' => 'Lemu',
                'area_km2' => 1499,
                'population_estimate' => 147011,
                'coordinates' => ['latitude' => 9.1000, 'longitude' => 6.0500],
                'wards' => [
                    ['name' => 'Dendo', 'code' => 'GBA01'],
                    ['name' => 'Gbako', 'code' => 'GBA02'],
                    ['name' => 'Kataeregi', 'code' => 'GBA03'],
                    ['name' => 'Kwagana', 'code' => 'GBA04'],
                    ['name' => 'Lemu', 'code' => 'GBA05'],
                    ['name' => 'Lesu', 'code' => 'GBA06'],
                    ['name' => 'Muye', 'code' => 'GBA07'],
                    ['name' => 'Paiko Central', 'code' => 'GBA08'],
                    ['name' => 'Paiko East', 'code' => 'GBA09'],
                    ['name' => 'Paiko West', 'code' => 'GBA10']
                ]
            ],
            [
                'name' => 'Gurara',
                'code' => 'GUR',
                'headquarters' => 'Gawu Babangida',
                'area_km2' => 1235,
                'population_estimate' => 85670,
                'coordinates' => ['latitude' => 9.3000, 'longitude' => 6.8000],
                'wards' => [
                    ['name' => 'Alawa', 'code' => 'GUR01'],
                    ['name' => 'Beji', 'code' => 'GUR02'],
                    ['name' => 'Diko', 'code' => 'GUR03'],
                    ['name' => 'Gawu Babangida', 'code' => 'GUR04'],
                    ['name' => 'Guni', 'code' => 'GUR05'],
                    ['name' => 'Izom', 'code' => 'GUR06'],
                    ['name' => 'Kwaki', 'code' => 'GUR07'],
                    ['name' => 'Lambata', 'code' => 'GUR08'],
                    ['name' => 'Leaba', 'code' => 'GUR09'],
                    ['name' => 'Tufa', 'code' => 'GUR10']
                ]
            ],
            [
                'name' => 'Katcha',
                'code' => 'KAT',
                'headquarters' => 'Katcha',
                'area_km2' => 2710,
                'population_estimate' => 135008,
                'coordinates' => ['latitude' => 8.8000, 'longitude' => 6.2000],
                'wards' => [
                    ['name' => 'Badeggi', 'code' => 'KAT01'],
                    ['name' => 'Baddna', 'code' => 'KAT02'],
                    ['name' => 'Dzwafu', 'code' => 'KAT03'],
                    ['name' => 'Emi', 'code' => 'KAT04'],
                    ['name' => 'Jima', 'code' => 'KAT05'],
                    ['name' => 'Katcha', 'code' => 'KAT06'],
                    ['name' => 'Kpada', 'code' => 'KAT07'],
                    ['name' => 'Sabon Gari', 'code' => 'KAT08'],
                    ['name' => 'Takuti', 'code' => 'KAT09'],
                    ['name' => 'Tswako', 'code' => 'KAT10']
                ]
            ],
            [
                'name' => 'Kontagora',
                'code' => 'KON',
                'headquarters' => 'Kontagora',
                'area_km2' => 3718,
                'population_estimate' => 149603,
                'coordinates' => ['latitude' => 10.4000, 'longitude' => 5.4667],
                'wards' => [
                    ['name' => 'Auna', 'code' => 'KON01'],
                    ['name' => 'Fadama', 'code' => 'KON02'],
                    ['name' => 'Gulbin Boka', 'code' => 'KON03'],
                    ['name' => 'Jikantoro', 'code' => 'KON04'],
                    ['name' => 'Kontagora I', 'code' => 'KON05'],
                    ['name' => 'Kontagora II', 'code' => 'KON06'],
                    ['name' => 'Mayanka', 'code' => 'KON07'],
                    ['name' => 'Rafi', 'code' => 'KON08'],
                    ['name' => 'Tudun Wada', 'code' => 'KON09'],
                    ['name' => 'Wushishi', 'code' => 'KON10']
                ]
            ]
        ];
    }

    /**
     * Get second batch of LGAs
     */
    private function getSecondBatchLGAs(): array
    {
        return [
            [
                'name' => 'Lapai',
                'code' => 'LAP',
                'headquarters' => 'Lapai',
                'area_km2' => 3889,
                'population_estimate' => 110127,
                'coordinates' => ['latitude' => 9.0500, 'longitude' => 6.5667],
                'wards' => [
                    ['name' => 'Agwata', 'code' => 'LAP01'],
                    ['name' => 'Ebbo', 'code' => 'LAP02'],
                    ['name' => 'Evuti', 'code' => 'LAP03'],
                    ['name' => 'Gulu', 'code' => 'LAP04'],
                    ['name' => 'Lapai', 'code' => 'LAP05'],
                    ['name' => 'Makera', 'code' => 'LAP06'],
                    ['name' => 'Shabamaliki', 'code' => 'LAP07'],
                    ['name' => 'Takuma', 'code' => 'LAP08'],
                    ['name' => 'Tswako', 'code' => 'LAP09'],
                    ['name' => 'Zhitta', 'code' => 'LAP10']
                ]
            ],
            [
                'name' => 'Lavun',
                'code' => 'LAV',
                'headquarters' => 'Kutigi',
                'area_km2' => 1667,
                'population_estimate' => 120077,
                'coordinates' => ['latitude' => 9.2000, 'longitude' => 5.7500],
                'wards' => [
                    ['name' => 'Dabban', 'code' => 'LAV01'],
                    ['name' => 'Doko', 'code' => 'LAV02'],
                    ['name' => 'Edozhigi', 'code' => 'LAV03'],
                    ['name' => 'Gaba', 'code' => 'LAV04'],
                    ['name' => 'Jere', 'code' => 'LAV05'],
                    ['name' => 'Kutigi', 'code' => 'LAV06'],
                    ['name' => 'Lafiagi', 'code' => 'LAV07'],
                    ['name' => 'Mambe', 'code' => 'LAV08'],
                    ['name' => 'Mayaki Ndajiya', 'code' => 'LAV09'],
                    ['name' => 'Rofia', 'code' => 'LAV10']
                ]
            ],
            [
                'name' => 'Magama',
                'code' => 'MAG',
                'headquarters' => 'Nasko',
                'area_km2' => 3356,
                'population_estimate' => 124464,
                'coordinates' => ['latitude' => 10.7500, 'longitude' => 5.0000],
                'wards' => [
                    ['name' => 'Auna', 'code' => 'MAG01'],
                    ['name' => 'Ibelu', 'code' => 'MAG02'],
                    ['name' => 'Kabi', 'code' => 'MAG03'],
                    ['name' => 'Kampani Waru', 'code' => 'MAG04'],
                    ['name' => 'Koro', 'code' => 'MAG05'],
                    ['name' => 'Libata', 'code' => 'MAG06'],
                    ['name' => 'Magama', 'code' => 'MAG07'],
                    ['name' => 'Nasko', 'code' => 'MAG08'],
                    ['name' => 'Riba', 'code' => 'MAG09'],
                    ['name' => 'Shagunu', 'code' => 'MAG10']
                ]
            ],
            [
                'name' => 'Mariga',
                'code' => 'MAR',
                'headquarters' => 'Bangi',
                'area_km2' => 4962,
                'population_estimate' => 344042,
                'coordinates' => ['latitude' => 11.4000, 'longitude' => 6.2000],
                'wards' => [
                    ['name' => 'Bangi', 'code' => 'MAR01'],
                    ['name' => 'Bobi', 'code' => 'MAR02'],
                    ['name' => 'Galma', 'code' => 'MAR03'],
                    ['name' => 'Igwama', 'code' => 'MAR04'],
                    ['name' => 'Kankara', 'code' => 'MAR05'],
                    ['name' => 'Kufana', 'code' => 'MAR06'],
                    ['name' => 'Mariga', 'code' => 'MAR07'],
                    ['name' => 'Pandogari', 'code' => 'MAR08'],
                    ['name' => 'Tegina', 'code' => 'MAR09'],
                    ['name' => 'Yandoto', 'code' => 'MAR10']
                ]
            ],
            [
                'name' => 'Mashegu',
                'code' => 'MAS',
                'headquarters' => 'Mashegu',
                'area_km2' => 4558,
                'population_estimate' => 171421,
                'coordinates' => ['latitude' => 10.0000, 'longitude' => 5.7500],
                'wards' => [
                    ['name' => 'Adunu', 'code' => 'MAS01'],
                    ['name' => 'Babban Rami', 'code' => 'MAS02'],
                    ['name' => 'Dapangi', 'code' => 'MAS03'],
                    ['name' => 'Kasanga', 'code' => 'MAS04'],
                    ['name' => 'Kwatachi', 'code' => 'MAS05'],
                    ['name' => 'Makera', 'code' => 'MAS06'],
                    ['name' => 'Mashegu', 'code' => 'MAS07'],
                    ['name' => 'Mazakuka', 'code' => 'MAS08'],
                    ['name' => 'Saho', 'code' => 'MAS09'],
                    ['name' => 'Tegina', 'code' => 'MAS10']
                ]
            ]
        ];
    }

    /**
     * Get third batch of LGAs
     */
    private function getThirdBatchLGAs(): array
    {
        return [
            [
                'name' => 'Mokwa',
                'code' => 'MOK',
                'headquarters' => 'Mokwa',
                'area_km2' => 4952,
                'population_estimate' => 188671,
                'coordinates' => ['latitude' => 9.2833, 'longitude' => 5.0500],
                'wards' => [
                    ['name' => 'Bokani', 'code' => 'MOK01'],
                    ['name' => 'Gbajibo', 'code' => 'MOK02'],
                    ['name' => 'Jebba North', 'code' => 'MOK03'],
                    ['name' => 'Jebba South', 'code' => 'MOK04'],
                    ['name' => 'Kpaki', 'code' => 'MOK05'],
                    ['name' => 'Kudu', 'code' => 'MOK06'],
                    ['name' => 'Mokwa', 'code' => 'MOK07'],
                    ['name' => 'Muregi', 'code' => 'MOK08'],
                    ['name' => 'Rabba', 'code' => 'MOK09'],
                    ['name' => 'Takuma', 'code' => 'MOK10']
                ]
            ],
            [
                'name' => 'Munya',
                'code' => 'MUN',
                'headquarters' => 'Sarkin Pawa',
                'area_km2' => 4519,
                'population_estimate' => 112018,
                'coordinates' => ['latitude' => 10.5000, 'longitude' => 6.7500],
                'wards' => [
                    ['name' => 'Beni', 'code' => 'MUN01'],
                    ['name' => 'Dangunu', 'code' => 'MUN02'],
                    ['name' => 'Fuka', 'code' => 'MUN03'],
                    ['name' => 'Guni', 'code' => 'MUN04'],
                    ['name' => 'Kabula', 'code' => 'MUN05'],
                    ['name' => 'Kazai', 'code' => 'MUN06'],
                    ['name' => 'Kuchi', 'code' => 'MUN07'],
                    ['name' => 'Munya', 'code' => 'MUN08'],
                    ['name' => 'Sarkin Pawa', 'code' => 'MUN09'],
                    ['name' => 'Yelwa', 'code' => 'MUN10']
                ]
            ],
            [
                'name' => 'Paikoro',
                'code' => 'PAI',
                'headquarters' => 'Kafin Koro',
                'area_km2' => 1746,
                'population_estimate' => 131367,
                'coordinates' => ['latitude' => 9.7500, 'longitude' => 6.7500],
                'wards' => [
                    ['name' => 'Adunu', 'code' => 'PAI01'],
                    ['name' => 'Chimbi', 'code' => 'PAI02'],
                    ['name' => 'Ishau', 'code' => 'PAI03'],
                    ['name' => 'Jere', 'code' => 'PAI04'],
                    ['name' => 'Kafin Koro', 'code' => 'PAI05'],
                    ['name' => 'Kwakuti', 'code' => 'PAI06'],
                    ['name' => 'Paikoro', 'code' => 'PAI07'],
                    ['name' => 'Tutungo Jedna', 'code' => 'PAI08'],
                    ['name' => 'Yakila', 'code' => 'PAI09'],
                    ['name' => 'Zungeru', 'code' => 'PAI10']
                ]
            ],
            [
                'name' => 'Rafi',
                'code' => 'RAF',
                'headquarters' => 'Kagara',
                'area_km2' => 4670,
                'population_estimate' => 246644,
                'coordinates' => ['latitude' => 10.3000, 'longitude' => 6.1000],
                'wards' => [
                    ['name' => 'Galkogo', 'code' => 'RAF01'],
                    ['name' => 'Genu', 'code' => 'RAF02'],
                    ['name' => 'Kagara', 'code' => 'RAF03'],
                    ['name' => 'Kongoma', 'code' => 'RAF04'],
                    ['name' => 'Kundu', 'code' => 'RAF05'],
                    ['name' => 'Kusasu', 'code' => 'RAF06'],
                    ['name' => 'Madaka', 'code' => 'RAF07'],
                    ['name' => 'Rafi', 'code' => 'RAF08'],
                    ['name' => 'Tegina', 'code' => 'RAF09'],
                    ['name' => 'Yakila', 'code' => 'RAF10']
                ]
            ],
            [
                'name' => 'Rijau',
                'code' => 'RIJ',
                'headquarters' => 'Rijau',
                'area_km2' => 4236,
                'population_estimate' => 165912,
                'coordinates' => ['latitude' => 11.1500, 'longitude' => 5.2500],
                'wards' => [
                    ['name' => 'Danrangi', 'code' => 'RIJ01'],
                    ['name' => 'Dugga', 'code' => 'RIJ02'],
                    ['name' => 'Dukku', 'code' => 'RIJ03'],
                    ['name' => 'Genu', 'code' => 'RIJ04'],
                    ['name' => 'Jama\'a', 'code' => 'RIJ05'],
                    ['name' => 'Rijau', 'code' => 'RIJ06'],
                    ['name' => 'Shambo', 'code' => 'RIJ07'],
                    ['name' => 'T/Magajiya', 'code' => 'RIJ08'],
                    ['name' => 'Ushe', 'code' => 'RIJ09'],
                    ['name' => 'Warrah', 'code' => 'RIJ10']
                ]
            ]
        ];
    }

    /**
     * Get fourth batch of LGAs (final batch)
     */
    private function getFourthBatchLGAs(): array
    {
        return [
            [
                'name' => 'Shiroro',
                'code' => 'SHI',
                'headquarters' => 'Kuta',
                'area_km2' => 3940,
                'population_estimate' => 206188,
                'coordinates' => ['latitude' => 10.1667, 'longitude' => 6.7500],
                'wards' => [
                    ['name' => 'Allawa', 'code' => 'SHI01'],
                    ['name' => 'Bangajiya', 'code' => 'SHI02'],
                    ['name' => 'Bassa', 'code' => 'SHI03'],
                    ['name' => 'Chukuba', 'code' => 'SHI04'],
                    ['name' => 'Erena', 'code' => 'SHI05'],
                    ['name' => 'Galkogo', 'code' => 'SHI06'],
                    ['name' => 'Gurmana', 'code' => 'SHI07'],
                    ['name' => 'Kuta', 'code' => 'SHI08'],
                    ['name' => 'Manta', 'code' => 'SHI09'],
                    ['name' => 'Shiroro', 'code' => 'SHI10']
                ]
            ],
            [
                'name' => 'Suleja',
                'code' => 'SUL',
                'headquarters' => 'Suleja',
                'area_km2' => 1344,
                'population_estimate' => 216578,
                'coordinates' => ['latitude' => 9.1833, 'longitude' => 7.1833],
                'wards' => [
                    ['name' => 'Bagama A', 'code' => 'SUL01'],
                    ['name' => 'Bagama B', 'code' => 'SUL02'],
                    ['name' => 'Hashimi A', 'code' => 'SUL03'],
                    ['name' => 'Hashimi B', 'code' => 'SUL04'],
                    ['name' => 'Iku South', 'code' => 'SUL05'],
                    ['name' => 'Kurmin Sarki', 'code' => 'SUL06'],
                    ['name' => 'Kwamba', 'code' => 'SUL07'],
                    ['name' => 'Magajiya', 'code' => 'SUL08'],
                    ['name' => 'Suleja', 'code' => 'SUL09'],
                    ['name' => 'Wuya', 'code' => 'SUL10']
                ]
            ],
            [
                'name' => 'Tafa',
                'code' => 'TAF',
                'headquarters' => 'Sabo Wuse',
                'area_km2' => 2161,
                'population_estimate' => 103015,
                'coordinates' => ['latitude' => 9.3500, 'longitude' => 7.3500],
                'wards' => [
                    ['name' => 'Dogon Kurmi', 'code' => 'TAF01'],
                    ['name' => 'Garam', 'code' => 'TAF02'],
                    ['name' => 'Ijah', 'code' => 'TAF03'],
                    ['name' => 'New Bwari', 'code' => 'TAF04'],
                    ['name' => 'Sabo Wuse', 'code' => 'TAF05'],
                    ['name' => 'Tafa', 'code' => 'TAF06'],
                    ['name' => 'Wuse', 'code' => 'TAF07'],
                    ['name' => 'Zuma', 'code' => 'TAF08'],
                    ['name' => 'Karshi', 'code' => 'TAF09'],
                    ['name' => 'Ushafa', 'code' => 'TAF10']
                ]
            ],
            [
                'name' => 'Wushishi',
                'code' => 'WUS',
                'headquarters' => 'Wushishi',
                'area_km2' => 1713,
                'population_estimate' => 103639,
                'coordinates' => ['latitude' => 9.7000, 'longitude' => 5.9500],
                'wards' => [
                    ['name' => 'Akare', 'code' => 'WUS01'],
                    ['name' => 'Bankanu', 'code' => 'WUS02'],
                    ['name' => 'Gwarjiko', 'code' => 'WUS03'],
                    ['name' => 'Kanko', 'code' => 'WUS04'],
                    ['name' => 'Lokogoma', 'code' => 'WUS05'],
                    ['name' => 'Maburya', 'code' => 'WUS06'],
                    ['name' => 'Mapai', 'code' => 'WUS07'],
                    ['name' => 'Tukunji', 'code' => 'WUS08'],
                    ['name' => 'Wushishi', 'code' => 'WUS09'],
                    ['name' => 'Zungeru', 'code' => 'WUS10']
                ]
            ]
        ];
    }

    /**
     * Determine zone based on LGA name
     */
    private function determineZone(string $lgaName): string
    {
        // Zone A (Northern Niger State)
        $zoneA = ['Agwara', 'Borgu', 'Kontagora', 'Magama', 'Mariga', 'Mashegu', 'Rijau', 'Wushishi'];

        // Zone B (Central Niger State)
        $zoneB = ['Agaie', 'Bida', 'Gbako', 'Katcha', 'Lapai', 'Lavun', 'Mokwa', 'Edati'];

        // Zone C (Eastern Niger State)
        $zoneC = ['Bosso', 'Chanchaga', 'Gurara', 'Munya', 'Paikoro', 'Rafi', 'Shiroro', 'Suleja', 'Tafa'];

        if (in_array($lgaName, $zoneA)) {
            return 'Zone A';
        } elseif (in_array($lgaName, $zoneB)) {
            return 'Zone B';
        } elseif (in_array($lgaName, $zoneC)) {
            return 'Zone C';
        }

        return 'Zone B'; // Default
    }

    /**
     * Generate ward coordinates near LGA coordinates
     */
    private function generateWardCoordinates(float $lgaCoordinate): float
    {
        // Generate coordinates within ±0.05 degrees of LGA coordinate
        $offset = (rand(-50, 50) / 1000); // ±0.05 degrees
        return round($lgaCoordinate + $offset, 6);
    }

    /**
     * Estimate ward population
     */
    private function estimateWardPopulation(int $lgaPopulation, int $wardCount): int
    {
        // Distribute population among wards with some variation
        $basePopulation = intval($lgaPopulation / $wardCount);
        $variation = rand(-20, 20); // ±20% variation
        return max(1000, intval($basePopulation * (1 + $variation / 100)));
    }
}
