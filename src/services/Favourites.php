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
use ournameismud\lorient\records\Favourites AS FavouriteRecord;

use Craft;
use craft\base\Component;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Favourites extends Component
{
    // Public Methods
    // =========================================================================
    protected $cartName = 'lorient_favourites';
    // Name: getFavourites
    // Purpose: 
    // Used by:
    //       lorient/src/variables/LorientVariable
    // Required: 
    //      none
    // Optional: 
    //      $element (ignore: legacy)
    // Services: 
    //      none
    // Returns: 
    //      $rels (favourite objects/records)
         
    public function getFavourites($element = NULL)
    {
        $user = Craft::$app->getUser();


        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        
        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }

        $site = Craft::$app->getSites()->getCurrentSite();   
        $criteria = [
            'owner' => $userRef,
            'siteId' => $site->id
        ];
        if ($element) $critiera['element'] = $element;
        $rels = FavouriteRecord::find()
            ->where( $criteria )
            ->all();
            return $rels;
    }

    // Name: processFavourites
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/FavouritesController
    // Required: 
    //      $user (id or string)
    //      $element (id)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $result (string)

    public function processFavourite($user, $element)
    {
        $site = Craft::$app->getSites()->getCurrentSite();

        $rels = FavouriteRecord::find()
            ->where([
                'owner' => $user,
                'element' => $element,
                'siteId' => $site->id
            ])->one();

        if (!$rels) {
            // insert
            $favourite = new FavouriteRecord;
            $favourite->owner = $user;
            $favourite->element  = $element;
            $favourite->siteId  = $site->id;
            $favourite->save();
            $result = 'favourite added';
        } else {
            // remove
            $rels->delete();
            $result = 'favourite removed';
        }

        return $result;
    }

    public function convertFaves($userRef) {

        $user = Craft::$app->getUser();
        $site = Craft::$app->getSites()->getCurrentSite();
        $rels = FavouriteRecord::updateAll(['owner'=>$user->id],[
            'owner' => $userRef,
            'siteId' => $site->id
        ]);        
    
    }
}
