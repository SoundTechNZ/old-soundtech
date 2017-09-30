<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
interface MageWorx_XSitemap_Model_GeneratorInterface
{
    /**
     * @param $storeId
     * @param $writer
     * @param $counter
     * @return mixed
     */
    public function generate($storeId, $writer, $counter);

    /**
     * @return int
     */
    public function getCounter();

    /**
     * @return int
     */
    public function getCurrentTotal();
}