ALTER TABLE edition DROP CONSTRAINT edition_id_contacts_fkey;

ALTER TABLE edition DROP COLUMN id_contacts;

DROP TABLE contacts;

DROP TABLE phone;

DROP TABLE address;
