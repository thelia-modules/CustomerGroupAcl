<?php

namespace CustomerGroupAcl\Controller\Admin;

use CustomerGroupAcl\CustomerGroupAcl;
use CustomerGroupAcl\Event\AclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\Form\AclForm;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;

/**
 * Controller for the administration of ACLs.
 */
class CustomerGroupAclAdminController extends BaseAdminController
{
    public function aclUpdateAction()
    {
        if (null !== $response =
                $this->checkAuth(AdminResources::MODULE, 'CustomerGroupAcl', AccessManager::CREATE)
        ) {
            return $response;
        }

        $form = new AclForm($this->getRequest());

        // TODO: catch possible exception
        $formValidate = $this->validateForm($form);

        $event = new AclEvent(
            $formValidate->get('code')->getData(),
            $formValidate->get('module_id')->getData(),
            $formValidate->get('locale')->getData(),
            $formValidate->get('title')->getData(),
            $formValidate->get('description')->getData(),
            ($formValidate->get('id')->getData() != null) ? $formValidate->get('id')->getData() : null
        );

        $this->dispatch(CustomerGroupAclEvents::ACL_UPDATE, $event);

        return $this->generateRedirectFromRoute(
            'admin.module.configure',
            [],
            [
                'module_code' => CustomerGroupAcl::getModuleCode(),
            ]
        );
    }

    public function toggleActivationAction($acl_id, $customer_group_id, $type)
    {
        if (null !== $response =
                $this->checkAuth(AdminResources::MODULE, 'CustomerGroupAcl', AccessManager::UPDATE)
        ) {
            return $response;
        }

        $event = new CustomerGroupAclEvent(
            $acl_id,
            $customer_group_id,
            $type
        );

        // TODO: catch possible exception
        $this->dispatch(CustomerGroupAclEvents::CUSTOMER_GROUP_ACL_UPDATE, $event);
    }
}
