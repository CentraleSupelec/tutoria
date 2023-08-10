<?php

namespace App\Tests\Controllers\Tutored;

use App\Tests\Controllers\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;

class TutoredControllerTest extends BaseWebTestCase
{
    public function testStudentIndex(): void
    {
        $tutored = StudentFixturesProvider::getTutored($this->entityManager);

        $this->client->loginUser($tutored);

        // Go to student home page
        $crawler = $this->client->request('GET', '/tutored/');
        $this->assertResponseIsSuccessful();

        $myTutoringCrawler = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/h4');
        $this->assertStringContainsString('Prochaines séances de tutorat', $myTutoringCrawler->getNode(0)->textContent);
    }
}
