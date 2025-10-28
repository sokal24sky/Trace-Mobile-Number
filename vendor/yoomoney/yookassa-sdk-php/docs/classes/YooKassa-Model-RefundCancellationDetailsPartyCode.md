# [YooKassa API SDK](../home.md)

# Class: \YooKassa\Model\RefundCancellationDetailsPartyCode
### Namespace: [\YooKassa\Model](../namespaces/yookassa-model.md)
---
**Summary:**

Класс, представляющий модель CancellationDetailsPartyCode.

**Description:**

Возможные инициаторы отмены возврата:
- `yoo_money` - ЮKassa
- `refund_network` - Любые участники процесса возврата, кроме ЮKassa и вас

---
### Constants
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [YOO_MONEY](../classes/YooKassa-Model-RefundCancellationDetailsPartyCode.md#constant_YOO_MONEY) |  | ЮKassa |
| public | [YANDEX_CHECKOUT](../classes/YooKassa-Model-RefundCancellationDetailsPartyCode.md#constant_YANDEX_CHECKOUT) | *deprecated* |  |
| public | [REFUND_NETWORK](../classes/YooKassa-Model-RefundCancellationDetailsPartyCode.md#constant_REFUND_NETWORK) |  | Любые участники процесса возврата, кроме ЮKassa и вас (например, эмитент банковской карты) |

---
### Properties
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| protected | [$validValues](../classes/YooKassa-Model-RefundCancellationDetailsPartyCode.md#property_validValues) |  |  |

---
### Methods
| Visibility | Name | Flag | Summary |
| ----------:| ---- | ---- | ------- |
| public | [getEnabledValues()](../classes/YooKassa-Common-AbstractEnum.md#method_getEnabledValues) |  | Возвращает значения в enum'е значения которых разрешены |
| public | [getValidValues()](../classes/YooKassa-Common-AbstractEnum.md#method_getValidValues) |  | Возвращает все значения в enum'e |
| public | [valueExists()](../classes/YooKassa-Common-AbstractEnum.md#method_valueExists) |  | Проверяет наличие значения в enum'e |

---
### Details
* File: [lib/Model/RefundCancellationDetailsPartyCode.php](../../lib/Model/RefundCancellationDetailsPartyCode.php)
* Package: YooKassa\Model
* Class Hierarchy: 
  * [\YooKassa\Common\AbstractEnum](../classes/YooKassa-Common-AbstractEnum.md)
  * \YooKassa\Model\RefundCancellationDetailsPartyCode

* See Also:
  * [](https://yookassa.ru/developers/api)

---
### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| category |  | Class |
| author |  | cms@yoomoney.ru |

---
## Constants
<a name="constant_YOO_MONEY" class="anchor"></a>
###### YOO_MONEY
ЮKassa

```php
YOO_MONEY = 'yoo_money'
```


<a name="constant_YANDEX_CHECKOUT" class="anchor"></a>
###### ~~YANDEX_CHECKOUT~~
```php
YANDEX_CHECKOUT = 'yandex_checkout'
```

**deprecated**
Устарел. Оставлен для обратной совместимости

<a name="constant_REFUND_NETWORK" class="anchor"></a>
###### REFUND_NETWORK
Любые участники процесса возврата, кроме ЮKassa и вас (например, эмитент банковской карты)

```php
REFUND_NETWORK = 'refund_network'
```



---
## Properties
<a name="property_validValues"></a>
#### protected $validValues : array
---
**Type:** <a href="../array"><abbr title="array">array</abbr></a>
Массив принимаемых enum&#039;ом значений
**Details:**



---
## Methods
<a name="method_getEnabledValues" class="anchor"></a>
#### public getEnabledValues() : string[]

```php
Static public getEnabledValues() : string[]
```

**Summary**

Возвращает значения в enum'е значения которых разрешены

**Details:**
* Inherited From: [\YooKassa\Common\AbstractEnum](../classes/YooKassa-Common-AbstractEnum.md)

**Returns:** string[] - Массив разрешённых значений


<a name="method_getValidValues" class="anchor"></a>
#### public getValidValues() : array

```php
Static public getValidValues() : array
```

**Summary**

Возвращает все значения в enum'e

**Details:**
* Inherited From: [\YooKassa\Common\AbstractEnum](../classes/YooKassa-Common-AbstractEnum.md)

**Returns:** array - Массив значений в перечислении


<a name="method_valueExists" class="anchor"></a>
#### public valueExists() : bool

```php
Static public valueExists(mixed $value) : bool
```

**Summary**

Проверяет наличие значения в enum'e

**Details:**
* Inherited From: [\YooKassa\Common\AbstractEnum](../classes/YooKassa-Common-AbstractEnum.md)

##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code lang="php">mixed</code> | value  | Проверяемое значение |

**Returns:** bool - True если значение имеется, false если нет



---

### Top Namespaces

* [\YooKassa](../namespaces/yookassa.md)

---

### Reports
* [Errors - 0](../reports/errors.md)
* [Markers - 1](../reports/markers.md)
* [Deprecated - 43](../reports/deprecated.md)

---

This document was automatically generated from source code comments on 2025-09-04 using [phpDocumentor](http://www.phpdoc.org/)

&copy; 2025 YooMoney