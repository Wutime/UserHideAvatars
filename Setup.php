<?php

namespace Wutime\UserHideAvatars;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;
use XF\Entity\Option;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

/*

IMPORTANT: ** REVIEW **

 */

	static $usesComposer 	= false;
	static $install 		= false;
	static $upgrade 		= false;
	static $uninstall 		= false;




/*

CHECK REQUIREMENTS

 */



    public function checkRequirements(&$errors = [], &$warnings = [])
    {
    	if (self::$usesComposer) {
	        $vendorDirectory = sprintf("%s/vendor", $this->addOn->getAddOnDirectory());
	        if (!file_exists($vendorDirectory))
	        {
	            $errors[] = "vendor folder does not exist - cannot proceed with addon install";
	        }
    	}
    }







/*

INSTALL

 */


	public function installStep1() {
		$this->setInstallUpgradeVersion();
	}



	public function installStep1()
	{

		$this->schemaManager()->alterTable('xf_user_option', function(Alter $table)
		{
			$table->addColumn('wutime_userhideavatars_enable', 'tinyint')->setDefault(0)->after('user_id');
		});
	}


	public function uninstallStep1()
	{
		$this->schemaManager()->alterTable('xf_user_option', function(Alter $table)
		{
			$table->dropColumns(['wutime_userhideavatars_enable']); 
		});
	}







/*

UPGRADE

 */









/*

POST UPGRADE

 */

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        $this->setInstallUpgradeVersion();
    }






/*

UNINSTALL

 */


    public function uninstallStep1()
    {
        $this->stepX('uninstall');
    }



/*

MISC.

 */



    protected function updateOptionValue($optionId, $newValue)
    {
        /** @var Option $option */
        $option = \XF::finder('XF:Option')->where('option_id', $optionId)->fetchOne();

        if ($option) {
            $option->option_value = $newValue;
            $option->save();
        }
    }

    private static function stepX($step) 
    {
        $className = __NAMESPACE__ . '\\Install\\Install';
        $className::install(__NAMESPACE__, $step);
    }

    protected function setInstallUpgradeVersion()
    {
        $version = $this->getAddonVersion();
        $optionId = $this->generateOptionId();
        $db = \XF::db();

        // Check if the option already exists
        $existingOption = $db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = ?", $optionId);

        if ($existingOption === false && !self::$install) {
            // Option does not exist, assume this is a first-time install
            $db->insert('xf_option', [
                'option_id' => $optionId,
                'option_value' => $version,
                'default_value' => $version,
                'edit_format_params' => '',
                'sub_options' => '',
                'data_type' => 'string',
                'addon_id' => $this->addOn->addon_id,
                'validation_class' => '',
                'validation_method' => '',
            ]);

            $this->stepX('install');

            self::$install = true;

        } elseif (!self::$upgrade && !self::$install) {

            $this->stepX('upgrade');

            self::$upgrade = true;

            // Option exists, assume this is an upgrade
            $db->update('xf_option', ['option_value' => $version], 'option_id = ?', $optionId);
        }

    }

    protected function getInstalledVersion()
    {
        $optionId = $this->generateOptionId();
        $db = \XF::db();
        return $db->fetchOne("SELECT option_value FROM xf_option WHERE option_id = ?", $optionId);
    }

    protected function getAddonVersion()
    {
        $addonJsonPath = $this->getAddonDirectory() . DIRECTORY_SEPARATOR . 'addon.json';
        $addonJson = file_get_contents($addonJsonPath);
        $addonData = json_decode($addonJson, true);
        return $addonData['version_id'];
    }

    protected function getAddonDirectory()
    {
        // Get add-on (remove leading \ and replace other slashes with /)
        $addon = str_replace('\\', '/', ltrim($this->addOn->addon_id, '\\'));

        // Build the full add-on directory path dynamically
        return \XF::getAddOnDirectory() . '/' . $addon;
    }

    protected function generateOptionId()
    {
        // Convert the addon name to lower case and replace slashes with underscores, then append "_install_version"
        $optionId = strtolower(str_replace(['/', '\\'], '_', $this->addOn->addon_id)) . '_install_version';
        return $optionId;
    }

}