<?php

namespace EDM\EntityTracking;

use EDM\Entities\User;

interface EntityUserTrackedInterface
{
	public function getCreatedBy(): ?User;

	public function setCreatedBy(?User $created_by);

	public function getUpdatedBy(): ?User;

	public function setUpdatedBy(?User $updated_by);
}