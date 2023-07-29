<?php

namespace App\DataFixtures;

use App\Entity\Municipality;
use App\Entity\Province;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProvinceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $provinces = ['Pinar del Río', 'La Habana', 'Artemisa', 'Mayabeque', 'Matanzas', 'Cienfuegos'];
        foreach ($provinces  as $provinceName){
            $province = $manager->getRepository(Province::class)->findOneBy(['name' => $provinceName]);
            if(is_null($province)){
                $result = match ($provinceName) {
                    'Pinar del Río' => $this->addProvinceMunicipality($manager, $provinceName, ['Candelaria']),
                    'La Habana' => $this->addProvinceMunicipality($manager, $provinceName, ['Arroyo Naranjo', '10 de Octubre']),
                    'Artemisa' => $this->addProvinceMunicipality($manager, $provinceName, ['Artemisa']),
                    'Mayabeque' => $this->addProvinceMunicipality($manager, $provinceName, ['San Antonio']),
                    'Matanzas' => $this->addProvinceMunicipality($manager, $provinceName, ['Varadero', 'Cárdens']),
                    default => false,
                };

                if(!$result){
                    $manager->persist((new Province())->setName($provinceName));
                }
            }
        }

        $manager->flush();
    }

    public function addProvinceMunicipality(ObjectManager $manager, string $provinceName, array $municipalities): bool
    {
        $province = (new Province())->setName($provinceName);
        foreach ($municipalities as $municipality){
            $province->addMunicipality((new Municipality())->setName($municipality));
        }

        $manager->persist($province);

        return true;
    }

}
