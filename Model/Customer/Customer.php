<?php
/**
 * A two factor authentication module that protects both the admin and customer logins
 * Copyright (C) 2017  Ross Mitchell
 *
 * This file is part of Rossmitchell/Twofactor.
 *
 * Rossmitchell/Twofactor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rossmitchell\Twofactor\Model\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class Customer
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var CustomerInterface|false
     */
    private $customer;

    /**
     * Getter constructor.
     *
     * @param CustomerRepositoryInterface $customerRepository
     * @param Session                     $customerSession
     */
    public function __construct(CustomerRepositoryInterface $customerRepository, Session $customerSession)
    {
        $this->customerRepository = $customerRepository;
        $this->customerSession    = $customerSession;
    }

    /**
     * Returns the customer model from the session. Will return false if there is no customer
     *
     * @return CustomerInterface|false
     */
    public function getCustomer()
    {
        if (null === $this->customer) {
            $this->customer = $this->getCustomerFromSession();
        }

        return $this->customer;
    }

    /**
     * @param $customerId
     *
     * @return CustomerInterface
     */
    public function getCustomerById($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * @return bool|CustomerInterface
     */
    private function getCustomerFromSession()
    {
        $customerId = $this->customerSession->getData('customer_id');
        $customer   = false;

        if (null !== $customerId) {
            $customer = $this->getCustomerById($customerId);
        }

        return $customer;
    }
}
