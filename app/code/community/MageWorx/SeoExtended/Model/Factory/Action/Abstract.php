<?php
/**
 * MageWorx
 * MageWorx SeoExtended Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoExtended
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
abstract class MageWorx_SeoExtended_Model_Factory_Action_Abstract
{
    /**
     * @param string|null $fullActionName
     * @return mixed
     */
    abstract public function getModel($fullActionName = null);
}