# filled
create table `calc_delivery_tariff` (
	`tariff` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) not null,
	`code` varchar(63) not null,
	`margin` decimal(4,2) not null,
	PRIMARY KEY  (`tariff`)
)

#filled
create table `calc_delivery_provider` (
	`provider` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(125) NOT NULL,
	`code` varchar(125) NOT NULL,
	`volume_weight_divider` int(11) not null,
	dimension_limit int(11) null,
	weight_limit int(11) null,
	oversize_limit_factor decimal(4,2),
	min_assessed_price decimal(15,2),
	PRIMARY KEY  (`provider`),
	UNIQUE (`code`)
)

#filled
create table `calc_delivery_delivery_type` (
	`delivery_type` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(125) NOT NULL,
	`from_office` tinyint(1) not null,
	`to_office` tinyint(1) not null,
	`code` varchar(63) not null,
	PRIMARY KEY  (`delivery_type`),
	UNIQUE (`from_office`, `to_office`),
	UNIQUE (`code`)
)

#filled
create table `calc_delivery_city` (
	`city` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(125) NOT NULL,
	`parent` int(11) null references `calc_calc_city`(city),
	`region_name` varchar(127) null,
	PRIMARY KEY  (`city`),
	UNIQUE (`name`, `parent`)
)

#filled
CREATE TABLE `calc_delivery_terminal` (
	`terminal` int NOT NULL AUTO_INCREMENT,
	`name` varchar(127) NOT NULL,
	`city` int not null references `calc_delivery_city`(city),
	`provider` int(11) NOT NULL references `calc_delivery_provider`,
	PRIMARY KEY  (`terminal`)
)

#filled
CREATE TABLE `calc_delivery_zone` (
  `zone` int NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL unique,
  `code` varchar(32) NOT NULL unique,
	`provider` int(11) NOT NULL references `calc_delivery_provider(provider)`,
   PRIMARY KEY  (`zone`)
)

#filled
CREATE TABLE `calc_delivery_direction2zone` (
  `city_from` int(11) NOT NULL references `calc_delivery_city`(city),
  `city_to` int(11) NOT NULL references `calc_delivery_city`(city),
  `zone` int not null references `calc_delivery_zone`(zone),
   PRIMARY KEY  (`zone`,`city_from`,`city_to`),
   unique  (`zone`,`city_from`,`city_to`)
)

#filled
CREATE TABLE `calc_delivery_assessed_value_price` (
	`from` decimal(15,2) NOT NULL,
	`to` decimal(15,2) NOT NULL,
	`base_price` decimal(15,2) not null,
	`overprice_percent` decimal(15,10) not null default 0,
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff)
)

#filled
CREATE TABLE `calc_delivery_discount` (
	`city_from` int(11) NOT NULL references `calc_delivery_city`(city),
	`city_to` int(11) NOT NULL references `calc_delivery_city`(city),
	`factor` decimal(4,2) not null,
	`user` int(11) NULL references `calc_users(id)`,
	PRIMARY KEY  (`city_from`, `city_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#filled
CREATE TABLE `calc_delivery_courier_price` (
  `courier_price` int NOT NULL AUTO_INCREMENT,
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff),
	`weight_from` decimal(15,2) not null,
	`weight_to` decimal(15,2) not null,
	`price` decimal(15,2) not null,
	PRIMARY KEY  (`courier_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#filled
create table `calc_delivery_city2delivery_time`(
	`city` int(11) NOT NULL references `calc_delivery_city`(city),
	min_time int(11) not null,
	max_time int(11) not null,
	`provider` int(11) NOT NULL references `calc_delivery_provider`
)

#filled
create table `calc_delivery_city_factor`(
	`city` int(11) NOT NULL references `calc_delivery_city`(city),
	factor decimal(4,2) not null,
	`tariff` int(11) NOT NULL references `calc_delivery_tariff`
)

#filled
create TABLE `calc_delivery_rate` (
	`rate` int(11) not null auto_increment,
	`city_from` int(11) NULL references `calc_delivery_city`(city),
	`city_to` int(11) NULL references `calc_delivery_city`(city),
	`zone` int(11) null references `calc_delivery_zone`(zone),
	`tariff` int(11) not null references `calc_delivery_tariff`(tariff),
	`provider` int(11) not null references `calc_delivery_provider`(provider),
	`delivery_hours` varchar(32) null ,
	PRIMARY KEY (`rate`)
)


#filled
CREATE TABLE `calc_delivery_weight_price` (
	`rate` int NOT NULL references `calc_delivery_rate`(rate),
	`from` decimal(10,2) NOT NULL,
	`to` decimal(10,2) NOT NULL,
	`base_price` decimal(15,2) not null,
	`overweight_cost` decimal(15,2) not null default 0
)


insert into calc_delivery_tariff(name, code, margin)
values ('Экспресс-Стандарт', 'express_standart', 1.3)

insert into `calc_delivery_provider` (	`name`,	`code`,	`volume_weight_divider`,dimension_limit ,weight_limit ,	oversize_limit_factor ,	min_assessed_price )
values ('СпецСвязь', 'special', 6000, 300, 200, 1.5, 0)

insert into calc_delivery_delivery_type (name, from_office, to_office, code)
values 
	('Дверь-дверь', 0, 0, 'door.door'),
	('Окно-дверь', 1, 0, 'office.door')

insert into calc_delivery_city(name, parent, region_name)
select name, parent, region_name from calc_calc_city


insert into calc_delivery_terminal(city, name, provider)
select t.city, t.name, p.provider from calc_calc_terminal t
	join calc_delivery_provider p on p.code='special'

set sql_mode='NO_AUTO_VALUE_ON_ZERO';
insert into calc_delivery_zone(zone, name, code, provider)
select z.zone, z.name, concat(p.code,'.',cast(z.zone as char)), p.provider
 from calc_calc_zone z 
	join calc_delivery_provider p on p.code='special'

set sql_mode='';

insert into calc_delivery_direction2zone(city_from, city_to, zone)
select city_from, city_to, zone from calc_calc_direction2zone


insert into calc_delivery_assessed_value_price (`from`, `to`, base_price, overprice_percent, tariff)
select 
	c.from,
	c.to,
	c.base_price,
	c.overprice_percent,
	t.tariff
from calc_calc_assessed_value_price c
	join calc_delivery_tariff t on t.code = 'express_standart'
where
	c.is_public = 0

insert into calc_delivery_discount(city_from, city_to, factor, user)
select city_from, city_to, factor, user from calc_calc_discount


insert into calc_delivery_courier_price(tariff, weight_from, weight_to, price)
select t.tariff, 0, 10000, 150 from calc_delivery_tariff t
where t.code = 'express_standart'


insert into calc_delivery_city2delivery_time(city, min_time, max_time, provider)
select c.city, c.express_min_delivery_time, c.express_max_delivery_time, p.provider
from calc_calc_city c
	join calc_delivery_provider p on p.code='special'

insert into calc_delivery_city_factor (city, factor, tariff)
select 
	c.city, f.value_for_inner_calculations, t.tariff
from calc_calc_city c
	join calc_calc_factor f on f.factor = c.factor
	join calc_delivery_tariff t on t.code = 'express_standart'



insert into calc_delivery_rate(zone, tariff, provider)
select 
	z.zone, t.tariff, p.provider
from calc_delivery_zone z
	join calc_delivery_tariff t on t.code = 'express_standart'
	join calc_delivery_provider p on p.code='special'


insert into calc_delivery_weight_price (rate, `from`, `to`, base_price, overweight_cost)
select 
	r.rate, owp.`from`, owp.`to`, owp.base_price, owp.overweight_cost
from calc_calc_weight_price owp 
	join calc_calc_tariff ot on ot.tariff = owp.tariff
	join calc_delivery_rate r on 
		r.zone = owp.zone
		and ot.tariff = 6





