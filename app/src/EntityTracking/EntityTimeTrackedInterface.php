<?php

namespace EDM\EntityTracking;

interface EntityTimeTrackedInterface
{
	public function getCreatedAt(): ?\DateTime;

	public function getUpdatedAt(): ?\DateTime;

	public function setUpdatedAt(?\DateTime $updated_at);
}