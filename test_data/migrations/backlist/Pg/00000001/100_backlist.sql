CREATE TABLE migration_schema_version (
    name character varying(128) NOT NULL,
    version real NOT NULL,
    CONSTRAINT migration_schema_version_pkey PRIMARY KEY (name)
);

CREATE TABLE migration_schema_log (
    id serial NOT NULL,
    schema_name character varying(128) NOT NULL,
    event_time timestamp with time zone DEFAULT now() NOT NULL,
    old_version real DEFAULT 0 NOT NULL,
    new_version real NOT NULL,
    CONSTRAINT migration_schema_log_pkey PRIMARY KEY (id),
    CONSTRAINT migration_schema_log_schema_name_fkey FOREIGN KEY (schema_name)
        REFERENCES migration_schema_version (name) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE CASCADE DEFERRABLE INITIALLY IMMEDIATE
);

CREATE TABLE date (
    id serial NOT NULL,
    title character varying(128) NOT NULL,
    CONSTRAINT date_pkey PRIMARY KEY (id)
);
COMMENT ON TABLE date IS 'Інформація про дату покупки книги і дату видання книги';
COMMENT ON COLUMN date.id IS 'Ідентифікатор дат';
COMMENT ON COLUMN date.title IS 'Конкретна дата';

CREATE TABLE edition (
    id serial NOT NULL,
    title character varying(128) NOT NULL,
    CONSTRAINT edition_pkey PRIMARY KEY (id)

);
COMMENT ON TABLE edition IS 'Дані про видавництво конкретної книги';
COMMENT ON COLUMN edition.id IS 'Ідентифікатор видавництва';
COMMENT ON COLUMN edition.title IS 'Назва видавництва';

CREATE TABLE book (
    id serial NOT NULL,
    title character varying(128) NOT NULL,
    isbn character varying(128) NOT NULL,
    id_edition integer NOT NULL,
    id_edition_date integer NOT NULL,
    CONSTRAINT book_pkey PRIMARY KEY (id),
    CONSTRAINT book_id_edition_fkey FOREIGN KEY (id_edition)
        REFERENCES edition (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE,
    CONSTRAINT book_id_edition_date_fkey FOREIGN KEY (id_edition_date)
        REFERENCES date (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE book IS 'Дані про конкретну книгу';
COMMENT ON COLUMN book.id IS 'Ідентифікатор книг';
COMMENT ON COLUMN book.title IS 'Назва книги';
COMMENT ON COLUMN book.isbn IS 'Міжнародний стандартний номер книги';
COMMENT ON COLUMN book.id_edition IS 'Видавництво, яке випустило цю книгу';
COMMENT ON COLUMN book.id_edition_date IS 'Дата видання книги';
