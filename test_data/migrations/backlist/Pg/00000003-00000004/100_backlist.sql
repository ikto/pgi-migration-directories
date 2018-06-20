CREATE TABLE author (
    id serial NOT NULL,
    full_name character varying(128) NOT NULL,
    alias character varying(128)[] NOT NULL,
    id_contacts integer NOT NULL,
    CONSTRAINT author_pkey PRIMARY KEY (id),
    CONSTRAINT author_id_contacts_fkey FOREIGN KEY (id_contacts)
        REFERENCES contacts (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE author IS 'Дані про конкретного автора книги';
COMMENT ON COLUMN author.id IS 'Ідентифікатор авторів';
COMMENT ON COLUMN author.full_name IS 'Повне ім''я автора';
COMMENT ON COLUMN author.alias IS 'Псевдонім автора';
COMMENT ON COLUMN author.id_contacts IS 'Контакти автора (посилання)';

CREATE TABLE book_author (
    id serial NOT NULL,
    id_author integer NOT NULL,
    id_book integer NOT NULL,
    CONSTRAINT book_author_pkey PRIMARY KEY (id),
    CONSTRAINT book_author_id_author_fkey FOREIGN KEY (id_author)
        REFERENCES author (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE,
    CONSTRAINT book_author_id_book_fkey FOREIGN KEY (id_book)
        REFERENCES book (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE book_author IS 'Зв''язок автора і його книг';
COMMENT ON COLUMN book_author.id IS 'Ідентифікатор таких зв''язків';
COMMENT ON COLUMN book_author.id_author IS 'Посилання на конкретного автора';
COMMENT ON COLUMN book_author.id_book IS 'Посилання на конкретну книгу';
