<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipAction;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipList;

/**
 * Disables JCoins Shop membership when expired.
 */
class JCoinsShopMembershipCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        // revoke expired memberships
        $membershipList = new JCoinsShopMembershipList();
        $membershipList->getConditionBuilder()->add('isActive = ?', [1]);
        $membershipList->getConditionBuilder()->add('endDate > ? AND endDate < ?', [0, TIME_NOW]);
        $membershipList->readObjects();

        if (\count($membershipList->getObjects())) {
            $action = new JCoinsShopMembershipAction($membershipList->getObjects(), 'revoke');
            $action->executeAction();
        }

        // warn before expiration
        $membershipList = new JCoinsShopMembershipList();
        $membershipList->getConditionBuilder()->add('isActive = ?', [1]);
        $membershipList->getConditionBuilder()->add('isWarned = ?', [0]);
        $membershipList->getConditionBuilder()->add('warnDate > ? AND warnDate < ?', [0, TIME_NOW]);
        $membershipList->readObjects();

        if (\count($membershipList->getObjects())) {
            $action = new JCoinsShopMembershipAction($membershipList->getObjects(), 'warn');
            $action->executeAction();
        }
    }
}
