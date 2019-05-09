<?php

namespace Wutime\UserHideAvatars\XF\Entity;
use XF\Mvc\Entity\Structure;

class UserOption extends XFCP_UserOption
{

	public static function getStructure(Structure $structure)
	{
		$parent = parent::getStructure($structure);
		$parent->columns['wutime_userhideavatars_enable'] = ['type' => self::BOOL, 'default' => false];


		//$test = \XF::options();
		//\XF::dumpSimple($test);


		return $parent;


	}
/*
	protected function _setupDefaults()
	{
		$options = \XF::options();

		$defaults = $options->registrationDefaults;
		//$this->wutime_userhideavatars_enable = $defaults['wutime_userhideavatars_enable'] ? true : false;
	}
*/
}

/*
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);
        $userColumn = \XF::options()->bdApi_subscriptionColumnUser;
        $userNotifyColumn = \XF::options()->bdApi_subscriptionColumnUserNotification;
        if (!empty($userColumn)) {
            $structure->columns[$userColumn] = ['type' => self::SERIALIZED_ARRAY, 'default' => []];
        }
        if (!empty($userNotifyColumn)) {
            $structure->columns[$userNotifyColumn] = ['type' => self::SERIALIZED_ARRAY, 'default' => []];
        }
        return $structure;
    }
 */