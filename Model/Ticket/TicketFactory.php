<?php
/*
 * Copyright (c) 2014 Eltrino LLC (http://eltrino.com)
 *
 * Licensed under the Open Software License (OSL 3.0).
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@eltrino.com so we can send you a copy immediately.
 */
namespace Diamante\DeskBundle\Model\Ticket;

use Diamante\DeskBundle\Model\Branch\Branch;
use Diamante\DeskBundle\Model\Shared\AbstractEntityFactory;
use Oro\Bundle\UserBundle\Entity\User as OroUser;
use Diamante\DeskBundle\Model\User\User;

class TicketFactory extends AbstractEntityFactory
{
    public function create(
        UniqueId $uniqueId,
        TicketSequenceNumber $number,
        $subject,
        $description,
        Branch $branch,
        User $reporter,
        OroUser $assignee,
        Priority $priority,
        Source $source,
        Status $status
    ) {
        return new $this->entityClassName(
            $uniqueId, $number, $subject, $description, $branch, $reporter, $assignee, $source, $priority, $status
        );
    }
}
