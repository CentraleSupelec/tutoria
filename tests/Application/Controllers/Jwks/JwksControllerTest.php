<?php

namespace App\Tests\Application\Controllers\Jwks;

use App\Model\Lti\Key;
use App\Model\Lti\KeyChain;
use App\Tests\Application\Utils\BaseWebTestCase;
use App\Utils\LtiToolUtils;

class JwksControllerTest extends BaseWebTestCase
{
    public function testGetTutorIaKeySetJson(): void
    {
        $this->client->xmlHttpRequest('GET', sprintf('/jwks/%s.json', LtiToolUtils::TUTOR_IA_KEY_SET_NAME));
        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals($responseData['keys'][0]['kid'], KeyChain::TUTOR_IA_KEY_CHAIN_ID);
        $this->assertEquals($responseData['keys'][0]['alg'], Key::ALG_RS256);
    }
}
