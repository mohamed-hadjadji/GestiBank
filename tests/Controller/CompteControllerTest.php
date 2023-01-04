<?php

namespace App\Test\Controller;

use App\Entity\Compte;
use App\Repository\CompteRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompteControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CompteRepository $repository;
    private string $path = '/compte/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Compte::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Compte index');

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
            'compte[num_compte]' => 'Testing',
            'compte[type]' => 'Testing',
            'compte[solde]' => 'Testing',
            'compte[date_creation]' => 'Testing',
            'compte[user]' => 'Testing',
        ]);

        self::assertResponseRedirects('/compte/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Compte();
        $fixture->setNum_compte('My Title');
        $fixture->setType('My Title');
        $fixture->setSolde('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Compte');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Compte();
        $fixture->setNum_compte('My Title');
        $fixture->setType('My Title');
        $fixture->setSolde('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'compte[num_compte]' => 'Something New',
            'compte[type]' => 'Something New',
            'compte[solde]' => 'Something New',
            'compte[date_creation]' => 'Something New',
            'compte[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/compte/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNum_compte());
        self::assertSame('Something New', $fixture[0]->getType());
        self::assertSame('Something New', $fixture[0]->getSolde());
        self::assertSame('Something New', $fixture[0]->getDate_creation());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Compte();
        $fixture->setNum_compte('My Title');
        $fixture->setType('My Title');
        $fixture->setSolde('My Title');
        $fixture->setDate_creation('My Title');
        $fixture->setUser('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/compte/');
    }
}
