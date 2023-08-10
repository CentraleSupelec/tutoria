<?php

namespace App\Tests\Application\Controllers\Tutor;

use App\Entity\TutoringSession;
use App\Tests\Application\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;
use App\Tests\Fixtures\TutoringFixturesProvider;
use DateInterval;
use DateTime;

class TutorControllerTest extends BaseWebTestCase
{
    public function testTutorIndex(): void
    {
        $tutor = StudentFixturesProvider::getTutor($this->entityManager);
        $this->client->loginUser($tutor);

        // Go to tutor home page
        $crawler = $this->client->request('GET', '/tutor/');
        $this->assertResponseIsSuccessful();

        $myTutoringCrawler = $crawler->filterXPath('//*[@id="main"]/div/div/div[1]/h4');
        $this->assertStringContainsString('Mes tutorats', $myTutoringCrawler->getNode(0)->textContent);
    }

    public function testBatchTutoringSessionCreation(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $today = (new DateTime());
        $afterTwoWeeks = (new DateTime())->add(new DateInterval('P13D'));

        $batchTutoringSessionCreationForm = [
            'batch_tutoring_session_creation' => [
                'tutoring' => $tutoring->getId(),
                'mondaySelected' => 'true',
                'tuesdaySelected' => 'false',
                'wednesdaySelected' => 'true',
                'thursdaySelected' => 'false',
                'fridaySelected' => 'false',
                'startDate' => [
                    'year' => $today->format('Y'),
                    'month' => $today->format('n'),
                    'day' => $today->format('j'),
                ],
                'endDate' => [
                    'year' => $afterTwoWeeks->format('Y'),
                    'month' => $afterTwoWeeks->format('n'),
                    'day' => $afterTwoWeeks->format('j'),
                ],
                'startTime' => [
                    'hour' => 12,
                    'minute' => 30,
                ],
                'endTime' => [
                    'hour' => 14,
                    'minute' => 30,
                ],
                'building' => $tutoring->getBuilding()->getId(),
                'room' => 'E1.01',
            ],
        ];

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseIsSuccessful();

        /** @var TutoringSession[] $tutoringSessions */
        $tutoringSessions = $this->entityManager->getRepository(TutoringSession::class)->findBy(['tutoring' => $tutoring]);
        $this->assertEquals(count($tutoringSessions), 4);

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['endTime']['hour'] = 11;

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseStatusCodeSame(400);

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['endTime']['hour'] = 14;

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseIsSuccessful();

        $yesterday = (new DateTime())->sub(new DateInterval('P1D'));

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['endDate'] = [
            'year' => $yesterday->format('Y'),
            'month' => $yesterday->format('n'),
            'day' => $yesterday->format('j'),
        ];

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseStatusCodeSame(400);
    }
}
