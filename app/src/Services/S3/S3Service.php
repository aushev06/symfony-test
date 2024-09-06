<?php

namespace EDM\Services\S3;


use Aws\S3\BatchDelete;
use Aws\S3\S3Client;
use EDM\Services\Aws\AwsServiceInterface;
use Aws\Result;
use GuzzleHttp\Psr7\MimeType;

class S3Service implements S3ServiceInterface
{
    private S3Client $client;
    private string $bucket;

    public function __construct(string $bucket, private readonly AwsServiceInterface $aws_service)
    {
        $this->client = $aws_service->createS3Client();
        $this->bucket = $bucket;
    }

    public function getClient(): S3Client
    {
        return $this->client;
    }

    public function releaseClient(): self
    {
        $this->client = $this->aws_service->createS3Client();
        return $this;
    }

    public function getBucket(): string
    {
        return $this->bucket;
    }

    public function setBucket(string $bucket): self
    {
        $this->bucket = $bucket;
        return $this;
    }

    public function listBuckets(): array
    {
        $result = $this->client->listBuckets();
        return $result['Buckets'];
    }

    public function createBucket(?string $bucket = null, bool $switch = false): \Aws\Result
    {
        $bucket = $bucket ?? $this->bucket;
        $result = $this->client->createBucket([
            'Bucket' => $bucket
        ]);
        if ($switch) {
            $this->setBucket($bucket);
        }
        return $result;
    }

    public function deleteBucket(?string $bucket = null): Result
    {
        return $this->client->deleteBucket([
            'Bucket' => $bucket ?? $this->bucket
        ]);
    }

    public function purge(string $prefix): self
    {
        $delete = BatchDelete::fromListObjects($this->client, [
            'Bucket' => $this->bucket,
            'Prefix' => $prefix
        ]);
        $delete->delete();
        return $this;
    }

    public function upload(string $key, $body, ?string $mime = null, ?array $metadata = null): Result
    {
        $request = [
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body,
            'ACL' => 'public-read'
        ];
        if ($mime = $mime ?? MimeType::fromFilename($key)) {
            $request['ContentType'] = $mime;
        }
        if ($metadata !== null) {
            $request['Metadata'] = $metadata;
        }
        return $this->client->putObject($request);
    }

    public function download(string $key, ?string $bucket = null): ?string
    {
        $result = $this->client->getObject([
            'Bucket' => $bucket ?? $this->bucket,
            'Key' => $key
        ]);
        $body = $result->get('Body');
        return $body ? $body->getContents() : null;
    }

    public function delete(string $key): Result
    {
        return $this->client->deleteObject([
            'Bucket' => $this->bucket,
            'Key' => $key
        ]);
    }

    public function listObjects(?string $bucket = null): array
    {
        $result = $this->client->listObjectsV2([
            'Bucket' => $bucket ?? $this->bucket
        ]);
        return $result['Contents'] ?? [];
    }

    public function getPolicy(): array
    {
        $result = $this->client->getBucketPolicy([
            'Bucket' => $this->bucket
        ]);
        return json_decode($result->get('Policy')->getContents(), true);
    }

    public function updatePolicy(array $policy): Result
    {
        return $this->client->putBucketPolicy([
            'Bucket' => $this->bucket,
            'Policy' => json_encode($policy)
        ]);
    }

    public function deletePolicy(): Result
    {
        return $this->client->deleteBucketPolicy([
            'Bucket' => $this->bucket
        ]);
    }

    public function getURL(string $key, ?string $bucket = null): string
    {
        return $this->client->getObjectUrl($bucket ?? $this->bucket, $key);
    }
}
