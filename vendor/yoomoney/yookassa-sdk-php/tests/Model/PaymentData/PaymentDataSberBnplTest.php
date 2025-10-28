<?php

namespace Tests\YooKassa\Model\PaymentData;

use YooKassa\Model\PaymentData\PaymentDataSberBnpl;
use YooKassa\Model\PaymentMethodType;

class PaymentDataSberBnplTest extends AbstractPaymentDataPhoneTest
{
    /**
     * @return PaymentDataSberBnpl
     */
    protected function getTestInstance()
    {
        return new PaymentDataSberBnpl();
    }

    /**
     * @return string
     */
    protected function getExpectedType()
    {
        return PaymentMethodType::SBER_BNPL;
    }
}
