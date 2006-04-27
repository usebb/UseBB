-- $Header$

ALTER TABLE usebb_forums ALTER COLUMN descr SET DEFAULT '';
ALTER TABLE usebb_members ALTER COLUMN banned_reason SET DEFAULT '';
ALTER TABLE usebb_members ALTER COLUMN signature SET DEFAULT '';
ALTER TABLE usebb_posts ALTER COLUMN content SET DEFAULT '';
ALTER TABLE usebb_searches ALTER COLUMN results SET DEFAULT '';
ALTER TABLE usebb_stats ALTER COLUMN content SET DEFAULT '';