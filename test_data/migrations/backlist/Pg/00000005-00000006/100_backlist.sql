CREATE TABLE book_structure (
	id serial NOT NULL,
	structure_element character varying(128) NOT NULL,
    CONSTRAINT book_structure_pkey PRIMARY KEY (id)
);
COMMENT ON TABLE book_structure IS 'Конкретні структурні елементи (анотація, зміст, ...)';
COMMENT ON COLUMN book_structure.id IS 'Ідентифікатор таких елементів';
COMMENT ON COLUMN book_structure.structure_element IS 'Структурні елементи';

CREATE TABLE book_type (
    id serial NOT NULL,
    id_book integer NOT NULL,
    title character varying(128)[] NOT NULL,
    id_book_structure integer NOT NULL,
    CONSTRAINT book_type_pkey PRIMARY KEY (id),
    CONSTRAINT book_type_id_book_fkey FOREIGN KEY (id_book)
        REFERENCES book (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE,
    CONSTRAINT book_type_id_book_structure_fkey FOREIGN KEY (id_book_structure)
        REFERENCES book_structure (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE book_type IS 'Конкретний тип книги';
COMMENT ON COLUMN book_type.id IS 'Ідентифікатор таких типів';
COMMENT ON COLUMN book_type.id_book IS 'Посилання на Конкретну книгу';
COMMENT ON COLUMN book_type.title IS 'Перелік типів';
COMMENT ON COLUMN book_type.id_book_structure IS 'Посилання на структуру';
