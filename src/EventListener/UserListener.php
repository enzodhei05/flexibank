<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Event\PrePersistEventArgs;

class UserListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        // Vérifier que l'entité concernée est bien un User
        if ($entity instanceof User) {
            if (!$entity->getCodeClient()) {
                $entity->setCodeClient(substr(uniqid('CLI-'), 0, 10));

            }
        }
    }
}
