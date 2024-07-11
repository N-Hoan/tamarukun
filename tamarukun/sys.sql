----------table----------
create table user(
    userID int auto_increment primary key,
    name varchar(20) not null,
    email varchar(50) unique not null,
    accountName varchar(20) not null,
    password varchar(20) not null,
    topImage varchar(255)
);

create table want(
    userID int ,
    thingName varchar(20) not null ,
    price int not null,
    startDate date not null ,         
    goalDate date not null,
    status int  default 0,
    primary key(userID), 
    foreign key(userID) references user(userID)
);

ALTER TABLE want MODIFY status BIGINT;
-- image varchar(255) not null,
-- check(price >= 10000 and 1000000 >= price)
-- ALTER TABLE want RENAME COLUMN starteDate TO startDate;

create table schedule(
    userID int,
    date date,
    detailText varchar(50) not null,
    primary key(userID,date),
    foreign key (userID) references user (userID) 
);


----------insert----------
insert into user (name,email,accountName,password)
values ('test','test','test','test');

insert into want (userID,thingName,price,starteDate,goalDate,image)
values (4,'test',10000,'1999-12-31','9999-12-31','wfadx');

insert into schedule
values(1,'9999-12-31','wwww');


----------update---------
--アカウント情報更新
update user 
set name = 'test2',email = 'test2',accountName = 'test2',password = 'test2',topImage = 'test2'
where userID = 1;
--欲しいものリストの更新
update want 
set thingName = 'test2',price = 100000,goalDate = '4000-09-21'
where userID = 1 and thingName='test';
--貯金状況が変わったとき
update want 
set status = status + 3
where userID = 1 and thingName = 'test2';
--シフト内容が変わったとき
update schedule
set detailText = 'eeeee'
where userID = 1 and date = '9999-12-31';


----------delete----------
--アカウント削除
delete from user where userID = 1;
--欲しいものリストの削除
delete from want where userID = 1 and thingName = 'test2';
--シフトが無くなったとき
delete from schedule where userID = 1 and date = '9999-12-31';


----------select----------
--ログイン時にuserIDを取得
select userID from user where email = 'test2';

select * from want where userID = ??? and thingName = ???;