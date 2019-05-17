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
use craft\web\Session;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class OrdersController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['place', 'confirm'];
    protected $cartName = 'lorient_cart';
    protected $addressFields = ['title','firstName','secondName','company','address1','address2','townCity','state','postcode','telephone','email'];
    protected $addressFieldsRequired = ['firstName','secondName','company','address1','townCity','postcode','telephone','email'];
    
    protected function log($message, $category = null) {
        $file = Craft::getAlias('@storage/logs/lorient.log');
        $log = date('Y-m-d H:i:s').' '.$message." (".$category.")\n";
        \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }


    // Public Methods
    // =========================================================================

    
    // Name: 
    //      confirm
    // Purpose: 
    //      action to transition order from draft to placed
    // Context: 
    //      templates/account/confirm
    // Required: 
    //      order
    // Optional: 
    //      none
    // Services: 
    //      orders/modifyOrder
    //      samples/modifyCart

    public function actionConfirm()
    {

        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $hash = $request->getBodyParam('order');

        $order = Craft::$app->security->validateData( $hash );
        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        $user = Craft::$app->getUser();
        if($user->id == null) {
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }
        
        Lorient::getInstance()->orders->modifyOrder( $order, ['status'=>'placed'] );
        Lorient::getInstance()->samples->modifyCart( $userRef, $order );
        
        Lorient::getInstance()->orders->mailOrder( $order );        

        if ($request->getAcceptsJson()) {
            return $this->asJson(['response' => 'Order Confirmed']);
        } else {
            Craft::$app->getSession()->setNotice('Order Confirmed');
            return $this->redirectToPostedUrl();
        } 
        // return $this->redirectToPostedUrl();
    }

    // Name: 
    //      place
    // Purpose: 
    //      action to transition order from draft to placed
    // Context: 
    //      templates/tmp/includes/_sampleOrder
    // Required: 
    //      order
    //      projectRef
    //      $this->addressFieldsRequired
    // Optional: 
    //      $this->addressFields
    // Services: 
    //      addresses/getAddress
    //      addresses/setAddress
    //      orders/setOrder

    public function actionPlace()
    {
        
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        $order['orderId'] = $request->getBodyParam('order');
        
        $error = array();
        $address = array();

        $session = Craft::$app->getSession();
        $cart = $session[$this->cartName];
        $user = Craft::$app->getUser();
        if($user->id == null) {
            $userRef = $cart;
        } else {
            $userRef = $user->id;
        }
        foreach ($this->addressFields AS $field) {
            $address[$field] = $request->getBodyParam($field);
            if (in_array($field,$this->addressFieldsRequired) && strlen(trim($address[$field])) == 0) {
                $error[] = $field;
                unset($address[$field]);                
            }
        }
        if (count($error) > 0) {
            $this->log('Error (User Ref# ' . $userRef . '): ' . json_encode($error), 'Lorient::Orders Order #' . $order['orderId']);
            if ($request->getAcceptsJson()) {
                return $this->asJson(['response' => 'Error', 'errors' => $error, 'address' => $address]);
            } else {
                Craft::$app->getSession()->setNotice('Order Placed');
                Craft::$app->getSession()->setError('There was a problem with your submission, please check the form and try again!');
                Craft::$app->getUrlManager()->setRouteParams([
                    'variables' => ['address' => $address, 'error' => $error]
                ]);
                return null;
                // return $this->redirectToPostedUrl();
            }  
        }
        $getAddress = Lorient::getInstance()->addresses->getAddress( $address );
        if ($getAddress == null) {
            $getAddress = Lorient::getInstance()->addresses->setAddress( $address, $userRef );
        }

        // got address id
        // save record id
        $order = array();
        $order['owner'] = $userRef;
        $order['addressId'] = $getAddress->id;
        $order['projectRef'] = $request->getBodyParam('ref');
        $order['status'] = 'placed';

        // TO DO: avoid duplication here?
        $setOrder = Lorient::getInstance()->orders->setOrder( $order );
        // if ($setOrder) Lorient::getInstance()->samples->emptyCart( $userRef );
        
        $hash = Craft::$app->security->hashData($setOrder->id);
        
        // $url = $request->getBodyParam('redirect');
        $url = Craft::$app->getRequest()->getValidatedBodyParam('redirect');
        
        // Lorient::getInstance()->orders->modifyOrder( $order, ['status'=>'placed'] );
        Lorient::getInstance()->samples->modifyCart( $userRef, $setOrder->id );        
        Lorient::getInstance()->orders->mailOrder( $setOrder->id ); 

        if ($request->getAcceptsJson()) {
            return $this->asJson(['response' => 'Order Placed', 'hash' => $hash]);
        } else {
            Craft::$app->getSession()->setNotice('Order Placed');
            return $this->redirect( $url . '?hash=' . $hash );
            // return $this->redirectToPostedUrl();
        }           
        
    }
}
