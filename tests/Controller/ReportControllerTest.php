<?php

namespace App\Test\Controller;

use App\Entity\Report;
use App\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReportControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ReportRepository $repository;
    private string $path = '/report/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Report::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Report index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'report[number]' => 'Testing',
            'report[specialty]' => 'Testing',
            'report[entryDate]' => 'Testing',
            'report[closeDate]' => 'Testing',
            'report[type]' => 'Testing',
            'report[interruptionReason]' => 'Testing',
            'report[priority]' => 'Testing',
            'report[flaw]' => 'Testing',
            'report[observation]' => 'Testing',
            'report[solution]' => 'Testing',
            'report[unit]' => 'Testing',
            'report[state]' => 'Testing',
            'report[aim]' => 'Testing',
            'report[equipment]' => 'Testing',
            'report[boss]' => 'Testing',
            'report[managementOfficer]' => 'Testing',
            'report[organ]' => 'Testing',
        ]);

        self::assertResponseRedirects('/report/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Report();
        $fixture->setNumber('My Title');
        $fixture->setSpecialty('My Title');
        $fixture->setEntryDate('My Title');
        $fixture->setCloseDate('My Title');
        $fixture->setType('My Title');
        $fixture->setInterruptionReason('My Title');
        $fixture->setPriority('My Title');
        $fixture->setFlaw('My Title');
        $fixture->setObservation('My Title');
        $fixture->setSolution('My Title');
        $fixture->setUnit('My Title');
        $fixture->setState('My Title');
        $fixture->setAim('My Title');
        $fixture->setEquipment('My Title');
        $fixture->setBoss('My Title');
        $fixture->setManagementOfficer('My Title');
        $fixture->setOrgan('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Report');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Report();
        $fixture->setNumber('My Title');
        $fixture->setSpecialty('My Title');
        $fixture->setEntryDate('My Title');
        $fixture->setCloseDate('My Title');
        $fixture->setType('My Title');
        $fixture->setInterruptionReason('My Title');
        $fixture->setPriority('My Title');
        $fixture->setFlaw('My Title');
        $fixture->setObservation('My Title');
        $fixture->setSolution('My Title');
        $fixture->setUnit('My Title');
        $fixture->setState('My Title');
        $fixture->setAim('My Title');
        $fixture->setEquipment('My Title');
        $fixture->setBoss('My Title');
        $fixture->setManagementOfficer('My Title');
        $fixture->setOrgan('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'report[number]' => 'Something New',
            'report[specialty]' => 'Something New',
            'report[entryDate]' => 'Something New',
            'report[closeDate]' => 'Something New',
            'report[type]' => 'Something New',
            'report[interruptionReason]' => 'Something New',
            'report[priority]' => 'Something New',
            'report[flaw]' => 'Something New',
            'report[observation]' => 'Something New',
            'report[solution]' => 'Something New',
            'report[unit]' => 'Something New',
            'report[state]' => 'Something New',
            'report[aim]' => 'Something New',
            'report[equipment]' => 'Something New',
            'report[boss]' => 'Something New',
            'report[managementOfficer]' => 'Something New',
            'report[organ]' => 'Something New',
        ]);

        self::assertResponseRedirects('/report/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNumber());
        self::assertSame('Something New', $fixture[0]->getSpecialty());
        self::assertSame('Something New', $fixture[0]->getEntryDate());
        self::assertSame('Something New', $fixture[0]->getCloseDate());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getInterruptionReason());
        self::assertSame('Something New', $fixture[0]->getPriority());
        self::assertSame('Something New', $fixture[0]->getFlaw());
        self::assertSame('Something New', $fixture[0]->getObservation());
        self::assertSame('Something New', $fixture[0]->getSolution());
        self::assertSame('Something New', $fixture[0]->getUnit());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getAim());
        self::assertSame('Something New', $fixture[0]->getEquipment());
        self::assertSame('Something New', $fixture[0]->getBoss());
        self::assertSame('Something New', $fixture[0]->getManagementOfficer());
        self::assertSame('Something New', $fixture[0]->getOrgan());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Report();
        $fixture->setNumber('My Title');
        $fixture->setSpecialty('My Title');
        $fixture->setEntryDate('My Title');
        $fixture->setCloseDate('My Title');
        $fixture->setType('My Title');
        $fixture->setInterruptionReason('My Title');
        $fixture->setPriority('My Title');
        $fixture->setFlaw('My Title');
        $fixture->setObservation('My Title');
        $fixture->setSolution('My Title');
        $fixture->setUnit('My Title');
        $fixture->setState('My Title');
        $fixture->setAim('My Title');
        $fixture->setEquipment('My Title');
        $fixture->setBoss('My Title');
        $fixture->setManagementOfficer('My Title');
        $fixture->setOrgan('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/report/');
    }
}
