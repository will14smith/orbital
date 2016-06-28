<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;

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
        $command = sprintf('%s --no-interaction', $command);

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

    public function login()
    {
        $client = $this->getClient();
        $container = $client->getContainer();

        $session = $container->get('session');
        $userManager = $container->get('fos_user.user_manager');
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(['username' => 'admin']);
        $loginManager->logInUser($firewallName, $user);

        $container->get('session')->set('_security_' . $firewallName, serialize($container->get('security.token_storage')->getToken()));
        $container->get('session')->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
