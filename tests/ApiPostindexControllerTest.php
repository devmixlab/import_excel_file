<?php
namespace Tests;

use App\Database\FakerPostIndex;
use Slim\Http\Environment;
use Slim\Http\Request;
use App\Models\Postindex;

//php vendor/bin/phpunit ./tests/ApiPostindexControllerTest.php

class ApiPostindexControllerTest extends TestCase
{
    static protected $app;

    public function __construct(...$args)
    {
        parent::__construct(...$args);
        if(empty(self::$app))
            self::$app = $this->getWebAppInstance();
    }

    public function testGet()
    {
        $request = $this->createRequest('GET', '/api/postindex');
        $response = self::$app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $response_body = (string)$response->getBody();
        $response_body = json_decode($response_body, true);

        $this->assertIsArray(
            $response_body,
            "assert variable is array or not"
        );
    }

    public function testPost()
    {
        $request = $this->createRequest(
            'POST',
            '/api/postindex',
            [
                'HTTP_ACCEPT' => 'application/json',
                "Content-Type" => 'application/json'
            ]
        );

        $faker = new FakerPostIndex();
        $data = $faker(count: 2, except: ['from_api']);

        $request = $request->withParsedBody($data);

        $response = self::$app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testPostSingle()
    {
        $request = $this->createRequest(
            'POST',
            '/api/postindex',
            [
                'HTTP_ACCEPT' => 'application/json',
                "Content-Type" => 'application/json'
            ]
        );

        $faker = new FakerPostIndex();
        $data = $faker(count: 1, except: ['from_api']);

        $request = $request->withParsedBody($data[0]);

        $response = self::$app->handle($request);

        $this->assertEquals(201, $response->getStatusCode());

    }

    public function testDelete()
    {
        $builder = Postindex::initSelect()->format(function ($sql) {
            return $sql . " ORDER BY `post_code_of_post_office` DESC LIMIT 1";
        });

        $result = $builder->fetch();

        $request = $this->createRequest(
            'DELETE',
            '/api/postindex/' . $result["post_code_of_post_office"],
            [
                'HTTP_ACCEPT' => 'application/json',
                "Content-Type" => 'application/json'
            ]
        );

        $response = self::$app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}