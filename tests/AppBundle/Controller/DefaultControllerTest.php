<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Create Post', $crawler->filter('#container h1')->text());
    }

    public function testCreatePost() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/create');
        $form = $crawler->selectButton('submit')->form();

        // set some values
         $form['title'] = 'Test title';
         $form['email'] = 'Test email';
         $form['description'] = 'Test description';
         $crawler = $client->submit($form);
         $this->assertContains('Submitted post!');
    }
}
