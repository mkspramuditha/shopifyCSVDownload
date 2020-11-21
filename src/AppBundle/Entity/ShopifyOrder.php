<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopifyOrder
 *
 * @ORM\Table(name="shopify_order",
 *       uniqueConstraints={
 *        @ORM\UniqueConstraint(name="shop",
 *            columns={"number", "shop"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShopifyOrderRepository")
 */
class ShopifyOrder
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
     * @var int
     *
     * @ORM\Column(name="number", type="integer")
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="string")
     */
    private $orderId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_name", type="string")
     */
    private $orderName;

    /**
     * @var int
     *
     * @ORM\Column(name="order_url", type="string",length=500)
     */
    private $orderUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="tags", type="string",length=500, nullable=true)
     */
    private $tags;

    /**
     * @var int
     *
     * @ORM\Column(name="waybill_id", type="string",length=500, nullable=true)
     */
    private $waybillId;

    /**
     * @var int
     *
     * @ORM\Column(name="tracking_url", type="string",length=500, nullable=true)
     */
    private $trackingUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="created_at", type="string", length=255)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_at", type="string", length=255)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="cancelled_at", type="string", length=255, nullable=true)
     */
    private $cancelledAt;

    /**
     * @var string
     *
     * @ORM\Column(name="fulfillment_status", type="string", length=255, nullable=true)
     */
    private $fulfillmentStatus;


    /**
     * @var string
     *
     * @ORM\Column(name="financial_status", type="string", length=255, nullable=true)
     */
    private $financialStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_country", type="string", length=255, nullable=true)
     */
    private $shippingCountry;

    /**
     * @var bool
     *
     * @ORM\Column(name="acceptMarketing", type="boolean")
     */
    private $acceptMarketing;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="string")
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="customerId", type="string")
     */
    private $customerId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_count", type="integer")
     */
    private $orderCount;

    /**
     * @var int
     *
     * @ORM\Column(name="total_spend", type="float")
     */
    private $totalSpend;

    /**
     * @var int
     *
     * @ORM\Column(name="last_order_id", type="string")
     */
    private $lastOrderId;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="zip", type="string", length=255, nullable=true)
     */
    private $zip;

    /**
     * @var string
     *
     * @ORM\Column(name="order_address", type="text", length=255, nullable=true)
     */
    private $orderAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=255, nullable=true)
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="product_count", type="integer", nullable=true)
     */
    private $productCount;



    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_note", type="string", length=255, nullable=true)
     */
    private $customerNote;

    /**
     * @var string
     *
     * @ORM\Column(name="staff_note", type="string", length=255, nullable=true)
     */
    private $staffNote;

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
     * @param integer $number
     *
     * @return ShopifyOrder
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return ShopifyOrder
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param string $updatedAt
     *
     * @return ShopifyOrder
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
     * Set cancelledAt
     *
     * @param string $cancelledAt
     *
     * @return ShopifyOrder
     */
    public function setCancelledAt($cancelledAt)
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    /**
     * Get cancelledAt
     *
     * @return string
     */
    public function getCancelledAt()
    {
        return $this->cancelledAt;
    }

    /**
     * Set fulfillmentStatus
     *
     * @param string $fulfillmentStatus
     *
     * @return ShopifyOrder
     */
    public function setFulfillmentStatus($fulfillmentStatus)
    {
        $this->fulfillmentStatus = $fulfillmentStatus;

        return $this;
    }

    /**
     * Get fulfillmentStatus
     *
     * @return string
     */
    public function getFulfillmentStatus()
    {
        return $this->fulfillmentStatus;
    }

    /**
     * Set acceptMarketing
     *
     * @param boolean $acceptMarketing
     *
     * @return ShopifyOrder
     */
    public function setAcceptMarketing($acceptMarketing)
    {
        $this->acceptMarketing = $acceptMarketing;

        return $this;
    }

    /**
     * Get acceptMarketing
     *
     * @return bool
     */
    public function getAcceptMarketing()
    {
        return $this->acceptMarketing;
    }


    /**
     * Set customerId
     *
     * @param integer $customerId
     *
     * @return ShopifyOrder
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * Get customerId
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return ShopifyOrder
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
     * @return ShopifyOrder
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
     * @return ShopifyOrder
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
     * @return ShopifyOrder
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
     * @return ShopifyOrder
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
     * @return ShopifyOrder
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
     * Set amount
     *
     * @param string $amount
     *
     * @return ShopifyOrder
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
     * Set orderCount
     *
     * @param integer $orderCount
     *
     * @return ShopifyOrder
     */
    public function setOrderCount($orderCount)
    {
        $this->orderCount = $orderCount;

        return $this;
    }



    /**
     * Get lastOrderId
     *
     * @return integer
     */
    public function getLastOrderId()
    {
        return $this->lastOrderId;
    }

    /**
     * Get orderCount
     *
     * @return integer
     */
    public function getOrderCount()
    {
        return $this->orderCount;
    }

    /**
     * Set lastOrderId
     *
     * @param string $lastOrderId
     *
     * @return ShopifyOrder
     */
    public function setLastOrderId($lastOrderId)
    {
        $this->lastOrderId = $lastOrderId;

        return $this;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return ShopifyOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set shop
     *
     * @param integer $shop
     *
     * @return ShopifyOrder
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

    /**
     * Set shippingCountry
     *
     * @param string $shippingCountry
     *
     * @return ShopifyOrder
     */
    public function setShippingCountry($shippingCountry)
    {
        $this->shippingCountry = $shippingCountry;

        return $this;
    }

    /**
     * Get shippingCountry
     *
     * @return string
     */
    public function getShippingCountry()
    {
        return $this->shippingCountry;
    }

    /**
     * Set orderName
     *
     * @param string $orderName
     *
     * @return ShopifyOrder
     */
    public function setOrderName($orderName)
    {
        $this->orderName = $orderName;

        return $this;
    }

    /**
     * Get orderName
     *
     * @return string
     */
    public function getOrderName()
    {
        return $this->orderName;
    }

    /**
     * Set orderUrl
     *
     * @param string $orderUrl
     *
     * @return ShopifyOrder
     */
    public function setOrderUrl($orderUrl)
    {
        $this->orderUrl = $orderUrl;

        return $this;
    }

    /**
     * Get orderUrl
     *
     * @return string
     */
    public function getOrderUrl()
    {
        return $this->orderUrl;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return ShopifyOrder
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set waybillId
     *
     * @param string $waybillId
     *
     * @return ShopifyOrder
     */
    public function setWaybillId($waybillId)
    {
        $this->waybillId = $waybillId;

        return $this;
    }

    /**
     * Get waybillId
     *
     * @return string
     */
    public function getWaybillId()
    {
        return $this->waybillId;
    }

    /**
     * Set financialStatus
     *
     * @param string $financialStatus
     *
     * @return ShopifyOrder
     */
    public function setFinancialStatus($financialStatus)
    {
        $this->financialStatus = $financialStatus;

        return $this;
    }

    /**
     * Get financialStatus
     *
     * @return string
     */
    public function getFinancialStatus()
    {
        return $this->financialStatus;
    }

    /**
     * Set customerNote
     *
     * @param string $customerNote
     *
     * @return ShopifyOrder
     */
    public function setCustomerNote($customerNote)
    {
        $this->customerNote = $customerNote;

        return $this;
    }

    /**
     * Get customerNote
     *
     * @return string
     */
    public function getCustomerNote()
    {
        return $this->customerNote;
    }

    /**
     * Set staffNote
     *
     * @param string $staffNote
     *
     * @return ShopifyOrder
     */
    public function setStaffNote($staffNote)
    {
        $this->staffNote = $staffNote;

        return $this;
    }

    /**
     * Get staffNote
     *
     * @return string
     */
    public function getStaffNote()
    {
        return $this->staffNote;
    }

    /**
     * Set totalSpend
     *
     * @param float $totalSpend
     *
     * @return ShopifyOrder
     */
    public function setTotalSpend($totalSpend)
    {
        $this->totalSpend = $totalSpend;

        return $this;
    }

    /**
     * Get totalSpend
     *
     * @return float
     */
    public function getTotalSpend()
    {
        return $this->totalSpend;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return ShopifyOrder
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     *
     * @return ShopifyOrder
     */
    public function setZip($zip)
    {
        $this->zip = $zip;

        return $this;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Set orderAddress
     *
     * @param string $orderAddress
     *
     * @return ShopifyOrder
     */
    public function setOrderAddress($orderAddress)
    {
        $this->orderAddress = $orderAddress;

        return $this;
    }

    /**
     * Get orderAddress
     *
     * @return string
     */
    public function getOrderAddress()
    {
        return $this->orderAddress;
    }

    /**
     * Set countryCode
     *
     * @param string $countryCode
     *
     * @return ShopifyOrder
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * Get countryCode
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ShopifyOrder
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set productCount
     *
     * @param integer $productCount
     *
     * @return ShopifyOrder
     */
    public function setProductCount($productCount)
    {
        $this->productCount = $productCount;

        return $this;
    }

    /**
     * Get productCount
     *
     * @return integer
     */
    public function getProductCount()
    {
        return $this->productCount;
    }

    /**
     * Set trackingUrl
     *
     * @param string $trackingUrl
     *
     * @return ShopifyOrder
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->trackingUrl = $trackingUrl;

        return $this;
    }

    /**
     * Get trackingUrl
     *
     * @return string
     */
    public function getTrackingUrl()
    {
        return $this->trackingUrl;
    }
}
