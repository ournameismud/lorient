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
use ournameismud\lorient\records\Samples AS SampleRecord;

use Craft;
use craft\web\Controller;
use craft\web\Session;
use craft\records\User;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class SamplesController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['add-cart', 'add-all', 'empty-cart', 'adjust-cart', 'remove-cart', 'get-cart'];
    protected $cartName = 'lorient_cart';

    // Public Methods
    // =========================================================================


    // Name: 
    //      removeCart
    // Purpose: 
    // Context: 
    //      templates/account/cart
    // Required: 
    //      elementId
    // Optional: 
    //      none
    // Services: 
    //      samples/setCardId
    //      samples/removeCart
    //      orders/setOrder
    
    public function actionRemoveCart()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        
        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];

        // get product id
        $elementId = $request->getBodyParam('elementId');
        $user = Craft::$app->getUser();
        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }
        $empty = Lorient::getInstance()->samples->removeCart( $elementId, $userRef );
        if ($request->getAcceptsJson()) {
            return $this->asJson(['response' => 'Item removed']);
        } else {
            Craft::$app->getSession()->setNotice('Item removed');
            return $this->redirectToPostedUrl();
        }        
    }

    // Name: 
    //      emptyCart
    // Purpose: 
    // Context: 
    //      templates/account/cart
    // Required: 
    //      elementId
    // Optional: 
    //      none
    // Services: 
    //      samples/setCardId
    //      samples/emptyCart

    public function actionEmptyCart()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        
        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        // get product id
        $elementId = $request->getBodyParam('elementId');
        $user = Craft::$app->getUser();
        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }
        $empty = Lorient::getInstance()->samples->emptyCart( $userRef );
        if ($request->getAcceptsJson()) {
            return $this->asJson(['response' => 'Cart emptied']);
        } else {
            Craft::$app->getSession()->setNotice('Cart emptied');
            return $this->redirectToPostedUrl();
        }        

    }

    public function actionGetCart()
    {
        $request = Craft::$app->getRequest();
        $session = Craft::$app->getSession();
        $site = Craft::$app->getSites()->getCurrentSite();
        $cart = $session[$this->cartName];
        $user = Craft::$app->getUser();
        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }
        $cart = Lorient::getInstance()->samples->getCart( $userRef );
        $items = [];
        foreach ($cart AS $row) {
            $tmp = [];
            $element = Craft::$app->getElements()->getElementById($row['element'], null, $site->id);
            $tmp['id'] = $row['element'];
            $tmp['title'] = $element['title'];
            $tmp['type'] = $element->type['handle'];
            $tmp['specs'] = json_decode($row['specs']);
            $items[] = $tmp;
        }
        $response = [
            'response' => 'Cart contents',
            'contents' => $items,
            'length' => count($cart)
        ];
        if ($request->getAcceptsJson()) {
            return $this->asJson($response);
        } else {
            return $this->asXml($response);
            // return $response;
        }   
    }
    
    // Name: 
    //      addCart
    // Purpose: 
    // Context: 
    //      templates/resource-centre/_resource_brochure
    // Required: 
    //      elementId
    // Optional: 
    //      $this->addressFields
    // Services: 
    //      samples/setCardId
    //      samples/checkCart
    //      samples/addToCart

    public function actionAddCart()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        
        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        // get product id
        $elementId = $request->getBodyParam('elementId');
        $specs = $request->getBodyParam('specs');
        $user = Craft::$app->getUser();
        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }

        // $current = Lorient::getInstance()->samples->checkCart( $elementId, $userRef, $specs );
        // if($current) {
        //     Craft::$app->getSession()->setNotice('Already in cart');
        //     return $this->redirectToPostedUrl();
        // }
        $sample = Lorient::getInstance()->samples->addToCart( $elementId, $userRef, $specs );

        if ($request->getAcceptsJson()) {
            return $this->asJson(['response' => 'Item added']);
        } else {
            Craft::$app->getSession()->setNotice('Item added');
            return $this->redirectToPostedUrl();
        }        
    }


    public function actionAddAll()
    {
        // $this->requirePostRequest();
        $request = Craft::$app->getRequest();        
        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        // get product id
        $type = $request->getBodyParam('type');
        $type = $type ? $type : 'brochure';
        
        $entries = \craft\elements\Entry::find()
            ->section('brochuresSamples')
            ->type($type)
            ->ids();
        
        $user = Craft::$app->getUser();
        if($user->id == null) {
            if (!$cart) {
                $cart = Lorient::getInstance()->samples->setCartId();
                $session->set($this->cartName, $cart);
            }
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }

        $affected = [];
        // pass array here or loop here?
        $count = 0;
        foreach($entries AS $elementId) {
            $sample = Lorient::getInstance()->samples->addToCart( $elementId, $userRef, null );
            if ($sample) {
                $entry = \craft\elements\Entry::find()
                    ->id($elementId)->one();
                $affected[] = array('id' => $elementId, 'title' => $entry->title);
                $count++;
            }
        }

        $responseString = $count . ' items added';
        $response = array(
            'response' => $responseString,
            'affected' => $affected
        );

        if ($request->getAcceptsJson()) {
            return $this->asJson( $response );
        } else {
            Craft::$app->getSession()->setNotice( $responseString );
            return $this->redirectToPostedUrl();
        }        
    }
}
