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

namespace Rossmitchell\Twofactor\Controller\Customerlogin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Rossmitchell\Twofactor\Model\Config\Customer as CustomerAdmin;
use Rossmitchell\Twofactor\Model\Customer\Attribute\IsUsingTwoFactor;
use Rossmitchell\Twofactor\Model\Customer\Customer;
use Rossmitchell\Twofactor\Model\Urls\Fetcher;

abstract class AbstractController extends Action
{

    /**
     * @var CustomerAdmin
     */
    private $customerAdmin;
    /**
     * @var Customer
     */
    private $customerGetter;
    /**
     * @var Redirect
     */
    private $redirectAction;
    /**
     * @var CustomerInterface
     */
    private $customerModel;
    /**
     * @var IsUsingTwoFactor
     */
    private $isUsingTwoFactor;
    /**
     * @var Fetcher
     */
    private $fetcher;

    /**
     * AbstractController constructor.
     *
     * @param Context          $context
     * @param CustomerAdmin    $customerAdmin
     * @param Customer         $customerGetter
     * @param Fetcher          $fetcher
     * @param IsUsingTwoFactor $isUsingTwoFactor
     */
    public function __construct(
        Context $context,
        CustomerAdmin $customerAdmin,
        Customer $customerGetter,
        Fetcher $fetcher,
        IsUsingTwoFactor $isUsingTwoFactor
    ) {
        parent::__construct($context);
        $this->customerAdmin    = $customerAdmin;
        $this->customerGetter   = $customerGetter;
        $this->isUsingTwoFactor = $isUsingTwoFactor;
        $this->fetcher          = $fetcher;
    }

    /**
     * The controllers should only be run if the following conditions are met:
     *
     *  - Two Factor Authentication is enabled for the store
     *  - There is a customer
     *  - That customer is using Two Factor Authentication
     *
     * If all of these conditions are met, then the method will return true, otherwise a redirect will be created and
     * the method will return false
     *
     * @return bool
     */
    public function shouldActionBeRun()
    {
        if ($this->isEnabled() === false) {
            $this->redirectAction = $this->handleDisabled();

            return false;
        }

        if ($this->getCustomer() === false) {
            $this->redirectAction = $this->handleMissingCustomer();

            return false;
        }

        if ($this->isCustomerUsingTwoFactor() === false) {
            $this->redirectAction = $this->handleNonOptInCustomer();

            return false;
        }

        return true;
    }

    /**
     * Returns the redirect action generated by the shouldActionBeRun method
     *
     * @return Redirect
     */
    public function getRedirectAction()
    {
        return $this->redirectAction;
    }

    /**
     * Used to create a redirect action to a specific page
     *
     * @param string $path - The path the the customer should be redirected to
     *
     * @return Redirect
     */
    public function redirect($path)
    {
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath($path);

        return $redirect;
    }

    /**
     * Used to fetch the customer from the session
     *
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        if (null === $this->customerModel) {
            $this->customerModel = $this->customerGetter->getCustomer();
        }

        return $this->customerModel;
    }

    /**
     * A wrapper method around the Customer::isTwoFactorEnabled method
     *
     * @return bool
     */
    private function isEnabled()
    {
        return ($this->customerAdmin->isTwoFactorEnabled() === true);
    }

    /**
     * A wrapper method around the IsUsingTwoFactor::getValue method
     *
     * @return bool
     */
    private function isCustomerUsingTwoFactor()
    {
        $customer = $this->getCustomer();

        return $this->isUsingTwoFactor->getValue($customer);
    }

    /**
     * If Two Factor Authentication is disabled redirect the customer to the home page
     *
     * @return Redirect
     */
    private function handleDisabled()
    {
        return $this->redirect('/');
    }

    /**
     * If there isn't a customer in the session then redirect the user to the login page
     *
     * @return Redirect
     */
    private function handleMissingCustomer()
    {
        $loginUrl = $this->fetcher->getCustomerLogInUrl();

        return $this->redirect($loginUrl);
    }

    /**
     * Redirect customers that are not using Two Factor Authentication to the home page
     *
     * @return Redirect
     */
    private function handleNonOptInCustomer()
    {
        return $this->redirect('/');
    }
}
