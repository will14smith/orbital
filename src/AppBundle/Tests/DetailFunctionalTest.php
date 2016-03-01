<?php

namespace AppBundle\Tests;

class DetailFunctionalTest extends FunctionalWebTestCase
{
    public function setUp() {
        parent::setUp();
    }

    /**
     * @dataProvider publicUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->getClient();

        $this->login();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function publicUrls()
    {
        return [
            ['/round/1'],
            ['/club/1'],
            ['/person/1'],
            ['/badge/1'],
            ['/record/1'],
            ['/league/1'],
            ['/score/1'],
        ];
    }
}
