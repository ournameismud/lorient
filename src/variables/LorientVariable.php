<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\variables;

use ournameismud\lorient\Lorient;
use ournameismud\lorient\records\Samples AS SampleRecord;
use ournameismud\lorient\records\Addresses AS AddressRecord;
use ournameismud\lorient\records\Orders AS OrderRecord;

use Craft;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class LorientVariable
{
    protected $cartName = 'lorient_cart';
    // Public Methods
    // =========================================================================

    // Name: 
    //      getFavourites
    // Purpose: 
    // Context: 
    //      templates/account/index
    // Required: 
    //      none
    // Optional: 
    //      $element (id)
    // Services:
    //      favourites->getFavourites
    // Returns: 
    //      $response (array of element IDs)
    
    public function getFavourites($element = null)
    {
        $favourites = Lorient::getInstance()->favourites->getFavourites( $element );
        $response = array();
        foreach( $favourites AS $favourite) $response[] = $favourite['element'];
        return $response;
    }

    // Name: 
    //      getSamples
    // Purpose: 
    // Context: 
    //      templates/account/index
    // Required: 
    //      none
    // Optional: 
    //      $sampleId (id)
    // Services:
    //      samples->getSamples
    // Returns: 
    //      $response (sample records)
    
    public function getSamples( $sampleId = null )
    {
        $samples = Lorient::getInstance()->samples->getSamples( $sampleId );
        $response = [];
        foreach ($samples AS $sample) {
            $tmpResponse = [];
            $tmpResponse['id'] = $sample->id;
            $tmpResponse['order'] = $sample->order;
            $tmpResponse['element'] = $sample->element;
            $tmpResponse['specs'] = json_decode($sample->specs);
            $response[] = $tmpResponse;
        }
        return $response;
    }

    // Name: 
    //      getAddress
    // Purpose: 
    //      get address record via the user/site
    // Context:
    //      src/templates/order
    //      src/templates/orders
    //      templates/account/confirm
    //      templates/account/order
    // Required: 
    //      none
    // Optional: 
    //      none
    // Services:
    //      $this->getUser
    // Returns: 
    //      $addressRecord (address record)
    
    public function getAddress()
    {
        $userRef = $this->getUser();
        $address = array();
        $site = Craft::$app->getSites()->getCurrentSite();   
        // $getAdd
        $address['siteId'] = $site->id;
        $address['owner'] = $userRef;
        // abstract to service?
        $addressRecord = AddressRecord::find()
            ->where($address)
            ->orderby('dateUpdated DESC')
            ->one();            
        return $addressRecord;
    }
    

    public function addressFields()
    {

        $addressFields = [
            [
                'label'=>'Title',
                'name'=>'title',    
                'type'=>'dropdown',
                'size'=>'small',
                'options'=>['Mr','Mrs','Miss','Dr'],
                'required' => false
            ],
            [
                'label'=>'First Name',
                'name'=>'firstName',    
                'type'=>'text',
                'size'=>'medium',
                'required' => false
            ],
            [
                'label'=>'Surname',
                'name'=>'secondName',    
                'type'=>'text',
                'size'=>'large',
                'required' => true
            ],
            [
                'label'=>'Company',
                'name'=>'company',    
                'type'=>'text',
                'size'=>'large',
                'required' => true
            ],
            [
                'label'=>'Address 1',
                'name'=>'address1',    
                'type'=>'text',
                'size'=>'large',
                'required' => true
            ],
            [
                'label'=>'Address 2',
                'name'=>'address2',    
                'type'=>'text',
                'size'=>'large',
                'required' => false
            ],
            [
                'label'=>'State',
                'name'=>'state',    
                'type'=>'text',
                'size'=>'medium',
                'required' => false
            ],
            [
                'label'=>'Town/City',
                'name'=>'townCity',    
                'type'=>'text',
                'size'=>'medium',
                'required' => true
            ],
            [
                'label'=>'Postcode',
                'name'=>'postcode',    
                'type'=>'text',
                'size'=>'medium',
                'required' => true
            ],
            [
                'label'=>'Telephone',
                'name'=>'telephone',    
                'type'=>'text',
                'size'=>'medium',
                'required' => true
            ],
        ];
        return $addressFields;
    }
    
    // Name: 
    //      getAddress
    // Purpose: 
    //      get address record via the record id
    // Context:
    //      lorient/src/templates/order
    //      lorient/src/templates/orders
    //      templates/account/confirm
    //      templates/account/order
    // Required: 
    //      none
    // Optional: 
    //      none
    // Services:
    //      none
    // Returns: 
    //      $addressRecord (address record)

    public function getAddressById( $id )
    {
        $addressRecord = AddressRecord::find()
            ->where([ 'id' => $id ])->one();
        return $addressRecord;
    }
    
    // Name: 
    //      getUser
    // Purpose: 
    //      get or generate a user reference
    // Context:
    //           
    // Required: 
    //      none
    // Optional: 
    //      none
    // Services:
    //      samples->setCardId
    // Returns: 
    //      $userRef (id or string)

    public function getUser()
    {
        $session = Craft::$app->getSession();
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
        return $userRef;
    }
    
    // Name: 
    //      getOrders
    // Purpose: 
    //      get orders by logged in user/session
    // Context:
    //      templates/account/index
    // Required: 
    //      none
    // Optional: 
    //      none
    // Services:
    //      $this->getUser
    // Returns: 
    //      $orderRecords (order Records)
         
    public function getOrders( )
    {
        $userRef = $this->getUser();
        // abstract to service?
        $orderRecords = OrderRecord::find()
            ->where( [ 'owner' => $userRef ] )->all();
        return $orderRecords;
    }

    // Name: 
    //      getSavedOrders
    // Purpose: 
    //      get orders by logged in user/session
    // Context:
    //      templates/account/index
    // Required: 
    //      none
    // Optional: 
    //      none
    // Services:
    //      $this->getUser
    // Returns: 
    //      $orderRecords (order Records)
         
    public function getSavedOrders( $sort = 'dateUpdated DESC' )
    {
        $userRef = $this->getUser();
        // abstract to service?
        $orderRecords = SampleRecord::find()
            ->where( [ 'owner' => $userRef ] )
            ->andWhere(['not',['order' => null]])
            ->orderBy( $sort )  
            ->all();
        $products = [];
        Craft::dd($orderRecords);
        foreach ( $orderRecords AS $order) {
            $tmp = [
                'orderId' => $order['order'],
                'date' => $order['dateUpdated'],
                'elementId' => $order['element'],
            ];
            $products[] = $tmp;
        }    
        return $products;
    }
    
    // Name: 
    //      getOrdersAll
    // Purpose: 
    //      get all orders or orders by defined criteria
    // Context:
    //      lorient/src/templates/orders
    // Required: 
    //      none
    // Optional: 
    //      $crit
    // Services:
    //      none
    // Returns: 
    //      $orderRecords (order Records)
         
    public function getOrdersAll( $crit = null )
    {
        $sort = array_key_exists('sort',$crit) ? $crit['sort'] : 'id'; 
        $by = array_key_exists('by',$crit) ? $crit['by'] : 'asc'; 
        $by = ($by == 'asc') ? SORT_ASC : SORT_DESC;
        
        $orderRecords = OrderRecord::find()->orderBy([$sort => $by]);
        $crit = array_filter($crit);
        // if (array_key_exists('limit',$crit)) $orderRecords->limit($crit['limit']);
        // if (array_key_exists('offset',$crit)) $orderRecords->offset($crit['offset']);
        if (array_key_exists('key',$crit) && array_key_exists('value',$crit)) $orderRecords->where([$crit['key'] => $crit['value']]);
        // if (array_key_exists('offset',$crit)) $orderRecords->offset($crit['offset']);
        
        return $orderRecords->all();
    }
    
    // Name: 
    //      getOrderByHash
    // Purpose: 
    //      get a specific order by its defined hash
    // Context:
    //      lorient/src/templates/order
    //      templates/account/confirm
    //      templates/account/order
    // Required: 
    //      $hash (string)
    // Optional: 
    //      none
    // Services:
    //      none
    // Returns: 
    //      $orderRecord (order Record)
         
    public function getOrderByHash( $hash )
    {
        $id = Craft::$app->security->validateData( $hash );
        // abstract to service?
        $orderRecord = OrderRecord::find()
            ->where( [ 'id' => $id ] )->one();            
        return $orderRecord;    
    }

    // Name: 
    //      getCart
    // Purpose: 
    //      get a specific cart by its id
    // Context:
    //      templates/account/cart
    //      templates/account/confirm
    //      templates/account/order
    // Required: 
    //      $id (integer)
    // Optional: 
    //      none
    // Services:
    //      samples->getCart
    //      $this->getUser
    // Returns: 
    //      $response (array of element IDs)
         
    public function getCart($id = null)
    {
        $userRef = $this->getUser();
        // abstract to service?
        $cart = Lorient::getInstance()->samples->getCart( $userRef, $id );
        $response = array();
        foreach( $cart AS $item) $response[] = $item['element'];
        return $response;
    }
    
    // Name: 
    //      getCart
    // Purpose: 
    //      get a specific cart by its id
    // Context:
    //      templates/account/cart
    //      templates/account/confirm
    //      templates/account/order
    // Required: 
    //      $id (integer)
    // Optional: 
    //      none
    // Services:
    //      samples->getCart
    //      $this->getUser
    // Returns: 
    //      $response (array of element IDs)
         
    public function getCartFull($id = null)
    {
        $userRef = $this->getUser();
        // abstract to service?
        $cart = Lorient::getInstance()->samples->getCart( $userRef, $id );
        $response = array();
        foreach( $cart AS $item) $response[] = $item;
        return $response;
    }

    // Name: 
    //      getCartById
    // Purpose: 
    //      get a specific cart by its order reference
    // Context:
    //      lorient/src/templates/order
    //      lorient/src/templates/orders
    //      templates/account/index
    // Required: 
    //      $id (integer)
    // Optional: 
    //      none
    // Services:
    //      samples->getCartByOrder
    // Returns: 
    //      $response (array of element IDs)
         
    public function getCartById( $id )
    {
        // $userRef = $this->getUser();
        // // abstract to service?
        $cart = Lorient::getInstance()->samples->getCartByOrder( $id );
        $response = array();
        foreach( $cart AS $item) $response[] = $item['element'];
        return $response;
    }
}
