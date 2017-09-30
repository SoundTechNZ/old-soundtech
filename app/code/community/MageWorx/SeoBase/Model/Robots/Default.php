<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Robots_Default extends MageWorx_SeoBase_Model_Robots_Abstract
{
    /**
     * Retrive robots
     *
     * @return string|null
     */
    protected function _getRobots()
    {
        return $this->_getRobotsBySettings();
    }
}