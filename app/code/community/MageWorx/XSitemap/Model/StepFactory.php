<?php
/**
 * MageWorx
 * MageWorx XSitemap Extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2017 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_XSitemap_Model_StepFactory
{
    const START_STEP_NAME  = 'start';
    const FINISH_STEP_NAME = 'finish';
    const ACTION_NAME      = 'next_action';

    /**
     * @return array
     */
    public function getSteps()
    {
        $helper = Mage::helper('mageworx_xsitemap');

        $generator = Mage::getSingleton('mageworx_xsitemap/generatorFactory');

        $startStep = array(
            self::START_STEP_NAME => array(
                MageWorx_XSitemap_Model_GeneratorFactory::TITLE_KEY => $helper->__('Start Generation'),
            )
        );

        $finishStep = array(
            self::FINISH_STEP_NAME => array(
                MageWorx_XSitemap_Model_GeneratorFactory::TITLE_KEY => $helper->__('Finish Generation'),
            )
        );

        $steps = $startStep + $generator->getData() + $finishStep;

        $i = 0;
        $convertedSteps = array();

        $titleKey = MageWorx_XSitemap_Model_GeneratorFactory::TITLE_KEY;

        foreach ($steps as $stepKey => $stepData) {
            if ($i == 0) {
                $prevStepKey = $stepKey;
                $i++;
                continue;
            }

            $convertedSteps[$prevStepKey] = array();
            $convertedSteps[$prevStepKey][$titleKey] = $stepData[$titleKey];
            $convertedSteps[$prevStepKey][self::ACTION_NAME] = $stepKey;
            $prevStepKey = $stepKey;
            $i++;
        }

        return $convertedSteps;
    }

    /**
     * @param string $action
     * @return bool
     */
    public function getIsStartStep($action)
    {
        return $action == self::START_STEP_NAME;
    }

    /**
     * @param string $action
     * @return bool
     */
    public function getIsFinishStep($action)
    {
        return $action == self::FINISH_STEP_NAME;
    }
}