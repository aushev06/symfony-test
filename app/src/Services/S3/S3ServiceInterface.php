<?php

namespace EDM\Services\S3;

use Aws\Result;
use Aws\S3\S3Client;

interface S3ServiceInterface
{
    public function getClient(): S3Client;

    public function releaseClient(): self;

    public function getBucket(): string;

    public function setBucket(string $bucket): self;

    public function listBuckets(): array;

    public function createBucket(?string $bucket = null, bool $switch = false): Result;
    public function deleteBucket(?string $bucket = null): Result;

    public function purge(string $prefix): self;
    public function upload(string $key, $body, ?string $mime = null, ?array $metadata = null): Result;

    public function download(string $key, ?string $bucket = null): ?string;

    public function delete(string $key): Result;

    public function listObjects(?string $bucket = null): array;

    public function getPolicy(): array;

    public function updatePolicy(array $policy): Result;

    public function deletePolicy(): Result;
    public function getURL(string $key, ?string $bucket = null): string;
}
