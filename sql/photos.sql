--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: photos; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE photos (
    score integer DEFAULT 0 NOT NULL,
    votes integer DEFAULT 0 NOT NULL,
    owner_id integer NOT NULL,
    owner_name character varying(256) NOT NULL,
    id bigint NOT NULL
);


ALTER TABLE public.photos OWNER TO postgres;

--
-- Name: pkey_id; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY photos
    ADD CONSTRAINT pkey_id PRIMARY KEY (id);


--
-- Name: id_score; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX id_score ON photos USING btree (id, score);


--
-- PostgreSQL database dump complete
--

