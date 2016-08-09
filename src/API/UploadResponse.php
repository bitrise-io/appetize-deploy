<?php
namespace DAG\Appetize\Deploy\API;

/**
 * Class UploadResponse
 */
final class UploadResponse
{
    /** @var string */
    private $publicKey;

    /** @var string */
    private $appURL;

    /**
     * UploadResponse constructor.
     *
     * @param string $publicKey
     * @param string $appURL
     */
    public function __construct($publicKey, $appURL)
    {
        $this->publicKey = $publicKey;
        $this->appURL = $appURL;
    }

    /**
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @return string
     */
    public function getAppURL()
    {
        return $this->appURL;
    }
}
