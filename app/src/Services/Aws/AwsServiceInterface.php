<?php

namespace EDM\Services\Aws;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

interface AwsServiceInterface
{
    public function getCredentials(): Credentials;

    public function getRegion(): ?string;

    public function getEndpoint(): ?string;

    public function createS3Client(): S3Client;
}
