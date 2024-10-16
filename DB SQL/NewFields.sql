alter table category_operations modify form longtext default null;
alter table procedures add column way_to_pay tinyint default null after status;
alter table procedures add column property_type tinyint default null after status;
alter table procedures add column real_estate_folio varchar(250) default null after status;
alter table procedures add column meters_land varchar(250) default null after status;
alter table procedures add column construction_meters varchar(250) default null after status;
alter table inversion_units change date name varchar(250);
alter table units change year name varchar(250);
alter table grantor_procedure add percentage decimal(15,4) default null after procedure_id;
alter table grantor_procedure add amount decimal(15,4) default null after procedure_id;
alter table units modify value decimal(20,8);
ALTER TABLE procedures DROP FOREIGN KEY procedures_operation_id_foreign;
alter table procedures modify operation_id integer default null;