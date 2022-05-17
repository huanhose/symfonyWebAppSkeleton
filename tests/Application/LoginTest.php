<?php

namespace App\Tests\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;

/**
 * We test login page. An user can sign in the app
 * It's a critical part of any app
 */
class LoginTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        StaticDriver::setKeepStaticConnections(false);
    }

    public static function tearDownAfterClass(): void
    {
        StaticDriver::setKeepStaticConnections(true);
    }

    public function testLoginSucessfull()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        // retrieve the Form object for the form belonging to this button
        $form = $this->getLoginForm($crawler);

        $testUser = $this->getTestUser();
        $form['email'] = $testUser->user->getEmail();
        $form['password'] = $testUser->password;
        $client->submit($form);

        $this->assertResponseRedirects('/home');
    }

     /**
      * We test an unautorized person can access the app
      *
      * @return void
      */
    public function testLoginUnsucessfull()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        // retrieve the Form object for the form belonging to this button
        $form = $this->getLoginForm($crawler);

        //Foo person that not exist
        $form['email'] = 'foo@anFooEmail.com';
        $form['password'] = '12';
        $client->submit($form);

        //Auth failed, return to login page
        $this->assertResponseRedirects('/login');
    }

     /**
      * Get the login form in the Login page
      * Used to be submited

      * @param Crawler $crawler
      * @return form
      */
    private function getLoginForm(Crawler $crawler): form
    {
        $buttonCrawlerNode = $crawler->selectButton('Sign in');
        $form = $buttonCrawlerNode->form();
        return $form;
    }

     /**
      * Get an existing user to use in test
      *
      * @return object  [
      *    'user' : User instance
      *    'password' : to use in Login
      * ]
      */
    private function getTestUser(): object
    {
        //A user we know that exist in a test database (fixtures)
        $userRepository = $this->getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findByEmail('pepe@gmail.com');

        if (null === $user) {
            throw new \Exception("'User 'pepe@gmail.com' not found in test database");
        }

        return (object) [
           'user' => $user,
           'password' => '123456'
        ];
    }
}
