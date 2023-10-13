CREATE TABLE IF NOT EXISTS "user" (
    id          bigserial CONSTRAINT primary_id PRIMARY KEY,
    guid        varchar(36) NOT NULL,
    first_name  varchar(50) NOT NULL,
    second_name varchar(50),
    birth_date  date,
    biography   text,
    password    varchar(255) NOT NULL,
    token       varchar(255) NOT NULL
)
