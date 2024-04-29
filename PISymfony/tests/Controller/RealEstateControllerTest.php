<?php

namespace App\Test\Controller;

use App\Entity\RealEstate;
use App\Repository\RealEstateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RealEstateControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private RealEstateRepository $repository;
    private string $path = '/real/estate/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(RealEstate::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RealEstate index');

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
            'real_estate[name]' => 'Testing',
            'real_estate[emplacement]' => 'Testing',
            'real_estate[ROI]' => 'Testing',
            'real_estate[valeur]' => 'Testing',
            'real_estate[nbrchambres]' => 'Testing',
            'real_estate[superficie]' => 'Testing',
            'real_estate[nbrclick]' => 'Testing',
            'real_estate[imageData]' => 'Testing',
        ]);

        self::assertResponseRedirects('/real/estate/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new RealEstate();
        $fixture->setName('My Title');
        $fixture->setEmplacement('My Title');
        $fixture->setROI('My Title');
        $fixture->setValeur('My Title');
        $fixture->setNbrchambres('My Title');
        $fixture->setSuperficie('My Title');
        $fixture->setNbrclick('My Title');
        $fixture->setImageData('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('RealEstate');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new RealEstate();
        $fixture->setName('My Title');
        $fixture->setEmplacement('My Title');
        $fixture->setROI('My Title');
        $fixture->setValeur('My Title');
        $fixture->setNbrchambres('My Title');
        $fixture->setSuperficie('My Title');
        $fixture->setNbrclick('My Title');
        $fixture->setImageData('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'real_estate[name]' => 'Something New',
            'real_estate[emplacement]' => 'Something New',
            'real_estate[ROI]' => 'Something New',
            'real_estate[valeur]' => 'Something New',
            'real_estate[nbrchambres]' => 'Something New',
            'real_estate[superficie]' => 'Something New',
            'real_estate[nbrclick]' => 'Something New',
            'real_estate[imageData]' => 'Something New',
        ]);

        self::assertResponseRedirects('/real/estate/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getEmplacement());
        self::assertSame('Something New', $fixture[0]->getROI());
        self::assertSame('Something New', $fixture[0]->getValeur());
        self::assertSame('Something New', $fixture[0]->getNbrchambres());
        self::assertSame('Something New', $fixture[0]->getSuperficie());
        self::assertSame('Something New', $fixture[0]->getNbrclick());
        self::assertSame('Something New', $fixture[0]->getImageData());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new RealEstate();
        $fixture->setName('My Title');
        $fixture->setEmplacement('My Title');
        $fixture->setROI('My Title');
        $fixture->setValeur('My Title');
        $fixture->setNbrchambres('My Title');
        $fixture->setSuperficie('My Title');
        $fixture->setNbrclick('My Title');
        $fixture->setImageData('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/real/estate/');
    }
}
