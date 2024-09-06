<?php

namespace EDM\Services\Aws;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AwsService implements AwsServiceInterface
{
    private Credentials $credentials;

    public function __construct(string $access_key, string $secret_key, private readonly ?string $region = null, private readonly ?string $endpoint = null)
    {
        $this->credentials = new Credentials($access_key, $secret_key);
    }

    public function getCredentials(): Credentials
    {
        return $this->credentials;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function createS3Client(): S3Client
    {
        return new S3Client([
            'endpoint' => $this->endpoint,
            'region' => $this->region,
            'credentials' => $this->credentials
        ]);
    }
}
