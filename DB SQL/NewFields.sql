alter table category_operations modify form longtext default null;
alter table procedures add column way_to_pay tinyint default null after status;
alter table procedures add column property_type tinyint default null after status;
alter table procedures add column real_estate_folio varchar(250) default null after status;
alter table procedures add column meters_land varchar(250) default null after status;
alter table procedures add column construction_meters varchar(250) default null after status;
alter table inversion_units change date name varchar(250);