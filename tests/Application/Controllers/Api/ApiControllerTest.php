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

        $this->client->xmlHttpRequest('GET', sprintf('/api/tutoring/%s', $tutoring->getId()));
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

        $this->client->xmlHttpRequest('GET', sprintf('/api/tutoring-session/%s', $tutoringSession->getId()));
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

        $this->client->xmlHttpRequest('GET', sprintf('/api/campuses'));
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($building->getCampus()->getId()->jsonSerialize(), $responseData[0]['id']);
    }
}
