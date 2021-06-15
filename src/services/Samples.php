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
use ournameismud\lorient\records\Samples AS SampleRecord;

use Craft;
use craft\base\Component;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Samples extends Component
{
    // Public Methods
    // =========================================================================

    // Name: getCart
    // Purpose: 
    // Used by:
    //       lorient/src/variables/LorientVariable
    // Required: 
    //      $userRef (id or string)
    // Optional: 
    //      $id (id)
    // Services: 
    //      none
    // Returns: 
    //      $rels (sample records)
         
    public function getCart( $userRef, $id = NULL )
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        if ($id) {
            $rels = SampleRecord::find()
            ->where([
                'owner' => (string)$userRef,
                'order' => (string)$id,
                'siteId' => (string)$site->id
            ])->all();
        } else {
            $rels = SampleRecord::find()
            ->where([
                'owner' => (string)$userRef,
                'order' => NULL,
                'siteId' => (string)$site->id
            ])->all();
        }
        // Craft::dd($rels);

        return $rels;
    }

    // Name: getCartByOrder
    // Purpose: 
    // Used by:
    //       lorient/src/variables/LorientVariable
    // Required: 
    //      $id (id)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $rels (sample records)
         
    public function getCartByOrder( $id )
    {
        $rels = SampleRecord::find()
            ->where([
                'order' => (string)$id
            ])->all();
        return $rels;
    }

    // Name: convertCart
    // Purpose: 
    // Used by:
    //       lorient/src/Lorient
    // Required: 
    //      $userRef (id or string)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      none
         
    public function convertCart( $userRef ) {
        $user = Craft::$app->getUser();
        $site = Craft::$app->getSites()->getCurrentSite();
        $rels = SampleRecord::updateAll(['owner'=>(string)$user->id],[
            'owner' => (string)$userRef,
            'order' => NULL,
            'siteId' => (string)$site->id
        ]);        
    }

    // Name: convertCart
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/OrdersController
    // Required: 
    //      $userRef (id or string)
    //      $orderRef (id)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      none

    public function modifyCart( $userRef, $orderRef ) {
        $user = Craft::$app->getUser();
        $site = Craft::$app->getSites()->getCurrentSite();
        $rels = SampleRecord::updateAll(['order'=>(string)$orderRef],[
            'owner' => (string)$userRef,
            'order' => NULL,
            'siteId' => (string)$site->id
        ]);        
    }

    // Name: checkCart
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/SamplesController
    // Required: 
    //      $element (id)
    //      $userRef (id or string)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $rels (sample records)
    
    public function checkCart( $element, $userRef )
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $rels = SampleRecord::find()
            ->where([
                'owner' => (string)$userRef,
                'order' => NULL,
                'element' => (string)$element,
                'siteId' => $site->id
            ])->all();
        return $rels;
    }

    // Name: emptyCart
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/SamplesController
    // Required: 
    //      $userRef (id or string)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $rels (sample records)

    public function emptyCart( $userRef )
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $rels = SampleRecord::deleteAll([
                'owner' => (string)$userRef,
                'order' => NULL,
                'siteId' => (string)$site->id
            ]);
        return $rels;
    }

    // Name: removeCart
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/SamplesController
    // Required: 
    //      $element (id)
    //      $userRef (id or string)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $rels (sample records)

    public function removeCart( $element, $userRef )
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $rels = SampleRecord::deleteAll([
                'element' => (string)$element,
                'owner' => (string)$userRef,
                'order' => NULL,
                'siteId' => (string)$site->id
            ]);
        return $rels;
    }
    
    // Name: addToCart
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/SamplesController
    // Required: 
    //      $element (id)
    //      $userRef (id or string)
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      $sample->id (sample id)

    public function addToCart( $element, $userRef, $specs = null )
    {
        $site = Craft::$app->getSites()->getCurrentSite();
        $sample = SampleRecord::find()
            ->where([
                'owner' => (string)$userRef,
                'order' => NULL,
                'element' => (string)$element,
                'siteId' => (string)$site->id
            ])->one();        
        
        if (!$sample)  {
            $sample = new SampleRecord;            
        } elseif($specs) {
            $tmpMerge = (array)json_decode($sample->specs);

            $vals = ['size','color','finish'];
            foreach ($vals AS $val) {
                $tmpArr = [];
                if (array_key_exists($val,$specs) && array_key_exists($val,$tmpMerge)) {
                    $tmpArr = @array_merge($tmpMerge[$val],$specs[$val]);    
                } elseif(array_key_exists($val,$tmpMerge)) {
                    $tmpArr = $tmpMerge[$val];
                } elseif(array_key_exists($val,$specs)) {
                    $tmpArr = $specs[$val];
                } 
                if (is_array($tmpArr)) $specs[$val] = array_unique($tmpArr);            
            }
        }
        // $sample = new SampleRecord;
        $sample->owner = $userRef;
        $sample->element = $element;
        $sample->specs = $specs;
        $sample->siteId  = $site->id;
        $sampleRow = $sample->save();
        if ($sampleRow) return $sample->id;
        else return 'Could not save item';
    }

    // Name: setCartId
    // Purpose: 
    // Used by:
    //       lorient/src/controllers/SamplesController
    // Required: 
    //      none
    // Optional: 
    //      none
    // Services: 
    //      none
    // Returns: 
    //      generated hash
         
    public function setCartId()
    {
        return md5(uniqid(mt_rand(), true));
    }

    // Name: getSamples
    // Purpose: 
    // Used by:
    //       lorient/src/variables/LorientVariable
    // Required: 
    //      none
    // Optional: 
    //      $sampleId
    // Services: 
    //      none
    // Returns: 
    //      Sample Record(s)
         
    public function getSamples( $sampleId = null )
    {
        $user = Craft::$app->getUser();
        $site = Craft::$app->getSites()->getCurrentSite();   
        
        if( $sampleId ) {
            $samples = SampleRecord::find()
            ->where([
                'id' => (string)$sampleId
            ])->one();    
        } else {
            $samples = SampleRecord::find()
            ->where([
                'owner' => (string)$user->id,
                // 'element' => $element,
                'siteId' => (string)$site->id
            ])->andWhere(['not',['specs' => null]])->all();    
        }
        
        return $samples;
    }

    public function getSamplesByOrder( $orderId )
    {

        $samples = SampleRecord::find()
            ->where([
                'order' => (string)$orderId
            ])->all();
        return $samples;
    }
}
