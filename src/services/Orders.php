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
use ournameismud\lorient\records\Addresses AS AddressRecord;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\mail\Message;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Orders extends Component
{
    // Public Methods
    // =========================================================================

    protected $addressFields = ['title' =>'Title','firstName'=>'First Name','secondName'=>'Second Name','company'=>'Company','address1'=>'Address 1','address2'=>'Address 2','townCity'=>'Town/City','state'=>'State','postcode'=>'Postcode','telephone'=>'Telephone','email'=>'Email'];

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

        if ($newOrderRow) {
            return $newOrder;
        }
        else return 'Could not save item';
    }

    // Name: mailOrder
    // Purpose: 
    // Used by:
    //       lorient/src/services/Orders
    // Required: 
    //      $id (order record)
    // Optional: 
    //      none
    // Services: 
    //      use Message
    // Returns: 
    //      $message (bool)

    public function mailOrder( $id )
    {
        $site = Craft::$app->getSites()->getCurrentSite();   
        
        $mailer = Craft::$app->getMailer();
        $email = Craft::$app->config->general->custom['emailRecipient'];

        $order = OrderRecord::find()->where(['id'=>$id])->one();

        // Craft::info( 'Order: ' . $order->id, 'ournameismud\lorient\services\orders\mailOrder:107' );
        $owner = $order->owner;
        
        // $getAdd
        $siteId = $site->id;
        $address = AddressRecord::find()
            ->where(['siteId'=>$siteId,'owner'=>$owner])
            ->orderby('dateUpdated DESC')
            ->one();
        
        $messageFields = [];
        
        $body = "You have received an order from the " . $site->name . " website:\r\n\r\n";
        // $messageFields['name'] = $address->firstName . ' ' . $address->secondName;
        // $messageFields['company'] = $address->company;
        // $messageFields['email'] = $address->email;
        $body .= "Customer Details:\r\n";
        foreach ($this->addressFields AS $handle => $label) $body .= $label . ": " . $address->$handle . "\r\n";
        // foreach ($messageFields AS $key => $value) $body .= ucfirst($key) . ": " . $value . "\r\n";
        // Craft::info( 'Message Fields: ' . json_encode($messageFields), 'ournameismud\lorient\services\orders\mailOrder:128' );

        $cart = Lorient::getInstance()->samples->getCartByOrder( $order->id );
        $samples = [];
        $brochures = [];
        foreach ($cart AS $item) {
            $element = Entry::find()
               ->id( $item->element )
               ->siteId( $site->id )
               ->one(); 
            $specs = json_decode($item->specs);
            Craft::info( 'Specs: ' . $item->specs, 'ournameismud\lorient\services\orders\mailOrder:139' );
            if (count($specs) > 0) {
                $tmpRow = $element->title . ": \r\n";
                foreach($specs AS $prop => $array) {
                    $tmpRow .= ucfirst($prop);
                    if (count($array) > 0) {
                        if ($prop == 'finish') {
                            $tmpArr = [];
                            foreach ($array AS $src) {
                                $tmpSrc = explode('/',$src);
                                $tmpArr[] = end($tmpSrc);
                            }
                            $tmpRow .= ': ' . implode(', ',$tmpArr);
                        } else {
                            $tmpRow .= ': ' . implode(', ',$array);      
                        }                        
                    } 
                    $tmpRow .=  "\r\n";
                }
                $samples[] = $tmpRow . "\r\n";
            } else {
                $brochures[] = $element->title;
            }
        }

        $body .= "\r\nOrder details:\r\n";
        $body .= "Order #: " . $order->id  . "\r\n";
        $body .= "Notes: " . $order->projectRef . "\r\n";
        if (count($samples)) {
            $body .= "\r\nSamples:\r\n";
            foreach ($samples AS $value) $body .= $value . "\r\n"; 
        }
        if (count($brochures)) {
            $body .= "\r\nBrochures:\r\n";
            foreach ($brochures AS $value) $body .= $value . "\r\n";    
        }

        Craft::info( 'Message: ' . $body, 'ournameismud\lorient\services\orders\mailOrder:164' );

        // Craft::info( 'Address: ' . $address->id, 'ournameismud\lorient\services\orders\mailOrder:110' );



        // $address = Lorient::getInstance()->addresses->getAddress( ['owner' => $owner] );
        // Craft::info( 'Address: ' . $address->id, 'ournameismud\lorient\services\orders\mailOrder:110' );
        // Craft::dd( $order );
        
        // define message
        $message = (new Message())
            ->setTo( $address->email )
            ->setSubject( 'Order #' . $id . ' from website' )
            ->setTextBody( $body );
        // send email
        return $mailer->send( $message );
    }
}
