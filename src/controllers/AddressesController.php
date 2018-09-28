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
use ournameismud\lorient\records\Addresses AS AddressRecord;

use Craft;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class AddressesController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = [];

    // Public Methods
    // =========================================================================
    protected $addressFields = ['title','firstName','secondName','company','address1','address2','townCity','state','postcode','telephone','email'];
    protected $addressFieldsRequired = ['firstName','secondName','company','address1','townCity','postcode','telephone','email'];

    // Public Methods
    // =========================================================================
    public function actionSave()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $id = $request->getBodyParam('id');
        $user = Craft::$app->getUser();
        $site = Craft::$app->getSites()->getCurrentSite();
        $addressRecord = new AddressRecord;
        if ($id) {
            $addressRecord =  AddressRecord::find()->where(['id' => $id ])->one();                   
        } 
        $addressRecord->owner = $user->id;
        $addressRecord->siteId = $site->id;
        $errors = [];
        foreach ($this->addressFields AS $field) {
            $value = $request->getBodyParam( $field );
            if (array_key_exists($field,$this->addressFieldsRequired) &&  strlen(trim($value)) == 0) {
                $errors[] = $field;
            }
            $addressRecord->$field = $value;
        }
        if(count($errors) == 0) {
            $addressRecord->save();
            $response = [
                'message' => 'Address Saved',
                // convert to model here
                'address' => $addressRecord
            ];
            if ($request->getAcceptsJson()) {
                return $this->asJson($response);
            } else {
                Craft::$app->getSession()->setNotice($response['message']);
                return $this->redirectToPostedUrl();
            }
        } else {
            $response['errors'] = $errors;
            if ($request->getAcceptsJson()) {
                return $this->asJson($response);
            }
            Craft::$app->getSession()->setError($response['message']);
            Craft::$app->getUrlManager()->setRouteParams($response);
            return null;   
        }   
    }
}
