create database lawga_blog;

use lawga_blog;

create table user(
    firstname varchar(255),
    lastname varchar(255),
    username varchar(255),
    email varchar(255),
    user_img varchar(255),
    password varchar(255),
    id int not null auto_increment,
    primary key(id)
);

create table category(
    category_id int not null auto_increment,
    category_name varchar(255), 
    primary key(category_id)
);

create table post(
    title varchar(255),
    content text(100000),
    post_id int not null auto_increment,
    author_id int,
    category_id int,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    post_img varchar(255),
    primary key(post_id),
    foreign key(author_id) references user(id),
    foreign key(category_id) references category(category_id)
);