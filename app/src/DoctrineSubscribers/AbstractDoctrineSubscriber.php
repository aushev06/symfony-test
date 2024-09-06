<?php

namespace EDM\DoctrineSubscribers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

use Symfony\Bundle\SecurityBundle\Security;

abstract class AbstractDoctrineSubscriber
{
	private Security $security;

	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	public function getSecurity(): Security
	{
		return $this->security;
	}

	abstract protected function supportsEntity(object $entity, bool $update): bool;
	abstract protected function processEntity(object $entity, EntityManager $manager, bool $update): bool;

	public function getSubscribedEvents(): array
	{
		return [Events::prePersist, Events::preUpdate];
	}

	public function prePersist(PrePersistEventArgs $args): void
	{
		$entity = $args->getObject();
		if ($this->supportsEntity($entity, false)) {
			$this->processEntity($entity, $args->getObjectManager(), false);
		}
	}

	public function preUpdate(PreUpdateEventArgs $args): void
	{
		$entity = $args->getObject();
		if ($this->supportsEntity($entity, true) == false) {
			return;
		}
		$manager = $args->getObjectManager();
		if ($this->processEntity($entity, $manager, true)) {
			// necessary to force the update to see the change
			$meta = $manager->getClassMetadata(get_class($entity));
			$manager->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
		}
	}
}