<?php

namespace App\Test\Controller;

use App\Entity\Commutator;
use App\Repository\CommutatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommutatorControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CommutatorRepository $repository;
    private string $path = '/crud/commutator/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Commutator::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commutator index');

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
            'commutator[brand]' => 'Testing',
            'commutator[ip]' => 'Testing',
            'commutator[physicalAddress]' => 'Testing',
            'commutator[physicalSerial]' => 'Testing',
            'commutator[model]' => 'Testing',
            'commutator[inventory]' => 'Testing',
            'commutator[contic]' => 'Testing',
            'commutator[state]' => 'Testing',
            'commutator[portsAmount]' => 'Testing',
            'commutator[gateway]' => 'Testing',
            'commutator[multicast]' => 'Testing',
            'commutator[municipality]' => 'Testing',
            'commutator[port]' => 'Testing',
        ]);

        self::assertResponseRedirects('/crud/commutator/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commutator();
        $fixture->setBrand('My Title');
        $fixture->setIp('My Title');
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPhysicalSerial('My Title');
        $fixture->setModel('My Title');
        $fixture->setInventory('My Title');
        $fixture->setContic('My Title');
        $fixture->setState('My Title');
        $fixture->setPortsAmount('My Title');
        $fixture->setGateway('My Title');
        $fixture->setMulticast('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setPort('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Commutator');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Commutator();
        $fixture->setBrand('My Title');
        $fixture->setIp('My Title');
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPhysicalSerial('My Title');
        $fixture->setModel('My Title');
        $fixture->setInventory('My Title');
        $fixture->setContic('My Title');
        $fixture->setState('My Title');
        $fixture->setPortsAmount('My Title');
        $fixture->setGateway('My Title');
        $fixture->setMulticast('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setPort('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'commutator[brand]' => 'Something New',
            'commutator[ip]' => 'Something New',
            'commutator[physicalAddress]' => 'Something New',
            'commutator[physicalSerial]' => 'Something New',
            'commutator[model]' => 'Something New',
            'commutator[inventory]' => 'Something New',
            'commutator[contic]' => 'Something New',
            'commutator[state]' => 'Something New',
            'commutator[portsAmount]' => 'Something New',
            'commutator[gateway]' => 'Something New',
            'commutator[multicast]' => 'Something New',
            'commutator[municipality]' => 'Something New',
            'commutator[port]' => 'Something New',
        ]);

        self::assertResponseRedirects('/crud/commutator/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getBrand());
        self::assertSame('Something New', $fixture[0]->getIp());
        self::assertSame('Something New', $fixture[0]->getPhysicalAddress());
        self::assertSame('Something New', $fixture[0]->getPhysicalSerial());
        self::assertSame('Something New', $fixture[0]->getModel());
        self::assertSame('Something New', $fixture[0]->getInventory());
        self::assertSame('Something New', $fixture[0]->getContic());
        self::assertSame('Something New', $fixture[0]->getState());
        self::assertSame('Something New', $fixture[0]->getPortsAmount());
        self::assertSame('Something New', $fixture[0]->getGateway());
        self::assertSame('Something New', $fixture[0]->getMulticast());
        self::assertSame('Something New', $fixture[0]->getMunicipality());
        self::assertSame('Something New', $fixture[0]->getPort());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Commutator();
        $fixture->setBrand('My Title');
        $fixture->setIp('My Title');
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPhysicalSerial('My Title');
        $fixture->setModel('My Title');
        $fixture->setInventory('My Title');
        $fixture->setContic('My Title');
        $fixture->setState('My Title');
        $fixture->setPortsAmount('My Title');
        $fixture->setGateway('My Title');
        $fixture->setMulticast('My Title');
        $fixture->setMunicipality('My Title');
        $fixture->setPort('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/crud/commutator/');
    }
}
