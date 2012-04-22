--
-- PostgreSQL database dump
--

SET client_encoding = 'UTF8';
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: category; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE category (
    id serial NOT NULL,
    name text,
    description text,
    comments text,
    amount numeric,
    parent integer,
    color text,
    deleted smallint DEFAULT 0
);


--
-- Name: category_period; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE category_period (
    id serial NOT NULL,
    category_id integer,
    period text,
    amount numeric,
    deleted smallint DEFAULT 0
);


--
-- Name: expense; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE expense (
    id serial NOT NULL,
    category_id integer,
    amount numeric,
    "comment" text,
    store text,
    entered_by integer,
    date date,
    deleted smallint DEFAULT 0,
    unique_id text,
	span_months integer default 1 NOT NULL
);


--
-- Name: person; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE person (
    id serial NOT NULL,
    name text
);


--
-- Name: category_period_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY category_period
    ADD CONSTRAINT category_period_pkey PRIMARY KEY (id);


--
-- Name: category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY category
    ADD CONSTRAINT category_pkey PRIMARY KEY (id);


--
-- Name: expense_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY expense
    ADD CONSTRAINT expense_pkey PRIMARY KEY (id);


--
-- Name: person_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY person
    ADD CONSTRAINT person_pkey PRIMARY KEY (id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

