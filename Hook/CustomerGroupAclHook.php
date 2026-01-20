<?php

namespace CustomerGroupAcl\Hook;

use CustomerGroupAcl\CustomerGroupAcl;
use Thelia\Core\Event\Hook\HookRenderBlockEvent;
use Thelia\Core\Hook\BaseHook;
use Thelia\Tools\URL;

/**
 * Hooks for the CustomerGroupAcl module.
 */
class CustomerGroupAclHook extends BaseHook
{
    /**
     * Insert a link to the ACL configuration page in the tool menu.
     * @param HookRenderBlockEvent $event Render event.
     */
    public function customerGroupAclHookTool(HookRenderBlockEvent $event): void
    {
        $event->add([
            "url" => URL::getInstance()->absoluteUrl("/admin/module/CustomerGroupAcl"),
            "title" => $this->trans(
                "Customer Group Acl",
                [],
                CustomerGroupAcl::DOMAIN_MESSAGE
            )
        ]);
    }
}
