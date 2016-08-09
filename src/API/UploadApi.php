<?php
namespace DAG\Appetize\Deploy\API;

use GuzzleHttp\Client;

/**
 * Class UploadApi
 */
final class UploadApi
{
    /**
     * @param string $appFilePath
     * @param string $token
     * @param string $platform
     * @param string $publicKey
     * @param false  $protectedByAccount
     *
     * @throws \Exception
     */
    public function upload($appFilePath, $token, $platform, $publicKey = null, $protectedByAccount = false)
    {
        $client = new Client();

        if ($publicKey !== null) {
            $url = sprintf('https://%s@api.appetize.io/v1/apps/%s', $token, $publicKey);
        } else {
            $url = sprintf('https://%s@api.appetize.io/v1/apps', $token);
        }

        $response = $client->request(
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

        $apiResponse = new Response($responseData['publicKey'], $responseData['appURL']);

        // Set environment variable
        $out = $returnValue = null;
        exec('envman add --key APPETIZE_APP_URL --value "'.$responseData['appURL'].'"', $out, $returnValue);

        if ($returnValue != 0) {
            throw new \Exception('Can not set the environement variable');
        }

        return $apiResponse;
    }
}
