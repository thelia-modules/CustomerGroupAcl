<?php

namespace CustomerGroupAcl\Form;

use CustomerGroupAcl\CustomerGroupAcl;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;
use Thelia\Model\ModuleQuery;

/**
 * Form for an ACL.
 */
class AclForm extends BaseForm
{
    public static function getName(): string
    {
        return 'acl_form';
    }

    protected function buildForm(): void
    {
        $activeModules = ModuleQuery::create()
            ->filterByActivate(1)
            ->orderByCode(Criteria::ASC)
            ->find();

        $this->formBuilder
            ->add(
                'id',
                IntegerType::class,
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
                ChoiceType::class,
                [
                    'choices' => $activeModules->toKeyValue('code', 'id'),
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
                TextType::class,
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
                TextType::class,
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
                TextType::class,
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
                TextType::class,
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
