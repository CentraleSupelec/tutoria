<?php

namespace App\Tests\Application\Controllers\Tutee;

use App\Tests\Application\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;

class HomeControllerTest extends BaseWebTestCase
{
    public function testTutorCasRedirection(): void
    {
        $crawler = $this->client->request('GET', '/');

        $crawler = $crawler->filterXPath('//*[@id="main"]/div[2]/div/div[1]/div/div[2]/a');
        $this->assertStringContainsString('Accès tuteur', $crawler->getNode(0)->textContent);

        $crawler = $this->client->click($crawler->link());
        $casUrl = $_ENV['CAS_BASE_URL'].'/login?service=http%3A%2F%2Flocalhost%2Ftutor%2F';

        $this->assertResponseRedirects($casUrl);
    }

    public function testTuteeCasRedirection(): void
    {
        $crawler = $this->client->request('GET', '/');

        $crawler = $crawler->filterXPath('//*[@id="main"]/div[2]/div/div[2]/div/div[2]/a');
        $this->assertStringContainsString('Accès tutoré', $crawler->getNode(0)->textContent);

        $crawler = $this->client->click($crawler->link());
        $casUrl = $_ENV['CAS_BASE_URL'].'/login?service=http%3A%2F%2Flocalhost%2Ftutee%2F';

        $this->assertResponseRedirects($casUrl);
    }

    public function testTutorRedirectionToTutorHomePage(): void
    {
        $tutor = StudentFixturesProvider::getTutor($this->entityManager);
        $this->client->loginUser($tutor);

        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/tutor/');
    }

    public function testTuteeRedirectionToTuteeHomePage(): void
    {
        $tutee = StudentFixturesProvider::getTutee($this->entityManager);
        $this->client->loginUser($tutee);

        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/tutee/');
    }
}
