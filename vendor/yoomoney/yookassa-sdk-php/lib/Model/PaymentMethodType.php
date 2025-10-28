<?php

/*
 * The MIT License
 *
 * Copyright (c) 2025 "YooMoney", NBСO LLC
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace YooKassa\Model;

use YooKassa\Common\AbstractEnum;

/**
 * PaymentMethodType - Тип источника средств для проведения платежа
 *
 * Возможные значения:
 * - `yoo_money` - Платеж из кошелька ЮMoney
 * - `bank_card` - Платеж с произвольной банковской карты
 * - `sberbank` - Платеж СбербанкОнлайн
 * - `cash` - Платеж наличными
 * - `mobile_balance` - Платеж с баланса мобильного телефона
 * - `apple_pay` - Платеж ApplePay
 * - `google_pay` - Платеж Google Pay
 * - `qiwi` - Платеж из кошелька Qiwi
 * - `webmoney` - Платеж из кошелька Webmoney
 * - `alfabank` - Платеж через Альфа-Клик
 * - `b2b_sberbank` - Сбербанк Бизнес Онлайн
 * - `tinkoff_bank` - Интернет-банк Тинькофф
 * - `psb` - ПромсвязьБанк
 * - `installments` - Заплатить по частям
 * - `wechat` - Платеж через WeChat
 * - `sbp` - Платеж через через сервис быстрых платежей
 */
class PaymentMethodType extends AbstractEnum
{
    /** Платеж из кошелька ЮMoney */
    const YOO_MONEY      = 'yoo_money';
    /** Платеж с произвольной банковской карты */
    const BANK_CARD      = 'bank_card';
    /** Платеж СбербанкОнлайн */
    const SBERBANK       = 'sberbank';
    /** Платеж наличными */
    const CASH           = 'cash';
    /** Платеж с баланса мобильного телефона */
    const MOBILE_BALANCE = 'mobile_balance';
    /** латеж ApplePay */
    const APPLE_PAY      = 'apple_pay';
    /** Платеж Google Pay */
    const GOOGLE_PAY     = 'google_pay';
    /**
     * Платеж из кошелька Qiwi
     * @deprecated Будет удален в следующих версиях
     */
    const QIWI           = 'qiwi';
    /**
     * Платеж из кошелька Webmoney
     * @deprecated Будет удален в следующих версиях
     */
    const WEBMONEY       = 'webmoney';
    /**
     * Платеж через Альфа-Клик
     * @deprecated Будет удален в следующих версиях
     */
    const ALFABANK       = 'alfabank';
    /** Сбербанк Бизнес Онлайн */
    const B2B_SBERBANK   = 'b2b_sberbank';
    /** Интернет-банк Тинькофф */
    const TINKOFF_BANK   = 'tinkoff_bank';
    /**
     * ПромсвязьБанк
     * @deprecated Будет удален в следующих версиях
     */
    const PSB            = 'psb';
    /**
     * Заплатить по частям
     * @deprecated Будет удален в следующих версиях
     */
    const INSTALLMENTS   = 'installments';
    /**
     * Оплата через WeChat
     * @deprecated Будет удален в следующих версиях
     */
    const WECHAT         = 'wechat';
    /** Оплата через сервис быстрых платежей */
    const SBP            = 'sbp';
    /** Прием оплаты с использованием Кредита от СберБанка */
    const SBER_LOAN = 'sber_loan';
    /** Оплата через сервис «Плати частями» */
    const SBER_BNPL = 'sber_bnpl';

    /**
     * Для неизвестных методов оплаты
     * @deprecated Не используется для реальных платежей
     */
    const UNKNOWN = 'unknown';

    protected static $validValues = array(
        self::YOO_MONEY      => true,
        self::BANK_CARD      => true,
        self::SBERBANK       => true,
        self::CASH           => true,
        self::MOBILE_BALANCE => true,
        self::APPLE_PAY      => false,
        self::GOOGLE_PAY     => false,
        self::QIWI           => false,
        self::WEBMONEY       => false,
        self::ALFABANK       => false,
        self::TINKOFF_BANK   => true,
        self::INSTALLMENTS   => false,
        self::B2B_SBERBANK   => true,
        self::PSB            => false,
        self::WECHAT         => false,
        self::SBP            => true,
        self::SBER_LOAN      => true,
        self::SBER_BNPL      => true,
        self::UNKNOWN        => false,
    );
}
