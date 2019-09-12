<?php

use Algolia\AlgoliaSearch\SearchClient;
use App\Key;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Testing\DatabaseMigrations;
use TiMacDonald\Log\LogFake;

final class GenerationOfKeysTest extends TestCase
{
    use DatabaseMigrations;

    public function testTravisService()
    {
        Log::swap(new LogFake);

        $response = $this->post('/1/algolia/keys/new', ['REPO_SLUG' => 'algoliasearch-client-test']);

        $response->assertResponseStatus(201);
        $response->seeJsonStructure([
            'app-id',
            'api-key',
            'api-search-key',
            'comment',
            'request-id',
        ]);

        $response->seeJsonContains([
            "comment" => "The keys will expire after 3 minutes.",
        ]);

        $content = json_decode($response->response->content(), true);

        $client = SearchClient::create($content['app-id'], $content['api-key']);
        $this->assertIsArray($client->listIndices());

        $client = SearchClient::create($content['ALGOLIA_APPLICATION_ID_1'], $content['ALGOLIA_ADMIN_KEY_1']);
        $this->assertIsArray($client->listIndices());

        $client = SearchClient::create($content['ALGOLIA_APPLICATION_ID_2'], $content['ALGOLIA_ADMIN_KEY_2']);
        $this->assertIsArray($client->listIndices());

        $client = SearchClient::create($content['app-id'], $content['api-search-key']);
        $this->assertEquals($client->isAlive(), ['message' => 'server is alive']);
    }
}
