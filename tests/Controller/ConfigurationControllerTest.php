<?php

namespace App\Test\Controller;

use App\Entity\Configuration;
use App\Repository\ConfigurationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConfigurationControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ConfigurationRepository $repository;
    private string $path = '/configuration/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Configuration::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Configuration index');

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
            'configuration[logo]' => 'Testing',
            'configuration[titre]' => 'Testing',
            'configuration[description]' => 'Testing',
            'configuration[service_titre]' => 'Testing',
            'configuration[contact_titre]' => 'Testing',
            'configuration[contact_desc]' => 'Testing',
            'configuration[telephone]' => 'Testing',
            'configuration[adresse]' => 'Testing',
            'configuration[mail]' => 'Testing',
            'configuration[facebook]' => 'Testing',
            'configuration[linkedin]' => 'Testing',
            'configuration[twitter]' => 'Testing',
            'configuration[footer_desc]' => 'Testing',
        ]);

        self::assertResponseRedirects('/configuration/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Configuration();
        $fixture->setLogo('My Title');
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setService_titre('My Title');
        $fixture->setContact_titre('My Title');
        $fixture->setContact_desc('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setMail('My Title');
        $fixture->setFacebook('My Title');
        $fixture->setLinkedin('My Title');
        $fixture->setTwitter('My Title');
        $fixture->setFooter_desc('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Configuration');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Configuration();
        $fixture->setLogo('My Title');
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setService_titre('My Title');
        $fixture->setContact_titre('My Title');
        $fixture->setContact_desc('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setMail('My Title');
        $fixture->setFacebook('My Title');
        $fixture->setLinkedin('My Title');
        $fixture->setTwitter('My Title');
        $fixture->setFooter_desc('My Title');

        $this->repository->save($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'configuration[logo]' => 'Something New',
            'configuration[titre]' => 'Something New',
            'configuration[description]' => 'Something New',
            'configuration[service_titre]' => 'Something New',
            'configuration[contact_titre]' => 'Something New',
            'configuration[contact_desc]' => 'Something New',
            'configuration[telephone]' => 'Something New',
            'configuration[adresse]' => 'Something New',
            'configuration[mail]' => 'Something New',
            'configuration[facebook]' => 'Something New',
            'configuration[linkedin]' => 'Something New',
            'configuration[twitter]' => 'Something New',
            'configuration[footer_desc]' => 'Something New',
        ]);

        self::assertResponseRedirects('/configuration/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getLogo());
        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getService_titre());
        self::assertSame('Something New', $fixture[0]->getContact_titre());
        self::assertSame('Something New', $fixture[0]->getContact_desc());
        self::assertSame('Something New', $fixture[0]->getTelephone());
        self::assertSame('Something New', $fixture[0]->getAdresse());
        self::assertSame('Something New', $fixture[0]->getMail());
        self::assertSame('Something New', $fixture[0]->getFacebook());
        self::assertSame('Something New', $fixture[0]->getLinkedin());
        self::assertSame('Something New', $fixture[0]->getTwitter());
        self::assertSame('Something New', $fixture[0]->getFooter_desc());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Configuration();
        $fixture->setLogo('My Title');
        $fixture->setTitre('My Title');
        $fixture->setDescription('My Title');
        $fixture->setService_titre('My Title');
        $fixture->setContact_titre('My Title');
        $fixture->setContact_desc('My Title');
        $fixture->setTelephone('My Title');
        $fixture->setAdresse('My Title');
        $fixture->setMail('My Title');
        $fixture->setFacebook('My Title');
        $fixture->setLinkedin('My Title');
        $fixture->setTwitter('My Title');
        $fixture->setFooter_desc('My Title');

        $this->repository->save($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/configuration/');
    }
}
