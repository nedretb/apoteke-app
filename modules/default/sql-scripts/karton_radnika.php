<?php
include '../../../configuration.php';
try{
//    $sql ="CREATE table users__podaci_o_rodjenju(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        datum_rodjena date null,
//        sifra_opstine_rodjenja nvarchar(20) null,
//        naziv_opstine_rodjenja nvarchar(50) null,
//        mjesto_rodjenja nvarchar(50) null,
//        sifra_drzave_rodjenja nvarchar(200) null,
//        grad_rodjenja nvarchar(200) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="CREATE table users__podaci_o_stanovanju(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        adresa nvarchar(200) null,
//        sifra_opstine nvarchar(200) null,
//        naziv_opstine nvarchar(200) null,
//        grad nvarchar(200) null,
//        postanski_broj int null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="CREATE table users__kontakt_informacije(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        kucni_telefonski_broj varchar(200) null,
//        kucni_regionalni_kod int null,
//        kucni_broj int null,
//        privatni_mobitel_broj varchar(200) null,
//        mobitel_regionalni_kod int null,
//        mobitel_broj int null,
//        privatna_email_adresa varchar(200) null,
//        ime_prezime_kontakt_osobe nvarchar(200) null,
//        odnos_sa_kontakt_osobe nvarchar(200) null,
//        kontakt_osoba_broj_telefona varchar(200) null,
//        kontakt_osoba_regionalni_kod int null,
//        kontakt_osoba_broj int null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__podaci_o_roditeljima(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        otac_ime_prezime nvarchar(50) null,
//        otac_datum_rodjenja date null,
//        majka_ime_prezime nvarchar(50) null,
//        majka_datum_rodjenja_ date null,
//        majka_djevojacko_prezime nvarchar(20) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__podaci_o_porodicnom_stanju(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        bracni_status nvarchar(200) null,
//        supruznik_ime_prezime nvarchar(200) null,
//        supruznik_datum_rodjenja date null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__podaci_o_rodbinskim_odnosima(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        srodnik nvarchar(200) null,
//        srodstvo nvarchar(200) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__licni_dokumenti(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        broj_licne_karte varchar(200) null,
//        drzavljanstvo nvarchar(200) null,
//        vozacka_dozvola varchar(2) null,
//        kategorija varchar(10) null,
//        aktivan_vozac varchar(2) null,
//        darivalac_krvi varchar(2) null,
//        krvna_grupa varchar(4) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__zdravstveno_stanje(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        invalid varchar (2) not null default 'NE',
//        stepen_invalidnosti varchar(200) null,
//        hronicne_bolesti varchar(2) not null default 'NE',
//        dijete_sa_posebnim_potrebama varchar(2) not null default 'NE',
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__zdravstveno_stanje(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        invalid varchar (2) not null default 'NE',
//        stepen_invalidnosti varchar(200) null,
//        hronicne_bolesti varchar(2) not null default 'NE',
//        dijete_sa_posebnim_potrebama varchar(2) not null default 'NE',
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    //TODO Broj certifikata i broj stranih jezika implementovati
//    $sql ="
//CREATE table users__skolovanje(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        strucna_sprema nvarchar(200) null,
//        zavrsena_obrazovna_ustanova nvarchar(200) null,
//        zvanje nvarchar(200) null,
//        struka nvarchar(200) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__skolovanje_certifikati(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        skolovanje_id int ,
//        naziv_institucije nvarchar(200) null,
//        opis nvarchar(200) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__skolovanje_jezivi(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        skolovanje_id int ,
//        jezik nvarchar(20) null,
//        nivo nvarchar(20) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__fond_solidarnosti_i_sindikat(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        clan_internog_fonda_solidarnosti varchar(2) not null default 'NE',
//        clan_sindikata varchar(2) not null default 'NE',
//        naziv_sindikata varchar(200) null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__poreska_olaksica_i_prevoz(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        employee_no varchar (50),
//        poreska_kartica varchar(2) not null default 'NE',
//        koeficijent_olaksice float null,
//        prevoz_na_odobrenoj_lokaciji float null,
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    //podaci za select opcije
//    $sql ="
//CREATE table users__sifarnik_opcine(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        kod nvarchar(10),
//        ime_opcina nvarchar(30),
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__sifarnik_drzava(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        kod nvarchar(10),
//        ime_drzava nvarchar(30),
//        created_at datetime
//     )";
//    $db->exec($sql);
//
//    $sql ="
//CREATE table users__sifarnik_grad(
//        id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//        kod nvarchar(10),
//        ime_grad nvarchar(30),
//        created_at datetime
//     )";
//    $db->exec($sql);

    $sql ="
CREATE TABLE users__sifarnik_opcine(
    id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
	[naziv_opcine] [nvarchar](30) NOT NULL,
	[grad] [nvarchar](30) NOT NULL,
	[sifra] [nvarchar](10) NOT NULL,
 )";
    $db->exec($sql);

$sql = "
CREATE TABLE users__sifarnik_drzave(
    id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
	[naziv_drzave] [nvarchar](50) NOT NULL,
	[sifra] [nvarchar](10) NOT NULL,
 )";
$db->exec($sql);
//
//    $sql = "CREATE TABLE users__sifarnik_grad(
//    id int NOT NULL IDENTITY(1,1) PRIMARY KEY,
//	[naziv_grada] [nvarchar](30) NOT NULL,
//	[sifra] [nvarchar](10) NOT NULL,
// )
//";
//    $db->exec($sql);

    echo 'done';
}catch (PDOException $e){
    die($e->getMessage());
}