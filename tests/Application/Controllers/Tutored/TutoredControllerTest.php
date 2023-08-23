<?php

namespace App\Tests\Application\Controllers\Tutored;

use App\Tests\Application\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;
use App\Tests\Fixtures\TutoringFixturesProvider;

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

    public function testTutoringSessionFilter(): void
    {
        $tutorings = TutoringFixturesProvider::getTutorings($this->entityManager);
        TutoringFixturesProvider::getTutoringSession($tutorings[1], $this->entityManager);

        $this->client->loginUser($tutorings[0]->getTutors()->get(0));

        $this->client->jsonRequest('POST', '/api/tutoring-sessions-by-tutorings', [
            'tutorings' => [$tutorings[0]->getId(), $tutorings[1]->getId()],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent()));

        $this->client->jsonRequest('POST', '/api/tutoring-sessions-by-tutorings', []);
        $this->assertResponseIsSuccessful();
        dump(json_decode($this->client->getResponse()->getContent()));
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent()));

        $this->client->jsonRequest('POST', '/api/tutoring-sessions-by-tutorings', [
            'tutorings' => [$tutorings[0]->getId()],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, json_decode($this->client->getResponse()->getContent()));

        $this->client->jsonRequest('POST', '/api/tutoring-sessions-by-tutorings', [
            'tutorings' => [$tutorings[1]->getId()],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent()));
    }
}
