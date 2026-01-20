<?php

namespace CustomerGroupAcl\Smarty;

use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Tools\CustomerGroupAclTool;
use Exception;
use Smarty_Internal_Template;
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
    protected Request $request;

    protected CustomerGroupAclTool $customerGroupAclTool;

    /**
     * List named acl Smarty block
     * @var array
     */
    protected array $rel;

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

    public function getPluginDescriptors(): array
    {
        return [
            new SmartyPluginDescriptor('function', 'get_access_pows', $this, 'getAccessPows'),
            new SmartyPluginDescriptor('function', 'check_acl', $this, 'checkAclPage'),
            new SmartyPluginDescriptor('block', 'acl', $this, 'checkAclBlock'),
            new SmartyPluginDescriptor('block', 'elseacl', $this, 'elseAclBlock'),
        ];
    }

    /**
     * Get pows (but I don't know what call pows is)
     *
     * @param array $params   Parameters
     * @param Smarty_Internal_Template|null $template Smarty template
     *
     * @return void
     */
    public function getAccessPows(array $params, Smarty_Internal_Template $template = null): void
    {
        $template->assign($params['load_access_pows'], CustomerGroupAclAccessManager::getAccessPows());
    }

    /**
     * Handler check_acl smarty function
     *
     * @param array $params Parameters
     * @param Smarty_Internal_Template $template Smarty template
     *
     * @return null
     * @throws AuthenticationException|SmartyPluginException
     * @throws Exception
     *
     */
    public function checkAclPage(array $params, Smarty_Internal_Template $template): null
    {
        list($codes, $accesses, $accessOr, $entityId) = $this->checkParameters($params);

        if ($this->customerGroupAclTool->checkAcl($this->explode($codes), $this->explode($accesses), $accessOr, $entityId)) {
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
     * @param array $params Parameters
     * @param string $content Block content
     * @param Smarty_Internal_Template $template Smarty template
     * @param boolean $repeat Block repeat
     *
     * @return null|string
     * @throws SmartyPluginException
     * @throws Exception
     *
     */
    public function checkAclBlock(array $params, string $content, Smarty_Internal_Template $template, bool &$repeat): ?string
    {
        if ($content === null) {
            list($codes, $accesses, $accessOr, $entityId, $name) = $this->checkParameters($params);

            if ($name !== null) {
                if (array_key_exists($name, $this->rel)) {
                    throw new SmartyPluginException('The named blocks "' . $name . '" was already declared.');
                }

                $this->rel[$name] = true;
            }

            if (!$this->customerGroupAclTool->checkAcl(explode(',', $codes), explode(',', $accesses), $accessOr, $entityId)) {
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
     * @param string $content
     * @param Smarty_Internal_Template $template
     * @param boolean $repeat
     *
     * @return null|string
     * @throws SmartyPluginException
     *
     */
    public function elseAclBlock(array $params, string $content, Smarty_Internal_Template $template, bool &$repeat): ?string
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
     * @return array
     * @throws SmartyPluginException
     *
     */
    protected function checkParameters(array $params): array
    {
        $codes = $this->getParam($params, 'code');
        $accesses = $this->getNormalizedParam($params, 'access');
        $accessOr = $this->getParam($params, 'access_or', false);
        $name = $this->getNormalizedParam($params, 'name');
        $entityId = $this->getNormalizedParam($params, 'entityId', null);

        if ($codes === null || $accesses === null) {
            throw new SmartyPluginException('Checking acl requires code and access parameters');
        }

        return [$codes, $accesses, $accessOr, $entityId, $name];
    }
}
