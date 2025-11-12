<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Support\Database\DatabaseIsolationByClassTrait;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class KnightControllerTest extends WebTestCase
{
    use DatabaseIsolationByClassTrait;

    public function testPostKnightBipolelm(): void
    {
        $client = static::createClient();

        $content = json_encode([
            'name' => 'Bipolelm',
            'strength' => 10,
            'weapon_power' => 20,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $content);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testPostKnightElrynd(): void
    {
        $client = static::createClient();

        $content = json_encode([
            'name' => 'Elrynd',
            'strength' => 10,
            'weapon_power' => 50,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $content);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testPostKnightBadData(): void
    {
        $client = static::createClient();

        $content = json_encode([
            'name' => 'FAILED',
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $content);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('message', $result);
    }

    public function testPostKnightBadType(): void
    {
        $client = static::createClient();

        $content = json_encode([
            'name' => 'Wrong type',
            'strength' => 10,
            'weapon_power' => 20,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'text/plain',
        ], $content);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @requires testPostKnightBipolelm
     * @requires testPostKnightElrynd
     */
    public function testGetKnights(): void
    {
        $client = static::createClient();

        $client->request('GET', '/knight');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(2, $result);

        foreach ($result as $item) {
            $this->assertArrayHasKey('id', $item);
            $this->assertArrayHasKey('name', $item);
            $this->assertArrayHasKey('strength', $item);
            $this->assertArrayHasKey('weapon_power', $item);
        }

        $this->assertFalse($result[0]['id'] === $result[1]['id'], 'Knights should not have same ID.');
    }

    public function testGetKnightNotFound(): void
    {
        $client = static::createClient();

        $client->request('GET', '/knight/123456789');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('message', $result);

        $this->assertEquals('Knight #123456789 not found.', $result['message']);
    }

    public function testPostKnightInvalidJson(): void
    {
        $client = static::createClient();
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], 'not-json');

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostKnightMissingName(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'strength' => 1,
            'weapon_power' => 2,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostKnightMissingStrength(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'name' => 'Missing strength',
            'weapon_power' => 2,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostKnightMissingWeaponPower(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'name' => 'Missing weapon power',
            'strength' => 2,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostKnightNonIntegerStrength(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'name' => 'Bad strength type',
            'strength' => 'ten',
            'weapon_power' => 2,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostKnightNonIntegerWeaponPower(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'name' => 'Bad weapon power type',
            'strength' => 2,
            'weapon_power' => 'twenty',
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPostKnightReturnsLocationAndFollow(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'name' => 'FollowMe',
            'strength' => 1,
            'weapon_power' => 2,
        ]);
        $client->request('POST', '/knight', [], [], [
            'HTTP_CONTENT_TYPE' => 'application/json',
        ], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHasHeader('Content-Type');
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseHasHeader('Location');
        $location = $client->getResponse()->headers->get('Location');
        $this->assertNotEmpty($location);
        $this->assertMatchesRegularExpression('#^/knight/[0-9a-f\\-]{36}$#', (string) $location);

        // Follow
        $client->request('GET', (string) $location);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $result);
        $this->assertSame(36, \strlen($result['id']));
        $this->assertSame('FollowMe', $result['name']);
        $this->assertSame(1, $result['strength']);
        $this->assertSame(2, $result['weapon_power']);
    }

    public function testGetKnightsOrderAndLimit(): void
    {
        $client = static::createClient();
        $conn = static::getContainer()->get(Connection::class);
        $base = new \DateTimeImmutable('2024-01-01T00:00:00+00:00');
        for ($i = 1; $i <= 55; $i++) {
            $id = Uuid::v4()->toRfc4122();
            $conn->insert('knight', [
                'external_id' => $id,
                'name' => \sprintf('K%02d', $i),
                'strength' => $i,
                'weapon_power' => $i,
                'created_at' => $base->modify(\sprintf('+%d seconds', $i))->format('Y-m-d H:i:sP'),
            ]);
        }

        $client->request('GET', '/knight');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $result = json_decode($client->getResponse()->getContent(), true);
        $this->assertCount(50, $result);

        $expected = $conn->fetchFirstColumn(
            'SELECT external_id FROM knight ORDER BY created_at ASC LIMIT 50'
        );
        $actual = array_column($result, 'id');
        $this->assertSame($expected, $actual);
    }

    public function testPostKnightValidJsonWithoutContentType(): void
    {
        $client = static::createClient();
        $payload = json_encode([
            'name' => 'NoHeader',
            'strength' => 3,
            'weapon_power' => 4,
        ]);
        // No HTTP_CONTENT_TYPE header on purpose: expect 400
        $client->request('POST', '/knight', [], [], [], $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
