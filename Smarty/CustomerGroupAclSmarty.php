<?php

namespace CustomerGroupAcl\Smarty;

use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Tools\CustomerGroupAclTool;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Core\Security\Exception\AuthenticationException;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\Exception\SmartyPluginException;
use TheliaSmarty\Template\SmartyPluginDescriptor;

/**
 * Smarty plugins for group ACL checks.
 *
 * @author Guillaume Barral <gbarral@openstudio.fr>
 * @author Jérôme BILLIRAS <jbilliras@openstudio.fr>
 */
class CustomerGroupAclSmarty extends AbstractSmartyPlugin
{
    /** @var Request */
    protected $request;

    /** @var CustomerGroupAclTool */
    protected $customerGroupAclTool;

    /**
     * List named acl Smarty block
     * @var array
     */
    protected $rel;

    /**
     * Class constructor
     *
     * @param CustomerGroupAclTool $customerGroupAclTool ACL helper tool
     * @param Request              $request              Thelia request
     */
    public function __construct(CustomerGroupAclTool $customerGroupAclTool, Request $request)
    {
        $this->request = $request;
        $this->customerGroupAclTool = $customerGroupAclTool;
        $this->rel = [];
    }

    public function getPluginDescriptors()
    {
        return [
            new SmartyPluginDescriptor('function', 'get_access_pows', $this, 'getAccessPows'),
            new SmartyPluginDescriptor('function', 'check_acl', $this, 'checkAclPage'),
            new SmartyPluginDescriptor('block', 'acl', $this, 'checkAclBlock'),
            new SmartyPluginDescriptor('block', 'elseacl', $this, 'elseAclBlock'),
        ];
    }

    /**
     * Get pows (but I don't know what is call pows)
     *
     * @param array                      $params   Parameters
     * @param \Smarty_Internal_Template  $template Smarty template
     *
     * @return array
     */
    public function getAccessPows($params, $template = null)
    {
        $template->assign($params['load_access_pows'], CustomerGroupAclAccessManager::getAccessPows());
    }

    /**
     * Handler check_acl smarty function
     *
     * @param array                      $params   Parameters
     * @param \Smarty_Internal_Template  $template Smarty template
     *
     * @throws \Thelia\Core\Security\Exception\AuthenticationException
     *
     * @return null
     */
    public function checkAclPage($params, $template)
    {
        list($codes, $accesses, $accessOr) = $this->checkParameters($params);

        if ($this->customerGroupAclTool->checkAcl($this->explode($codes), $this->explode($accesses), $accessOr)) {
            return null;
        }

        $exception = new AuthenticationException('User not granted for action');

        $loginTpl = $this->getParam($params, 'login_tpl');
        if ($loginTpl !== null) {
            $exception->setLoginTemplate($loginTpl);
        }

        throw $exception;
    }

    /**
     * Handle acl smarty block structure
     *
     * @param array                     $params   Parameters
     * @param string                    $content  Block content
     * @param \Smarty_Internal_Template $template Smarty template
     * @param boolean                   $repeat   Block repeat
     *
     * @throws \TheliaSmarty\Template\Exception\SmartyPluginException
     *
     * @return null|string
     */
    public function checkAclBlock(array $params, $content, $template, &$repeat)
    {
        if ($content === null) {
            list($codes, $accesses, $accessOr, $name) = $this->checkParameters($params);

            if ($name !== null) {
                if (array_key_exists($name, $this->rel)) {
                    throw new SmartyPluginException('The named blocks "' . $name . '" was already declared.');
                }

                $this->rel[$name] = true;
            }

            if (!$this->customerGroupAclTool->checkAcl(explode(',', $codes), explode(',', $accesses), $accessOr)) {
                if ($name !== null) {
                    $this->rel[$name] = false;
                }
                $repeat = false;
            }
        }

        return $content;
    }

    /**
     * Handle elseacl smarty block structure
     *
     * @param array                     $params
     * @param string                    $content
     * @param \Smarty_Internal_Template $template
     * @param boolean                   $repeat
     *
     * @throws \TheliaSmarty\Template\Exception\SmartyPluginException
     *
     * @return null|string
     */
    public function elseAclBlock(array $params, $content, $template, &$repeat)
    {
        $rel = $this->getNormalizedParam($params, 'rel');

        if ($rel === null) {
            throw new SmartyPluginException('Smarty block "elseacl" requires rel parameter.');
        }
        if (!array_key_exists($rel, $this->rel)) {
            throw new SmartyPluginException('The named blocks "' . $rel . '" was not found.');
        }

        if ($repeat && $this->rel[$rel]) {
            $repeat = false;
        }

        return $content;
    }

    /**
     * Check parameters
     *
     * @param array $params
     *
     * @throws \TheliaSmarty\Template\Exception\SmartyPluginException
     *
     * @return array
     */
    protected function checkParameters(array $params)
    {
        $codes = $this->getParam($params, 'code');
        $accesses = $this->getNormalizedParam($params, 'access');
        $accessOr = $this->getParam($params, 'access_or', false);
        $name = $this->getNormalizedParam($params, 'name');

        if ($codes === null || $accesses === null) {
            throw new SmartyPluginException('Checking acl requires code and access parameters');
        }

        return [$codes, $accesses, $accessOr, $name];
    }
}
