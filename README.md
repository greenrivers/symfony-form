## Greenrivers symfony-form

### Info

Example Symfony project (import data from csv, use messenger, build form, api platform endpoints).<br/>
Local environment based on Warden.

### Environment

- PHP 8.1
- PHPUnit 9
- Symfony 6.3
- MariaDB 10.6
- RabbitMQ 3.11

### Usage

Sample csv file:

```csv
id,product,price,category,manufacturer_company,manufacturer_tax_id,manufacturer_city,manufacturer_postcode,manufacturer_street,manufacturer_street_number
1,Creme De Menthe Green,888.4,"Doors, Frames & Hardware",Blogtags,5421468682,Arroio Grande,96330-000,Waywood,76

```

Import file with command:

```shell
php bin/console app:import-data <path_to_csv_file>
```

Run worker:

```shell
php bin/console messenger:consume async
```

Place an order from: https://app.symfony.test/order.

API Platform endpoints: https://app.symfony.test/api.

### Tests

Run command:

```shell
./vendor/bin/phpunit
```
