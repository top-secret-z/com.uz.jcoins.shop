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
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipEditor;
use wcf\data\jcoins\shop\membership\JCoinsShopMembershipList;
use wcf\data\user\User;

/**
 * Disables JCoins Shop membership item when group is left.
 */
class JCoinsShopMembershipGroupCronjob extends AbstractCronjob
{
    /**
     * @inheritDoc
     */
    public function execute(Cronjob $cronjob)
    {
        parent::execute($cronjob);

        // get active memberships
        $membershipList = new JCoinsShopMembershipList();
        $membershipList->getConditionBuilder()->add('isActive = ?', [1]);
        $membershipList->getConditionBuilder()->add('startDate < ? AND endDate > ?', [TIME_NOW, TIME_NOW]);
        $membershipList->readObjects();
        $memberships = $membershipList->getObjects();

        if (!\count($memberships)) {
            return;
        }

        // check whether users are still in membership groups, 200 users
        $count = 0;
        foreach ($memberships as $membership) {
            $count++;

            $user = new User($membership->userID);
            if (!$user->userID) {
                continue;
            }

            $groups = $user->getGroupIDs();
            if (!\in_array($membership->groupID, $groups)) {
                $editor = new JCoinsShopMembershipEditor($membership);
                $editor->update([
                    'isActive' => 0,
                    'endDate' => TIME_NOW,
                ]);
            }

            if ($count > 200) {
                break;
            }
        }
    }
}
