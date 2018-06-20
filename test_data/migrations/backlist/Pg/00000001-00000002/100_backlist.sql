CREATE TABLE address (
    id serial NOT NULL,
    city character varying(128) NOT NULL,
    street character varying(128) NOT NULL,
    house_number integer NOT NULL,
    hull_number character varying(128) NOT NULL,
    apartment_number integer NOT NULL,
    CONSTRAINT address_pkey PRIMARY KEY (id)
);
COMMENT ON TABLE address IS 'Для запису адреси автора, покупця та видавництва';
COMMENT ON COLUMN address.id IS 'Ідентифікатор адрес';
COMMENT ON COLUMN address.city IS 'Місто проживання';
COMMENT ON COLUMN address.street IS 'Вулиця проживання';
COMMENT ON COLUMN address.house_number IS 'Будинок проживання';
COMMENT ON COLUMN address.hull_number IS 'Номер корпусу';
COMMENT ON COLUMN address.apartment_number IS 'Номер квартири';

CREATE TABLE phone (
    id serial NOT NULL,
    phone_type character varying(128) NOT NULL,
    phone_number integer NOT NULL,
    CONSTRAINT phone_pkey PRIMARY KEY (id)
);
COMMENT ON TABLE phone IS 'Для запису телефонів автора, покупця та видавництва';
COMMENT ON COLUMN phone.id IS 'Ідентифікатор телефонів';
COMMENT ON COLUMN phone.phone_type IS 'Тип телефону';
COMMENT ON COLUMN phone.phone_number IS 'Номер телефону';

CREATE TABLE contacts (
    id serial NOT NULL,
    id_address integer NOT NULL,
    id_phone integer NOT NULL,
    email character varying(128)[] NOT NULL,
    CONSTRAINT contacts_pkey PRIMARY KEY (id),
    CONSTRAINT contacts_id_address_fkey FOREIGN KEY (id_address)
        REFERENCES address (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE,
    CONSTRAINT contacts_id_phone_fkey FOREIGN KEY (id_phone)
        REFERENCES phone (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE contacts IS 'Для запису контактів автора, покупця та видавництва';
COMMENT ON COLUMN contacts.id IS 'Ідентифікатор контактів';
COMMENT ON COLUMN contacts.id_address IS 'Посилання на адресу';
COMMENT ON COLUMN contacts.id_phone IS 'Посилання на номер телефону';
COMMENT ON COLUMN contacts.email IS 'Поштова скринька';

ALTER TABLE edition ADD COLUMN id_contacts integer NOT NULL;

ALTER TABLE edition ADD CONSTRAINT edition_id_contacts_fkey FOREIGN KEY (id_contacts)
    REFERENCES contacts (id) MATCH SIMPLE
    ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE;

COMMENT ON COLUMN edition.id_contacts IS 'Додавання поля в таблицю видавництва, що посилається на контакти';
