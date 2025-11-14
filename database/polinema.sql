--
-- PostgreSQL database dump
--

-- Dumped from database version 16.8
-- Dumped by pg_dump version 16.8

-- Started on 2025-04-22 06:36:34

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 4 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: pg_database_owner
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO pg_database_owner;

--
-- TOC entry 4963 (class 0 OID 0)
-- Dependencies: 4
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: pg_database_owner
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 220 (class 1259 OID 17162)
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 17169)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 17395)
-- Name: communities; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.communities (
    community_id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    logo character varying(255),
    created_dt timestamp(0) without time zone
);


ALTER TABLE public.communities OWNER TO postgres;

--
-- TOC entry 230 (class 1259 OID 17402)
-- Name: community_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.community_user (
    community_id character varying(255) NOT NULL,
    user_id character varying(255) NOT NULL,
    joined_dt timestamp(0) without time zone
);


ALTER TABLE public.community_user OWNER TO postgres;

--
-- TOC entry 226 (class 1259 OID 17347)
-- Name: discussion; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discussion (
    discus_id character varying(50) NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    attachment character varying(255),
    attachment_mime character varying(50),
    created_dt timestamp(0) without time zone NOT NULL,
    approved_dt timestamp(0) without time zone,
    user_id character varying(50) NOT NULL,
    view_cnt integer DEFAULT 0 NOT NULL,
    like_cnt integer DEFAULT 0 NOT NULL,
    comment_cnt integer DEFAULT 0 NOT NULL,
    community_id character varying(255)
);


ALTER TABLE public.discussion OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 17362)
-- Name: discussion_comment; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discussion_comment (
    user_id character varying(50) NOT NULL,
    discus_id character varying(50) NOT NULL,
    created_dt timestamp(0) without time zone NOT NULL,
    content text NOT NULL,
    attachment character varying(255),
    attachment_mime character varying(50)
);


ALTER TABLE public.discussion_comment OWNER TO postgres;

--
-- TOC entry 228 (class 1259 OID 17379)
-- Name: discussion_like; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.discussion_like (
    user_id character varying(50) NOT NULL,
    discus_id character varying(50) NOT NULL,
    created_dt timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.discussion_like OWNER TO postgres;

--
-- TOC entry 222 (class 1259 OID 17182)
-- Name: friend; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.friend (
    user_id_1 character varying(50) NOT NULL,
    user_id_2 character varying(50) NOT NULL,
    created_dt timestamp(0) without time zone,
    accepted_dt timestamp(0) without time zone,
    status character varying(15) DEFAULT 'pending'::character varying NOT NULL
);


ALTER TABLE public.friend OWNER TO postgres;

--
-- TOC entry 216 (class 1259 OID 16853)
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- TOC entry 215 (class 1259 OID 16852)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- TOC entry 4964 (class 0 OID 0)
-- Dependencies: 215
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 219 (class 1259 OID 16870)
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_id character varying(50) NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- TOC entry 218 (class 1259 OID 16869)
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- TOC entry 4965 (class 0 OID 0)
-- Dependencies: 218
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- TOC entry 223 (class 1259 OID 17226)
-- Name: tenant_categories; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tenant_categories (
    tencat_id character varying(50) NOT NULL,
    name character varying(255),
    icon character varying(255),
    status character varying(15) DEFAULT 'active'::character varying NOT NULL
);


ALTER TABLE public.tenant_categories OWNER TO postgres;

--
-- TOC entry 224 (class 1259 OID 17234)
-- Name: tenants; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.tenants (
    tenant_id character varying(50) NOT NULL,
    tencat_id character varying(50) NOT NULL,
    name character varying(255),
    banner character varying(255),
    address text,
    about text,
    image_1 character varying(255),
    image_2 character varying(255),
    image_3 character varying(255),
    image_4 character varying(255),
    lat character varying(20),
    lng character varying(20),
    status character varying(15) DEFAULT 'active'::character varying NOT NULL,
    created_dt timestamp(0) without time zone,
    created_by character varying(50)
);


ALTER TABLE public.tenants OWNER TO postgres;

--
-- TOC entry 217 (class 1259 OID 16859)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    user_id character varying(50) NOT NULL,
    name character varying(255),
    email character varying(255) NOT NULL,
    password character varying(255),
    photo character varying(255),
    major character varying(255),
    year_generation integer,
    job character varying(255),
    location character varying(255),
    status character varying(15) DEFAULT 'inactive'::character varying,
    lat character varying(20),
    lng character varying(20),
    role character varying(50),
    otp_code character varying(6),
    otp_expires_at timestamp(0) without time zone,
    created_dt timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 17247)
-- Name: vouchers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.vouchers (
    voucher_id character varying(50) NOT NULL,
    tenant_id character varying(50) NOT NULL,
    title character varying(255),
    description text,
    banner_1 character varying(255),
    banner_2 character varying(255),
    banner_3 character varying(255),
    banner_4 character varying(255),
    discount_type character varying(50),
    discount_value numeric(10,2),
    minimum_amount numeric(10,2),
    maximum_discount numeric(10,2),
    start_dt timestamp(0) without time zone,
    end_dt timestamp(0) without time zone,
    is_claimed boolean DEFAULT false NOT NULL,
    quota integer DEFAULT 0 NOT NULL,
    used integer DEFAULT 0 NOT NULL,
    created_dt timestamp(0) without time zone,
    status character varying(15) DEFAULT 'active'::character varying NOT NULL
);


ALTER TABLE public.vouchers OWNER TO postgres;

--
-- TOC entry 4741 (class 2604 OID 16856)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 4743 (class 2604 OID 16873)
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- TOC entry 4947 (class 0 OID 17162)
-- Dependencies: 220
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- TOC entry 4948 (class 0 OID 17169)
-- Dependencies: 221
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 4956 (class 0 OID 17395)
-- Dependencies: 229
-- Data for Name: communities; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.communities (community_id, name, description, logo, created_dt) FROM stdin;
comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf	Komunitas Sepeda Santai	Komunitas sepeda santai yang suka aktif di jam pulang kerja dan weekend. Untuk semua alumni yang punya hobi olahraga sepeda boleh gabung di komunitas kita ya, seluruh informasi mengenai acaranya nanti di informasikan di dalam postingan grup komunitas ini. Terima kasih. Salah Sepedah!	\N	2025-04-20 16:38:59
comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e	Pecinta Catur	Tempat berkumpulnya alumni yang suka bermain catur, baik yang hobi santai maupun yang sering ikut turnamen. Kita rutin adakan sparring online dan offline!	\N	2025-04-20 16:38:59
comun-ce26191f-bac7-4a43-b582-f57ce3dc1382	Sunmori Bareng	Sunmori alias Sunday Morning Ride bareng alumni lintas angkatan. Start dari tempat yang udah disepakati, dan finish di tempat makan bareng. Santai aja, nggak harus motor gede!	\N	2025-04-20 16:38:59
comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86	Bisnis Kuliner UMKM	Komunitas ini cocok buat kamu yang sedang atau ingin memulai bisnis kuliner. Sharing resep, strategi marketing, dan tips jualan setiap minggu!	\N	2025-04-20 16:38:59
comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a	Musik Tradisional Angklung	Yuk lestarikan budaya! Komunitas ini terbuka untuk semua alumni yang ingin belajar, memainkan, atau sekadar menikmati musik tradisional angklung.	\N	2025-04-20 16:38:59
\.


--
-- TOC entry 4957 (class 0 OID 17402)
-- Dependencies: 230
-- Data for Name: community_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.community_user (community_id, user_id, joined_dt) FROM stdin;
comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf	user-d57bd433-369b-4160-9b0b-aecb81eb6864	2025-03-28 16:38:59
comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf	user-a409abcf-1484-401e-800a-2d38d692dd20	2025-04-11 16:38:59
comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e	user-d57bd433-369b-4160-9b0b-aecb81eb6864	2025-04-09 16:38:59
comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e	user-a409abcf-1484-401e-800a-2d38d692dd20	2025-04-19 16:38:59
comun-ce26191f-bac7-4a43-b582-f57ce3dc1382	user-d57bd433-369b-4160-9b0b-aecb81eb6864	2025-04-14 16:38:59
comun-ce26191f-bac7-4a43-b582-f57ce3dc1382	user-a409abcf-1484-401e-800a-2d38d692dd20	2025-04-11 16:38:59
comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86	user-d57bd433-369b-4160-9b0b-aecb81eb6864	2025-03-29 16:38:59
comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86	user-a409abcf-1484-401e-800a-2d38d692dd20	2025-04-17 16:38:59
comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a	user-d57bd433-369b-4160-9b0b-aecb81eb6864	2025-03-26 16:38:59
comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a	user-a409abcf-1484-401e-800a-2d38d692dd20	2025-04-05 16:38:59
\.


--
-- TOC entry 4953 (class 0 OID 17347)
-- Dependencies: 226
-- Data for Name: discussion; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discussion (discus_id, title, content, attachment, attachment_mime, created_dt, approved_dt, user_id, view_cnt, like_cnt, comment_cnt, community_id) FROM stdin;
discus-b71578c0-345d-4b8e-8631-acaaad8e619d	Diskusi #1 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-03 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	127	28	4	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-855cc0ea-b0ad-4794-b39a-d7e868a1c912	Diskusi #2 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	75	9	7	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-59c89ca0-74be-4446-ac69-583c613f8a1b	Diskusi #3 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	59	20	8	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-1b95de32-d00e-4205-85f0-7146ed3acb25	Diskusi #4 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-19 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	142	20	3	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-a18d500b-47a5-44dc-a351-3b61d2ec9b58	Diskusi #5 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	104	19	0	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-cf42eae9-6969-4f82-a186-85197d84e7fb	Diskusi #6 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-19 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	47	26	3	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-a0810562-0dfc-4601-9690-8bd1e5468173	Diskusi #7 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-03-31 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	103	24	4	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-4a83790d-2707-4b31-90b9-85b737b85e6e	Diskusi #8 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-01 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	92	26	0	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-a2803011-4f3f-4eda-9907-00e1688d5187	Diskusi #9 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-03-31 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	31	27	1	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-0f0e4797-b791-4211-a5a8-bfad9a76e607	Diskusi #10 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-02 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	114	16	5	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-0d81d581-982d-4521-95e9-31c5235b147a	Diskusi #11 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-09 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	114	17	6	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-52d6a42e-ac0f-44af-8a27-6ca30bb360f6	Diskusi #12 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-16 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	80	25	10	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-a45b8df6-edc8-4de4-bcda-d99752ec4814	Diskusi #13 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-03 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	43	26	7	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-12c17d7c-d473-435e-a703-8890a991da0a	Diskusi #14 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-09 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	83	28	10	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-a10240cb-a6d4-4ccd-b4cb-c4f9e823a3aa	Diskusi #15 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-05 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	16	19	0	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-5f9401c5-fd76-44de-8447-242201b223b6	Diskusi #16 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-20 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	9	24	10	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-e297ce2e-ef26-4010-9aec-23632fe622ed	Diskusi #17 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-08 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	141	1	4	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-a5b359cc-5040-43c0-b2b9-1c647bc751ea	Diskusi #18 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	92	30	7	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-8e9c2015-9901-47f1-a052-88469660f7d6	Diskusi #19 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	23	12	3	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-d03425e0-f297-4467-afa9-817f963ae486	Diskusi #20 di Komunitas Sepeda Santai	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Komunitas Sepeda Santai". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-04 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	8	2	2	comun-0ed5fa43-3e96-4eff-8d51-dfb84e1cdcdf
discus-08bff32e-ca85-4949-bd0a-d6449b013b67	Diskusi #1 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	54	10	3	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-9ae86380-ca11-48aa-81a2-1908eb231878	Diskusi #2 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-02 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	18	28	2	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-ca6017c6-938d-4494-a34f-6895ad9197af	Diskusi #3 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	17	1	9	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-56287427-56be-4530-8902-14386bc1f7b0	Diskusi #4 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	46	1	9	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-5fd69609-719d-46e5-9c00-02c4dbf90ff0	Diskusi #5 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	109	21	1	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-75abcd25-2245-44f1-9c95-d3db968d4320	Diskusi #6 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-19 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	42	13	0	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-c8ec99bc-5c48-4cbe-81df-1ca185bdf41a	Diskusi #7 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-20 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	31	14	0	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-211f87bb-a0fb-4d3c-8e40-f43e2084283a	Diskusi #8 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-12 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	107	18	4	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-cb87bf05-56c0-418e-b9c0-66a06ba3f775	Diskusi #9 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-05 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	122	14	4	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-44267883-5603-4070-b212-319cd7b3ed96	Diskusi #10 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	92	18	6	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-06740cd1-af43-4352-ad11-fd3c44880840	Diskusi #11 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-03-31 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	89	21	9	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-4d2652ec-536c-41ed-bc42-c4886a5d1c17	Diskusi #12 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-03-31 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	147	14	0	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-0258faa4-69d8-4a22-a2d9-c4421dcfaac6	Diskusi #13 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	42	4	7	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-88958649-7afd-4c5b-92d5-8264c654ab83	Diskusi #14 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	51	27	5	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-7fc14113-5d03-4c9c-8892-1021db627bb7	Diskusi #15 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-16 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	41	0	7	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-b66d6067-968e-481b-ab41-6a4f1b8bbd33	Diskusi #16 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	107	23	2	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-447d44aa-6061-4fd2-9199-4b548d798e62	Diskusi #17 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	64	15	3	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-e659b6f6-421b-4684-b5be-cb077448ff50	Diskusi #18 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-03-31 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	114	26	5	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-bbe661ca-56ce-459b-8b23-80a5179c0df3	Diskusi #19 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-05 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	130	1	9	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-390778c2-0597-4a8f-b5c4-812ef4917faf	Diskusi #20 di Pecinta Catur	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Pecinta Catur". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-05 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	113	21	6	comun-97c6e016-3ab9-481a-bf16-36e3bc678e1e
discus-641aadda-f10a-47df-90ba-00f4a637c263	Diskusi #1 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-05 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	132	1	9	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-7cabe165-3586-45dd-9c25-9f5b519de6c8	Diskusi #2 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-06 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	80	2	7	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-4215d89d-365e-4f18-b799-5566843b45a8	Diskusi #3 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	76	27	3	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-3edff11c-0d5a-4c4b-ae00-abe8bfd7f623	Diskusi #4 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	55	16	1	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-af706396-e0bc-4955-a10b-2f7ccb9b4dba	Diskusi #5 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-02 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	109	4	3	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-800b0ae5-abe8-49a0-bd0c-2df5842c27be	Diskusi #6 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	124	27	2	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-6c4d7858-30bb-453a-92ec-f5669670231a	Diskusi #7 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-02 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	52	5	9	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-32aac713-0728-498f-8890-988ef763f115	Diskusi #8 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-02 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	70	25	3	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-2e133939-a136-423b-b6e0-541f51f7a523	Diskusi #9 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	109	24	6	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-ac53e075-f0ac-4b17-a70e-379cd3a06de1	Diskusi #10 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	9	2	3	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-34ec7640-53c6-499f-b161-9231ec03988d	Diskusi #11 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-04 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	88	2	1	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-13812f65-36c5-44a3-bb1f-9affaf8b6b1a	Diskusi #12 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	54	24	0	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-d982231e-cb30-41d6-9604-72f67ef6e6ca	Diskusi #13 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-19 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	123	4	0	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-0f7daf4d-63dd-46a1-b905-7c3c244febb5	Diskusi #14 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-03-31 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	85	15	3	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-459fd575-fc9f-4061-a97d-afb82bc2a785	Diskusi #15 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-09 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	23	0	6	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-661454e8-37fe-4ac0-aedf-d32e9b8cba57	Diskusi #16 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-04 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	142	4	0	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-fb43a03a-b8e0-4eb0-a783-e55a61190690	Diskusi #17 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-08 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	53	6	4	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-866e2a9f-2f30-4c3e-8eb7-510033fb4b7e	Diskusi #18 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	13	6	1	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-5e944c95-936c-45c8-b85c-ed005a0fcaa3	Diskusi #19 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	140	23	7	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-773a9b3b-61d8-4b72-a243-2af27ac5f89a	Diskusi #20 di Sunmori Bareng	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Sunmori Bareng". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-06 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	113	12	8	comun-ce26191f-bac7-4a43-b582-f57ce3dc1382
discus-cb71b745-c11c-44bb-9fdc-9af76c600d1a	Diskusi #1 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-09 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	11	28	6	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-e79e3717-df2a-4eb7-b732-56050d04b111	Diskusi #2 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-07 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	142	24	6	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-b20728c5-b9cb-4481-91a6-c3197b813a71	Diskusi #3 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	15	16	0	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-aad68ed9-f3bc-4750-98db-54e9d778b096	Diskusi #4 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	84	22	4	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-c7406bbd-a40b-423c-a793-f43b44506437	Diskusi #5 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-10 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	41	11	9	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-9b524938-b68f-482e-96ca-850ebf2b0e1b	Diskusi #6 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-08 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	145	1	1	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-de080e8e-7921-446e-9498-577e01858818	Diskusi #7 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	46	5	1	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-ae4b1c1b-b7f0-4f61-a4b3-c9812787a6a7	Diskusi #8 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-08 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	95	17	7	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-3060c21e-50e4-480d-8c3e-a8c306ef39bf	Diskusi #9 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	98	7	7	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-ad401289-dbe7-4d12-a2f1-7374927e55a1	Diskusi #10 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-01 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	102	20	9	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-193459a7-d143-4a69-996b-c61b4d9a4648	Diskusi #11 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-09 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	147	9	10	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-a429edda-3f03-49c1-89fa-835b262f5178	Diskusi #12 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-08 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	94	6	4	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-428e09ef-67c0-4126-8d6b-e66d90d58449	Diskusi #13 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	15	7	2	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-f73632af-a040-4e97-b7a7-a92aad4de2a9	Diskusi #14 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-16 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	46	27	9	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-81280288-b5e7-45e3-8550-b4dea7d63414	Diskusi #15 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-07 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	103	17	0	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-21c63376-72ae-4ec5-b1ad-bb4e2b583ee1	Diskusi #16 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	74	15	9	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-cab78795-97b6-4a09-ae49-2870026067c6	Diskusi #17 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-10 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	62	18	5	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-96583748-d462-4320-aeba-f105ed453ce5	Diskusi #18 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-08 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	105	12	9	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-e9a94b96-041e-446a-b798-1b15194f147e	Diskusi #19 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	142	27	9	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-295e1946-536d-4999-a839-9de9650abfbe	Diskusi #20 di Bisnis Kuliner UMKM	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Bisnis Kuliner UMKM". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-07 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	113	16	8	comun-c6d87948-fa74-427b-99ca-cb5d9ba6bf86
discus-f1610401-5105-4310-8286-8e8c8191cbb3	Diskusi #1 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-03 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	35	9	7	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-830447cf-1ded-47cd-b2c7-80fa62cf05e8	Diskusi #2 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-04 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	69	6	2	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-b7f48714-3dd8-4900-910e-af3c47adbb2b	Diskusi #3 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-19 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	119	6	0	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-d4937944-858f-490c-b752-265f89439f7e	Diskusi #4 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-13 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	141	24	7	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-0bbe348d-62d0-43db-a9a5-bcf682a16f26	Diskusi #5 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	19	5	10	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-ab000d7a-e462-4954-a309-edcae14ff13e	Diskusi #6 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	122	1	8	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-cd7950ca-af90-4034-a43e-e85da702188c	Diskusi #7 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	121	7	8	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-065c2bf5-c37b-4d77-acfd-ba3dc72d71ea	Diskusi #8 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-11 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	110	15	10	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-2f57c944-dda1-4736-8948-7630e61d09e0	Diskusi #9 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-18 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	94	28	10	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-2858c979-f656-4345-929a-e8e049078b53	Diskusi #10 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-16 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	62	6	2	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-19fb4a49-f0a7-46a3-9df0-293830c44084	Diskusi #11 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-20 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	75	6	5	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-1bc730f8-5e34-441d-a42a-615799ef0e58	Diskusi #12 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-09 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	73	7	1	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-8e38fc81-d3d7-460b-b00f-1a290269675f	Diskusi #13 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-15 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	34	2	4	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-0fc3c817-f0d8-4724-9af7-0857a2fbf1e8	Diskusi #14 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	47	26	2	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-2640a74e-9fa4-4fc9-b3cd-57a7cb1db71b	Diskusi #15 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-02 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	63	3	3	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-bd37c6f5-dd91-47eb-b110-9d3e5e216688	Diskusi #16 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-14 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	25	28	8	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-7069ab0d-a4a0-4523-8bff-cdc9c0489c19	Diskusi #17 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	38	16	10	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-3399d036-fcc8-4340-a832-7289a76233ae	Diskusi #18 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	137	1	4	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-788ab55b-b2af-4e80-8e19-485f25d70e08	Diskusi #19 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-20 16:38:59	\N	user-d57bd433-369b-4160-9b0b-aecb81eb6864	63	16	0	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-71e110da-5e4b-4d20-85be-226068f31b3a	Diskusi #20 di Musik Tradisional Angklung	Halo semua! Yuk sharing pengalaman, cerita lucu, atau tips seputar "Musik Tradisional Angklung". Ada yang pernah mengalami hal menarik?	\N	\N	2025-04-17 16:38:59	\N	user-a409abcf-1484-401e-800a-2d38d692dd20	121	21	2	comun-d81fd147-7cf0-4373-9cc4-ee76ddfa264a
discus-71f10939-1979-469c-8f8a-a36e285df6c9	Testing di postman	oke gais ini adalah testing data apakah masuk atau tidak??	http://127.0.0.1:8000/discussion/1745153296_uts-gis_haristfadlilah_232605005_SINR4_mapbandasari (1).png	image/png	2025-04-20 19:48:17	\N	user-58c9f8cd-e38a-43ad-bb0d-7b55ea88d1f1	0	0	0	\N
\.


--
-- TOC entry 4954 (class 0 OID 17362)
-- Dependencies: 227
-- Data for Name: discussion_comment; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discussion_comment (user_id, discus_id, created_dt, content, attachment, attachment_mime) FROM stdin;
\.


--
-- TOC entry 4955 (class 0 OID 17379)
-- Dependencies: 228
-- Data for Name: discussion_like; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.discussion_like (user_id, discus_id, created_dt) FROM stdin;
\.


--
-- TOC entry 4949 (class 0 OID 17182)
-- Dependencies: 222
-- Data for Name: friend; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.friend (user_id_1, user_id_2, created_dt, accepted_dt, status) FROM stdin;
\.


--
-- TOC entry 4943 (class 0 OID 16853)
-- Dependencies: 216
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2025_04_09_035959_create_user_table	1
2	2025_04_09_040022_create_personal_access_tokens_table	1
3	2025_04_11_173003_create_cache_table	2
4	2025_04_12_004041_create_friend_table	3
5	2025_04_12_233929_create_tenant_categories_table	4
6	2025_04_12_233938_create_tenants_table	4
7	2025_04_12_233943_create_vouchers_table	4
8	2025_04_20_103056_create_discussion_table	5
9	2025_04_20_103104_create_discussion_comment_table	5
10	2025_04_20_103110_create_discussion_like_table	5
11	2025_04_20_112034_create_communities_table	6
12	2025_04_20_112041_create_community_user_table	6
13	2025_04_20_163532_add_community_id_to_discussion_table	7
\.


--
-- TOC entry 4946 (class 0 OID 16870)
-- Dependencies: 219
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_id, tokenable_type, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 4950 (class 0 OID 17226)
-- Dependencies: 223
-- Data for Name: tenant_categories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tenant_categories (tencat_id, name, icon, status) FROM stdin;
ec743170-14d5-4633-8f99-7e0685a230c4	Resto	resto.png	active
45f6aecd-64fa-43d7-ae21-3904d6829ba4	Mini Market	mini_market.png	active
c1839880-fd12-44e6-bb8c-3ee2596da2f2	Cafe	cafe.png	active
809eec38-1432-43f8-92da-dcbc91d93d90	Penginapan	penginapan.png	active
63941107-4cc7-4532-80e9-eebe93f5fa30	Jasa	jasa.png	active
\.


--
-- TOC entry 4951 (class 0 OID 17234)
-- Dependencies: 224
-- Data for Name: tenants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.tenants (tenant_id, tencat_id, name, banner, address, about, image_1, image_2, image_3, image_4, lat, lng, status, created_dt, created_by) FROM stdin;
f0af41e0-14a9-4286-9d7a-4dd8aedfbd97	ec743170-14d5-4633-8f99-7e0685a230c4	Ayam Geprek Juara	ayam-geprek-juara_banner.jpg	Jl. Contoh No. 1, Bandung	Nikmati hidangan khas dari Ayam Geprek Juara	ayam-geprek-juara_1.jpg	ayam-geprek-juara_2.jpg	ayam-geprek-juara_3.jpg	ayam-geprek-juara_4.jpg	-6.908744	107.59631	active	2025-04-15 13:59:45	admin-1
ca8fa23e-3cbf-4c79-ab1e-5e7886370686	45f6aecd-64fa-43d7-ae21-3904d6829ba4	Bakso Mang Udin	bakso-mang-udin_banner.jpg	Jl. Contoh No. 2, Bandung	Nikmati hidangan khas dari Bakso Mang Udin	bakso-mang-udin_1.jpg	bakso-mang-udin_2.jpg	bakso-mang-udin_3.jpg	bakso-mang-udin_4.jpg	-6.943944	107.60401	active	2025-04-15 13:59:45	admin-2
4082079f-9e55-43e2-bdcb-f8c02ee38451	c1839880-fd12-44e6-bb8c-3ee2596da2f2	Sate Padang Uni	sate-padang-uni_banner.jpg	Jl. Contoh No. 3, Bandung	Nikmati hidangan khas dari Sate Padang Uni	sate-padang-uni_1.jpg	sate-padang-uni_2.jpg	sate-padang-uni_3.jpg	sate-padang-uni_4.jpg	-6.923244	107.62761	active	2025-04-15 13:59:45	admin-3
dafe54a5-9588-414b-bb17-caae7c322368	809eec38-1432-43f8-92da-dcbc91d93d90	Kopi Senja	kopi-senja_banner.jpg	Jl. Contoh No. 4, Bandung	Nikmati hidangan khas dari Kopi Senja	kopi-senja_1.jpg	kopi-senja_2.jpg	kopi-senja_3.jpg	kopi-senja_4.jpg	-6.934744	107.63771	active	2025-04-15 13:59:45	admin-4
3e3c21f4-03ee-40a2-ab1a-354b459aab22	63941107-4cc7-4532-80e9-eebe93f5fa30	Martabak Bang Jali	martabak-bang-jali_banner.jpg	Jl. Contoh No. 5, Bandung	Nikmati hidangan khas dari Martabak Bang Jali	martabak-bang-jali_1.jpg	martabak-bang-jali_2.jpg	martabak-bang-jali_3.jpg	martabak-bang-jali_4.jpg	-6.925344	107.62301	active	2025-04-15 13:59:45	admin-5
\.


--
-- TOC entry 4944 (class 0 OID 16859)
-- Dependencies: 217
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (user_id, name, email, password, photo, major, year_generation, job, location, status, lat, lng, role, otp_code, otp_expires_at, created_dt) FROM stdin;
user-d57bd433-369b-4160-9b0b-aecb81eb6864	Andi Wijaya	andi@example.com	$2y$12$9D6aju/TTVAycMyb9NBi6Op/Jj7UJBureBtzuCrd4cSQfYkdcBTwS	http://127.0.0.1:8000/uploads/profile_pictures/1744464238_user-perempuan.jpeg	Teknik Informatika	2020	Software Engineer	Alun-Alun Bandung	active	-6.921967	107.606979	user	\N	\N	2025-04-20 15:37:17
user-a409abcf-1484-401e-800a-2d38d692dd20	Budi Santoso	budi@example.com	$2y$12$3EJO/kR2t7qNsxJe0B0W6.Gz.FSmlG4yKk08gM54Ohni3d5Vvz5a6	http://127.0.0.1:8000/uploads/profile_pictures/1744464238_user-perempuan.jpeg	Sistem Informasi	2019	Data Analyst	Braga, Bandung	active	-6.916469	107.609558	user	\N	\N	2025-04-20 15:37:17
user-3a57283b-5e04-4c5b-b14f-571705aee110	Citra Dewi	citra@example.com	$2y$12$R85inpS04tF/.LR9L1GrGeSUPa2m31JA/IKwOB1VHoGytACm1XEhq	http://127.0.0.1:8000/uploads/profile_pictures/1744464238_user-perempuan.jpeg	Teknik Komputer	2021	UI/UX Designer	Balai Kota Bandung	active	-6.912755	107.609146	user	\N	\N	2025-04-20 15:37:17
user-0ff240b3-53be-46b8-a7e8-877688c0a7d2	Dewi Lestari	dewi@example.com	$2y$12$Q6vLxqA0vdc5P6T84/ipauFedzqzusuD2uRhWzMC6tUunW9ewChri	http://127.0.0.1:8000/uploads/profile_pictures/1744464238_user-perempuan.jpeg	Manajemen	2022	Project Manager	BIP, Bandung	active	-6.900496	107.618347	user	\N	\N	2025-04-20 15:37:17
user-059d74e1-75af-4be7-8b6a-92dce21c9555	Eko Prasetyo	eko@example.com	$2y$12$u/EX13AZM7QwdXva4nIKU.w2RTiVE1pIfSq.gKi7bWLP7V6Iojls6	http://127.0.0.1:8000/uploads/profile_pictures/1744464238_user-perempuan.jpeg	Teknik Elektro	2023	DevOps Engineer	Pasar Baru, Bandung	active	-6.919632	107.604027	user	\N	\N	2025-04-20 15:37:18
user-58c9f8cd-e38a-43ad-bb0d-7b55ea88d1f1	Polinema Indonesia	testingakunajalah@gmail.com	$2y$12$r7vu65VMS5cYG0v7gqXwe.f2JDn0S1Lri1Hx4zfjH/efw6XmhYLxC	http://127.0.0.1:8000/uploads/profile_pictures/1744464238_user-perempuan.jpeg	Teknik Komputer	2020	Freelancer	Bandung	active	-6.921967	107.606979	user	\N	\N	2025-04-20 15:37:18
\.


--
-- TOC entry 4952 (class 0 OID 17247)
-- Dependencies: 225
-- Data for Name: vouchers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.vouchers (voucher_id, tenant_id, title, description, banner_1, banner_2, banner_3, banner_4, discount_type, discount_value, minimum_amount, maximum_discount, start_dt, end_dt, is_claimed, quota, used, created_dt, status) FROM stdin;
cc026d3b-e7dd-4359-9831-f5821c6ccfbf	f0af41e0-14a9-4286-9d7a-4dd8aedfbd97	Diskon 35%	Gunakan voucher 'Diskon 35%' sekarang juga! 	voucher_1_1.jpg	voucher_1_2.jpg	voucher_1_3.jpg	voucher_1_4.jpg	percentage	30.00	71.00	32.00	2025-04-15 13:59:51	2025-05-06 13:59:51	f	100	0	2025-04-15 13:59:51	active
8d68f24c-ecfd-4cdd-8d58-73f28d6340b3	ca8fa23e-3cbf-4c79-ab1e-5e7886370686	Buy 1 Get 1	Gunakan voucher 'Buy 1 Get 1' sekarang juga! 	voucher_2_1.jpg	voucher_2_2.jpg	voucher_2_3.jpg	voucher_2_4.jpg	percentage	50.00	187.00	94.00	2025-04-15 13:59:51	2025-06-07 13:59:51	f	100	0	2025-04-15 13:59:51	active
9e31472f-8d9a-4a70-8a3f-c8610bd7ee6b	4082079f-9e55-43e2-bdcb-f8c02ee38451	Free Breakfast	Gunakan voucher 'Free Breakfast' sekarang juga! 	voucher_3_1.jpg	voucher_3_2.jpg	voucher_3_3.jpg	voucher_3_4.jpg	percentage	32.00	185.00	48.00	2025-04-15 13:59:51	2025-05-25 13:59:51	f	100	0	2025-04-15 13:59:51	active
0fa901a6-af6a-4434-a15c-e88ef358fb24	dafe54a5-9588-414b-bb17-caae7c322368	Free Drink	Gunakan voucher 'Free Drink' sekarang juga! 	voucher_4_1.jpg	voucher_4_2.jpg	voucher_4_3.jpg	voucher_4_4.jpg	percentage	44.00	59.00	66.00	2025-04-15 13:59:51	2025-05-28 13:59:51	f	100	0	2025-04-15 13:59:51	active
868bca87-034a-40d1-8eb9-9b9dc20fee1e	3e3c21f4-03ee-40a2-ab1a-354b459aab22	Menu Paket Hemat	Gunakan voucher 'Menu Paket Hemat' sekarang juga! 	voucher_5_1.jpg	voucher_5_2.jpg	voucher_5_3.jpg	voucher_5_4.jpg	percentage	40.00	159.00	77.00	2025-04-15 13:59:51	2025-06-04 13:59:51	f	100	0	2025-04-15 13:59:51	active
\.


--
-- TOC entry 4966 (class 0 OID 0)
-- Dependencies: 215
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 13, true);


--
-- TOC entry 4967 (class 0 OID 0)
-- Dependencies: 218
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- TOC entry 4768 (class 2606 OID 17175)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 4766 (class 2606 OID 17168)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 4784 (class 2606 OID 17401)
-- Name: communities communities_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.communities
    ADD CONSTRAINT communities_pkey PRIMARY KEY (community_id);


--
-- TOC entry 4786 (class 2606 OID 17408)
-- Name: community_user community_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.community_user
    ADD CONSTRAINT community_user_pkey PRIMARY KEY (community_id, user_id);


--
-- TOC entry 4780 (class 2606 OID 17368)
-- Name: discussion_comment discussion_comment_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion_comment
    ADD CONSTRAINT discussion_comment_pkey PRIMARY KEY (user_id, discus_id, created_dt);


--
-- TOC entry 4782 (class 2606 OID 17383)
-- Name: discussion_like discussion_like_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion_like
    ADD CONSTRAINT discussion_like_pkey PRIMARY KEY (user_id, discus_id, created_dt);


--
-- TOC entry 4778 (class 2606 OID 17361)
-- Name: discussion discussion_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion
    ADD CONSTRAINT discussion_pkey PRIMARY KEY (discus_id);


--
-- TOC entry 4770 (class 2606 OID 17187)
-- Name: friend friend_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.friend
    ADD CONSTRAINT friend_pkey PRIMARY KEY (user_id_1, user_id_2);


--
-- TOC entry 4755 (class 2606 OID 16858)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 4761 (class 2606 OID 16877)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 4763 (class 2606 OID 16885)
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- TOC entry 4772 (class 2606 OID 17233)
-- Name: tenant_categories tenant_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tenant_categories
    ADD CONSTRAINT tenant_categories_pkey PRIMARY KEY (tencat_id);


--
-- TOC entry 4774 (class 2606 OID 17246)
-- Name: tenants tenants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_pkey PRIMARY KEY (tenant_id);


--
-- TOC entry 4757 (class 2606 OID 16868)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 4759 (class 2606 OID 16866)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- TOC entry 4776 (class 2606 OID 17262)
-- Name: vouchers vouchers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vouchers
    ADD CONSTRAINT vouchers_pkey PRIMARY KEY (voucher_id);


--
-- TOC entry 4764 (class 1259 OID 16883)
-- Name: personal_access_tokens_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_id);


--
-- TOC entry 4797 (class 2606 OID 17409)
-- Name: community_user community_user_community_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.community_user
    ADD CONSTRAINT community_user_community_id_foreign FOREIGN KEY (community_id) REFERENCES public.communities(community_id) ON DELETE CASCADE;


--
-- TOC entry 4798 (class 2606 OID 17414)
-- Name: community_user community_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.community_user
    ADD CONSTRAINT community_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- TOC entry 4793 (class 2606 OID 17374)
-- Name: discussion_comment discussion_comment_discus_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion_comment
    ADD CONSTRAINT discussion_comment_discus_id_foreign FOREIGN KEY (discus_id) REFERENCES public.discussion(discus_id);


--
-- TOC entry 4794 (class 2606 OID 17369)
-- Name: discussion_comment discussion_comment_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion_comment
    ADD CONSTRAINT discussion_comment_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(user_id);


--
-- TOC entry 4795 (class 2606 OID 17389)
-- Name: discussion_like discussion_like_discus_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion_like
    ADD CONSTRAINT discussion_like_discus_id_foreign FOREIGN KEY (discus_id) REFERENCES public.discussion(discus_id);


--
-- TOC entry 4796 (class 2606 OID 17384)
-- Name: discussion_like discussion_like_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion_like
    ADD CONSTRAINT discussion_like_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(user_id);


--
-- TOC entry 4792 (class 2606 OID 17355)
-- Name: discussion discussion_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.discussion
    ADD CONSTRAINT discussion_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(user_id);


--
-- TOC entry 4788 (class 2606 OID 17188)
-- Name: friend friend_user_id_1_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.friend
    ADD CONSTRAINT friend_user_id_1_foreign FOREIGN KEY (user_id_1) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- TOC entry 4789 (class 2606 OID 17193)
-- Name: friend friend_user_id_2_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.friend
    ADD CONSTRAINT friend_user_id_2_foreign FOREIGN KEY (user_id_2) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- TOC entry 4787 (class 2606 OID 16878)
-- Name: personal_access_tokens personal_access_tokens_tokenable_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_tokenable_id_foreign FOREIGN KEY (tokenable_id) REFERENCES public.users(user_id) ON DELETE CASCADE;


--
-- TOC entry 4790 (class 2606 OID 17240)
-- Name: tenants tenants_tencat_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.tenants
    ADD CONSTRAINT tenants_tencat_id_foreign FOREIGN KEY (tencat_id) REFERENCES public.tenant_categories(tencat_id);


--
-- TOC entry 4791 (class 2606 OID 17256)
-- Name: vouchers vouchers_tenant_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.vouchers
    ADD CONSTRAINT vouchers_tenant_id_foreign FOREIGN KEY (tenant_id) REFERENCES public.tenants(tenant_id);


-- Completed on 2025-04-22 06:36:37

--
-- PostgreSQL database dump complete
--

