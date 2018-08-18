CREATE TABLE Korisnik (
  id             integer(2) NOT NULL AUTO_INCREMENT UNIQUE,
  email          varchar(50) NOT NULL UNIQUE,
  password       varchar(255) NOT NULL,
  timeRegistered datetime DEFAULT CURRENT_TIMESTAMP NULL,
  status         int(10) DEFAULT 1,
  phone         varchar(50),
  PRIMARY KEY (id)) ENGINE=InnoDB ;
CREATE TABLE Poruka (
  id        int(10) NOT NULL AUTO_INCREMENT,
  text      varchar(255) NOT NULL,
  recepient int(10),
  sender    int(10) NOT NULL,
  timeSent  datetime DEFAULT CURRENT_TIMESTAMP NULL,
  PRIMARY KEY (id)) ENGINE=InnoDB;
CREATE TABLE Sertifikat (
  id          int(10) NOT NULL AUTO_INCREMENT,
  serial      int(10),
  hash        varchar(50) NOT NULL UNIQUE,
  owner       int(10) NOT NULL,
  timeCreated datetime DEFAULT CURRENT_TIMESTAMP NULL,
  PRIMARY KEY (id)) ENGINE=InnoDB;
ALTER TABLE Poruka ADD CONSTRAINT FKPoruka896416 FOREIGN KEY (sender) REFERENCES Korisnik (id);
ALTER TABLE Sertifikat ADD CONSTRAINT FKSertifikat559669 FOREIGN KEY (owner) REFERENCES Korisnik (id);

create table Stanje
(
	timeCreated datetime default CURRENT_TIMESTAMP null,
	id int not null
		primary key,
	promena int null,
	stanje int null,
	korisnik int null,
	constraint Stanje_Korisnik_id_fk
		foreign key (korisnik) references Korisnik (id)
)

