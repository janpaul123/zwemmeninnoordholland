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
-- Name: locatie_photos; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE locatie_photos (
    locatie_id character varying(80) NOT NULL,
    photo_id bigint NOT NULL
);


ALTER TABLE public.locatie_photos OWNER TO postgres;

--
-- Name: locatie_photos_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY locatie_photos
    ADD CONSTRAINT locatie_photos_pkey PRIMARY KEY (locatie_id, photo_id);


--
-- Name: fkey_locatie; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY locatie_photos
    ADD CONSTRAINT fkey_locatie FOREIGN KEY (locatie_id) REFERENCES locaties(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fkey_photo; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY locatie_photos
    ADD CONSTRAINT fkey_photo FOREIGN KEY (photo_id) REFERENCES photos(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

