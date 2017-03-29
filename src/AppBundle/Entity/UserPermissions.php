<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPermissions
 *
 * @ORM\Table(name="user_permissions", indexes={@ORM\Index(name="IDX_84F605FAFED90CCA", columns={"permission_id"}), @ORM\Index(name="IDX_84F605FAA76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class UserPermissions
{
    /**
     * @var integer
     *
     * @ORM\Column(name="user_permission_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="user_permissions_user_permission_id_seq", allocationSize=1, initialValue=1)
     */
    private $userPermissionId;

    /**
     * @var \AppBundle\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * @var \AppBundle\Entity\Permissions
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Permissions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="permission_id", referencedColumnName="permission_id")
     * })
     */
    private $permission;



    /**
     * Get userPermissionId
     *
     * @return integer
     */
    public function getUserPermissionId()
    {
        return $this->userPermissionId;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\Users $user
     *
     * @return UserPermissions
     */
    public function setUser(\AppBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set permission
     *
     * @param \AppBundle\Entity\Permissions $permission
     *
     * @return UserPermissions
     */
    public function setPermission(\AppBundle\Entity\Permissions $permission = null)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return \AppBundle\Entity\Permissions
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
