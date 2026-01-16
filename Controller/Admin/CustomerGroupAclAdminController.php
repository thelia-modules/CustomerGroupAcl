<?php

namespace CustomerGroupAcl\Controller\Admin;

use CustomerGroupAcl\CustomerGroupAcl;
use CustomerGroupAcl\Event\AclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvent;
use CustomerGroupAcl\Event\CustomerGroupAclEvents;
use CustomerGroupAcl\Form\AclForm;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;

/**
 * Controller for the administration of ACLs.
 */
class CustomerGroupAclAdminController extends BaseAdminController
{
    public function __construct(protected EventDispatcherInterface $eventDispatcher)
    {
    }

    public function aclUpdateAction(ParserContext $parserContext)
    {
        if (null !== $response =
                $this->checkAuth(AdminResources::MODULE, 'CustomerGroupAcl', AccessManager::CREATE)
        ) {
            return $response;
        }

        $form = $this->createForm(AclForm::getName());

        try {
            $formValidate = $this->validateForm($form);

            $event = new AclEvent(
                $formValidate->get('code')->getData(),
                $formValidate->get('module_id')->getData(),
                $formValidate->get('locale')->getData(),
                $formValidate->get('title')->getData(),
                $formValidate->get('description')->getData(),
                ($formValidate->get('id')->getData() != null) ? $formValidate->get('id')->getData() : null
            );

            $this->eventDispatcher->dispatch($event, CustomerGroupAclEvents::ACL_UPDATE);

            return $this->generateRedirectFromRoute(
                'admin.module.configure',
                [],
                [
                    'module_code' => CustomerGroupAcl::getModuleCode(),
                ]
            );
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
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
        $this->eventDispatcher->dispatch($event,CustomerGroupAclEvents::CUSTOMER_GROUP_ACL_UPDATE);
    }
}
