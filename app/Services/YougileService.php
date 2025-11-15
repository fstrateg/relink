<?php
namespace App\Services;

use CodeIgniter\HTTP\CURLRequest;

class YougileService
{
    private string $api = 'api-v2';

    private string $apiUrl = 'https://yougile.com/';
    private string $token;
    private CURLRequest $client;

    public function __construct()
    {
        $config = config('Yougile');
        $this->token = $config->accessToken;

        $this->client = service('curlrequest', [
            'baseURI' => $this->apiUrl,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    private function post($command, $payload): ?array
    {
        $response = $this->client->post("$this->api/$command", ['json' => $payload]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($response->getBody(), true);
        }

        log_message('error', 'YouGile API error: ' . $response->getStatusCode() . ' — ' . $response->getBody());
        return null;
    }
    /**
     * Создание новой задачи в YouGile
     */
    public function createTask(string $title, string $description, string $columnId, array $others=[]): ?array
    {
        $payload = [
            'title' => $title,
            'description' => $description,
            'columnId' => $columnId,
        ];

        $payload = array_merge($payload,$others);

       return $this->post('tasks', $payload);
    }
}
