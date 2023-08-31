<?php

namespace App\Tests\Application\Controllers\Tutor;

use App\Tests\Application\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;
use App\Tests\Fixtures\TutoringFixturesProvider;

class ApiControllerTest extends BaseWebTestCase
{
    public function testGetTutoring(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $this->client->xmlHttpRequest('GET', sprintf('/student/api/tutoring/%s', $tutoring->getId()));
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($tutoring->getId(), $responseData['id']);
        $this->assertEquals($tutoring->getDefaultBuilding()->getId(), $responseData['defaultBuilding']['id']);
        $this->assertEquals($tutoring->getDefaultRoom(), $responseData['defaultRoom']);
        $this->assertEquals($tutoring->getDefaultWeekDays(), $responseData['defaultWeekDays']);
    }

    public function testGetTutoringSession(): void
    {
        $tutoring = TutoringFixturesProvider::getTutoring($this->entityManager);
        $tutoringSession = TutoringFixturesProvider::getTutoringSession($tutoring, $this->entityManager);

        $this->client->loginUser($tutoring->getTutors()->get(0));

        $this->client->xmlHttpRequest('GET', sprintf('/student/api/tutoring-session/%s', $tutoringSession->getId()));
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($tutoringSession->getId(), $responseData['id']);
        $this->assertEquals($tutoringSession->getBuilding()->getId(), $responseData['building']['id']);
        $this->assertEquals($tutoringSession->getRoom(), $responseData['room']);
        $this->assertEquals($tutoringSession->getIsRemote(), $responseData['isRemote']);
    }

    public function testGetCampuses(): void
    {
        $tutor = StudentFixturesProvider::getTutor($this->entityManager);
        $building = TutoringFixturesProvider::getBuilding($this->entityManager);

        $this->client->loginUser($tutor);

        $this->client->xmlHttpRequest('GET', '/student/api/campuses');
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($building->getCampus()->getId()->jsonSerialize(), $responseData[0]['id']);
    }

    public function testGetIncomingTutoringSession(): void
    {
        $tutee = StudentFixturesProvider::getTutee($this->entityManager);
        $tutoringSessions = TutoringFixturesProvider::getTutoringSessions($tutee, $this->entityManager);

        $this->client->loginUser($tutee);

        $this->client->xmlHttpRequest('GET', '/student/api/incoming-tutoring-sessions');
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(1, $responseData);
        $responseTutoringSession = $responseData[0];
        $this->assertEquals($tutoringSessions[0]->getId(), $responseTutoringSession['id']);
        $this->assertEquals($tutoringSessions[0]->getBuilding()->getId(), $responseTutoringSession['building']['id']);
        $this->assertEquals($tutoringSessions[0]->getRoom(), $responseTutoringSession['room']);
        $this->assertEquals($tutoringSessions[0]->getIsRemote(), $responseTutoringSession['isRemote']);
    }

    public function testGetPastTutoringSession(): void
    {
        $tutee = StudentFixturesProvider::getTutee($this->entityManager);
        $tutoringSessions = TutoringFixturesProvider::getTutoringSessions($tutee, $this->entityManager);

        $this->client->loginUser($tutee);

        $this->client->xmlHttpRequest('GET', '/student/api/past-tutoring-sessions');
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(2, $responseData);
        $responseTutoringSession = $responseData[0];
        $this->assertEquals($tutoringSessions[1]->getId(), $responseTutoringSession['id']);
        $this->assertEquals($tutoringSessions[1]->getBuilding()->getId(), $responseTutoringSession['building']['id']);
        $this->assertEquals($tutoringSessions[1]->getRoom(), $responseTutoringSession['room']);
        $this->assertEquals($tutoringSessions[1]->getIsRemote(), $responseTutoringSession['isRemote']);
    }
}
