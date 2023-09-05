<?php

namespace App\Test\Controller;

use App\Entity\Modem;
use App\Repository\ModemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModemControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ModemRepository $repository;
    private string $path = '/modem/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Modem::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Modem index');

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
            'modem[brand]' => 'Testing',
            'modem[ip]' => 'Testing',
            'modem[physicalAddress]' => 'Testing',
            'modem[physicalSerial]' => 'Testing',
            'modem[model]' => 'Testing',
            'modem[inventory]' => 'Testing',
            'modem[contic]' => 'Testing',
            'modem[state]' => 'Testing',
            'modem[municipality]' => 'Testing',
            'modem[port]' => 'Testing',
            'modem[slaveModem]' => 'Testing',
            'modem[structuredCable]' => 'Testing',
        ]);

        self::assertResponseRedirects('/modem/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Modem();
        $fixture->setBrand('My Title');
        $fixture->setIp('My Title');
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPhysicalSerial('My Title');
        $fixture->setModel('My Title');
        $fixture->setInventory('My Title');
        $fixture->setContic('My Title');
        $fixture->setState('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setPort('My Title');
        $fixture->setMasterModem('My Title');
        $fixture->setStructuredCable('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Modem');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Modem();
        $fixture->setBrand('My Title');
        $fixture->setIp('My Title');
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPhysicalSerial('My Title');
        $fixture->setModel('My Title');
        $fixture->setInventory('My Title');
        $fixture->setContic('My Title');
        $fixture->setState('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setPort('My Title');
        $fixture->setMasterModem('My Title');
        $fixture->setStructuredCable('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'modem[brand]' => 'Something New',
            'modem[ip]' => 'Something New',
            'modem[physicalAddress]' => 'Something New',
            'modem[physicalSerial]' => 'Something New',
            'modem[model]' => 'Something New',
            'modem[inventory]' => 'Something New',
            'modem[contic]' => 'Something New',
            'modem[state]' => 'Something New',
            'modem[municipality]' => 'Something New',
            'modem[port]' => 'Something New',
            'modem[slaveModem]' => 'Something New',
            'modem[structuredCable]' => 'Something New',
        ]);

        self::assertResponseRedirects('/modem/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getBrand());
        self::assertSame('Something New', $fixture[0]->getIp());
        self::assertSame('Something New', $fixture[0]->getPhysicalAddress());
        self::assertSame('Something New', $fixture[0]->getPhysicalSerial());
        self::assertSame('Something New', $fixture[0]->getModel());
        self::assertSame('Something New', $fixture[0]->getInventory());
        self::assertSame('Something New', $fixture[0]->getContic());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getMunicipality());
        self::assertSame('Something New', $fixture[0]->getPort());
        self::assertSame('Something New', $fixture[0]->getMasterModem());
        self::assertSame('Something New', $fixture[0]->getStructuredCable());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Modem();
        $fixture->setBrand('My Title');
        $fixture->setIp('My Title');
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPhysicalSerial('My Title');
        $fixture->setModel('My Title');
        $fixture->setInventory('My Title');
        $fixture->setContic('My Title');
        $fixture->setState('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setPort('My Title');
        $fixture->setMasterModem('My Title');
        $fixture->setStructuredCable('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/modem/');
    }
}
