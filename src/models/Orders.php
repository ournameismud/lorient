<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\models;

use ournameismud\lorient\Lorient;

use Craft;
use craft\base\Model;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Orders extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $someAttribute = 'Some Default';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           [
               [
                   'owner',
                   'projectRef',
                   'status',
               ], 'string'],
           ['addressId','number','min' => 1],
        ];
    }
}
