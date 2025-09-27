<?php

namespace Database\Seeders;

use App\Models\Lga;
use App\Models\Ward;
use Illuminate\Database\Seeder;

class WardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all LGAs
        $lgas = Lga::all();

        foreach ($lgas as $lga) {
            // Create sample wards for each LGA
            $wards = $this->getWardsForLga($lga->name, $lga->id);

            foreach ($wards as $wardData) {
                Ward::create($wardData);
            }
        }
    }

    private function getWardsForLga(string $lgaName, int $lgaId): array
    {
        $wardsData = [
            'Minna' => [
                ['name' => 'Barkin Sale', 'code' => 'MNN-BS'],
                ['name' => 'Bosso', 'code' => 'MNN-BSS'],
                ['name' => 'Chanchaga', 'code' => 'MNN-CHG'],
                ['name' => 'Dutsen Kura', 'code' => 'MNN-DK'],
                ['name' => 'Kpakungu', 'code' => 'MNN-KPK'],
                ['name' => 'Limawa', 'code' => 'MNN-LMW'],
                ['name' => 'Maitumbi', 'code' => 'MNN-MTB'],
                ['name' => 'Minna Central', 'code' => 'MNN-CTR'],
                ['name' => 'Nasarawa', 'code' => 'MNN-NSR'],
                ['name' => 'Sauka Kahuta', 'code' => 'MNN-SKH'],
            ],
            'Bida' => [
                ['name' => 'Bida Central', 'code' => 'BDA-CTR'],
                ['name' => 'Dokodza', 'code' => 'BDA-DKZ'],
                ['name' => 'Kyari', 'code' => 'BDA-KYR'],
                ['name' => 'Landzun', 'code' => 'BDA-LDZ'],
                ['name' => 'Masaba I', 'code' => 'BDA-MS1'],
                ['name' => 'Masaba II', 'code' => 'BDA-MS2'],
                ['name' => 'Mayaki Ndajiya', 'code' => 'BDA-MND'],
                ['name' => 'Nassarafu', 'code' => 'BDA-NSF'],
                ['name' => 'Umaru/Majigi I', 'code' => 'BDA-UM1'],
                ['name' => 'Umaru/Majigi II', 'code' => 'BDA-UM2'],
                ['name' => 'Wadata', 'code' => 'BDA-WDT'],
            ],
            'Suleja' => [
                ['name' => 'Bagama', 'code' => 'SLJ-BGM'],
                ['name' => 'Hashimi', 'code' => 'SLJ-HSM'],
                ['name' => 'Iku South', 'code' => 'SLJ-IKS'],
                ['name' => 'Kurmin Sarki', 'code' => 'SLJ-KSK'],
                ['name' => 'Magajiya', 'code' => 'SLJ-MGJ'],
                ['name' => 'Suleja', 'code' => 'SLJ-SLJ'],
                ['name' => 'Wambai', 'code' => 'SLJ-WMB'],
            ],
            'Kontagora' => [
                ['name' => 'Auna', 'code' => 'KTG-AUN'],
                ['name' => 'Gulbin Boka', 'code' => 'KTG-GBK'],
                ['name' => 'Jikantoro', 'code' => 'KTG-JKT'],
                ['name' => 'Kontagora I', 'code' => 'KTG-KT1'],
                ['name' => 'Kontagora II', 'code' => 'KTG-KT2'],
                ['name' => 'Kontagora III', 'code' => 'KTG-KT3'],
                ['name' => 'Mayama', 'code' => 'KTG-MYM'],
                ['name' => 'Ruma', 'code' => 'KTG-RUM'],
                ['name' => 'Ushe', 'code' => 'KTG-USH'],
                ['name' => 'Wushishi', 'code' => 'KTG-WSH'],
            ]
        ];

        // Default wards for LGAs not specifically defined
        $defaultWards = [
            ['name' => $lgaName . ' Central', 'code' => substr($lgaName, 0, 3) . '-CTR'],
            ['name' => $lgaName . ' North', 'code' => substr($lgaName, 0, 3) . '-NTH'],
            ['name' => $lgaName . ' South', 'code' => substr($lgaName, 0, 3) . '-STH'],
            ['name' => $lgaName . ' East', 'code' => substr($lgaName, 0, 3) . '-EST'],
            ['name' => $lgaName . ' West', 'code' => substr($lgaName, 0, 3) . '-WST'],
        ];

        $wards = $wardsData[$lgaName] ?? $defaultWards;

        // Add LGA ID and additional data to each ward
        return array_map(function ($ward) use ($lgaId) {
            return [
                'lga_id' => $lgaId,
                'name' => $ward['name'],
                'code' => $ward['code'],
                'latitude' => null, // Will be populated later if needed
                'longitude' => null,
                'population_estimate' => rand(5000, 25000),
                'description' => 'Ward in ' . $ward['name'] . ' area',
            ];
        }, $wards);
    }
}
