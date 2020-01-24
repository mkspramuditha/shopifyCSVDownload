<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopifyCheckout
 *
 * @ORM\Table(name="shopify_checkout",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(name="shop",
 *            columns={"number", "shop"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopifyCheckoutRepository")
 */
class ShopifyCheckout
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
     * @ORM\Column(name="shop", type="integer", nullable=false)
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=255)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="abandonedDate", type="string", length=255)
     */
    private $abandonedDate;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="string", length=255)
     */
    private $updatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="acceptMarketing", type="boolean")
     */
    private $acceptMarketing;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="customerPhone", type="string", length=255, nullable=true)
     */
    private $customerPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="shippingPhone", type="string", length=255, nullable=true)
     */
    private $shippingPhone;


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
     * Set number
     *
     * @param string $number
     *
     * @return ShopifyCheckout
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return ShopifyCheckout
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set abandonedDate
     *
     * @param string $abandonedDate
     *
     * @return ShopifyCheckout
     */
    public function setAbandonedDate($abandonedDate)
    {
        $this->abandonedDate = $abandonedDate;

        return $this;
    }

    /**
     * Get abandonedDate
     *
     * @return string
     */
    public function getAbandonedDate()
    {
        return $this->abandonedDate;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return ShopifyCheckout
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return ShopifyCheckout
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ShopifyCheckout
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return ShopifyCheckout
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set customerPhone
     *
     * @param string $customerPhone
     *
     * @return ShopifyCheckout
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }

    /**
     * Get customerPhone
     *
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }

    /**
     * Set shippingPhone
     *
     * @param string $shippingPhone
     *
     * @return ShopifyCheckout
     */
    public function setShippingPhone($shippingPhone)
    {
        $this->shippingPhone = $shippingPhone;

        return $this;
    }

    /**
     * Get shippingPhone
     *
     * @return string
     */
    public function getShippingPhone()
    {
        return $this->shippingPhone;
    }

    /**
     * Set acceptMarketing
     *
     * @param boolean $acceptMarketing
     *
     * @return ShopifyCheckout
     */
    public function setAcceptMarketing($acceptMarketing)
    {
        $this->acceptMarketing = $acceptMarketing;

        return $this;
    }

    /**
     * Get acceptMarketing
     *
     * @return boolean
     */
    public function getAcceptMarketing()
    {
        return $this->acceptMarketing;
    }

    /**
     * Set updatedAt
     *
     * @param string $updatedAt
     *
     * @return ShopifyCheckout
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set shop
     *
     * @param integer $shop
     *
     * @return ShopifyCheckout
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop
     *
     * @return integer
     */
    public function getShop()
    {
        return $this->shop;
    }
}
