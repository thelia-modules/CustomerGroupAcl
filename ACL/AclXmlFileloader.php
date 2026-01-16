<?php

namespace CustomerGroupAcl\ACL;

use CustomerGroup\Model\CustomerGroup;
use CustomerGroup\Model\CustomerGroupQuery;
use CustomerGroupAcl\CustomerGroupAcl;
use CustomerGroupAcl\Manager\CustomerGroupAclAccessManager;
use CustomerGroupAcl\Model\Acl;
use CustomerGroupAcl\Model\AclQuery;
use CustomerGroupAcl\Model\CustomerGroupAclQuery;
use Propel\Runtime\Exception\PropelException;
use SimpleXMLElement;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Module;

/**
 * Loader for the XML ACL configuration files
 */
class AclXmlFileLoader
{
    /** @var Translator */
    protected Translator $translator;

    /**
     * Map of the access types => access type code
     * @var array
     */
    protected array $accessPows;

    /**
     * Path of the file being processed
     * @var string
     */
    protected string $xmlFilePath;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        $this->accessPows = CustomerGroupAclAccessManager::getAccessPows();
    }

    /**
     * Search the acl.xml file in the module folder and call methods to parse it
     * @param Module $module The enabled module
     * @throws \Exception
     */
    public function load(Module $module): void
    {
        $this->xmlFilePath = $module->getAbsoluteConfigPath(). DS ."acl.xml";

        if (file_exists($this->xmlFilePath)) {
            $xml = $this->parseFile($this->xmlFilePath);

            $xml->registerXPathNamespace('config', 'https://thelia.net/acl');

            $this->parseAcls($xml, $module);

            $this->parseCustomerGroups($xml);
        }
    }

    /**
     * Parse the acl in acl.xml file
     * @param SimpleXMLElement $xml The xml parsed by parseFile
     * @param Module $module The enabled module
     * @throws PropelException
     */
    protected function parseAcls(SimpleXMLElement $xml, Module $module)
    {
        //If there are no acl node continue to parse the xml
        $acls = $xml->xpath('//config:acls/config:acl');
        if (false === $acls) {
            return;
        }

        //Add the acl if they no exists and parse his descriptive
        /** @var SimpleXMLElement $acl */
        foreach ($acls as $acl) {
            $code = $acl->attributes("code");

            if (AclQuery::create()->findOneByCode($code)) {
                continue;
            }

            $newAcl = new Acl();
            $newAcl
                ->setCode($code)
                ->setModuleId($module->getId());

            $newAcl->save();

            $this->parseDescriptives($acl);
        }
    }

    /**
     * Parse all descriptive of an acl
     *
     * @param SimpleXMLElement $acl The acl node
     * @throws PropelException
     */
    protected function parseDescriptives(SimpleXMLElement $acl)
    {
        /** @var SimpleXMLElement $descriptive */
        foreach ($acl->children() as $descriptive) {
            $aclI18n = AclQuery::create()->findOneByCode($acl->attributes("code"));

            $aclI18n->setLocale($descriptive->attributes('locale'));

            if ($title = $descriptive->attributes('title')) {
                $aclI18n->setTitle($title[0]);
            }

            if ($description = $descriptive->attributes('description')) {
                $aclI18n->setDescription($description[0]);
            }

            $aclI18n->save();
        }
    }

    /**
     * Browse the customer group and parse their children
     *
     * @param SimpleXMLElement $xml * The xml parsed by parseFile
     *
     * @throws PropelException
     * @throws \Exception
     */
    protected function parseCustomerGroups(SimpleXMLElement $xml): void
    {
        //If there are no customer groups node continue to parse the XML
        $customerGroups = $xml->xpath('//config:customergroups/config:customergroup');
        if (empty($customerGroups)) {
            return;
        }

        /** @var SimpleXMLElement $customerGroup */
        foreach ($customerGroups as $customerGroup) {
            $customerGroupName = $customerGroup->attributes('group');
            $customerGroupModel = CustomerGroupQuery::create()->findOneByCode($customerGroupName);

            /** @var SimpleXMLElement $customerGroupChild */
            foreach ($customerGroup->children() as $customerGroupChild) {
                switch ($customerGroupChild->getName()) {
                    case 'extends-customergroupacl':
                        $this->parseExtendCustomerGroupAcl($customerGroupChild, $customerGroupModel, $xml);
                        break;
                    case 'customergroupacl':
                        $this->parseCustomerGroupAcl($customerGroupChild, $customerGroupModel);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * Parse one extend-customergroupacl
     *
     * @param SimpleXMLElement $extendCustomerGroupAcl
     * @param CustomerGroup $customerGroupModel
     * @param SimpleXMLElement $xml The global XML for retrieve the parent
     * @throws \Exception
     * @throws \Propel\Runtime\Exception\PropelException
     */
    protected function parseExtendCustomerGroupAcl(
        SimpleXMLElement $extendCustomerGroupAcl,
        CustomerGroup $customerGroupModel,
        SimpleXMLElement $xml
    ): void
    {
        $extendCustomerGroupCode = $extendCustomerGroupAcl->attributes('group');

        //If an aclcode is specified in extends
        $aclcode = $extendCustomerGroupAcl->attributes('aclcode');
        if ($aclcode !== null && $aclcode !== '') {
            $extendCustomerGroupAcls =$xml->xpath(
                '//config:customergroups'
                .'/config:customergroup[@group="'.$extendCustomerGroupCode.'"]'
                .'/config:customergroupacl[@aclcode="'.$aclcode.'"]'
            );

            //Parse acls who match to customer group and aclcode given in extends
            /** @var SimpleXMLElement $extendCustomerGroupAcl */
            foreach ($extendCustomerGroupAcls as $extendCustomerGroupAcl) {
                $this->parseCustomerGroupAcl($extendCustomerGroupAcl, $customerGroupModel);
            }

            //Don't add the other acl
            return;
        }

        //Get the customerGroups who matches to the 'group' attribute given in the extends-customergroupacl node
        $extendCustomerGroups = $xml->xpath(
            '//config:customergroups'
            .'/config:customergroup[@group="'.$extendCustomerGroupCode.'"]'
        );

        /** @var SimpleXMLElement  $extendsCustomerGroup */
        foreach ($extendCustomerGroups as $extendCustomerGroup) {
            /** @var SimpleXMLElement $extendCustomerGroupChild */
            foreach ($extendCustomerGroup->children() as $extendCustomerGroupChild) {
                switch ($extendCustomerGroupChild->getName()) {
                    case 'extends-customergroupacl':
                        $this->parseExtendCustomerGroupAcl($extendCustomerGroupChild, $customerGroupModel, $xml);
                        break;
                    case 'customergroupacl':
                        $this->parseCustomerGroupAcl($extendCustomerGroupChild, $customerGroupModel);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * Parse one customergroupacl
     *
     * @param SimpleXMLElement $customerGroupAcl A customergroupacl
     * @param CustomerGroup $customerGroupModel CustomerGroup propel object for who the access have to be created
     *
     * @throws \Exception When an error is detected on xml file (customer group or acl don't exist)
     */
    protected function parseCustomerGroupAcl(SimpleXMLElement $customerGroupAcl, CustomerGroup $customerGroupModel): void
    {
        $acl = AclQuery::create()->findOneByCode($customerGroupAcl->attributes('aclcode'));

        if (null === $acl) {
            throw new \Exception(
                $this->translator->trans(
                    "Error in %a file the acl '%s' doesn't exist",
                    ['%a' => $this->xmlFilePath, '%s' => $customerGroupAcl->attributes('aclcode')],
                    CustomerGroupAcl::DOMAIN_MESSAGE
                )
            );
        }

        $this->parseAccesses($customerGroupAcl->children(), $acl, $customerGroupModel);
    }

    /**
     * Parses accesses of one customer group
     * Browse access and add them if not already existing
     *
     * @param $accesses * An array of all the access in the customergroupacl who is actually parsed
     * @param Acl $acl Acl propel object for what the access has to be created
     * @param CustomerGroup $customerGroup CustomerGroup propel an object for who the access have to be created
     *
     * @throws PropelException
     */
    protected function parseAccesses($accesses, Acl $acl, CustomerGroup $customerGroup): void
    {
        /** @var SimpleXMLElement $access */
        foreach ($accesses as $access) {
            if ("access" !== $access->getName()) {
                return;
            }

            if ("ALL" === $access->attributes('right')) {
                //Add all access if not already exists
                foreach ($this->accessPows as $right) {
                    $customerGroupAcl = CustomerGroupAclQuery::create()
                        ->filterByAcl($acl)
                        ->filterByCustomerGroup($customerGroup)
                        ->filterByType($right)
                        ->findOneOrCreate();

                    if (0 !== $customerGroupAcl->getActivate()) {
                        $customerGroupAcl
                            ->setActivate(1)
                            ->save();
                    }
                }

                return;
            }

            //Add specific access if not already exists
            $customerGroupAcl = CustomerGroupAclQuery::create()
                ->filterByAcl($acl)
                ->filterByCustomerGroup($customerGroup)
                ->filterByType($this->accessPows[$access->attributes('right')])
                ->findOneOrCreate();

            if (0 !== $customerGroupAcl->getActivate()) {
                $customerGroupAcl
                    ->setActivate(1)
                    ->save();
            }
        }
    }


    /**
     * Parses an XML file.
     *
     * @param string $file Path to a file
     *
     * @return SimpleXMLElement
     *
     * @throws \Exception When loading of an XML file returns an error
     */
    protected function parseFile(string $file): SimpleXMLElement
    {
        try {
            $dom = XmlUtils::loadFile($file, [$this, 'validateSchema']);
        } catch (\InvalidArgumentException $e) {
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

        return simplexml_import_dom($dom, 'Symfony\\Component\\DependencyInjection\\SimpleXMLElement');
    }

    /**
     * Validates a documents XML schema.
     *
     * @param \DOMDocument $dom
     *
     * @return Boolean
     *
     * @throws RuntimeException When extension references a non-existent XSD file
     */
    public function validateSchema(\DOMDocument $dom)
    {
        $schemaLocations = [
            'http://thelia.net/acl' => str_replace('\\', '/', __DIR__.'/acl.xsd')
        ];

        $tmpfiles = [];
        $imports = '';
        foreach ($schemaLocations as $namespace => $location) {
            $parts = explode('/', $location);
            if (0 === stripos($location, 'phar://')) {
                $tmpfile = tempnam(sys_get_temp_dir(), 'sf2');
                if ($tmpfile) {
                    copy($location, $tmpfile);
                    $tmpfiles[] = $tmpfile;
                    $parts = explode('/', str_replace('\\', '/', $tmpfile));
                }
            }
            $drive = '\\' === DIRECTORY_SEPARATOR ? array_shift($parts).'/' : '';
            $location = 'file:///'.$drive.implode('/', array_map('rawurlencode', $parts));

            $imports .= sprintf('  <xsd:import namespace="%s" schemaLocation="%s" />'."\n", $namespace, $location);
        }

        $source = <<<EOF
<?xml version="1.0" encoding="utf-8" ?>
<xsd:schema xmlns="http://symfony.com/schema"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    targetNamespace="http://symfony.com/schema"
    elementFormDefault="qualified">

    <xsd:import namespace="http://www.w3.org/XML/1998/namespace"/>
$imports
</xsd:schema>
EOF
        ;

        $valid = @$dom->schemaValidateSource($source);

        foreach ($tmpfiles as $tmpfile) {
            @unlink($tmpfile);
        }

        return $valid;
    }
}
