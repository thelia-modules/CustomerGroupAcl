<?php

namespace CustomerGroupAcl\Form;

use CustomerGroupAcl\CustomerGroupAcl;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ModuleQuery;

/**
 * Form for an ACL.
 */
class AclForm extends BaseForm
{
    public function getName()
    {
        return 'acl_form';
    }

    protected function buildForm()
    {
        $activeModules = ModuleQuery::create()
            ->filterByActivate(1)
            ->orderByCode(Criteria::ASC)
            ->find();

        $this->formBuilder
            ->add(
                'id',
                'integer',
                [
                    'label' => '',
                    'label_attr' => [
                        'for' => 'id'
                    ],
                    'required' => false
                ]
            )
            ->add(
                'module_id',
                'choice',
                [
                    'choices' => $activeModules->toKeyValue('id', 'code'),
                    'label' => Translator::getInstance()->trans(
                        'Concerned module',
                        [],
                        CustomerGroupAcl::DOMAIN_MESSAGE
                    ),
                    'label_attr' => [
                        'for' => 'module_id'
                    ],
                    'required' => true
                ]
            )
            ->add(
                'code',
                'text',
                [
                    'label' => Translator::getInstance()->trans(
                        'Code',
                        [],
                        CustomerGroupAcl::DOMAIN_MESSAGE
                    ),
                    'label_attr' => [
                        'for' => 'code'
                    ],
                    'constraints' => [
                        new NotBlank()
                    ],
                    'required' => true
                ]
            )
            ->add(
                'locale',
                'text',
                [
                    'label' => '',
                    'label_attr' => [
                        'for' => 'locale'
                    ],
                    'constraints' => [
                        new NotBlank()
                    ],
                    'required' => true
                ]
            )
            ->add(
                'title',
                'text',
                [
                    'label' => Translator::getInstance()->trans(
                        'Title',
                        [],
                        CustomerGroupAcl::DOMAIN_MESSAGE
                    ),
                    'label_attr' => [
                        'for' => 'title'
                    ],
                    'constraints' => [
                        new NotBlank()
                    ],
                    'required' => true
                ]
            )
            ->add(
                'description',
                'text',
                [
                    'label' => Translator::getInstance()->trans(
                        'Description',
                        [],
                        CustomerGroupAcl::DOMAIN_MESSAGE
                    ),
                    'label_attr' => [
                        'for' => 'description'
                    ],
                    'required' => false
                ]
            );
    }
}
