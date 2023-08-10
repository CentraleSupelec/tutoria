<?php

namespace App\Tests\Controllers\Tutor;

use App\Tests\Controllers\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;

class TutorControllerTest extends BaseWebTestCase
{
    public function testTutorIndex(): void
    {
        $tutor = StudentFixturesProvider::getTutor($this->entityManager);

        $this->client->loginUser($tutor);

        // Go to tutor home page
        $crawler = $this->client->request('GET', '/tutor/');
        $this->assertResponseIsSuccessful();

        $myTutoringCrawler = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/h4');
        $this->assertStringContainsString('Mes tutorats', $myTutoringCrawler->getNode(0)->textContent);
    }
}
