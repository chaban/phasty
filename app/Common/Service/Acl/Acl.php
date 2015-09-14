<?php
namespace Phasty\Common\Service\Acl;

use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Config;
use Phalcon\Acl\Exception;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;

/**
 * Acl\Acl
 *
 *
 */
class Acl extends Component
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Memory
     */
    private $acl;

    /**
     * Array of defined role objects.
     *
     * @var Role[]
     */
    private $roles = array();

    /**
     * path to cache file for acl object
     * @var string
     */
    private $cacheFilePath;

    /**
     * Creates configured instance of acl.
     *
     * @throws Exception      If configuration is wrong
     */
    public function __construct(){
        $this->cacheFilePath = BASE_DIR . '/var/cache/acl/data.txt';
        $this->config = include BASE_DIR . '/config/acl.php';
        if (!is_numeric($this->config->get('defaultAction'))) {
            throw new Exception('Key "defaultAction" must exist and must be of numeric value.');
        }
        $this->getAcl();
        //$this->rebuild();
    }

    /**
     * Check is user allowed
     * @param string $role
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function isAllowed($role, $controller, $action){
        return $this->acl->isAllowed($role, $controller, $action);
    }

    /**
     * Adds resources from config to acl object.
     *
     * @return $this
     * @throws Exception
     */
    protected function addResources()
    {
        if (!(array)$this->config->get('resource')) {
            throw new Exception('Key "resource" must exist and must be traversable.');
        }

        // resources
        foreach ($this->config->resource as $name => $resource) {
            $actions = (array) $resource->get('actions');
            if (!$actions) {
                $actions = null;
            }
            $this->acl->addResource(
                $this->makeResource($name, $resource->description),
                $actions
            );
        }

        return $this;
    }

    /**
     * Adds role from config to acl object.
     *
     * @return $this
     * @throws Exception
     */
    protected function addRoles()
    {
        if (!(array)$this->config->get('role')) {
            throw new Exception('Key "role" must exist and must be traversable.');
        }

        foreach ($this->config->role as $role => $rules) {
            $this->roles[$role] = $this->makeRole($role, $rules->get('description'));
            $this->addRole($role, $rules);
            $this->addAccessRulesToRole($role, $rules);
        }

        return $this;
    }

    /**
     * Adds access rules to role.
     *
     * @param string          $role  role
     * @param Config $rules rules
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function addAccessRulesToRole($role, Config $rules)
    {
        foreach ($rules as $method => $rules) {
            // skip not wanted rules
            if (in_array($method, array('inherit', 'description'))) {
                continue;
            }

            foreach ($rules as $controller => $actionRules) {
                $actions = (array) $actionRules->get('actions');
                if (!$actions) {
                    throw new Exception(
                        'Key "actions" must exist and must be traversable.'
                    );
                }
                if (!in_array($method, array('allow', 'deny'))) {
                    throw new Exception(sprintf(
                        'Wrong access method given. Expected "allow" or "deny" but "%s" was set.',
                        $method
                    ));
                }
                $this->acl->{$method}($role, $controller, $actions);
            }
        }

        return $this;
    }

    /**
     * Add role to acl.
     *
     * @param string          $role  role
     * @param Config $rules rules
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function addRole($role, Config $rules)
    {
        // role has inheritance ?
        if ($rules->get('inherit')) {
            // role exists?
            if (!array_key_exists($rules->inherit, $this->roles)) {
                throw new Exception(sprintf(
                    'Role "%s" cannot inherit non-existent role "%s".
                     Either such role does not exist or it is set to be inherited before it is actually defined.',
                    $role,
                    $rules->inherit
                ));
            }
            $this->acl->addRole($this->roles[$role], $this->roles[$rules->inherit]);
        } else {
            $this->acl->addRole($this->roles[$role]);
        }

        return $this;
    }

    /**
     * Creates acl resource.
     *
     * @param string      $name        resource name
     * @param string|null $description optional, resource description
     *
     * @return Resource
     */
    protected function makeResource($name, $description = null)
    {
        return new Resource(
            $name,
            $description
        );
    }

    /**
     * Creates acl role.
     *
     * @param string      $role        role name
     * @param string|null $description optional, description
     *
     * @return Role
     */
    protected function makeRole($role, $description = null)
    {
        return new Role($role, $description);
    }

	/**
	 * Returns the ACL list
	 *
	 */
	private function getAcl()
	{
		//Check if the ACL is already created
		if (is_object($this->acl))
		{
			return $this;
		}

		//Check if the ACL is in APC
		if (function_exists('apc_fetch'))
		{
			$acl = apc_fetch('phasty-admin_module-acl');
			if (is_object($acl))
			{
				$this->acl = $acl;
				return $this;
			}
		}

		//Check if the ACL is already generated
		if (!file_exists($this->cacheFilePath))
		{
			$this->rebuild();
			return $this;
		}

		//Get the ACL from the data file
		$data = file_get_contents($this->cacheFilePath);
		$acl = unserialize($data);
        if (!is_object($acl))
        {
            $this->rebuild();
            return $this;
        }else{
            $this->acl = $acl;
        }

		//Store the ACL in APC
		if (function_exists('apc_store'))
		{
			apc_store('phasty-admin_module-acl', $this->acl);
		}

		return $this;
	}

	/**
	 * Rebuils the access list
	 *
	 */
	private function rebuild()
	{
        $this->acl = new Memory();
        $this->acl->setDefaultAction((int) $this->config->defaultAction);
        $this->addResources();
        $this->addRoles();

		file_put_contents($this->cacheFilePath, serialize($this->acl));

		//Store the ACL in APC
		if (function_exists('apc_store'))
		{
			apc_store('phasty-admin_module-acl', $this->acl);
		}

		return $this;
	}

}
