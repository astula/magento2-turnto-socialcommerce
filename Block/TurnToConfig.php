<?php
/**
 * @category    ClassyLlama
 * @package
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace TurnTo\SocialCommerce\Block;

use Magento\Framework\Locale\Resolver;
use Magento\Framework\View\Element\Template;
use TurnTo\SocialCommerce\Api\TurnToConfigDataSourceInterface;
use TurnTo\SocialCommerce\Helper\Config as TurnToConfigHelper;

/**
 * @method void setConfigData(TurnToConfigDataSourceInterface|array $config)
 * @method TurnToConfigDataSourceInterface|array getConfigData()
 */
class TurnToConfig extends Template implements TurnToConfigInterface
{
    /**
     * @var TurnToConfigHelper
     */
    protected $configHelper;

    /**
     * @var Resolver
     */
    protected $localeResolver;

    /**
     * @param Template\Context   $context
     * @param TurnToConfigHelper $configHelper
     * @param Resolver           $localeResolver
     * @param array              $data
     */
    public function __construct(
        Template\Context $context,
        TurnToConfigHelper $configHelper,
        Resolver $localeResolver,
        array $data = []
    )
    {
        // Set the template here so that it's easier to manually create a config block to place anywhere, such as widget
        // Set the template first so that if it's overwritten at the block level we don't force this template
        $this->setTemplate('TurnTo_SocialCommerce::turnto-config.phtml');

        parent::__construct($context, $data);

        $this->configHelper = $configHelper;
        $this->localeResolver = $localeResolver;
    }

    /**
     * Takes an array and converts it to JavasScript output with support for \Zend_Json_Expr
     * @return string
     */
    public function getJavaScriptConfig()
    {
        $configData = $this->getConfigData();

        if ($configData instanceof TurnToConfigDataSourceInterface) {
            $configData = $configData->getData();
        }

        $additionalConfigData = ['locale' => $this->localeResolver->getLocale()];

        if ($this->configHelper->getQaEnabled()) {
            $additionalConfigData['qa'] = [];
        }

        $configData = array_merge($additionalConfigData, $configData);

        /*
         * Zend_Json::encode is used instead of json_encode because the values of iTeaserFunc and reviewsTeaserFunc
         * have to be a JavaScript object. json_encode has no way to accomplish this. See this stack overflow question
         * for more context http://stackoverflow.com/questions/6169640/php-json-encode-encode-a-function
         */

        return \Zend_Json::encode($configData, false, ['enableJsonExprFinder' => true]);
    }
}
