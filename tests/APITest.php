<?php

namespace App\Tests;

use App\DataFixtures\StartUsers;
use Symfony\Component\HttpFoundation\Response;

class APITest extends AbstractTest
{
    private $serializer;


    protected function getFixtures(): array
    {
        return [StartUsers::class];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->serializer = self::$kernel->getContainer()->get('jms_serializer');
    }

    public function testAuth(): void
    {
        $user = $this->serializer->serialize([
            'username' => 'admin@example.test',
            'password' => 'asdewq123'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/auth',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseOk();
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['token']);
    }

    public function testAuthNonExistentUser(): void
    {
        $user = $this->serializer->serialize([
            'username' => 'superadmin@example.test',
            'password' => 'pass1234'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/auth',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_UNAUTHORIZED);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid credentials.', $jsonResponse['message']);
    }

    public function testRegister(): void
    {
        $user = $this->serializer->serialize([
            'username' => 'testuser@example.test',
            'password' => 'somepass'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_CREATED);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['token']);
    }

    public function testRegisterExistentUser(): void
    {
        $user = $this->serializer->serialize([
            'username' => 'admin@example.test',
            'password' => 'somepass'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_BAD_REQUEST);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['error']);
        $this->assertEquals('???????????????????????? admin@example.test ?????? ????????????????????', $jsonResponse['error']);
    }

    public function testRegisterInvalidEmail(): void
    {
        $user = $this->serializer->serialize([
            'username' => '',
            'password' => 'somepass'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_BAD_REQUEST);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['errors']['username']);
        $this->assertContains('?????? ???????????????????????? ???? ???????????? ???????? ????????????', $jsonResponse['errors']['username']);

        $user = $this->serializer->serialize([
            'username' => 'someemailprostotak.net',
            'password' => 'somepass'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_BAD_REQUEST);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['errors']['username']);
        $this->assertContains('???????????????????????? email', $jsonResponse['errors']['username']);
    }

    public function testRegisterInvalidPassword(): void
    {
        $user = $this->serializer->serialize([
            'username' => 'wronguser@example.test',
            'password' => ''
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_BAD_REQUEST);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['errors']['password']);
        $this->assertContains('???????? ???????????? ???? ???????????? ???????? ????????????', $jsonResponse['errors']['password']);

        $user = $this->serializer->serialize([
            'username' => 'wronguser@example.test',
            'password' => 's'
        ], 'json');
        $client = self::getClient();
        $client->request(
            'POST',
            '/api/v1/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $user);
        $this->assertResponseCode(Response::HTTP_BAD_REQUEST);
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'
        ));
        $jsonResponse = json_decode($client->getResponse()->getContent(), true);
        $this->assertNotEmpty($jsonResponse['errors']['password']);
        $this->assertContains('???????????? ???????????? ???????? ?????????????? 6 ????????????????', $jsonResponse['errors']['password']);
    }

}