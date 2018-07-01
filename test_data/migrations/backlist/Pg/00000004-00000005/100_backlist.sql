CREATE TABLE genre (
    id serial NOT NULL,
    title character varying(128) NOT NULL,
    CONSTRAINT genre_pkey PRIMARY KEY (id)
);
COMMENT ON TABLE genre IS 'Конкретний жанр книги';
COMMENT ON COLUMN genre.id IS 'Ідентифікатор жанрів';
COMMENT ON COLUMN genre.title IS 'Назва жанру';

CREATE TABLE book_genre (
    id serial NOT NULL,
    id_book integer NOT NULL,
    id_genre integer NOT NULL,
    CONSTRAINT book_genre_pkey PRIMARY KEY (id),
    CONSTRAINT book_genre_id_book_fkey FOREIGN KEY (id_book)
        REFERENCES book (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE,
    CONSTRAINT book_genre_id_genre_fkey FOREIGN KEY (id_genre)
        REFERENCES genre (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE book_genre IS 'Зв''язок книги з її жанрами';
COMMENT ON COLUMN book_genre.id IS 'Ідентифікатор таких зв''язків';
COMMENT ON COLUMN book_genre.id_book IS 'Посилання на конкретну книгу';
COMMENT ON COLUMN book_genre.id_genre IS 'Посилання на конкретні жанри';
