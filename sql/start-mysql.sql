CREATE TABLE category (
    id integer AUTO_INCREMENT PRIMARY KEY,
    name text,
    description text,
    comments text,
    amount numeric,
    parent integer,
    color text,
    deleted smallint DEFAULT 0
);


CREATE TABLE category_period (
    id integer AUTO_INCREMENT PRIMARY KEY,
    category_id integer,
    period text,
    amount numeric,
    deleted smallint DEFAULT 0
);


CREATE TABLE expense (
    id integer AUTO_INCREMENT PRIMARY KEY,
    category_id integer,
    amount numeric,
    comment text,
    store text,
    entered_by integer,
    date date,
    deleted smallint DEFAULT 0,
    unique_id text
);


CREATE TABLE person (
    id integer AUTO_INCREMENT PRIMARY KEY,
    name text
);

