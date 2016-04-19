<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lead.
 *
 * @ORM\Table(name="lead")
 * @ORM\Entity()
 */
class Lead extends Base
{
    const TARIFF_ONE = 1;

    const TARIFF_THREE = 3;

    const TARIFF_FOUR = 4;
    
    const TARIFF_FIVE = 5;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $address;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $building;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $deliveryDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $currency;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    protected $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    protected $signature;

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }


    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Returns array of statuses.
     *
     * @return []
     */
    public static function valuesOfAmount()
    {
        return [
            self::TARIFF_ONE => 1099,
            self::TARIFF_THREE => 1800,
            self::TARIFF_FOUR => 2400,
            self::TARIFF_FIVE => 3000,
        ];
    }

    /**
     * Returns status name.
     *
     * @return string
     */
    public function calculateAmount()
    {
        if (array_key_exists($this->getTariff(), self::valuesOfAmount())) {
            $statusArray = self::valuesOfAmount();

            return $statusArray[$this->getTariff()];
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param mixed $deliveryDate
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * @ORM\Column(type="integer")
     */
    protected $tariff;

    /**
     * @return mixed
     */
    public function getTariff()
    {
        return $this->tariff;
    }

    /**
     * @param mixed $tariff
     */
    public function setTariff($tariff)
    {
        $this->tariff = $tariff;
    }
    
    /**
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param int $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param mixed $building
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }
}
