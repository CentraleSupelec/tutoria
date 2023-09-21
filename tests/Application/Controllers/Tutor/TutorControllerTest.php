<?php

namespace App\Tests\Application\Controllers\Tutor;

use App\Entity\Tutoring;
use App\Entity\TutoringSession;
use App\Tests\Application\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;
use App\Tests\Fixtures\TutoringFixturesProvider;
use DateInterval;
use DateTime;
use DateTimeInterface;

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

    public function testUpdateTutoring(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $tutoringForm = [
            'tutoring' => [
                'defaultStartTime' => [
                    'hour' => 13,
                    'minute' => 30,
                ],
                'defaultEndTime' => [
                    'hour' => 16,
                    'minute' => 15,
                ],
                'defaultWeekDays' => ['monday', 'thursday'],
                'defaultBuilding' => $tutoring->getDefaultBuilding()->getId(),
                'defaultRoom' => 'E201',
            ],
        ];

        $this->client->xmlHttpRequest('POST', sprintf('/tutor/tutoring/%s/update', $tutoring->getId()), $tutoringForm);
        $this->assertResponseIsSuccessful();

        /** @var Tutoring $tutoring */
        $tutoring = $this->entityManager->getRepository(Tutoring::class)->findOneBy(['id' => $tutoring->getId()]);

        $this->assertEquals($tutoring->getDefaultWeekDays(), ['monday', 'thursday']);
        $this->assertEquals($tutoring->getDefaultStartTime()->format('H'), '13');
        $this->assertEquals($tutoring->getDefaultStartTime()->format('i'), '30');
        $this->assertEquals($tutoring->getDefaultEndTime()->format('H'), '16');
        $this->assertEquals($tutoring->getDefaultEndTime()->format('i'), '15');
        $this->assertEquals($tutoring->getDefaultRoom(), 'E201');

        $tutoringForm['tutoring']['defaultWeekDays'] = [];

        $this->client->xmlHttpRequest('POST', sprintf('/tutor/tutoring/%s/update', $tutoring->getId()), $tutoringForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.defaultWeekDays', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals('Veuillez sélectionner au moins un jour de la semaine', $responseData['errors'][0]['message']);
    }

    public function testBatchTutoringSessionCreationWithoutSavingDefaultValues(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $batchTutoringSessionCreationForm = [
            'batch_tutoring_session_creation' => [
                'tutoring' => $tutoring->getId(),
                'weekDays' => ['monday', 'wednesday'],
                'startDate' => [
                    'year' => 2023,
                    'month' => 9,
                    'day' => 15,
                ],
                'endDate' => [
                    'year' => 2023,
                    'month' => 9,
                    'day' => 28,
                ],
                'startTime' => [
                    'hour' => 12,
                    'minute' => 30,
                ],
                'endTime' => [
                    'hour' => 14,
                    'minute' => 30,
                ],
                'building' => $tutoring->getDefaultBuilding()->getId(),
                'room' => 'E1.01',
                'saveDefaultValues' => 'false',
            ],
        ];

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseIsSuccessful();

        /** @var TutoringSession[] $tutoringSessions */
        $tutoringSessions = $this->entityManager->getRepository(TutoringSession::class)->findBy(['tutoring' => $tutoring]);
        $this->assertEquals(count($tutoringSessions), 4);
        $this->assertNotEquals($tutoring->getDefaultWeekDays(), ['monday', 'wednesday']);
        $this->assertNotEquals($tutoring->getDefaultRoom(), 'E1.01');

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['weekDays'] = [];

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.weekDays', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals('Veuillez sélectionner au moins un jour de la semaine', $responseData['errors'][0]['message']);

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['weekDays'] = ['monday', 'wednesday'];
        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['endTime']['hour'] = 11;

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.endTime', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals("L'horaire de début est après l'horaire de fin", $responseData['errors'][0]['message']);

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['endTime']['hour'] = 14;

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseIsSuccessful();

        $batchTutoringSessionCreationForm['batch_tutoring_session_creation']['endDate'] = [
            'year' => 2023,
            'month' => 9,
            'day' => 14,
        ];

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.endDate', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals('La date de début est après la date de fin', $responseData['errors'][0]['message']);
    }

    public function testBatchTutoringSessionCreationAndSavingDefaultValues(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $batchTutoringSessionCreationForm = [
            'batch_tutoring_session_creation' => [
                'tutoring' => $tutoring->getId(),
                'weekDays' => ['monday', 'wednesday'],
                'startDate' => [
                    'year' => 2023,
                    'month' => 9,
                    'day' => 15,
                ],
                'endDate' => [
                    'year' => 2023,
                    'month' => 9,
                    'day' => 28,
                ],
                'startTime' => [
                    'hour' => 12,
                    'minute' => 30,
                ],
                'endTime' => [
                    'hour' => 14,
                    'minute' => 30,
                ],
                'building' => $tutoring->getDefaultBuilding()->getId(),
                'room' => 'E1.01',
                'saveDefaultValues' => 'true',
            ],
        ];

        $this->client->xmlHttpRequest('POST', '/tutor/batch-create-sessions', $batchTutoringSessionCreationForm);
        $this->assertResponseIsSuccessful();

        /** @var TutoringSession[] $tutoringSessions */
        $tutoringSessions = $this->entityManager->getRepository(TutoringSession::class)->findBy(['tutoring' => $tutoring]);
        $this->assertEquals(count($tutoringSessions), 4);
        $this->assertEquals($tutoring->getDefaultWeekDays(), ['monday', 'wednesday']);
        $this->assertEquals($tutoring->getDefaultRoom(), 'E1.01');
    }

    public function testValidSingleTutoringSessionCreationIsRemote(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $newTutoringSessionForm = [
            'tutoring_session' => [
                'tutoring' => $tutoring->getId(),
                'startDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 15,
                    ],
                    'time' => [
                        'hour' => 14,
                        'minute' => 0,
                    ],
                ],
                'endDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 15,
                    ],
                    'time' => [
                        'hour' => 15,
                        'minute' => 0,
                    ],
                ],
                'isRemote' => 'true',
                'onlineMeetingUri' => 'https://wwww.google.com',
            ],
        ];

        $this->client->xmlHttpRequest('POST', 'tutor/tutoring-session/new', $newTutoringSessionForm);
        $this->assertResponseIsSuccessful();

        /** @var TutoringSession[] $tutoringSessions */
        $tutoringSessions = $this->entityManager->getRepository(TutoringSession::class)->findBy(['tutoring' => $tutoring]);
        $this->assertEquals(count($tutoringSessions), 1);
        $this->assertTrue($tutoringSessions[0]->getIsRemote());
    }

    public function testValidSingleTutoringSessionCreationIsNotRemote(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $newTutoringSessionForm = [
            'tutoring_session' => [
                'tutoring' => $tutoring->getId(),
                'startDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 14,
                        'minute' => 45,
                    ],
                ],
                'endDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 15,
                        'minute' => 30,
                    ],
                ],
                'building' => $tutoring->getDefaultBuilding()->getId(),
                'room' => 'E1.01',
                'isRemote' => 'false',
            ],
        ];

        $this->client->xmlHttpRequest('POST', 'tutor/tutoring-session/new', $newTutoringSessionForm);
        $this->assertResponseIsSuccessful();

        /** @var TutoringSession[] $tutoringSessions */
        $tutoringSessions = $this->entityManager->getRepository(TutoringSession::class)->findBy(['tutoring' => $tutoring]);
        $this->assertEquals(count($tutoringSessions), 1);
        $this->assertFalse($tutoringSessions[0]->getIsRemote());
    }

    public function testInvalidSingleTutoringSessionCreationStartDateTimeAfterEndDateTime(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $newTutoringSessionForm = [
            'tutoring_session' => [
                'tutoring' => $tutoring->getId(),
                'startDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 15,
                        'minute' => 0,
                    ],
                ],
                'endDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 14,
                        'minute' => 30,
                    ],
                ],
                'building' => $tutoring->getDefaultBuilding()->getId(),
                'room' => 'E1.01',
                'isRemote' => 'false',
            ],
        ];

        $this->client->xmlHttpRequest('POST', 'tutor/tutoring-session/new', $newTutoringSessionForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.endDateTime', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals("L'horaire de début est après l'horaire de fin", $responseData['errors'][0]['message']);
    }

    public function testInvalidSingleTutoringSessionCreationStartDateDifferentThanEndDate(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $newTutoringSessionForm = [
            'tutoring_session' => [
                'tutoring' => $tutoring->getId(),
                'startDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 15,
                        'minute' => 0,
                    ],
                ],
                'endDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 21,
                    ],
                    'time' => [
                        'hour' => 14,
                        'minute' => 30,
                    ],
                ],
                'building' => $tutoring->getDefaultBuilding()->getId(),
                'room' => 'E1.01',
                'isRemote' => 'false',
            ],
        ];

        $this->client->xmlHttpRequest('POST', 'tutor/tutoring-session/new', $newTutoringSessionForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.endDateTime', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals('La date de début est différente de la date de fin', $responseData['errors'][0]['message']);
    }

    public function testInvalidSingleTutoringSessionCreationNoOnlineMeetingURL(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $newTutoringSessionForm = [
            'tutoring_session' => [
                'tutoring' => $tutoring->getId(),
                'startDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 15,
                        'minute' => 0,
                    ],
                ],
                'endDateTime' => [
                    'date' => [
                        'year' => 2023,
                        'month' => 9,
                        'day' => 20,
                    ],
                    'time' => [
                        'hour' => 15,
                        'minute' => 30,
                    ],
                ],
                'isRemote' => 'true',
            ],
        ];

        $this->client->xmlHttpRequest('POST', 'tutor/tutoring-session/new', $newTutoringSessionForm);
        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('data.onlineMeetingUri', $responseData['errors'][0]['propertyPath']);
        $this->assertEquals("Pas de lien de visio saisi alors que l'option 'Distanciel' est cochée", $responseData['errors'][0]['message']);
    }

    public function testUpdateTutoringSession(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);
        $tutoringSession = TutoringFixturesProvider::getTutoringSession($tutoring, $this->entityManager);

        $this->client->loginUser($tutoringSession->getTutors()->get(0));

        $startDateTime = (new DateTime('2023-10-16 12:30'));
        $endDateTime = $startDateTime->add(new DateInterval('PT2H'));
        $onlineMeetingUri = 'https://wwww.google.com';

        $tutoringSessionForm = [
            'tutoring_session' => [
                'tutoring' => $tutoringSession->getTutoring()->getId(),
                'startDateTime' => [
                    'date' => [
                        'year' => $startDateTime->format('Y'),
                        'month' => $startDateTime->format('n'),
                        'day' => $startDateTime->format('j'),
                    ],
                    'time' => [
                        'hour' => $startDateTime->format('G'),
                        'minute' => $startDateTime->format('i'),
                    ],
                ],
                'endDateTime' => [
                    'date' => [
                        'year' => $endDateTime->format('Y'),
                        'month' => $endDateTime->format('n'),
                        'day' => $endDateTime->format('j'),
                    ],
                    'time' => [
                        'hour' => $endDateTime->format('G'),
                        'minute' => $endDateTime->format('i'),
                    ],
                ],
                'isRemote' => 'true',
                'onlineMeetingUri' => $onlineMeetingUri,
            ],
        ];

        $this->client->xmlHttpRequest('POST', sprintf('tutor/tutoring-session/%s/update', $tutoringSession->getId()), $tutoringSessionForm);
        $this->assertResponseIsSuccessful();

        /** @var TutoringSession $tutoringSession */
        $tutoringSession = $this->entityManager->getRepository(TutoringSession::class)->findOneBy(['id' => $tutoringSession->getId()]);
        $this->assertTrue($tutoringSession->getIsRemote());
        $this->assertEquals($tutoringSession->getOnlineMeetingUri(), $onlineMeetingUri);

        $this->assertEqualsDateTime($tutoringSession->getStartDateTime(), $startDateTime);
        $this->assertEqualsDateTime($tutoringSession->getEndDateTime(), $endDateTime);
    }

    public function testDeleteTutoringSession(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);
        $tutoringSession = TutoringFixturesProvider::getTutoringSession($tutoring, $this->entityManager);

        $this->client->loginUser($tutoring->getTutors()[0]);

        $this->client->request('GET', sprintf('/tutor/tutoring-session/%s/delete', $tutoringSession->getId()));
        $this->assertResponseIsSuccessful();

        $tutoringSession = $this->entityManager->getRepository(TutoringSession::class)->findOneBy(['id' => $tutoringSession->getId()]);

        $this->assertNull($tutoringSession);
    }

    private function assertEqualsDateTime(DateTimeInterface $firstDate, DateTimeInterface $secondDate): void
    {
        $formats = ['Y', 'n', 'j', 'H', 'i'];
        foreach ($formats as $format) {
            $this->assertEquals($firstDate->format($format), $secondDate->format($format));
        }
    }
}
