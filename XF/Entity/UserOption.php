<?php

namespace Wutime\UserHideAvatars\XF\Entity;
use XF\Mvc\Entity\Structure;

class UserOption extends XFCP_UserOption
{

	public static function getStructure(Structure $structure)
	{
		$parent = parent::getStructure($structure);
		$parent->columns['wutime_userhideavatars_enable'] = ['type' => self::BOOL, 'default' => false];

		return $parent;


	}

	protected function _setupDefaults()
	{
		$options = \XF::options();

		$defaults = $options->registrationDefaults;
		$this->wutime_userhideavatars_enable = $defaults['wutime_userhideavatars_enable'] ? true : false;
	}

}
