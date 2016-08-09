<?php
namespace DAG\Appetize\Deploy\API;

use GuzzleHttp\Client;

/**
 * Class Api
 */
final class Api
{
    /** @var string */
    private $token;

    /** @var Client */
    private $client;

    public function __construct($token)
    {
        $this->token = $token;
        $this->client = new Client();
    }

    /**
     * @param string $appFilePath
     * @param string $platform
     * @param string $publicKey
     * @param false  $protectedByAccount
     *
     * @return UploadResponse
     *
     * @throws \Exception
     */
    public function upload($appFilePath, $platform, $publicKey = null, $protectedByAccount = false)
    {
        if ($publicKey !== null) {
            $url = sprintf('https://%s@api.appetize.io/v1/apps/%s', $this->token, $publicKey);
        } else {
            $url = sprintf('https://%s@api.appetize.io/v1/apps', $this->token);
        }

        $response = $this->client->request(
            'POST',
            $url,
            [
                'multipart' => [
                    [
                        'name' => 'platform',
                        'contents' => $platform,
                    ],
                    [
                        'name' => 'protectedByAccount',
                        'contents' => $protectedByAccount ? '1' : '0',
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($appFilePath, 'r'),
                    ],
                ],
            ]
        );

        if ($response->getStatusCode() != 200) {
            throw new \Exception(sprintf('API returned HTTP response %d', $response->getStatusCode()));
        }

        $responseContent = $response->getBody()->getContents();
        $responseData = json_decode($responseContent, true);

        if (!isset($responseData['appURL'])) {
            throw new \Exception('Missing app URL in response');
        }

        if (!isset($responseData['publicKey'])) {
            throw new \Exception('Missing public key in response');
        }

        $apiResponse = new UploadResponse($responseData['publicKey'], $responseData['appURL']);

        // Set environment variable
        $out = $returnValue = null;
        exec('envman add --key APPETIZE_APP_URL --value "'.$responseData['appURL'].'"', $out, $returnValue);

        if ($returnValue != 0) {
            throw new \Exception('Can not set the environement variable');
        }

        return $apiResponse;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function fetchAll()
    {
        $baseUrl = sprintf('https://%s@api.appetize.io/v1/apps', $this->token);

        $builds = [];

        $nextKey = null;

        while (true) {
            if ($nextKey) {
                $url = $baseUrl.'?'.http_build_query(['nextKey' => $nextKey]);
            } else {
                $url = $baseUrl;
            }

            $response = $this->client->request('GET', $url);

            $responseContent = $response->getBody()->getContents();

            if ($response->getStatusCode() != 200) {
                throw new \Exception(sprintf('API returned HTTP response %d', $response->getStatusCode()));
            }

            $responseData = json_decode($responseContent, true);

            $builds = array_merge($builds, $responseData['data']);

            if (!$responseData['hasMore']) {
                break;
            }

            $nextKey = $responseData['nextKey'];
        }

        return $builds;
    }

    public function protectBuild($publicKey)
    {
        $url = sprintf('https://%s@api.appetize.io/v1/apps/%s', $this->token, $publicKey);

        $response = $this->client->request(
            'POST',
            $url,
            [
                'json' => [
                    'protectedByAccount' => true,
                ],
            ]
        );

        if ($response->getStatusCode() != 200) {
            throw new \Exception(sprintf('API returned HTTP response %d', $response->getStatusCode()));
        }
    }
}
