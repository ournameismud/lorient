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

use clearbold\cmlists\CmLists;

use Craft;
use craft\web\Controller;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class ListController extends Controller
{

    //public $enableSnaptchaValidation = false;
    
    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================


    
    
    public function actionIndex()
    {
        
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        //check honeypot
        $honeypot =  $request->getParam('preferred-time') ? true : false;
        
        if($honeypot):

            $response = [
                    'success' => true,
                    'statusCode' => 201,
                    'body' => $request->getParam('email')
                ];;

            return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);

        endif;

        // Fetch list id from hidden input
        $listId = $request->getRequiredBodyParam('listId') ? Craft::$app->security->validateData($request->post('listId')) : null;
        $redirect =  $request->getParam('redirect') ? Craft::$app->security->validateData($request->post('redirect')) : null;

        $additionalFields = array();
        $email = $request->getParam('email');
        $fullName = '';
        if ($request->getParam('fullname') !== null)
            $fullName = $request->getParam('fullname');
        if ($request->getParam('firstname') !== null)
            $fullName = $request->getParam('firstname');
        if ($request->getParam('lastname') !== null)
            $fullName .= ' ' . $request->getParam('lastname');

        if ($request->getParam('fields') !== null)
        {
            foreach($request->getParam('fields') as $key => $value) {
                if ($key != 'email' && $key != 'firstname' && $key != 'lastname' && $key != 'fullname')
                {
                    $additionalFields[] = array(
                        'Key' => $key,
                        'Value' => $value
                    );
                }
            }
        }

        // $subscriber = array(
        //     'EmailAddress' => $email,
        //     'Name' => $fullName,
        //     'CustomFields' => $additionalFields,
        //     'Resubscribe' => true
        // );

        // if ($request->getParam('email') !== null) {
        //     $response = CmLists::getInstance()->campaignmonitor->addSubscriber($listId, $subscriber);
        // }

        $response = CmLists::getInstance()->cmListService->subscribe($listId, $email, $fullName, $additionalFields);

        return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);

    }

   
}
