<?php

declare(strict_types=1);

/**
 * This file is part of a Upply project.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class KnightControllerTest extends WebTestCase
{
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
}
