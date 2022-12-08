-- DB Fiddle: https://www.db-fiddle.com/f/3TSbU9ZnpSMkCRgnWYeBep/1

-- Create a customers table with an array JSON column containing their favourite numbers.
create table `customers` (
  `id` bigint unsigned auto_increment,
  `name` varchar(222),
  `numbers` json not null,
  primary key (`id`),
  key `customers_numbers_index` ((cast(json_extract(`numbers`,_utf8mb4'$[*]') as unsigned array)))
) engine=InnoDB default charset=utf8mb4 collate=utf8mb4_unicode_ci;

insert into `customers` (`name`, `numbers`) values 
("Joe Bloggs", "[42, 101]"),
("Jane Bloggs", "[32, 64]"),
("John Smith", "[42]"),
("Jane Smith", "[42, 99, 180]");

-- Use json_extract() and json_contains() to find an exact number in the field using the created index.
select * from `customers` where json_contains(json_extract(numbers, "$[*]"), '[42]');
explain select * from `customers` where json_contains(json_extract(numbers, "$[*]"), '[42]');