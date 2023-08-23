<?php

namespace App\Tests\Application\Controllers\Tutee;

use App\Tests\Application\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;
use App\Tests\Fixtures\TutoringFixturesProvider;

class TuteeControllerTest extends BaseWebTestCase
{
    public function testStudentIndex(): void
    {
        $tutee = StudentFixturesProvider::getTutee($this->entityManager);

        $this->client->loginUser($tutee);

        // Go to student home page
        $crawler = $this->client->request('GET', '/tutee/');
        $this->assertResponseIsSuccessful();

        $myTutoringCrawler = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/h4');
        $this->assertStringContainsString('Prochaines séances de tutorat', $myTutoringCrawler->getNode(0)->textContent);
    }

    public function testTutoringSessionFilter(): void
    {
        $tutorings = TutoringFixturesProvider::getTutorings($this->entityManager);
        TutoringFixturesProvider::getTutoringSession($tutorings[1], $this->entityManager);

        $this->client->loginUser($tutorings[0]->getTutors()->get(0));

        $this->client->xmlHttpRequest('POST', '/api/tutoring-sessions-by-tutorings', [
            'tutoring_session_search' => [
                'tutorings' => [$tutorings[0]->getId(), $tutorings[1]->getId()],
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent()));

        $this->client->xmlHttpRequest('POST', '/api/tutoring-sessions-by-tutorings');
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent()));

        $this->client->xmlHttpRequest('POST', '/api/tutoring-sessions-by-tutorings', [
            'tutoring_session_search' => [
                'tutorings' => [$tutorings[0]->getId()],
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, json_decode($this->client->getResponse()->getContent()));

        $this->client->xmlHttpRequest('POST', '/api/tutoring-sessions-by-tutorings', [
            'tutoring_session_search' => [
                'tutorings' => [$tutorings[1]->getId()],
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, json_decode($this->client->getResponse()->getContent()));
    }
}
