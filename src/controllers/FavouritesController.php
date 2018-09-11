<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\controllers;

use ournameismud\lorient\Lorient;

use Craft;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class FavouritesController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['add-favourite'];
    protected $cartName = 'lorient_favourites';
    // Public Methods
    // =========================================================================

    
    // Name: addFavourite
    // Purpose: action to add favourite record (requires user logged in)
    // Context: 
    //      templates/account/index
    //      templates/products/_entry
    // Required: 
    //      elementId
    // Optional: 
    //      none
    // Services: 
    //      favourites/processFavourite


    public function actionAddFavourite()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        
        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        // get product id
        // $elementId = Craft::request->getParam('elementId');
        $elementId = $request->getBodyParam('elementId');
        $user = Craft::$app->getUser();
        // Craft::dd( $user->id );
        // $userId = $user->id == null ? 'xxx' : $user->id
        // if ($user->id == null) {
            // if ($request->getAcceptsJson()) return $this->asJson(['errors' => 'Not logged in']);
            // else {
            //     Craft::$app->getSession()->setNotice('Not logged in');
            //     return $this->redirectToPostedUrl('$submission');
            // }
        // } 

        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }
        $processFavourite = Lorient::getInstance()->favourites->processFavourite( $userRef, $elementId );
        // Craft::dd( $processFavourite );
        if ($processFavourite) {
            if ($request->getAcceptsJson()) {
                return $this->asJson(['response' => $processFavourite]);
            } else {
                Craft::$app->getSession()->setNotice($processFavourite);
                return $this->redirectToPostedUrl();
            }
        } else {
            Craft::dd("couldn't post to service");
        }        
    }
}
