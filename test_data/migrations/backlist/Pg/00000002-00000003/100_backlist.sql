CREATE TABLE buyer (
    id serial NOT NULL,
    full_name character varying(128) NOT NULL,
    id_contacts integer NOT NULL,
    CONSTRAINT buyer_pkey PRIMARY KEY (id),
    CONSTRAINT buyer_id_contacts_fkey FOREIGN KEY (id_contacts)
        REFERENCES contacts (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE buyer IS 'Дані конкретного покупця книги';
COMMENT ON COLUMN buyer.id IS 'Ідентифікатор покупців';
COMMENT ON COLUMN buyer.full_name IS 'Повне ім''я покупця';
COMMENT ON COLUMN buyer.id_contacts IS 'Контакти покупця (посилання)';

CREATE TABLE price (
    id serial NOT NULL,
    book_price double precision NOT NULL,
    id_date_of_buy integer NOT NULL,
    id_buyer integer NOT NULL,
    CONSTRAINT price_pkey PRIMARY KEY (id),
    CONSTRAINT price_id_date_of_buy_fkey FOREIGN KEY (id_date_of_buy)
        REFERENCES date (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE,
    CONSTRAINT price_id_buyer FOREIGN KEY (id_buyer)
        REFERENCES buyer (id) MATCH SIMPLE
        ON UPDATE CASCADE ON DELETE RESTRICT DEFERRABLE INITIALLY IMMEDIATE
);
COMMENT ON TABLE price IS 'Дані про ціну конкретної книги, а також дата її покупки';
COMMENT ON COLUMN price.id IS 'Ідентифікатор цін';
COMMENT ON COLUMN price.book_price IS 'Ціна конкретної книги';
COMMENT ON COLUMN price.id_date_of_buy IS 'Дата покупки конкретної книги (посилання)';
COMMENT ON COLUMN price.id_buyer IS 'Дані про покупця (посилання)';
