<?php

namespace AppBundle\Tests;

class ListFunctionalTest extends FunctionalWebTestCase
{
    /**
     * @dataProvider publicUrls
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->getClient();

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
