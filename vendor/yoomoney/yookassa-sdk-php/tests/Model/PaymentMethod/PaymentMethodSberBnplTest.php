<?php

namespace Tests\YooKassa\Model\PaymentMethod;

use YooKassa\Model\PaymentMethod\PaymentMethodSberBnpl;
use YooKassa\Model\PaymentMethodType;

class PaymentMethodSberBnplTest extends AbstractPaymentMethodTest
{
    /**
     * @return PaymentMethodSberBnpl
     */
    protected function getTestInstance()
    {
        return new PaymentMethodSberBnpl();
    }

    /**
     * @return string
     */
    protected function getExpectedType()
    {
        return PaymentMethodType::SBER_BNPL;
    }
}
