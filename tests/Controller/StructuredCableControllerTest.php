<?php

namespace App\Test\Controller;

use App\Entity\StructuredCable;
use App\Repository\StructuredCableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StructuredCableControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private StructuredCableRepository $repository;
    private string $path = '/structured/cable/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(StructuredCable::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('StructuredCable index');

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
            'structured_cable[physicalAddress]' => 'Testing',
            'structured_cable[point]' => 'Testing',
            'structured_cable[path]' => 'Testing',
            'structured_cable[feederCable]' => 'Testing',
            'structured_cable[pair]' => 'Testing',
        ]);

        self::assertResponseRedirects('/structured/cable/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new StructuredCable();
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPoint('My Title');
        $fixture->setPath('My Title');
        $fixture->setFeederCable('My Title');
        $fixture->setPair('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('StructuredCable');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new StructuredCable();
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPoint('My Title');
        $fixture->setPath('My Title');
        $fixture->setFeederCable('My Title');
        $fixture->setPair('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'structured_cable[physicalAddress]' => 'Something New',
            'structured_cable[point]' => 'Something New',
            'structured_cable[path]' => 'Something New',
            'structured_cable[feederCable]' => 'Something New',
            'structured_cable[pair]' => 'Something New',
        ]);

        self::assertResponseRedirects('/structured/cable/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getPhysicalAddress());
        self::assertSame('Something New', $fixture[0]->getPoint());
        self::assertSame('Something New', $fixture[0]->getPath());
        self::assertSame('Something New', $fixture[0]->getFeederCable());
        self::assertSame('Something New', $fixture[0]->getPair());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new StructuredCable();
        $fixture->setPhysicalAddress('My Title');
        $fixture->setPoint('My Title');
        $fixture->setPath('My Title');
        $fixture->setFeederCable('My Title');
        $fixture->setPair('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/structured/cable/');
    }
}
