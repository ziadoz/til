-----------
-- Links --
-----------

drop table links;

create table links (
	id bigserial primary key,
	link text,
	created_at timestamp without time zone default now(),
	updated_at timestamp without time zone default now()
);

create index links_link_idx on links(link);

insert into links (link) 
values ('https://ziadoz.net'), ('https://www.bbc.co.uk');

explain
select * from links where link = 'https://ziadoz.net';

alter table links
add column tags json default '[]';

update links
set tags = '["portfolio"]'::json
where link = 'https://ziadoz.net';

update links
set tags = '["news"]'::json
where link = 'https://www.bbc.co.uk';

alter table links
alter column tags set data type jsonb using tags::jsonb,
alter column tags set default '[]';

create index links_tags_idx on links using gin(tags);

explain
select * from links where tags @@ '$[*] == "news"';

explain
select * from links where tags ? 'news';

explain
select * from links where tags ?| array['news', 'portfolio'];

select jt.*
from links,
json_table(tags, '$[*]' columns (id for ordinality, tag text path '$[*]')) as jt;

alter table links
add column deleted_at timestamp without time zone default null;

create index links_deleted_at_idx on links (deleted_at)
where deleted_at is null;

insert into links (link, deleted_at)
select 
    'https://www.example.com/path-' || num, 
    case 
        when random(1, 2) = 1 
    then now() 
        else null 
    end
from generate_series(1, 10000) num;

explain analyze
select * from links
where link like '%example.com%'
and deleted_at is null;

select count(*) from links where deleted_at is null;

-------------
-- Folders --
-------------

create extension ltree;

drop table folders;

create table folders (
	id bigserial primary key,
	name text,
	path ltree,
	created_at timestamp without time zone default now(),
	updated_at timestamp without time zone default now()
);

create index folders_path_idx on folders using gist(path);

insert into folders (name, path) values
('Main', 'main'),
('Sports', 'main.sports'),
('Football', 'main.sports.football'),
('Basketball', 'main.sports.basketball'),
('News', 'main.news'),
('Local', 'main.news.local'),
('Politics', 'main.news.politics'),
('Gaming', 'main.gaming'),
('PC', 'main.gaming.pc'),
('Console', 'main.gaming.console'),
('PlayStation', 'main.gaming.console.playstation'),
('Xbox', 'main.gaming.console.xbox'),
('Nintendo', 'main.gaming.console.nintendo');

select * from folders;
select * from folders where path ~ 'main.news.*';
select * from folders where path ~ 'main.sports.*';
select * from folders where path ~ '*.football';
select * from folders where path <@ 'main.sports';
select * from folders where path <@ 'main.gaming';

explain
select * from folders where path ~ '*.console.*';

----------------------
-- Full Text Search --
----------------------

create table documents (
	id bigserial primary key,
	title text,
	body text,
	created_at timestamp without time zone default now(),
	updated_at timestamp without time zone default now()
);

alter table documents
add column searchable tsvector
generated always as (to_tsvector('english', coalesce(title, '') || ' ' || coalesce(body, '') )) stored;

create index documents_searchable_idx on documents using gin(searchable);

select * from documents 
where searchable @@ to_tsquery('english', 'silent & hill');

select * from documents 
where searchable @@ to_tsquery('english', 'slipgate');

select * from documents 
where searchable @@ plainto_tsquery('english', 'silent hill 2 pyramid');

select * from documents 
where searchable @@ websearch_to_tsquery('english', 'much -"silent hill"');

select id, title, ts_rank_cd(searchable, query) as rank
from documents, websearch_to_tsquery('english', 'colour') as query
where searchable @@ query
order by rank desc;

select 
    id,
    title,
    ts_headline('english', body, query) as headline,
    ts_rank_cd(searchable, query) as rank
from documents, websearch_to_tsquery('english', 'colour') as query
where searchable @@ query
order by rank desc;