# Quantic Telecom Invoices Storage

[![Build Status](https://travis-ci.org/QuanticTelecom/invoices-storage.svg?branch=develop)](https://travis-ci.org/QuanticTelecom/invoices-storage)

This package gives you an easy way to store your invoices.

## Installation

Per usual, install Invoices Storage through Composer.

```js
"require": {
    "quantic-telecom/invoices-storage": "~1.0"
}
```

## Contracts

### Repositories

The `InvoiceRepositoryInterface` provides methods' signatures to get (`get` and `getAll`) and save (`save`) invoices.

### Factories

This package requires a lot of factories in order to create the invoices from the data stored in the database.
- `CustomerFactoryInterface`
- `PaymentFactoryInterface`
- `ItemFactoryInterface`
- `GroupOfItemsFactoryInterface`
- `InvoiceFactoryInterface`

All these factories have a `build` and an `inverseResolution` methods.

#### Inverse Resolution

Inverse resolution works like DNS. The repository need to know which concrete class he have to instanciate. The `inverseResolution` method must return a string (a domain name for classes) that will be stored with the object data. When the repository will need to get this object, he will pass to the `build` method that string and the data. Then, the factory will need to know how to build the requested object and return it.  

## Implementations

### Repositories

This package provides a MongoDB implementation for the `InvoiceRepositoryInterface` named `InvoiceMongoRepository`. This repository don't use any ORM and build MongoDB queries by his own.

### Factories

Each factory implementation build and resolve one or many concrete classes.

There is no implementation for `CustomerFactoryInterface` and `PaymentFactoryInterface` because `quantic-telecom/invoices` doesn't provide any concrete class for `CustomerInterface` and `PaymentInterface`. The client need to implement these factories because there are required by the `InvoiceFactory`.

#### `InvoiceFactory`

| Concrete class                                | Domain name for class |
|:---------------------------------------------:|:---------------------:|
| `QuanticTelecom\Invoices\ExcludingTaxInvoice` |  includingTaxInvoice  |
| `QuanticTelecom\Invoices\IncludingTaxInvoice` |  excludingTaxInvoice  |

#### `ItemFactory`

| Concrete class                 | Domain name for class |
|:------------------------------:|:---------------------:|
| `QuanticTelecom\Invoices\Item` | item                  |

#### `GroupOfItemsFactory`

| Concrete class                         | Domain name for class |
|:--------------------------------------:|:---------------------:|
| `QuanticTelecom\Invoices\GroupOfItems` | groupOfItems          |
