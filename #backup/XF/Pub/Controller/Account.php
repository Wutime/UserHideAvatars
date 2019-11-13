<?php

namespace Wutime\UserHideAvatars\XF\Pub\Controller;

use XF\Entity\User;

class Account extends XFCP_Account
{
	protected function preferencesSaveProcess(\XF\Entity\User $visitor)
	{
		$form = parent::preferencesSaveProcess($visitor);

        $input = $this->filter([
                'option' => [
                        'wutime_userhideavatars_enable' => 'bool',
                ],
        ]);
		
        $userOptions = $visitor->getRelationOrDefault('Option');
        $form->setupEntityInput($userOptions, $input['option']);
        return $form;

	}

}

