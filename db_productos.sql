CREATE DATABASE IF NOT EXISTS db_productos;
USE db_productos;


CREATE TABLE categories(
id       int(255) auto_increment not null,
code     varchar(255),
name     varchar(255),
description  text,
active     boolean,
CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE products(
id       int(255) auto_increment not null,
category_id int(255) not null,
code     varchar(255),
name     varchar(255),
description    text,
brand   varchar(255),
price float,
CONSTRAINT pk_products PRIMARY KEY(id),
CONSTRAINT fk_products_categories foreign key(category_id) references categories(id)
)ENGINE=InnoDb;
