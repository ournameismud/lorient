<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient;

use ournameismud\lorient\services\Addresses as AddressesService;
use ournameismud\lorient\services\Favourites as FavouritesService;
use ournameismud\lorient\services\Orders as OrdersService;
use ournameismud\lorient\services\Samples as SamplesService;
use ournameismud\lorient\variables\LorientVariable;
use ournameismud\lorient\widgets\LorientWidget as LorientWidgetWidget;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;
use craft\services\Dashboard;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\web\User;
use yii\web\UserEvent;

/**
 * Class Lorient
 *
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 *
 * @property  AddressesService $addresses
 * @property  FavouritesService $favourites
 * @property  OrdersService $orders
 * @property  SamplesService $samples
 */
class Lorient extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Lorient
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '0.1.6';

    // Public Methods
    // =========================================================================

    protected $sampleCart = 'lorient_cart';
    protected $favouriteCart = 'lorient_favourites';
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'lorient/addresses';
                $event->rules['siteActionTrigger2'] = 'lorient/favourites';
                $event->rules['siteActionTrigger3'] = 'lorient/orders';
                $event->rules['siteActionTrigger4'] = 'lorient/samples';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['lorient/orders'] = ['template' => 'lorient/orders'];
                $event->rules['lorient/order/<orderId:\w+>'] = ['template' => 'lorient/order'];
            }
        );

        // Event::on(
        //     Dashboard::class,
        //     Dashboard::EVENT_REGISTER_WIDGET_TYPES,
        //     function (RegisterComponentTypesEvent $event) {
        //         $event->types[] = LorientWidgetWidget::class;
        //     }
        // );

        Event::on(
            User::class,
            User::EVENT_AFTER_LOGIN,
            function (UserEvent $userEvent) {
                
                $session = Craft::$app->getSession();
                $sampleCart = $session[$this->sampleCart];
                $favouriteCart = $session[$this->favouriteCart];
                $this->samples->convertCart( $sampleCart );
                $this->favourites->convertFaves( $favouriteCart );
            }
        );

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('lorient', LorientVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'lorient',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
