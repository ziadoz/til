-- Typically it's common to add timestamp columns to the end of a database table, like this:
create table links (
	id bigserial primary key,
	link text,
	created_at timestamp without time zone not null default now(),
	updated_at timestamp without time zone not null default now(),
	deleted_at timestamp without time zone null default null  
);

-- However, when new columns are added it starts to look weird
-- links: id, link, created_at, updated_at, deleted_at, name, description

-- In databases that support it, it's possible to insert new columns *after* the column before the first timestamp column
-- However, some databases don't support that, and in the ones that do there can be a performance cost
-- links: id, link, name, description, created_at, updated_at, deleted_at

-- An alternative is to put the timestamps directly after the id instead
create table links (
	id bigserial primary key,
	created_at timestamp without time zone not null default now(),
	updated_at timestamp without time zone not null default now(),
	deleted_at timestamp without time zone null default null,
	link text
);

-- This way new columns look natural on the end without any performance cost or database support
-- links: id, created_at, updated_at, deleted_at, link, name, description

insert into links (link)
select 'https://www.example.com/path-' || num
from generate_series(1, 10000) num;