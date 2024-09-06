<?php

namespace EDM\Entities;

use EDM\EntityEnhancements\EntityIntegerIDInterface;
use EDM\EntityEnhancements\EntityIntegerIDTrait;
use EDM\EntityTracking\EntityTimeTrackedInterface;
use EDM\EntityTracking\EntityTimeTrackedTrait;
use EDM\EntityTracking\EntityUserTrackedInterface;
use EDM\EntityTracking\EntityUserTrackedTrait;

abstract class AbstractEntity implements EntityIntegerIDInterface, EntityTimeTrackedInterface, EntityUserTrackedInterface
{
	use EntityIntegerIDTrait;
	use EntityTimeTrackedTrait;
	use EntityUserTrackedTrait;
}