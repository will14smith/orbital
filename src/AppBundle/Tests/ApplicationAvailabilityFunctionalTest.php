<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider publicUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function publicUrls()
    {
        return [
            ['/'],
            ['/rounds'],
            ['/clubs'],
            ['/people'],
            ['/records'],
            ['/leagues'],
            ['/competitions'],
            ['/scores'],
            ['/badges'],
            ['/login'],
        ];
    }
}
