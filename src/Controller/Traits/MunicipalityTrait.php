<?php

namespace App\Controller\Traits;

use App\Entity\Equipment;
use App\Entity\Municipality;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

trait MunicipalityTrait
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param int|null $municipalityId
     * @return Municipality
     */
    public function findMunicipalityForEquipment(EntityManagerInterface $entityManager, ?int $municipalityId): Municipality
    {
        if (!is_null($municipalityId)) {
            return $entityManager->getRepository(Municipality::class)->find($municipalityId);
        } else {
            return $entityManager->getRepository(Municipality::class)->findOneBy(['name' => 'Sin municipio']);
        }
    }

    /**
     * @param Equipment $equipment
     * @param Request $request
     * @param string $dataFiler
     * @return int
     */
    public function findMunicipalityForExistEquipment(Equipment $equipment, Request $request, string $dataFiler): int
    {

        if (isset($request->request->all()[$dataFiler])) {
            $equipmentPostData = $request->request->all()[$dataFiler];
            $address = $equipmentPostData['address'];
            $municipalityId = $address['municipality'] ?? null;
//            if (isset($address['municipality'])) {
//                $municipalityId = $address['municipality'];
//            } else {
//                $municipalityId = null;
//            }
        } else {
            $municipalityId = $equipment->getMunicipality()->getId();
        }

        return $municipalityId;
    }
}