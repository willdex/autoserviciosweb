create table taxi(
id int primary key auto_increment,
nombre varchar(15),
id_empresa int not null,
foreign key(id) references empresa(id)
);

create table pedido(
id int primary key auto_increment,
id_taxi int null,
id_usuario int not null,
foreign key(id_usuario)references usuario(id),
foreign key(id_taxi)references taxi(id) 
);