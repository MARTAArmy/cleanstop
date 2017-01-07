create table busstops(
    stopcode varchar(20) primary key,
    name varchar(255) not null,
    lat float not null,
    lng float not null,
    ridership int
) engine=myisam default charset=utf8;

create table donations(
    id int auto_increment primary key,
    insert_datetime timestamp default current_timestamp,
    token varchar(255) not null unique,
    name varchar(255) not null,
    email varchar(255) not null,
    comments text,
    amount int(10),
    stopcode varchar(10) not null,
    status text not null
) engine=myisam default charset=utf8;
