<?php
namespace DAG\Appetize\Deploy\API;

/**
 * Class Response
 */
final class Response
{
    /** @var string */
    private $publicKey;

    /** @var string */
    private $appURL;

    /**
     * Response constructor.
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
