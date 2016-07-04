<?php

namespace AppBundle\Tests;

class CriticalPageFunctionalTest extends FunctionalWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @dataProvider publicUrls
     *
     * @param string $url
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
            ['/score/create'],
            ['/badge/claim'],
            ['/approvals'],
        ];
    }
}
