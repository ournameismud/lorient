<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\services;

use ournameismud\lorient\Lorient;
use ournameismud\lorient\records\Orders AS OrderRecord;

use Craft;
use craft\base\Component;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Orders extends Component
{
    // Public Methods
    // =========================================================================

    // Name: modifyOrder
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/OrdersController
    // Required: 
    //      $id (integer)
    //      $atts (array)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $order (record)

    public function modifyOrder( $id, $atts )
    {
        $order = OrderRecord::find()->where(['id'=>$id])->one();
        foreach ($atts AS $key => $value) $order->$key = $value;
        $order->save();
        return $order;
    }

    // Name: setOrder
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/OrdersController
    // Required: 
    //      $order (array)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $newOrder (record)

    public function setOrder( $order )
    {
        // TO DO: need something here to avoid duplication
        $site = Craft::$app->getSites()->getCurrentSite();
        $newOrder = new OrderRecord;
        foreach ($order AS $key => $value) {
            $newOrder->$key = $value;
        }
        $newOrder->siteId = $site->id;
        $newOrderRow = $newOrder->save();

        if ($newOrderRow) return $newOrder;
        else return 'Could not save item';
    }
}
