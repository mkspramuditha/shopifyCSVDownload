<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shop
 *
 * @ORM\Table(name="shop")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopRepository")
 */
class Shop
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="authorization", type="string", length=255)
     */
    private $authorization;

    /**
     * @var string
     *
     * @ORM\Column(name="country_select", type="boolean", nullable=true)
     */
    private $countrySelect;

    /**
     * @var string
     *
     * @ORM\Column(name="number_correction", type="boolean", nullable=true)
     */
    private $numberCorrection;

    /**
     * @var string
     *
     * @ORM\Column(name="all_orders_sheet_name", type="string", nullable=true)
     */
    private $allOrdersSheetName;





    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Shop
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Shop
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set authorization
     *
     * @param string $authorization
     *
     * @return Shop
     */
    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;

        return $this;
    }

    /**
     * Get authorization
     *
     * @return string
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Set countrySelect
     *
     * @param boolean $countrySelect
     *
     * @return Shop
     */
    public function setCountrySelect($countrySelect)
    {
        $this->countrySelect = $countrySelect;

        return $this;
    }

    /**
     * Get countrySelect
     *
     * @return boolean
     */
    public function getCountrySelect()
    {
        return $this->countrySelect;
    }

    /**
     * Set numberCorrection
     *
     * @param boolean $numberCorrection
     *
     * @return Shop
     */
    public function setNumberCorrection($numberCorrection)
    {
        $this->numberCorrection = $numberCorrection;

        return $this;
    }

    /**
     * Get numberCorrection
     *
     * @return boolean
     */
    public function getNumberCorrection()
    {
        return $this->numberCorrection;
    }

    /**
     * Set allOrdersSheetName
     *
     * @param boolean $allOrdersSheetName
     *
     * @return Shop
     */
    public function setAllOrdersSheetName($allOrdersSheetName)
    {
        $this->allOrdersSheetName = $allOrdersSheetName;

        return $this;
    }

    /**
     * Get allOrdersSheetName
     *
     * @return boolean
     */
    public function getAllOrdersSheetName()
    {
        return $this->allOrdersSheetName;
    }
}
