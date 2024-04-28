<?php

namespace App\Test\Controller;

use App\Entity\DigitalCoins;
use App\Repository\DigitalCoinsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DigitalCoinsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DigitalCoinsRepository $repository;
    private string $path = '/digital/coins/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(DigitalCoins::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DigitalCoin index');

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
            'digital_coin[recentValue]' => 'Testing',
            'digital_coin[dateAchat]' => 'Testing',
            'digital_coin[dateVente]' => 'Testing',
            'digital_coin[montant]' => 'Testing',
            'digital_coin[leverage]' => 'Testing',
            'digital_coin[stopLoss]' => 'Testing',
            'digital_coin[userId]' => 'Testing',
            'digital_coin[ROI]' => 'Testing',
            'digital_coin[prixAchat]' => 'Testing',
            'digital_coin[tax]' => 'Testing',
            'digital_coin[code]' => 'Testing',
        ]);

        self::assertResponseRedirects('/digital/coins/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new DigitalCoins();
        $fixture->setRecentValue('My Title');
        $fixture->setDateAchat('My Title');
        $fixture->setDateVente('My Title');
        $fixture->setMontant('My Title');
        $fixture->setLeverage('My Title');
        $fixture->setStopLoss('My Title');
        $fixture->setUserId('My Title');
        $fixture->setROI('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setTax('My Title');
        $fixture->setCode('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('DigitalCoin');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new DigitalCoins();
        $fixture->setRecentValue('My Title');
        $fixture->setDateAchat('My Title');
        $fixture->setDateVente('My Title');
        $fixture->setMontant('My Title');
        $fixture->setLeverage('My Title');
        $fixture->setStopLoss('My Title');
        $fixture->setUserId('My Title');
        $fixture->setROI('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setTax('My Title');
        $fixture->setCode('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'digital_coin[recentValue]' => 'Something New',
            'digital_coin[dateAchat]' => 'Something New',
            'digital_coin[dateVente]' => 'Something New',
            'digital_coin[montant]' => 'Something New',
            'digital_coin[leverage]' => 'Something New',
            'digital_coin[stopLoss]' => 'Something New',
            'digital_coin[userId]' => 'Something New',
            'digital_coin[ROI]' => 'Something New',
            'digital_coin[prixAchat]' => 'Something New',
            'digital_coin[tax]' => 'Something New',
            'digital_coin[code]' => 'Something New',
        ]);

        self::assertResponseRedirects('/digital/coins/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getRecentValue());
        self::assertSame('Something New', $fixture[0]->getDateAchat());
        self::assertSame('Something New', $fixture[0]->getDateVente());
        self::assertSame('Something New', $fixture[0]->getMontant());
        self::assertSame('Something New', $fixture[0]->getLeverage());
        self::assertSame('Something New', $fixture[0]->getStopLoss());
        self::assertSame('Something New', $fixture[0]->getUserId());
        self::assertSame('Something New', $fixture[0]->getROI());
        self::assertSame('Something New', $fixture[0]->getPrixAchat());
        self::assertSame('Something New', $fixture[0]->getTax());
        self::assertSame('Something New', $fixture[0]->getCode());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new DigitalCoins();
        $fixture->setRecentValue('My Title');
        $fixture->setDateAchat('My Title');
        $fixture->setDateVente('My Title');
        $fixture->setMontant('My Title');
        $fixture->setLeverage('My Title');
        $fixture->setStopLoss('My Title');
        $fixture->setUserId('My Title');
        $fixture->setROI('My Title');
        $fixture->setPrixAchat('My Title');
        $fixture->setTax('My Title');
        $fixture->setCode('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/digital/coins/');
    }
}
