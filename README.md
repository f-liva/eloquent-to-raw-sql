# Eloquent toRawSql

Exposes the eloquent toRawSql method to display the raw query in beautified form.

## Install

`composer require f-liva/eloquent-to-raw-sql`

## Use

```php
$products = Product
    ::where('availability', 'available')
    ->where('type', 'goods')
    ->groupBy('category')
    ->orderByDesc('price')
    ->limit(10);

dump($products->toRawSql()); // Beautified SQL (as default)

//    SELECT 
//        *
//    FROM 
//        `products`
//    WHERE 
//        `products`.`availability` = 'available'
//         AND `products`.`type` = 'goods'
//    GROUP BY 
//        `products`.`category`
//    ORDER BY 
//        `products`.`price` DESC
//    LIMIT 10;

dump($products->toRawSql(false)); // Unbeautified SQL

//  SELECT * FROM `products` WHERE `products`.`availability` ...
```
