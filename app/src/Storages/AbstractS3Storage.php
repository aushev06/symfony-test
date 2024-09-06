<?php

namespace EDM\Storages;


use EDM\Services\S3\S3ServiceInterface;

abstract class AbstractS3Storage extends AbstractImageStorage
{

    public function __construct(private readonly S3ServiceInterface $s3)
    {
    }

    public function getS3(): S3ServiceInterface
    {
        return $this->s3;
    }

    public function getBucket(): string
    {
        return $this->s3->getBucket();
    }
}
