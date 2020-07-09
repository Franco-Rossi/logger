<?php

namespace Tests\Unit\HealthCheck;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class GetHealthCheckTest extends TestCase
{
    protected $baseUri = 'api/logger/';

    protected function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([]);
        $mock->append(
            new Response(200, [], json_encode([
                'status' => true,
                'message' => '',
                'data' => [
                    'access' => true
                ]
            ]))
        );

        $handler = HandlerStack::create($mock);
        $http = new Client(['handler' => $handler]);

        $this->app->instance(Client::class, $http);
    }

    /** @test */
    public function getPhpinfoInformation()
    {
        $response = $this->getJson("{$this->baseUri}health-check/phpinfo");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => ['phpinfo']
            ]);
    }

    /** @test */
    public function getHealthOkMessage()
    {
        $response = $this->getJson("{$this->baseUri}health-check/health");
        $response->assertSuccessful()
            ->assertSee('ok');
    }

    /** @test */
    public function getEnvironmentInfo()
    {
        $response = $this->getJson("{$this->baseUri}health-check/environmentInfo");

        $response->assertSuccessful()
            ->assertJsonStructure([
                'data' => [
                    'app' => [],
                ]
            ])
            ->assertJsonMissing([
                'health-check' => [
                    'exlude_from_environment_check' => []
                ]
            ]);
    }
}
