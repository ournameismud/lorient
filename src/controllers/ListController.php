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

    // for testing/dev mode
    // public $enableSnaptchaValidation = false;
    // public $enableCsrfValidation = false;
    
    public function actionIndex()
    {
        
        // See /adapt/deploy/modules/mudmodule/src/MudModule.php:206
        // get module settings
        $settings = Craft::$app->config->getConfigFromFile('lorient');
        // set API Access Key
        $access_key = $settings['apiLayerKey'];

        // Craft::dd($settings);
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();

        // if devmode then ignore spam prevention methods
        if(!Craft::$app->getConfig()->general->devMode) {

            $session = Craft::$app->session->get('signup');

            if($session): //session has already submitted

                $response = [
                        'success' => true,
                        'statusCode' => 201,
                        'body' => $request->getParam('email')
                    ];

                return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);

            endif;

            //check honeypot
            $honeypot =  $request->getParam('preferred-time') ? true : false;

            if($honeypot):

                $response = [
                        'success' => true,
                        'statusCode' => 201,
                        'body' => $request->getParam('email')
                    ];

                return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);

            endif;

        }

        // Fetch list id from hidden input
        $listId = $request->getRequiredBodyParam('listId') ? Craft::$app->security->validateData($request->post('listId')) : null;
        $redirect =  $request->getParam('redirect') ? Craft::$app->security->validateData($request->post('redirect')) : null;

        $additionalFields = array();

        // get email
        $email = $request->getRequiredBodyParam('email');

        // do spam lookup
        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/check?access_key='.$access_key.'&email='.$email.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $result = json_decode($json, true);
        // we can work with the result
        if (is_array($result) && array_key_exists('score', $result)) {

            $score = $result['score'];
            // do we need smtp_check or catch_all for more rigorous?
            if ($score < $settings['apiLayerBenchmark']) {
                $response = [
                    'success' => true,
                    'statusCode' => 201,
                    'body' => $request->getParam('email')
                ];

                return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);
            }
        }

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

        $response = CmLists::getInstance()->cmListService->subscribe($listId, $email, $fullName, $additionalFields);

        if($response['success']): 
            Craft::$app->session->set('signup',true); 
        endif;

        return $request->getBodyParam('redirect') ? $this->redirectToPostedUrl() : $this->asJson($response);

    }

   
}
