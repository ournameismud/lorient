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
use ournameismud\lorient\records\Addresses AS AddressRecord;

use Craft;
use craft\base\Component;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Addresses extends Component
{
    // Public Methods
    // =========================================================================

    // Name: getAddress
    // Purpose: 
    // Used by:
    //      lorient/src/controllers/OrdersController
    // Required: 
    //      $address (array)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $rels (address object/record)
    
    public function getAddress( $address )
    {
        $site = Craft::$app->getSites()->getCurrentSite();   
        // $getAdd
        $address['siteId'] = $site->id;
        $rels = AddressRecord::find()
            ->where($address)->one();            
        return $rels;
    }

    // Name: setAddress
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/OrdersController
    // Required: 
    //      $address (array)
    //      $userRef (id or string)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $newAddressRow (address object/record)

    public function setAddress( $address, $userRef )
    {
        $site = Craft::$app->getSites()->getCurrentSite();   
        // $getAdd
        $address['siteId'] = $site->id;
        $address['owner'] = $userRef;

        $newAddress = new AddressRecord;
        foreach ($address AS $key => $value) {
            $newAddress->$key = $value;
        }
        $newAddressRow = $newAddress->save();
        if ($newAddressRow) return $newAddress;
        else return 'Could not save item';
    }


}
