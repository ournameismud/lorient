<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\widgets;

use ournameismud\lorient\Lorient;
use ournameismud\lorient\assetbundles\lorientwidgetwidget\LorientWidgetWidgetAsset;

use Craft;
use craft\base\Widget;

/**
 * Lorient Widget
 *
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class LorientWidget extends Widget
{

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $message = 'Hello, world.';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('lorient', 'LorientWidget');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath()
    {
        return Craft::getAlias("@ournameismud/lorient/assetbundles/lorientwidgetwidget/dist/img/LorientWidget-icon.svg");
    }

    /**
     * @inheritdoc
     */
    public static function maxColspan()
    {
        return null;
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules,
            [
                ['message', 'string'],
                ['message', 'default', 'value' => 'Hello, world.'],
            ]
        );
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'lorient/_components/widgets/LorientWidget_settings',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        Craft::$app->getView()->registerAssetBundle(LorientWidgetWidgetAsset::class);

        return Craft::$app->getView()->renderTemplate(
            'lorient/_components/widgets/LorientWidget_body',
            [
                'message' => $this->message
            ]
        );
    }
}
