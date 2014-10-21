-- -------------------------------------------------------------------------------------- --
-- CREACION DE ELEMENTOS PARA ADAPTACION CRM_BIOTECSA Y ADMINISTRACION DE GASTOS          --
-- DESARROLLADO POR: FARIAS TELECOMUNICACIONES Y COMPUTO                                  --
-- FECHA DE LIBERACION: 24 MARZO 2011                                                     --
-- VERSION: 1.0                                                                           --
-- DESCRIPCION: MODULO DE ADMINISTRACION DE GASTOS QUE TRABAJA EN CONJUNTO CON SUGAR_CRM  --
-- IMPLEMENTACION REALIZADA POR ESTRASOL.                                                 --
-- -------------------------------------------------------------------------------------- --

-- INICIA SCRIPT DE ADECUACIONES A BASE DE DATOS. --

-- CREACION DE TABLAS
-- TABLA de catalogos
DROP TABLE IF EXISTS ktcm00t;
CREATE TABLE ktcm00t (
   cdtabla char(12) not null,
   cdelem varchar(6) not null,
   dselem varchar(80) not null,
   cdusuari varchar(12) not null,
   programa varchar(40) not null,
   tmstmp datetime not null,
   primary key (cdtabla,cdelem)
);

-- TABLA de registro de actividad para gasto
DROP TABLE IF EXISTS kexm00t;
CREATE TABLE kexm00t (
   id char(36) not null,
   date_entered date not null,
   status char(1) not null,
   usuario char(12) not null,
   programa varchar(40) not null,
   tmstmp datetime not null,
   primary key (id)
);

-- TABLA de registro de gastos
DROP TABLE IF EXISTS kexm01t;
CREATE TABLE kexm01t (
   id char(36) not null,
   consecutive int auto_increment,
   date_expenditure date not null,
   type_expenditure char(1) not null,
   value float(13,2) not null,
   is_iva char(1) not null,
   status char(1) not null,
   usuario char(12) not null,
   programa varchar(40) not null,
   tmstmp datetime not null,
   primary key (id,consecutive)
);

-- TABLA de registro de notas asociadas a gastos
DROP TABLE IF EXISTS kexm02t;
CREATE TABLE kexm02t (
   id char(36) not null,
   consecutive_expenditure int not null,
   consecutive_comment int auto_increment,
   comment varchar(250) not null,
   usuario char(12) not null,
   programa varchar(40) not null,
   tmstmp datetime not null,
   primary key(id, consecutive_expenditure, consecutive_comment)
);

-- tabla de pago a gastos
DROP TABLE IF EXISTS kexm10t;
CREATE TABLE kexm10t (
   id char(36) not null,
   consecutive_expenditure int not null,
   consecutive_pay int auto_increment,
   payed float(13,2) not null,
   date_pay date not null,
   comment varchar(250) not null,
   usuario char(12) not null,
   programa varchar(40) not null,
   tmstmp datetime not null,
   primary key(id,consecutive_expenditure,consecutive_pay)
);

DROP TABLE IF EXISTS kaqm00t;
create table kaqm00t (
   user_id char(36) not null,
   is_admin char(1) default '0',
   usuario char(12) not null,
   programa varchar(40) not null,
   tmstmp datetime not null,
   primary key(user_id)
);

-- Genera los procedimiento y las funciones de trabajo.
DROP PROCEDURE IF EXISTS updateAdminExpenditure;
delimiter //
create procedure updateAdminExpenditure(IN userid char(36), IN isadmin char(1))
BEGIN
    update kaqm00t set is_admin = isadmin where user_id = userid;
END
//
delimiter ;

-- calcula el user_name a partir del user_id
DROP FUNCTION IF EXISTS getUserAccount;
create function getUserAccount(user_id char(36))
    returns varchar(60) deterministic
return (select user_name from users where id = user_id);

DROP FUNCTION IF EXISTS getUserName;
create function getUserName(user_id char(36))
    returns varchar(60) deterministic
return (select concat(first_name,' ',last_name) from users where id = user_id);

DROP FUNCTION IF EXISTS getUserId;
create function getUserId(username varchar(60))
    returns char(36) deterministic
return (select id from users where user_name = username);

-- calcula el usuario que realiza el enlace
DROP FUNCTION IF EXISTS getUser;
create function getUser()
    returns char(12) deterministic
return substring(user(),1,instr(user(),'@')-1);

-- calcula el ultimo registro de gasto
DROP FUNCTION IF EXISTS getLastConsecutive;
create function getLastConsecutive(actividad char(36))
    returns int deterministic
return (select max(consecutive) from kexm01t where id=actividad);

-- calcula la descripcion de un codigo de elemento del catalogo
DROP FUNCTION IF EXISTS getDescription;
create function getDescription(tabla varchar(9), codigo varchar(6))
       returns varchar(100) deterministic
return (select dselem from ktcm00t where cdtabla = tabla and cdelem = codigo);

-- calcula totales de gastos por actividad:
DROP FUNCTION IF EXISTS getTotalExpenditure;
create function getTotalExpenditure(activitie_id char(36))
       returns float(13,2) deterministic
return (select sum(value) from kexm01t where id = activitie_id);

-- calcula totales de gastos pagados por actividad:
DROP FUNCTION IF EXISTS getTotalPayed;
create function getTotalPayed(activitie_id char(36))
       returns float(13,2) deterministic
return (select sum(payed) from kexm10t where id = activitie_id);


-- calcula si un usuario es administrador de gastos
DROP FUNCTION IF EXISTS isAdminExpenditure;
create function isAdminExpenditure(username varchar(60))
        returns char(1) deterministic
return (select is_admin from kaqm00t where user_id = getuserId(username));

-- CREACION DE CONSULTAS.

-- Extracción de tareas.
DROP VIEW IF EXISTS tareas;
CREATE VIEW tareas AS
select A.id, assigned_user_id, modified_user_id, created_by, name, c_producto_c productos
from tasks A inner join tasks_cstm B on A.id = B.id_c
order by date_modified desc;

-- Extracción de tareas asociadas a proyectos.
DROP VIEW IF EXISTS tareas_proyecto;
CREATE VIEW tareas_proyecto AS
select A.id, assigned_user_id, modified_user_id, created_by, name, c_producto_c productos
from project_task A inner join project_task_cstm B on A.id = B.id_c
order by date_modified desc;

-- Extracción de reuniones.
DROP VIEW IF EXISTS reuniones;
CREATE VIEW reuniones AS
select A.id, assigned_user_id, modified_user_id, created_by, name, l_producto_c productos
from meetings A inner join meetings_cstm B on A.id = B.id_c
order by date_modified desc;

-- Extracción de todas las actividades.
DROP VIEW IF EXISTS actividades;
CREATE VIEW actividades AS
select 't', id, assigned_user_id, modified_user_id, created_by, name, productos
  from tareas
UNION
select 'p', id, assigned_user_id, modified_user_id, created_by, name, productos
  from tareas_proyecto
UNION
select 'm', id, assigned_user_id, modified_user_id, created_by, name, productos
  from reuniones order by name;

-- Extracción de actividades registradas para gastos
DROP VIEW IF EXISTS cabeceras;
CREATE VIEW cabeceras AS
select A.id, assigned_user_id, name, productos
  from kexm00t A inner join actividades B on A.id = B.id;

-- Extracción de gastos relacionados a actividades
DROP VIEW IF EXISTS detalles;
CREATE VIEW detalles AS
select id actividad, consecutive consecutivo, date_expenditure fecha, type_expenditure tipo, value valor, is_iva iva, status estatus
  from kexm01t
order by date_expenditure;

-- Extracción de totales por actividad registrada
DROP VIEW IF EXISTS totales;
CREATE VIEW totales AS
select id, sum(value) importe, count(consecutive) registros
  from kexm01t
group by id;

DROP VIEW IF EXISTS actividades_pendientes;
CREATE VIEW actividades_pendientes AS
select A.id activitie_id, A.assigned_user_id, getUserAccount(A.assigned_user_id) user_account, getUserName(A.assigned_user_id) user_name,
       A.name activitie_name, B.date_entered, B.status, ifnull(getTotalExpenditure(A.id),0) expenditure, ifnull(getTotalPayed(A.id),0) payed
  from actividades A inner join kexm00t B on A.id = B.id
order by user_name, B.date_entered;

DROP VIEW IF EXISTS gastos;
CREATE VIEW gastos AS
select A.id activitie, A.consecutive expenditure, date_expenditure, type_expenditure, 
       getDescription('cattipgto',type_expenditure) type_expenditure_name, value, status
  from kexm01t A
order by date_expenditure;

DROP VIEW IF EXISTS gastos_pendientes;
CREATE VIEW gastos_pendientes AS
select activitie, expenditure, date_expenditure, type_expenditure, type_expenditure_name, value
  from gastos
 where status = 'P'
order by date_expenditure;


-- Inserta datos de catalogo
truncate table ktcm00t;
INSERT INTO ktcm00t values ('catgral', 'tipact', 'Tipos de actividad','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('catgral', 'tipgto', 'Tipos de gasto','admin','carga.inicial',current_timestamp);

INSERT INTO ktcm00t values ('cattipact', 'T', 'Tarea','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipact', 'P', 'Tarea de proyecto','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipact', 'M', 'Reuni&oacute;n','admin','carga.inicial',current_timestamp);

INSERT INTO ktcm00t values ('cattipgto', 'H', 'Hospedaje','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipgto', 'A', 'Alimentaci&oacute;n','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipgto', 'T', 'Transportaci&oacute;n  Aerea','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipgto', 'G', 'Gasolina','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipgto', 'D', 'Propina','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipgto', 'P', 'Estacionamiento','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipgto', 'V', 'Diversos','admin','carga.inicial',current_timestamp);

INSERT INTO ktcm00t values ('catestgto', 'C', 'Cerrado','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('catestgto', 'P', 'Pendiente','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('catestgto', 'L', 'Liquidado','admin','carga.inicial',current_timestamp);

INSERT INTO ktcm00t values ('catestact', 'O', 'Abierto','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('catestact', 'C', 'Cerrado','admin','carga.inicial',current_timestamp);

INSERT INTO kaqm00t values ('1', '1', 'getUser()','carga.inicial',current_timestamp);


-- ************************************************************************** --
-- Ajustes para la versión 1.1

-- calcula el ultimo ID de usuarios para crear uno nuevo:
create function getLastUserId()
    returns int deterministic
return (select max(cast(id as unsigned)) from users);

-- calcula un nuevo código para los registros sin actividad
create function getCode(param varchar(20))
    returns char(36) deterministic
    return (select concat(substring(SHA1(param),1,4),'-',
                            substring(SHA1(param),5,12),'-',
                            substring(SHA1(param),18,12),'-',
                            substring(SHA1(param),29,4)));

alter table kexm00t
    add column user_id char(36) null,
    add column activitie_name varchar(120) null;



DROP VIEW IF EXISTS oportunidades;
CREATE VIEW oportunidades AS
select A.id, assigned_user_id, modified_user_id, created_by, name, c_producto_c productos
from opportunities A inner join opportunities_cstm B on A.id = B.id_c
order by date_modified desc;

DROP VIEW IF EXISTS campanias;
CREATE VIEW campanias AS
select A.id, assigned_user_id, modified_user_id, created_by, name, c_producto_c productos
from campaigns A inner join campaigns_cstm B on A.id = B.id_c
order by date_modified desc;


DROP VIEW IF EXISTS actividades;
CREATE VIEW actividades AS
select 't', id, assigned_user_id, modified_user_id, created_by, name, productos
  from tareas 
UNION
select 'p', id, assigned_user_id, modified_user_id, created_by, name, productos
  from tareas_proyecto 
UNION
select 'm', id, assigned_user_id, modified_user_id, created_by, name, productos
  from reuniones 
UNION
select 'o', id, assigned_user_id, modified_user_id, created_by, name, productos
  from oportunidades 
UNION
select 'c', id, assigned_user_id, modified_user_id, created_by, name, productos
  from campanias order by name;

INSERT INTO ktcm00t values ('cattipact', 'O', 'Oportunidad','admin','carga.inicial',current_timestamp);
INSERT INTO ktcm00t values ('cattipact', 'C', 'Campa&ntilde;a','admin','carga.inicial',current_timestamp);

-- ************************************************************************** --

-- TERMINA SCRIPT DE ADECUACIONES A BASE DE DATOS. --