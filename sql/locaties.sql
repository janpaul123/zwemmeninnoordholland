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
-- Name: locaties; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE locaties (
    zwemfolder character varying(8) NOT NULL,
    naam character varying(48) NOT NULL,
    cbs character varying(24) NOT NULL,
    loc geometry NOT NULL,
    gloc geometry NOT NULL,
    drijflijn boolean NOT NULL,
    aflopend boolean NOT NULL,
    zandstrand boolean NOT NULL,
    toiletten boolean NOT NULL,
    douches boolean NOT NULL,
    restaurant boolean NOT NULL,
    toegankelijk boolean NOT NULL,
    ov boolean NOT NULL,
    parkeerplaats boolean NOT NULL,
    huisdieren boolean NOT NULL,
    ehbo boolean NOT NULL,
    doorzicht integer NOT NULL,
    giftig integer NOT NULL,
    advies character varying(44) NOT NULL,
    opmerking character varying(254) NOT NULL,
    id character varying(80) NOT NULL,
    CONSTRAINT enforce_dims_gloc CHECK ((st_ndims(gloc) = 2)),
    CONSTRAINT enforce_dims_loc CHECK ((st_ndims(loc) = 2)),
    CONSTRAINT enforce_geotype_gloc CHECK (((geometrytype(gloc) = 'POINT'::text) OR (gloc IS NULL))),
    CONSTRAINT enforce_geotype_loc CHECK (((geometrytype(loc) = 'POINT'::text) OR (loc IS NULL))),
    CONSTRAINT enforce_srid_gloc CHECK ((st_srid(gloc) = 4326)),
    CONSTRAINT enforce_srid_loc CHECK ((st_srid(loc) = 28992))
);


ALTER TABLE public.locaties OWNER TO postgres;

--
-- Name: locaties_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY locaties
    ADD CONSTRAINT locaties_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

