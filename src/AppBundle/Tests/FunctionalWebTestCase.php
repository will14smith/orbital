<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class FunctionalWebTestCase extends WebTestCase
{
    /** @var Client */
    private $client;
    /** @var Application */
    private $application;

    /** @var bool */
    private static $dbConfigured;

    protected function setUp()
    {
        $this->client = $this->createClient();
        $this->getApplication();
    }

    protected function runCommand($command)
    {
        $command = sprintf('%s', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected function getApplication()
    {
        if (null === $this->application) {
            $this->application = new Application(self::getClient()->getKernel());
            $this->application->setAutoExit(false);

            self::configureDb();
        }

        return $this->application;
    }

    protected function getClient()
    {
        return $this->client;
    }

    protected function configureDb()
    {
        if (self::$dbConfigured) {
            return;
        }

        self::$dbConfigured = true;

        $this->runCommand('doctrine:database:drop --force');
        $this->runCommand('doctrine:database:create');
        $this->runCommand('doctrine:schema:update --force');
        $this->runCommand('doctrine:fixtures:load');
    }

    public function logIn()
    {
        $client = $this->getClient();

        $session = $client->getContainer()->get('session');

        $firewall = 'secured_area';
        $token = new UsernamePasswordToken('admin', null, $firewall, ['ROLE_ADMIN']);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
