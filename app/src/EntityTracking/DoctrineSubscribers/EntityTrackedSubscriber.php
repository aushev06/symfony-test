<?php

namespace EDM\EntityTracking\DoctrineSubscribers;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;

use EDM\DoctrineSubscribers\AbstractDoctrineSubscriber;
use EDM\EntityTracking\EntityTimeTrackedInterface;
use EDM\EntityTracking\EntityUserTrackedInterface;
use EDM\Entities\User;

#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::prePersist)]
class EntityTrackedSubscriber extends AbstractDoctrineSubscriber
{
	protected function supportsEntity(object $entity, bool $update): bool
	{
		return $entity instanceof EntityTimeTrackedInterface || $entity instanceof EntityUserTrackedInterface;
	}

	protected function processEntity(object $entity, EntityManager $manager, bool $update): bool
	{
		$result = false;

		if ($update && $entity instanceof EntityTimeTrackedInterface) {
			$entity->setUpdatedAt(new \DateTime());
			$result = true;
		}

		if ($entity instanceof EntityUserTrackedInterface) {
			$user = $this->getSecurity()->getUser();
			if ($user instanceof User == false) {
				return $result;
			}
			/** @var User $user */
			if ($update == false && $entity->getCreatedBy() == null) {
				$entity->setCreatedBy($user);
				$result = true;
			}
			if ($update && $entity->getUpdatedBy() == null) {
				$entity->setUpdatedBy($user);
				$result = true;
			}
		}

		return $result;
	}
}