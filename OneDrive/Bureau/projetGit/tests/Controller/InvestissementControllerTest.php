<?php

namespace App\Test\Controller;

use App\Entity\Investissement;
use App\Repository\InvestissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvestissementControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private InvestissementRepository $repository;
    private string $path = '/investissement/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Investissement::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Investissement index');

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
            'investissement[dateAchat]' => 'Testing',
            'investissement[prixAchat]' => 'Testing',
            'investissement[ROI]' => 'Testing',
            'investissement[montant]' => 'Testing',
            'investissement[tax]' => 'Testing',
            'investissement[reId]' => 'Testing',
            'investissement[userId]' => 'Testing',
        ]);

        self::assertResponseRedirects('/investissement/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Investissement();
        $fixture->setDateAchat('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setROI('My Title');
        $fixture->setMontant('My Title');
        $fixture->setTax('My Title');
        $fixture->setReId('My Title');
        $fixture->setUserId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Investissement');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Investissement();
        $fixture->setDateAchat('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setROI('My Title');
        $fixture->setMontant('My Title');
        $fixture->setTax('My Title');
        $fixture->setReId('My Title');
        $fixture->setUserId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'investissement[dateAchat]' => 'Something New',
            'investissement[prixAchat]' => 'Something New',
            'investissement[ROI]' => 'Something New',
            'investissement[montant]' => 'Something New',
            'investissement[tax]' => 'Something New',
            'investissement[reId]' => 'Something New',
            'investissement[userId]' => 'Something New',
        ]);

        self::assertResponseRedirects('/investissement/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getDateAchat());
        self::assertSame('Something New', $fixture[0]->getPrixAchat());
        self::assertSame('Something New', $fixture[0]->getROI());
        self::assertSame('Something New', $fixture[0]->getMontant());
        self::assertSame('Something New', $fixture[0]->getTax());
        self::assertSame('Something New', $fixture[0]->getReId());
        self::assertSame('Something New', $fixture[0]->getUserId());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Investissement();
        $fixture->setDateAchat('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setROI('My Title');
        $fixture->setMontant('My Title');
        $fixture->setTax('My Title');
        $fixture->setReId('My Title');
        $fixture->setUserId('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/investissement/');
    }
}
