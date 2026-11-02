create table comment
(
    id       integer not null primary key autoincrement,
    post_id  integer not null references post (id) on delete cascade,
    content  text not null,
    gif_path text
);
