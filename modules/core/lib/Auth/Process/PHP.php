<?php

namespace SimpleSAML\Module\core\Auth\Process;

use Webmozart\Assert\Assert;

/**
 * Attribute filter for running arbitrary PHP code.
 *
 * @package SimpleSAMLphp
 */

class PHP extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * The PHP code that should be run.
     *
     * @var string
     */
    private $code;


    /**
     * Initialize this filter, parse configuration
     *
     * @param array &$config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws \SimpleSAML\Error\Exception if the 'code' option is not defined.
     */
    public function __construct(&$config, $reserved)
    {
        parent::__construct($config, $reserved);

        Assert::isArray($config);

        if (!isset($config['code'])) {
            throw new \SimpleSAML\Error\Exception("core:PHP: missing mandatory configuration option 'code'.");
        }
        $this->code = (string) $config['code'];
    }


    /**
     * Apply the PHP code to the attributes.
     *
     * @param array &$request The current request
     * @return void
     */
    public function process(&$request)
    {
        Assert::isArray($request);
        Assert::keyExists($request, 'Attributes');

        /**
         * @param array &$attributes
         * @param array &$state
         * @return void
         */
        $function = function (
            /** @scrutinizer ignore-unused */ &$attributes,
            /** @scrutinizer ignore-unused */ &$state
        ) {
            eval($this->code);
        };
        $function($request['Attributes'], $request);
    }
}
