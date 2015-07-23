# CustomerGroupAcl

Extends the CustomerGroup module with an access control list mechanism that can be used to allow or deny access
to some resource to a customer group.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and make sure that the name of the module is CustomerGroupAcl.
* Activate it in your Thelia administration panel

### Composer

Add it in your main Thelia composer.json file

```
composer require thelia/customer-group-acl-module:~0.1
```

## Update

### 0.1.3

* Change acl data table to add class_name, the class name to check specific object acl ;
* Dispatch `CheckAclEvent_[resource]` ;
* Change checkAcl definition to checkAcl($resources, $accesses, $accessOr = false, $entityId = null, $dispatchEvent = false) :
    * $entityId, object identifying, if isset resources must be alone,
    * $dispatchEvent, if is set, CheckAclEvent are dispatch, default false ;

Execute `setup/update-0.1.2-0.1.3.sql` script.

## Configuration

### Using the configuration file

Modules that use customer group ACLs must define them in the `acl.xml` file in the module configuration directory.
The ACLs will be created when the module is activated.

Since ACLs are applied to customer group, you must have already have created some in order to use them
(see the CustomerGroup module documentation).
In this exemple, we will assume that the `client`, `vip`, `also-vip-1` and `also-vip-2` groups exists.

```XML
<?xml version="1.0" encoding="UTF-8" ?>
<config xmlns="http://thelia.net/acl"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://thelia.net/acl http://thelia.net/acl/acl.xsd">
      <acls>
          <acl code="vip-perks">
              <descriptive locale="en_US">
                  <title>VIP perks</title>
                  <description>Special VIP perks.</description>
              </descriptive>
              <descriptive locale="en_US">
                  <title>Avantages VIP</title>
                  <description>Avantages spéciaux VIP.</description>
              </descriptive>
          </acl>
          <customergroups>
              <customergroup group="vip">
                  <customergroupacl aclcode="vip-perks">
                      <access right="VIEW"/>
                  </customergroupacl>
              </customergroup>

              <customergroup group="extra-vip-1">
                  <extends-customergroupacl
                      group="vip"
                  />
              </customergroup>

              <customergroup group="extra-vip-2">
                  <extends-customergroupacl
                      group="vip"
                      aclcode="vip-perks"
                  />
              </customergroup>
          </customergroups>
      <acls>
</config>
```

Here we define an ACL resource `vip-perks` and grant `VIEW` type access to this resource to the `vip` group.
The `extra-vip-1` group is set to have the same accesses as the `vip` group.
The `extra-vip-2` group is also set to have the same rights as the `vip` group, but only for the `vip-perks` ACL resource.

### Using the back office 

ACLs and group accesses can also be configured in the Thelia back office.
A link to the configuration page is available in the **Tools** menu.

### Access types

The available access types are defined in the Thelia access manager:

- `VIEW`
- `CREATE`
- `UPDATE`
- `DELETE`

Additionally, the `ALL` access can be used to grant all available accesses.

## Check group access

### PHP

The `customer_group_acl.tool` can be used to check ACL access.

```PHP
$aclTool = $container->get("customer_group_acl.tool");

// simple check
$aclTool->checkAcl("vip-perks", AccessManager::VIEW);

// resources and accesess arguments can be a single value or an array of values
$aclTool->checkAcl(
    [
        "myAcl1",
        "myAcl2",
    ],
    [
        AccessManager::VIEW,
        AccessManager::CREATE,
    ]
);

// by default, checks that at all accesses are granted
// but you can also check that only at least one access is granted
$aclTool->checkAcl(
    "myAcl",
    [
        AccessManager::VIEW,
        AccessManager::CREATE,
    ],
    true
);
```

### Smarty

Smarty plugins are provided to check ACL access in templates.

As with the PHP function, you can check multiple resources and accesses at once, and optionally require only one access.

#### Simple check

The `check_acl` function will throw an exception if the required access(es) are not granted.

```smarty
{check_acl code='vip-perks' access='view'}

{check_acl code='vip-perks,myAcl' access='view,create'}

{check_acl code='myAcl' access='view,create,update' access_or=true}
```

#### Block check

The `acl` and `elseacl` tags can also be used to check ACL accesses with a block syntax.

```smarty
{acl name='acl-check' code='vip-perks' access='view'}
    Here is some super secret stuff !
{/acl}

{elseacl rel='acl-check'}
   Get VIP access for more cool stuff !
{/elseacl}
```

## Loop

### acl

This loop list ACLs.

#### Input arguments

|Argument  |Description                 |
|----------|----------------------------|
|**id**    | Id or list of ACL ids.     |
|**module**| Id or list of module ids.  |
|**code**  | Code or list of ACL codes. |
|**order** | Order of the results.      |
|**lang**  | Locale of the results.     |

**order** can be one of:

- `id` (default)
- `module`
- `module_reverse`

#### Output arguments

|Variable    |Description                              |
|------------|-----------------------------------------|
|$ACL_ID     | ACL id.                                 |
|$MODULE_ID  | Id of the module defining the ACL.      |
|$CODE       | ACL code.                               |
|$TITLE      | ACL title in the selected locale.       |
|$DESCRIPTION| ACL description in the selected locale. |

### customer-group-acl

This loop list customer group access grants.

#### Input arguments

|Argument          |Description            |
|------------------|-----------------------|
|**acl**           | Id or list of ACL ids.|
|**customer_group**| Id or list of customer group ids. |
|**acl_type**      | Access type or list of access types. |
|**activate**      | Whether to only list active access grants (`true`) or not (`false`) or both (`*`). |

#### Output arguments

|Variable          |Description                          |
|------------------|-------------------------------------|
|$ACL_ID           | ACL id.                             |
|$CUSTOMER_GROUP_ID| Customer group id.                  |
|$TYPE             | Access type.                        |
|$ACTIVATE         | Whether the access grant is active. |


