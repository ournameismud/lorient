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
class Samples extends Model
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
            ['owner', 'string'],
            ['specs','mixed'],
            [['orderId','element'],'number','min' => 1],
        ];
    }
}
