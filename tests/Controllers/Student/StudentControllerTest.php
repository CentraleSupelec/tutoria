<?php

namespace App\Tests\Controllers\Student;

use App\Tests\Controllers\Utils\BaseWebTestCase;
use App\Tests\Fixtures\StudentFixturesProvider;

class StudentControllerTest extends BaseWebTestCase
{
    public function testStudentIndex(): void
    {
        $student = StudentFixturesProvider::getStudent($this->entityManager);

        $this->client->loginUser($student);

        // Go to student home page
        $crawler = $this->client->request('GET', '/student/');
        $this->assertResponseIsSuccessful();

        $myTutoringCrawler = $crawler->filterXPath('//*[@id="main"]/div/div[2]/div[1]/h4');
        $this->assertStringContainsString('Prochaines séances de tutorat', $myTutoringCrawler->getNode(0)->textContent);
    }
}
