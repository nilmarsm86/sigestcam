<?php

namespace App\Test\Controller;

use App\Entity\Server;
use App\Repository\ServerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ServerRepository $repository;
    private string $path = '/server/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Server::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Server index');

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
            'server[brand]' => 'Testing',
            'server[ip]' => 'Testing',
            'server[physicalAddress]' => 'Testing',
            'server[physicalSerial]' => 'Testing',
            'server[model]' => 'Testing',
            'server[inventory]' => 'Testing',
            'server[contic]' => 'Testing',
            'server[state]' => 'Testing',
            'server[municipality]' => 'Testing',
            'server[port]' => 'Testing',
        ]);

        self::assertResponseRedirects('/server/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Server();
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

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Server');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Server();
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

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'server[brand]' => 'Something New',
            'server[ip]' => 'Something New',
            'server[physicalAddress]' => 'Something New',
            'server[physicalSerial]' => 'Something New',
            'server[model]' => 'Something New',
            'server[inventory]' => 'Something New',
            'server[contic]' => 'Something New',
            'server[state]' => 'Something New',
            'server[municipality]' => 'Something New',
            'server[port]' => 'Something New',
        ]);

        self::assertResponseRedirects('/server/');

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
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Server();
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

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/server/');
    }
}
